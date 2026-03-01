<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\Portfolio;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PortfolioController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Validator;

class CheckEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check email inbox for new portfolio entries';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //NOTE: To get this to work, had to add PATH variable to top of crontab
        // to point to MAMP php version
        // Might need to do this to remote site as well

        // https://github.com/Webklex/laravel-imap#attachmentclass

        // Log::debug("Checking emails...");
        // Log::debug(shell_exec("php -v"));

        $oClient = Client::account('default');
        $oClient->connect();
        $aFolder = $oClient->getFolders();
        // Log::debug($aFolder);

        // Needed for plain imap php commands as part of text extraction
        // for Apple Mail emails with attachments
        $mail = $oClient->getConnection();

        $oFolder = $oClient->getFolder('INBOX');

        //Get all Messages of the current Mailbox $oFolder
        $aMessage = $oFolder->query()->all()->get();

        if($aMessage->count() > 0 ) {
            /** @var \Webklex\IMAP\Message $oMessage */
            foreach($aMessage as $oMessage){
                $id = $oMessage->getMessageNo();
                // Log::debug("Message id is $id and is from ".$oMessage->getTo()[0]->mailbox);

                // User will need converting from hex
                $username = base64_decode($oMessage->getTo()[0]->mailbox);
                // Log::debug("Username is $username");

                // Check User database to ensure only 1 (or none) user is returned
                if(User::where('email', $username)->count() == 1) {

                    $user = User::where('email', $username)->first();
                    // Log::debug($user);

                    $title = $oMessage->getSubject();
                    $description = "";

                    if($oMessage->hasTextBody()) {
                        $description .= $oMessage->getTextBody();
                    }
                    if($oMessage->hasHTMLBody()) {
                        $description .= strip_tags($oMessage->getHTMLBody());
                    }


                    // Put portfolio entry together

                    // Description cannot be empty
                    if($description == "") $description = "This portfolio entry was created by email";

                    // Log::debug("Title: $title");

                    // Create a request, then can send it all to PortfoliosController store() function
                    $request = new \Illuminate\Http\Request();

                    $request->replace([
                        'user_id'       => $user->id,
                        'actdate'       => now()->format('Y-m-d'),
                        'title'         => $title,
                        'description'   => $description,
                        'benefit'       => "",
                        'activity_id'   => 5,
                        'profile'       => 0,
                        'start'         => now()->format('Y-m-d H:m:s'),
                        'end'           => now()->addMinutes(30)->format('Y-m-d H:m:s')
                    ]);

                    // Log::debug($request->all());

                    $pc = new PortfolioController();
                    $portfolio_id = $pc->store($request, true);
                    //Log::debug("Portfolio ID: $portfolio_id");

                    $mimetype_array = array(
                        'application/msword', 
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'application/octet-stream',
                        'application/vnd.ms-powerpoint',
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'application/rtf',
                        'text/plain'
                    );

                    // Attachments
                    
                    foreach($oMessage->getAttachments() as $oAttachment) {
                        // Log::debug($oAttachment->getMimeType());

                        if(in_array($oAttachment->getMimeType(), $mimetype_array)) {
                            $orig_filename = $oAttachment->name;
                            // Save the file
                            $path_name = 'store/'.$user->id.'/tmp/'.$orig_filename;
                            $path = Storage::put($path_name, $oAttachment->content);

                            $this->processAttachment('App\Models\Portfolio', $portfolio_id, $user->id, 'Emailed document', '', '1 document', $orig_filename, $path_name, $oAttachment->getMimeType(), $oAttachment->size);
                        };
                    };


                }

                $oMessage->delete();
            }

            
        }

        $oClient->expunge();
        $oClient->disconnect();

    }


    public function processAttachment($subject_type, $subject_id, $user_id, $title, $description, $format, $orig_name, $filepath, $mimetype, $size) {
        // Log::debug("Processing attachment");
        $test = config('app.debug')? 1: 0;
        // Log::debug($test);
        // Log::debug($filepath);
        // Log::debug(storage_path('app'));

        $filepath2 = storage_path('app').'/'.$filepath;
        $path = Storage::putFile('store/'.$user_id, new File($filepath2));
        Storage::delete($filepath);

        $doc = new Document();
        $doc->saveDocument($test, $title, $description, $format, $mimetype, $size, $orig_name, $path, $subject_type, $subject_id, $user_id, TRUE);
    }

    // https://www.php.net/manual/en/function.imap-fetchbody.php

	private function create_part_array($structure, $prefix="") {
	    //print_r($structure);
	    if (sizeof($structure->parts) > 0) {    // There some sub parts
	        foreach ($structure->parts as $count => $part) {
	            $this->add_part_to_array($part, $prefix.($count+1), $part_array);
	        }
	    }else{    // Email does not have a seperate mime attachment for text
	        $part_array[] = array('part_number' => $prefix.'1', 'part_object' => $obj);
	    }
	   return $part_array;
    }

	// Sub function for create_part_array(). Only called by create_part_array() and itself.
	private function add_part_to_array($obj, $partno, & $part_array) {
	    $part_array[] = array('part_number' => $partno, 'part_object' => $obj);
	    if ($obj->type == 2) { // Check to see if the part is an attached email message, as in the RFC-822 type
	        //print_r($obj);
	        if (sizeof($obj->parts) > 0) {    // Check to see if the email has parts
	            foreach ($obj->parts as $count => $part) {
	                // Iterate here again to compensate for the broken way that imap_fetchbody() handles attachments
	                if (sizeof($part->parts) > 0) {
	                    foreach ($part->parts as $count2 => $part2) {
	                        $this->add_part_to_array($part2, $partno.".".($count2+1), $part_array);
	                    }
	                }else{    // Attached email does not have a seperate mime attachment for text
	                    $part_array[] = array('part_number' => $partno.'.'.($count+1), 'part_object' => $obj);
	                }
	            }
	        }else{    // Not sure if this is possible
	            $part_array[] = array('part_number' => $prefix.'.1', 'part_object' => $obj);
	        }
	    }else{    // If there are more sub-parts, expand them out.
	        if (property_exists($obj, 'parts') && sizeof($obj->parts) > 0) {
	            foreach ($obj->parts as $count => $p) {
	                $this->add_part_to_array($p, $partno.".".($count+1), $part_array);
	            }
	        }
	    }
	}



}
