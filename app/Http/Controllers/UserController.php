<?php

namespace App\Http\Controllers;

// Can't seem to get the php.ini file to respect on localhost.
ini_set('max_execution_time', '300');

use App\Models\User;
use App\Models\Service;
use App\Models\Role;
use App\Models\Summary;
use App\Models\Portfolio;
use App\Models\Document;
use App\Models\Pdp;
use App\Models\Swot;
use App\Models\Audit;
use App\Models\Airway;
use App\Models\Vascular;
use App\Models\Ksf;
use App\Models\Clf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\MigrationCompleted;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    // Admin functions
    public function search($query) {

        abort_if(auth()->user()->admin !== 1, 403);
        //Log::debug("Inside the portfolio search function");
        $result = User::where(function ($q) use ($query) {
                $q->where('name', 'like', '%'.$query.'%')
                    ->orWhere('email', 'like', '%'.$query.'%');
            })
            ->orderBy('name', 'asc')
            ->select(['id','name', 'email'])
            ->limit(10)
            ->get();

        //Log::debug($result);

        return response()->json($result);

    }

    public function admin_destroy(User $user) {
        abort_if(auth()->user()->admin !== 1, 403);

        $id = $user->id;

        $files = $user->documents()->pluck('filepath')->toArray();

        // Log::debug($files);

        if(count($files)) {
            // Log::debug('Deleting files');

            Storage::delete($files);
        }
        // Delete polymorphic first
        $user->documents()->delete();

        // Then delete everything else
        $user->delete();

        return redirect('/admin')->with('success', 'User has been deleted');

    }

    public function admin_user() {
        //dd(auth()->user()->admin);
        abort_if(auth()->user()->admin !== 1, 403);

        // dd(url('/'));

        $services = Service::orderBy('title')->pluck('title','id');
        // dd($services);
        $roles = Role::orderBy('title')->pluck('title','id');

        $users = DB::table('users')->orderBy('last_login', 'DESC')->paginate(25);

        $portfolio_count = [];
        $audit_count = [];
        foreach ($users as $u) {
            #print_r($u);
            $portfolio_count[$u->id] = Portfolio::where('user_id', $u->id)->count();
            $audit_count[$u->id] = Audit::where('user_id', $u->id)->count();
        }

        #dd($portfolio_count);

        return view('user.admin_user', compact(['users', 'services', 'roles', 'portfolio_count', 'audit_count']));
    }

    public function admin_clients() {
        abort_if(auth()->user()->admin !== 1, 403);

        $clients = auth()->user()->clients;

        return view('user.admin_client', compact(['clients']));
    }


    public function ksf() {

        $referer = request()->headers->get('referer');

        $ksfrefer = !is_null($referer) && strpos($referer, 'ksf') ? 1: 0;
        $user_ksfs = auth()->user()->ksfs()->pluck('ksf_id')->toArray();
        $ksfs = Ksf::get();
        //dd($user_ksfs);

        return view('user.ksf', compact(['ksfs', 'user_ksfs', 'ksfrefer']));

    }

    public function ksfupdate(Request $request) {

        //dd($request);
        $user = auth()->user();
        $user->ksfs()->detach(); // Remove all KSFs
        if(!is_null($request->ksf_id)) {
            $update_ksfs = array_keys($request->ksf_id);
            $user->ksfs()->attach($update_ksfs); // Add new ones.
        }

        return redirect('/portfolio/ksf')->with('success', 'Your KSF dimensions have been updated');

    }

    public function clf() {

        $referer = request()->headers->get('referer');

        $clfrefer = !is_null($referer) && strpos($referer, 'clf') ? 1: 0;
        $user_clfs = auth()->user()->clfs()->pluck('clf_id')->toArray();
        $clfs = Clf::get();
        //dd($clfs);

        return view('user.clf', compact(['clfs', 'user_clfs', 'clfrefer']));

    }

    public function clfupdate(Request $request) {

        //dd($request);
        $user = auth()->user();
        $user->clfs()->detach(); // Remove all KSFs
        if(!is_null($request->clf_id)) {
            $update_clfs = array_keys($request->clf_id);
            $user->clfs()->attach($update_clfs); // Add new ones.
        }
    
        return redirect('/portfolio/clf')->with('success', 'Your CLF competencies have been updated');

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user, $profile)
    {

        $auth_user = \Auth::user();

        abort_if(
            (auth()->user()->admin === 0 && $user->id !== $auth_user->id)
            , 403);

        // Log::debug($user);
        $services = Service::orderBy('title')->get();
        $roles = Role::orderBy('title')->get();

        return view('user.edit', compact(['user', 'services', 'roles', 'profile']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'service_id' => 'required',
            'email' => ['required',
                'email',
                Rule::unique('users')->ignore($user)
            ]
        ]);

        // Not sure this is strictly necessary given the way
        // the reference is made to user() below, but belt and braces...
        $auth_user = \Auth::user();

        abort_if(
            (auth()->user()->admin === 0 && $user->id !== $auth_user->id)
            , 403);
        
        // Only admin allowed to have non-matching user->id and auth_user->id
        if($auth_user->admin === 1 && $user->id !== $auth_user->id) {
            $request['id'] = $user->id;
        } else {
            $request['id'] = auth()->user()->id;
        }
        
        if($request['password'] !== $user->password) {
            // Only change if new password entered
            // Log::debug('Password changing');
            // Log::debug($request->password);
            // Log::debug(Hash::make($request->password));
            // Log::debug($user->password);
            $request['password'] = Hash::make($request->password);
        }
        

        // Save profile variable as we'll need to remove it before updating
        $profile = $request['profile'];
        // Remove profile variable
        unset($request['profile']);

        $user->update($request->all());

        if($profile == 1) {
            // CPD profile updating....
            // Redirect to summary of practice page.
            $summary = Summary::where('user_id', auth()->user()->id)->first();
            //dd($summary);

            if($summary == null) {
                // No summary
                return redirect('/summary/create/1')->with('success', 'Step 1 completed');
            } else {
                return redirect('/summary/'.$summary->id.'/edit/1')->with('success', 'Step 1 completed');
            }
        }
        else {
            if($auth_user->admin === 1) {
                return redirect('/admin')->with('success', 'User details updated');
            } else {
                return redirect('/home')->with('success', 'Your details have been updated');
            }
            
        }

    }

    public function migrateP() {

        if(auth()->user()->admin != 1) {
            return redirect('/home')->with('error', 'Oops.');
        }

        $migrate_type = 'Portfolio';

        return view('user.migrate', compact(['migrate_type']));

    }

    public function migrateA($olduserid, $user_id) {

        // $olduserid is resuscitate.me user if
        // $user_id is the new id that has been set up when user migrated to this site

        if(auth()->user()->admin != 1) {
            return redirect('/home')->with('error', 'Oops.');
        }

        $migrate_type = 'Audit';

        return view('user.migrate-audit', compact(['migrate_type', 'olduserid', 'user_id']));

    }

    public function processMigrationP(Request $request) {

        if(auth()->user()->admin != 1) {
            return redirect('/home')->with('error', 'Oops.');
        }

        //dd($request->username);
        //Log::debug($request->all());

        /* SEND HASH OF PASSWORD NOT ACTUAL PASSWORD */
        $remote_url = url('/') == "http://127.0.0.1:8000" ? 'http://192.168.1.203/users/migrateBirdsToAfrica' : 'http://migration.com/users/migrateBirdsToAfrica';

        $response = Http::asForm()->post($remote_url, [
            'username' => $request->username,
            'password' => $request->password,
        ]);

        if($response->ok()) {
            $data = $response->body();

            //Log::debug(unserialize($data));

            // Set up new user

            $new_data = unserialize($data);

            if(!empty($new_data['User'])) {

                $user_info = $new_data['User'];
                $summary_info = $new_data['Summary'];
                $role = $new_data['Job'][0]['id'];
                $service = $new_data['Service']['id'] == '3' ? '13' : $new_data['Service']['id'];

                // Check if there is summary info

                $pin = null;

                if($summary_info['id'] != null) {
                    $pin = $summary_info['pin'];
                }

                $new_password = $this->randomPassword(10,1,"lower_case,upper_case,numbers,special_symbols");
                // Will need to send this to person.

                $user_id = DB::table("users")->insertGetId([
                    "name"              => $user_info['firstname']." ".$user_info['lastname'],
                    "email"             => $user_info['email'],
                    "email_verified_at" => null,
                    "password"          => bcrypt($new_password),
                    "admin"             => 0,
                    "remember_token"    => null,
                    "pin"               => $pin,
                    "role_id"           => $role,
                    "service_id"        => $service,
                   // "last_login"        => $user_info['last_login'] == '0000-00-00 00:00:00' ? date("Y-m-d H:i:s") : $user_info['last_login'],
                    "created_at"        => date("Y-m-d H:i:s"),
                    "updated_at"        => date("Y-m-d H:i:s")
                ]);

                // Set up summary

                // Check if there is a summary

                if($summary_info['id'] != null) {

                    if(DB::table("summaries")->insert([
                        "user_id"           => $user_id,
                        "work_details"      => $summary_info['work_details'],
                        "service_users"     => $summary_info['service_users'],
                        "job_description"   => $summary_info['job_description'],
                        "standard1"         => $summary_info['standard1'],
                        "standard2"         => $summary_info['standard2'],
                        "created_at"        => date("Y-m-d H:i:s"),
                        "updated_at"        => date("Y-m-d H:i:s")
                    ])) {
                        // Log::debug("Summary table created");
                    }

                } else {
                    // Log::debug("No summary table provided");
                }

                Log::debug("Username: ".$user_info['email']);
                Log::debug("New user id is $user_id");
                Log::debug("Password is: $new_password");

                // Check for portfolio entries

                $portfolio_info = $new_data['PortfolioEntry'];

                if(count($portfolio_info)) {

                    $portsuccess = 0;
                    $portsave = 0;

                    foreach($portfolio_info as $p) {

                        $portsave++;
                        // Organised as follows:
                        // PortfolioEntry
                        // User
                        // Activity
                        // Pdp
                        // Swot
                        // Upload - array
                        // KsfDimension - array
                        // Safe  - These are CLFs - very few in user (355 total)

                        // Log::debug($p);

                        $start = Carbon::create($p['PortfolioEntry']['when']);
                        $end = $start->add("30 minutes");

                        // Calculate start and end times
                        if($p['PortfolioEntry']['duration'] != '00:00:00') {

                            // Duration set
                            $durationAsTime = Carbon::create($p['PortfolioEntry']['duration']);
                            $hours = $durationAsTime->hour;
                            $minutes = $durationAsTime->minute;
                            $end = $start->addHours($hours)->addMinutes($minutes);

                        }

                        $portfolio = Portfolio::create([
                            "title"         => $p['PortfolioEntry']['title'],
                            "description"   => $p['PortfolioEntry']['description'],
                            "benefit"       => $p['PortfolioEntry']['benefit'],
                            "activity_id"   => $p['PortfolioEntry']['activity_id'],
                            "user_id"       => $user_id,
                            "profile"       => $p['PortfolioEntry']['profile'],
                            "actdate"       => date($p['PortfolioEntry']['when']),
                            "updated_at"   => $p['PortfolioEntry']['modified'],
                            "created_at"    => $p['PortfolioEntry']['created'],
                            "start"         => $start,
                            "end"           => $end
                        ]);

                        $portsuccess++;

                        // Add PDPs

                        if(count($p['Pdp'])) {

                            foreach($p['Pdp'] as $pdp) {
                                Pdp::create([
                                    'portfolio_id' => $portfolio->id,
                                    'user_id'      => $user_id,
                                    'objective'    => $pdp['objective'],
                                    'activity'     => $pdp['activity'],
                                    'measure'      => $pdp['measure'],
                                    'finishdate'   => $pdp['finishdate'],
                                    'support'      => $pdp['support'],
                                    'barriers'     => $pdp['barriers']
                                ]);
                            }
                        }

                        // Add SWOTs

                        if(count($p['Swot'])) {

                            foreach($p['Swot'] as $swot) {
                                Swot::create([
                                    'portfolio_id' => $portfolio->id,
                                    'strength'     => $swot['strength'],
                                    'weakness'     => $swot['weakness'],
                                    'opportunity'  => $swot['opportunity'],
                                    'threat'       => $swot['threat']
                                ]);
                            }
                        }

                        // Add KSF Dimensions

                        if(count($p['KsfDimension'])) {

                            $ksf = [];
                            foreach($p['KsfDimension'] as $k) {
                                $ksf[] = $k['id'];
                            }

                            $portfolio->ksfs()->attach($ksf);

                        }


                        // Add CLF

                        if(count($p['Safe'])) {

                            $clf = [];
                            foreach($p['Safe'] as $c) {
                                $clf[] = $c['id'];
                            }

                            $portfolio->clfs()->attach($clf);

                        }

                        // Check for Uploads


                        if(isset($p['Upload'])) {

                            // Log::debug("We have an upload");

                            // Document function saveDocument($test, $title, $description, $format, $mimetype, $size, $orig_name, $filepath, $subject_type, $subject_id, $user_id, $email = FALSE)

                            // Need to check if filename == NULL then no file to upload
                            // However, also if title, description and format are ''
                            // Then no need to create upload

                            //  array (
                            // 'id' => '18523',
                            // 'portfolio_entry_id' => '17289',
                            // 'title' => 'photo.JPG',
                            // 'description' => 'Uploaded via email',
                            // 'format' => 'JPEG image data, EXIF standard 2.21',
                            // 'filename' => '1370085305_photo.JPG',
                            // 'dir' => 'tricky999',
                            // 'mimetype' => 'image/jpeg',
                            // 'filesize' => '697915',
                            // 'created' => '2013-06-01 12:15:06',
                            // 'modified' => '2013-06-01 12:15:06',
                            // ),

                            foreach($p['Upload'] as $u) {
                                if($u['title'] == '' && $u['description'] == '' && $u['format'] == '' ) {
                                    // Ignore this one as it has nothing in it!
                                    // Log::debug("Empty upload");

                                } else {
                                    // Log::debug("Saving: ".$u['title']);

                                    $document = new Document;

                                    if($u['filename'] == NULL) {

                                        // No file upload so just create documnet entry without
                                        // file details.
                                        // Also no need to send to saveDocument function

                                        $document->saveDocument(
                                            0,
                                            $u['title'],
                                            $u['description'],
                                            $u['format'],
                                            NULL,
                                            NULL,
                                            NULL,
                                            NULL,
                                            'App\Models\Portfolio',
                                            $portfolio->id,
                                            $user_id,
                                            $email = FALSE,
                                            'sftp'
                                        );

                                    } else {
                                        $document->saveDocument(
                                            0,
                                            $u['title'],
                                            $u['description'],
                                            $u['format'],
                                            $u['mimetype'],
                                            $u['filesize'],
                                            $u['filename'],
                                            $u['dir'],
                                            'App\Models\Portfolio',
                                            $portfolio->id,
                                            $user_id,
                                            $email = FALSE,
                                            'sftp'
                                        );
                                    }
                                }

                            }



                        }
                    }


                    Log::debug("$portsuccess portfolio entries saved out of $portsave");

                    if(url('/') !== "http://127.0.0.1:8000") {
                        // Send email to user
                        Mail::to($user_info['email'])->send(new MigrationCompleted($user_info['email'], $new_password));
                    }

                    // Check if there are audit entries and redirect to import those if so
                    if(isset($new_data['Audit']) && !empty($new_data['Audit'])) {
                        return redirect('/user/migrate-audit/'.$user_info['id'].'/'.$user_id)->with('success', 'User '.$request->username.' portfolio migrated. Just audits to do.');
                    } else {
                        return redirect('/home')->with('success', 'User '.$request->username.' has been migrated');
                    }


                } else {
                    // Repition, but portfolios seem to be the ones most commonly failing for some reason.
                    if(url('/') !== "http://127.0.0.1:8000") {
                        // Send email to user
                        Mail::to($user_info['email'])->send(new MigrationCompleted($user_info['email'], $new_password));
                    }
                    // Log::debug("No portfolio entries provided");
                    // Check if there are audit entries and redirect to import those if so
                    if(isset($new_data['Audit']) && !empty($new_data['Audit'])) {
                        return redirect('/user/migrate-audit/'.$user_info['id'].'/'.$user_id)->with('success', 'User '.$request->username.' portfolio migrated. Just audits to do.');
                    } else {
                        return redirect('/home')->with('success', 'User '.$request->username.' has been migrated');
                    }
                }
            } else {
                // We got a response from the server, but there is No
                // User array, which means incorrect details entered.
                return redirect('/user/migrate-portfolio')->with('error', 'Response received, but no account matching those details');
            }


        } else {

            // There was a problem with the response
            // Need to alert the viewer
            Log::debug("Connection was not OK");
            return redirect('/home')->with('success', 'Connection not OK...probably IP address blocked');

        }



    }

    public function processMigrationA(Request $request) {

        if(auth()->user()->admin != 1) {
            return redirect('/home')->with('error', 'Oops.');
        }

        $old_user_id = $request->olduserid;
        $user_id = $request->user_id;
        //dd("Old User id is: ".$old_user_id." and new one is: ".$user_id);

        // NOTE: This address will only respond to a specific IP address to change prior to port

        $remote_url = url('/') == "http://127.0.0.1:8000" ? 'http://192.168.1.203/audits/migrating/' : 'http://migration.com/audits/migrating/';
        $response = Http::get($remote_url.$old_user_id);

        // dd($response);

        if($response->ok()) {
            $data = $response->body();

            //Log::debug("Response is okay");
            //Log::debug(unserialize($data));

            $result = unserialize($data);

            $auditcount = 0;
            $auditsaved = 0;

            if(!empty($result['audits'])) {

                foreach($result['audits'] as $a) {
                    $auditcount++;
                    $audit = Audit::create([
                        'user_id' => $user_id,
                        'age' => $a['Audit']['age'],
                        'ageunit' => $a['Audit']['ageunit'],
                        'sex' => $a['Audit']['sex'],
                        'incnumber' => $a['Audit']['incnumber'],
                        'provdiag' => $a['Audit']['provdiag'],
                        'note' => $a['Audit']['note'],
                        'incdatetime' => Carbon::create($a['Audit']['incdatetime'])
                    ]);
                    $auditsaved++;

                    // Create array of key => value pairs
                    // Audit id => Audititem id
                    $ai = $this->sortaudititems($result['items']);

                    if(count($ai[$a['Audit']['id']]) > 0) {

                        // Check if any items are airway and remove
                        $postairway = $this->checkAirwayItems($ai[$a['Audit']['id']], $a['Audit']);

                        // Associate airway items with Audit entry that has been created

                        $audit->audititems()->attach($postairway);
                    }

                        $this->saveAirwayItems($ai[$a['Audit']['id']], $a['Audit'], $audit->id);
                        $this->saveVascularItems($a['Audit'], $audit->id);
                }

            } else {
                // Something wrong with audit items
                // Return to migrate audit page with old and new IDs

            }

            Log::debug("$auditsaved Audits saved out of $auditcount");

            return redirect('/home')->with('success', "$auditsaved audits saved from total of $auditcount");
        }
    }

      /// From https://www.phpjabbers.com/generate-a-random-password-with-php-php70.html
    public function randomPassword($length, $count, $characters) {

        // $length - the length of the generated password
        // $count - number of passwords to be generated
        // $characters - types of characters to be used in the password

        // define variables used within the function
            $symbols = array();
            $passwords = array();
            $used_symbols = '';
            $pass = '';

        // an array of different character types
            $symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
            $symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $symbols["numbers"] = '1234567890';
            $symbols["special_symbols"] = '!?~@#-_+<>[]{}';

            $characters = explode(",",$characters); // get characters types to be used for the passsword
            foreach ($characters as $key=>$value) {
                $used_symbols .= $symbols[$value]; // build a string with all characters
            }
            $symbols_length = strlen($used_symbols) - 1; //strlen starts from 0 so to get number of characters deduct 1

            for ($p = 0; $p < $count; $p++) {
                $pass = '';
                for ($i = 0; $i < $length; $i++) {
                    $n = rand(0, $symbols_length); // get a random character from the string with all characters
                    $pass .= $used_symbols[$n]; // add the character to the password string
                }
                $passwords[] = $pass;
            }

            return $passwords[0]; // return the generated password
    }

    private function sortaudititems($items) {

        $itemsResult = [];
        foreach($items as $i) {
            //$this->log($i);
            if(array_key_exists($i['AuditsAudititem']['audit_id'], $itemsResult)) {
                $key = $i['AuditsAudititem']['audit_id'];
                $value = $i['AuditsAudititem']['audititem_id'];
                $itemsResult[$key][] = $value;
            } else {
                $itemsResult[$i['AuditsAudititem']['audit_id']] = [$i['AuditsAudititem']['audititem_id']];
            }
        }
        return($itemsResult);
    }

    private function checkAirwayItems($items) {
        //$this->log("Airway items");
        //$this->log($audit);
        // key: Airway items and value: their ID in airwaytypes table
        $airwayArray = [
            44 => 8,
            45 => 9,
            46 => 2,
            47 => 1,
            48 => 6,
            52 => 15,
            53 => 5,
            55 => 10,
            56 => 4,
            58 => 3,
            59 => 16,
            60 => 17,
            278 => 0,
            279 => 0,
            54 => 11,
            57 => 12,
            61 => 13,
            94 => 0,
            243 => 14
        ];
        // 278 = bougie
        // 279 = positube
        // 94 = capnography
        $airwaykeys = array_keys($airwayArray);
        $returnArray = array_diff($items, $airwaykeys);

        return($returnArray);

    }

    private function saveAirwayItems($items, $audit, $id) {
        // Log::debug($items);
        // Log::debug($audit);
        // Log::debug($id);
        $airwayArray = [
            44 => 8,
            45 => 9,
            46 => 2,
            47 => 1,
            48 => 6,
            52 => 15,
            53 => 5,
            55 => 10,
            56 => 4,
            58 => 3,
            59 => 16,
            60 => 17,
            278 => 0,
            279 => 0,
            54 => 11,
            57 => 12,
            61 => 13,
            94 => 0,
            243 => 14
        ];
        // 278 = bougie
        // 279 = positube
        // 94 = capnography
        $airwaykeys = array_keys($airwayArray);
        $inairways = array_intersect($items, $airwaykeys);


        $capnography = (in_array(94, $inairways))? 1 : 0;
        $positube = (in_array(279, $inairways))? 1 : 0;
        $bougie = (in_array(278, $inairways))? 1 : 0;
        $capvalues = [6, 10, 5, 11, 12, 13];

        if(count($inairways) > 0 || $audit['intattempts'] > 0) {
            // We have some airway stuff
            foreach($inairways as $air) {
                // Get new value we want
                $value = $airwayArray[$air];
                if($value > 0) {
                    $data = [
                        'audit_id' => $id,
                        'airwaytype_id' => $value,
                        'success' => 1,
                        'notes' => ''
                    ];
                    if(in_array($value, $capvalues) && $capnography) {
                        $data['capnography_id'] = ($positube)? 5 : 4;
                    }
                    //Log::debug("Airway data for saving is: ");
                    //Log::debug($data);
                    $airway = Airway::create($data);
                }
            }

            // Now check out intubation
            // This probably needs to iterate for as many times
            // as intattempts....
            if($audit['intattempts'] > 0) {
                $intsuccess = $audit['intsuccess'];
                for($i = 1; $i <= $audit['intattempts']; $i++) {
                    $intdata = [
                        'audit_id' => $id,
                        'airwaytype_id' => 7,
                        'success' => ($i <= $intsuccess)? 1 : 0,
                        'grade' => $audit['intgrade'],
                        'size' => $audit['tubesize'],
                        'bougie' => $bougie,
                        'capnography_id' => ($capnography)? 4 : NULL,
                        'notes' => ''
                    ];
                    //Log::debug("Intubation data for saving is: ");
                    //Log::debug($intdata);
                    $airway = Airway::create($intdata);
                }
            }

        }
    }

    private function saveVascularItems($audit, $id) {

        if($audit['canattempts'] > 0) {
            //Log::debug($audit);
            $canloc = [
                'ACF' => 3,
                'EJ' => 1,
                'Other' => 4,
                'Dorsum' => 2
            ];
            $cansuccess = $audit['cansuccess'];
            for($i = 1; $i <= $audit['canattempts']; $i++) {
                $guage = null;
                if(isset($audit['canguage'])) {
                    $guage = preg_replace('/[^0-9]/', '', $audit['canguage']);
                }
                $candata = [
                    'audit_id' => $id,
                    'ivtype_id' => 1,
                    'success' => ($i <= $cansuccess)? 1 : 0,
                    'size' => $guage,
                    'ivsite_id' => (!empty($audit['cansite']))? $canloc[$audit['cansite']] : 4
                ];
                $entry = Vascular::create($candata);
            }

        }
        if($audit['ioattempts'] > 0) {
            // IO attempts
            // Log::debug($audit);
            $ioloc = [
                'Proximal tibia' => 5,
                'Distal tibia' => 6,
                'Other' => 4,
                'Sternum' => 7,
                'Proximal Humerus' => 8
            ];
            $iotype = [
                'BIG' => 3,
                'Cook' => 2,
                'EZIO' => 4,
                'FAST' => 5,
                'Other' => 6
            ];
            $iosuccess = $audit['iosuccess'];
            for($i = 1; $i <= $audit['ioattempts']; $i++) {
                $iodata = [
                    'audit_id' => $id,
                    'ivtype_id' => !empty($iotype[$audit['iodevice']])? $iotype[$audit['iodevice']] : 4,
                    'success' => ($i <= $iosuccess)? 1 : 0,
                    'size' => 0,
                    'ivsite_id' => (!empty($audit['iosite']))? $ioloc[$audit['iosite']] : 4
                ];
                $entry = Vascular::create($iodata);
            }
        }
    }



}
