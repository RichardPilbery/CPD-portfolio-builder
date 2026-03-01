<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Laravel\Scout\Searchable;
use League\Flysystem\MountManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    protected $guarded = [];

    // This will update the updated_at column of the portfolio table too!
    protected $touches = ['portfolio'];

    public function toSearchableArray()
    {
        $array = $this->toArray();

        return array('id' => $array['id'], 'user_id' => $array['user_id'], 'title' => $array['title'], 'origfilename' => $array['origfilename']);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class, 'portfolio_id', 'id');
    }

    public function store(Document $document) {
        dd($document);
    }

    public function saveDocument($test, $title, $description, $format, $mimetype, $size, $orig_name, $filepath, $subject_type, $subject_id, $user_id, $email = FALSE, $local = 'local')
    {
        // Log::debug("Inside saveDoc");

        // Need to set test variable as if it's localhost, different
        // command needed for processing cases that are docx/pptx etc.
        // Job send out command line instruction to convert docs using pandoc

        $test = url('/') == "http://127.0.0.1:8000"? 1 : 0;

        if($local == 'sftp') {

            // Log::debug($title);

            // Need to retrieve remote file and save it locally
            // Then amend filepath, mimetype, filesize
            // Note $filepath is actually $dir field for original site

            $remote_url = 'uploads/'.$filepath.'/'.$orig_name;
            Log::debug("Remote URL: ".$remote_url);

            $local_url = "store/".$user_id."/";

            // https://stackoverflow.com/a/19083216/3650230
            $filepath = $local_url.$this->random_string(32).".".pathinfo($orig_name, PATHINFO_EXTENSION);

            //$file = new File();
            // Check file exists first!
            if(Storage::disk('sftp')->exists($remote_url)) {
                $bool = Storage::disk('local')->writeStream($filepath, Storage::disk('sftp')->readStream($remote_url));

                // Log::debug("Result of file transfer: $bool");
            }
            else {
                // Remove mention of file
                $filepath = NULL;
                $mimetype = NULL;
                $filesize = NULL;
                $origfilename = NULL;
            }

            // Log::debug("Filepath is: ".$filepath);

        }

        if(is_null($title)) $title = $orig_name;
        if(is_null($format)) $format = $mimetype;

        $document = [
            'test' => $test,
            'subject_type' => $subject_type,
            'subject_id' => $subject_id,
            'user_id' => $user_id,
            'title' => $title,
            'description' => $description,
            'format' => $format,
            'origfilename' => $orig_name,
            'filepath' => $filepath,
            'mimetype' => $mimetype,
            'filesize' => $size,
            'user_id' => $user_id
        ];

        // Change of plan
        // Will now leave docs as is, but convert them when they are requested
        // to be downloaded.

        // $mimetype_array = array(
        //     'application/msword', 
        //     'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
        //     'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        //     'application/octet-stream',
        //     'application/vnd.ms-powerpoint',
        //     'application/rtf',
        //     'text/plain'
        // );

        // if(in_array($mimetype, $mimetype_array)) {
            
        //     Log::debug("Dispatching a document for conversion");
        //     // ProcessUpload::dispatch($attributes)->delay(now()->addMinutes(2));
        //     ProcessUpload::dispatch($document);

        //     return "";

        // }
        // else {
        //     unset($document['test']);
        //     return $this->create($document);
        // }

        unset($document['test']);
        return $this->create($document);

    }

    public function random_string($length) {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }
}
