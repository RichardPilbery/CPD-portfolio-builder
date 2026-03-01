<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Audit;
use App\Models\Airway;
use App\Models\Vascular;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;

class AuditTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_check_guest_user_cannot_access_audit_page() {
        $response = $this->get('/audit/');
        $response->assertRedirect('/login');
    }

    public function test_check_user_can_create_new_audit_entry()
    {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();


        $audit = Audit::factory()->create(['user_id'=>$user->id]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('audits', [
            'user_id' => $user->id,
            'incnumber' => $audit->incnumber
        ]);

    }

    public function test_check_user_cannot_access_another_users_audit_entry()
    {
        $user = User::factory()->create();
        $audit = Audit::factory()->create(['user_id'=>$user->id]);

        $this->assertDatabaseHas('audits', [
            'user_id' => $user->id,
            'incnumber' => $audit->incnumber
        ]);

        $user2 = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user2->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $response = $this->get('/audit/'.$audit->id);
        $response->assertStatus(403);

    }

    public function test_check_user_can_update_audit_entry() {

        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $audit = Audit::factory()->create(['user_id'=>$user->id]);

        $test_inc = $audit->incnumber;

        $this->assertDatabaseHas('audits', [
            'user_id' => $user->id,
            'incnumber' => $test_inc
        ]);

        $newA = $audit;
        $newA->incdatetime = '2022-01-01T12:30';
        $newA->incnumber = "999";
        $newA->age = 69;

        $response = $this->patch('/audit/'.$audit->id, $newA->toArray());
        // dd($response);

        $this->assertDatabaseHas('audits', [
            'incnumber' => $newA->incnumber,
            'age' => $newA->age
        ]);

        

    }

    public function test_check_user_can_delete_audit_entry() {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $audit = Audit::factory()->create(['user_id'=>$user->id]);
        $response = $this->delete('/audit/'.$audit->id);

        $this->assertDatabaseMissing('audits', [
            'id' => $audit->id
        ]);


    }

    public function test_check_user_can_delete_an_audit_entry_with_a_document() {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $audit = Audit::factory()->create(['user_id'=>$user->id]);

        $typeOfFile = '1 PDF file';
        $test_image_dir = "store_test/";
        $orig_filename = "sample.pdf";
        $test_file_path = $test_image_dir.$orig_filename;
        $mimetype = "application/pdf";
        $fullpath = "store/".$user->id."/".md5($orig_filename).".pdf";
        $path = Storage::copy($test_file_path, $fullpath);
        $filesize = Storage::size($test_file_path);

        $document = Document::factory()->create([
            'user_id'       => $user->id,
            'subject_id'    => $audit->id,
            'subject_type'  => "App\Models\Audit",
            'title'         => 'Test document',
            'description'   => 'Test document description',
            'format'        => $typeOfFile,
            'origfilename'  => $orig_filename,
            'filepath'      => $fullpath,
            'mimetype'      => $mimetype,
            'filesize'      => $filesize
        ]);

        // Log::debug('Document ID is');
        // Log::debug($document->id);
        // Log::debug($document);

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'subject_type' => 'App\Models\Audit',
            'subject_id' => $audit->id,
            'user_id' => $user->id
        ]);

        $response = $this->delete('/audit/'.$audit->id);

        $this->assertDatabaseMissing('audits', [
            'id' => $audit->id
        ]);

        Storage::assertMissing($fullpath);

        Storage::delete($fullpath);
        Storage::deleteDirectory("store/".$user->id);
    }

    public function test_check_user_can_delete_a_audit_entry_with_a_document_and_not_delete_other_documents() {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $audit = Audit::factory()->create(['user_id'=>$user->id]);
        $audit2 = Audit::factory()->create(['user_id'=>$user->id]);

        $typeOfFile = '1 PDF file';
        $test_image_dir = "store_test/";
        $orig_filename = "sample.pdf";
        $test_file_path = $test_image_dir.$orig_filename;
        $mimetype = "application/pdf";
        $fullpath = "store/".$user->id."/".md5($orig_filename).".pdf";
        $path = Storage::copy($test_file_path, $fullpath);
        $filesize = Storage::size($test_file_path);

        $document = Document::factory()->create([
            'user_id'       => $user->id,
            'subject_id'    => $audit->id,
            'subject_type'  => "App\Models\Audit",
            'title'         => 'Test document',
            'description'   => 'Test document description',
            'format'        => $typeOfFile,
            'origfilename'  => $orig_filename,
            'filepath'      => $fullpath,
            'mimetype'      => $mimetype,
            'filesize'      => $filesize
        ]);

        $typeOfFile2 = '1 image file';
        $test_image_dir2 = "store_test/";
        $orig_filename2 = "sample.jpg";
        $test_file_path2 = $test_image_dir2.$orig_filename2;
        $mimetype2 = "image/jpeg";
        $fullpath2 = "store/".$user->id."/".md5($orig_filename2).".jpg";
        $path2 = Storage::copy($test_file_path2, $fullpath2);
        $filesize2 = Storage::size($test_file_path2);

        $document2 = Document::factory()->create([
            'user_id'       => $user->id,
            'subject_id'    => $audit2->id,
            'subject_type'  => "App\Models\Audit",
            'title'         => 'Test document',
            'description'   => 'Test document description',
            'format'        => $typeOfFile2,
            'origfilename'  => $orig_filename2,
            'filepath'      => $fullpath2,
            'mimetype'      => $mimetype2,
            'filesize'      => $filesize2
        ]);

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'subject_type' => 'App\Models\Audit',
            'subject_id' => $audit->id,
            'user_id' => $user->id
        ]);

        $response = $this->delete('/audit/'.$audit->id);

        $this->assertDatabaseMissing('audits', [
            'id' => $audit->id
        ]);

        $this->assertDatabaseHas('documents', [
            'id' => $document2->id,
            'subject_type' => 'App\Models\Audit',
            'subject_id' => $audit2->id,
            'user_id' => $user->id
        ]);

        Storage::assertMissing($fullpath);
        Storage::assertExists($fullpath2);

        Storage::delete($fullpath);
        Storage::delete($fullpath2);
        Storage::deleteDirectory("store/".$user->id);
    }

    public function test_add_audit_entry_with_skills() {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->post('/audit',[
            'incdatetime' => "2022-06-29T19:01",
            'user_id' => $user->id,
            'incnumber' => null,
            'age' => null,
            'ageunit' => "years",
            'sex' => 'male',
            'ampds' => null,
            'call_type' => '4',
            'provdiag' => null,
            'outcome' => null,
            'skillsearch' => null,
            'skill' => [
                115 => "Combiboard / Orthopaedic (scoop) Stretcher",
                98 => "Glasgow Coma Score"
            ],
            "note" => null,
            "doctitle" => null,
            "docdescription" => null,
            "docformat" => null
        ]);

        $response->assertSessionHasNoErrors();

        $audit = Audit::latest()->first();

        $this->assertDatabaseHas('audits', [
            'user_id' => $user->id,
            'sex' => 'male'
        ]);

        $this->assertDatabaseHas('audit_audititem', [
            'audit_id' => $audit->id,
            'audititem_id' => 115
        ]);

        $this->assertDatabaseHas('audit_audititem', [
            'audit_id' => $audit->id,
            'audititem_id' => 98
        ]);

    }

    public function test_update_audit_entry_with_skills() {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->post('/audit',[
            'incdatetime' => "2022-06-29T19:01",
            'user_id' => $user->id,
            'incnumber' => null,
            'age' => null,
            'ageunit' => "years",
            'sex' => 'male',
            'ampds' => null,
            'call_type' => '4',
            'provdiag' => null,
            'outcome' => null,
            'skillsearch' => null,
            'skill' => [
                115 => "Combiboard / Orthopaedic (scoop) Stretcher",
                98 => "Glasgow Coma Score"
            ],
            "note" => null,
            "doctitle" => null,
            "docdescription" => null,
            "docformat" => null
        ]);

        $response->assertSessionHasNoErrors();

        $audit = Audit::latest()->first();

        $this->assertDatabaseHas('audits', [
            'user_id' => $user->id,
            'sex' => 'male'
        ]);

        $this->assertDatabaseHas('audit_audititem', [
            'audit_id' => $audit->id,
            'audititem_id' => 115
        ]);

        $this->assertDatabaseHas('audit_audititem', [
            'audit_id' => $audit->id,
            'audititem_id' => 98
        ]);


        $newA = $audit;

        $newA->incdatetime = "2022-06-29T19:01";
        $newA->skill = [
            95 => "Heart Rate",
            114 => "Traction Splint"
        ];

        $response = $this->patch('/audit/'.$audit->id, $newA->toArray());

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('audit_audititem', [
            'audit_id' => $audit->id,
            'audititem_id' => 115
        ]);

        $this->assertDatabaseMissing('audit_audititem', [
            'audit_id' => $audit->id,
            'audititem_id' => 98
        ]);

        $this->assertDatabaseHas('audit_audititem', [
            'audit_id' => $audit->id,
            'audititem_id' => 95
        ]);

        $this->assertDatabaseHas('audit_audititem', [
            'audit_id' => $audit->id,
            'audititem_id' => 114
        ]);


    }

    public function test_delete_audit_entry_with_skills() {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->post('/audit',[
            'incdatetime' => "2022-06-29T19:01",
            'user_id' => $user->id,
            'incnumber' => null,
            'age' => null,
            'ageunit' => "years",
            'sex' => 'male',
            'ampds' => null,
            'call_type' => '4',
            'provdiag' => null,
            'outcome' => null,
            'skillsearch' => null,
            'skill' => [
                115 => "Combiboard / Orthopaedic (scoop) Stretcher",
                98 => "Glasgow Coma Score"
            ],
            "note" => null,
            "doctitle" => null,
            "docdescription" => null,
            "docformat" => null
        ]);

        $response->assertSessionHasNoErrors();

        $audit = Audit::latest()->first();

        $this->assertDatabaseHas('audits', [
            'user_id' => $user->id,
            'sex' => 'male'
        ]);

        $this->assertDatabaseHas('audit_audititem', [
            'audit_id' => $audit->id,
            'audititem_id' => 115
        ]);

        $this->assertDatabaseHas('audit_audititem', [
            'audit_id' => $audit->id,
            'audititem_id' => 98
        ]);

        $response = $this->delete('audit/'.$audit->id);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('audits', [
            'id' => $audit->id,
        ]);

        $this->assertDatabaseMissing('audit_audititem', [
            'audit_id' => $audit->id,
            'audititem_id' => 115
        ]);

        $this->assertDatabaseMissing('audit_audititem', [
            'audit_id' => $audit->id,
            'audititem_id' => 98
        ]);
    }

    public function test_check_user_can_create_new_audit_entry_with_airways()
    {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->post('/audit', [
            "incdatetime" => "2022-07-02T09:58",
            'user_id' => $user->id,
            "incnumber" => '12345678',
            "age" => "22",
            "ageunit" => "years",
            "sex" => "female",
            "ampds" => null,
            "call_type" => "4",
            "provdiag" => null,
            "outcome" => null,
            "skillsearch" => null,
            "airway" => [
              [
                "display" => "Oropharyngeal airway (OPA)",
                "airwaytype_id" => "1",
                "success" => "1",
                "grade" => null,
                "size" => null,
                "bougie" => "0",
                "capnography_id" => "0",
                "notes" => "Airway note",
              ],
              [
                "display" => "Intubation",
                "airwaytype_id" => "7",
                "success" => "0",
                "grade" => "2",
                "size" => "2",
                "bougie" => "1",
                "capnography_id" => "1",
                "notes" => "Failed intubation",
              ]
            ],
            "note" => null,
            "doctitle" => null,
            "docdescription" => null,
            "docformat" => null,
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('audits', [
            'user_id' => $user->id,
            'incnumber' => '12345678'
        ]);

        $audit = Audit::latest()->first();

        $this->assertDatabaseHas('airways', [
            'audit_id' => $audit->id,
            'airwaytype_id' => 7,
            'success' => 0
        ]);

    }

    public function test_check_user_can_update_airway_entries()
    {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->post('/audit', [
            "incdatetime" => "2022-07-02T09:58",
            'user_id' => $user->id,
            "incnumber" => '12345678',
            "age" => "22",
            "ageunit" => "years",
            "sex" => "female",
            "ampds" => null,
            "call_type" => "4",
            "provdiag" => null,
            "outcome" => null,
            "skillsearch" => null,
            "airway" => [
              [
                "display" => "Intubation",
                "airwaytype_id" => "7",
                "success" => "0",
                "grade" => "2",
                "size" => "2",
                "bougie" => "1",
                "capnography_id" => "1",
                "notes" => "Failed intubation",
              ]
            ],
            "note" => null,
            "doctitle" => null,
            "docdescription" => null,
            "docformat" => null,
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('audits', [
            'user_id' => $user->id,
            'incnumber' => '12345678'
        ]);

        $audit = Audit::with('airways')->latest()->first();

        // dd($audit);

        $this->assertDatabaseHas('airways', [
            'audit_id' => $audit->id,
            'airwaytype_id' => 7,
            'success' => 0
        ]);

        $newA = $audit;
        $newA->incdatetime = "2022-07-02T09:58";
        $newA->airway = [
            [
                "display" => "Intubation",
                "airwaytype_id" => "7",
                "success" => "1",
                "grade" => "3",
                "size" => "3",
                "bougie" => "1",
                "capnography_id" => "1",
                "notes" => "Successful intubation",
              ]
        ];

        $response = $this->patch('/audit/'.$audit->id, $newA->toArray());
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('airways', [
            'audit_id' => $audit->id,
            'airwaytype_id' => 7,
            'success' => 1,
            'size' => 3,
            'grade' => 3
        ]);

    }

    public function test_check_user_can_delete_airway_entries()
    {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->post('/audit', [
            "incdatetime" => "2022-07-02T09:58",
            'user_id' => $user->id,
            "incnumber" => '12345678',
            "age" => "22",
            "ageunit" => "years",
            "sex" => "female",
            "ampds" => null,
            "call_type" => "4",
            "provdiag" => null,
            "outcome" => null,
            "skillsearch" => null,
            "airway" => [
              [
                "display" => "Intubation",
                "airwaytype_id" => "7",
                "success" => "0",
                "grade" => "2",
                "size" => "2",
                "bougie" => "1",
                "capnography_id" => "1",
                "notes" => "Failed intubation",
              ]
            ],
            "note" => null,
            "doctitle" => null,
            "docdescription" => null,
            "docformat" => null,
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('audits', [
            'user_id' => $user->id,
            'incnumber' => '12345678'
        ]);

        $audit = Audit::with('airways')->latest()->first();

        // dd($audit);

        $this->assertDatabaseHas('airways', [
            'audit_id' => $audit->id,
            'airwaytype_id' => 7,
            'success' => 0
        ]);

        $newA = $audit;
        $newA->incdatetime = "2022-07-02T09:58";
        $newA->airway = null;

        $response = $this->patch('/audit/'.$audit->id, $newA->toArray());
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('airways', [
            'audit_id' => $audit->id,
            'airwaytype_id' => 7,
        ]);

    }

    public function test_check_user_can_create_new_audit_entry_with_vasculars()
    {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->post('/audit', [
            "incdatetime" => "2022-07-02T09:58",
            'user_id' => $user->id,
            "incnumber" => '12345678',
            "age" => "22",
            "ageunit" => "years",
            "sex" => "female",
            "ampds" => null,
            "call_type" => "4",
            "provdiag" => null,
            "outcome" => null,
            "skillsearch" => null,
            "vascular" => [
                [
                    "display" => "IV cannula left dorsum",
                    "size" => "18",
                    "success" => "1",
                    "ivtype_id" => "1",
                    "location" => "left",
                    "ivsite_id" => "2"
                ],
                [
                    "display" => "IV cannula right antecubital fossa",
                    "size" => "14",
                    "success" => "0",
                    "ivtype_id" => "1",
                    "location" => "right",
                    "ivsite_id" => "3",
              ]
            ],
            "note" => null,
            "doctitle" => null,
            "docdescription" => null,
            "docformat" => null,
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('audits', [
            'user_id' => $user->id,
            'incnumber' => '12345678'
        ]);

        $audit = Audit::latest()->first();

        $this->assertDatabaseHas('vasculars', [
            'audit_id' => $audit->id,
            'ivtype_id' => 1,
            'success' => 0
        ]);

    }

    public function test_check_user_can_update_vascular_entries()
    {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->post('/audit', [
            "incdatetime" => "2022-07-02T09:58",
            'user_id' => $user->id,
            "incnumber" => '12345678',
            "age" => "22",
            "ageunit" => "years",
            "sex" => "female",
            "ampds" => null,
            "call_type" => "4",
            "provdiag" => null,
            "outcome" => null,
            "skillsearch" => null,
            "vascular" => [
                [
                    "display" => "IV cannula left dorsum",
                    "size" => "18",
                    "success" => "1",
                    "ivtype_id" => "1",
                    "location" => "left",
                    "ivsite_id" => "2"
                ]
            ],
            "note" => null,
            "doctitle" => null,
            "docdescription" => null,
            "docformat" => null,
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('audits', [
            'user_id' => $user->id,
            'incnumber' => '12345678'
        ]);

        $audit = Audit::with('vasculars')->latest()->first();

        // dd($audit);

        $this->assertDatabaseHas('vasculars', [
            'audit_id' => $audit->id,
            'ivtype_id' => 1,
            'success' => 1
        ]);

        $newV = $audit;
        $newV->incdatetime = "2022-07-02T09:58";
        $newV->vascular = [
            [
                "display" => "IV cannula left dorsum",
                "size" => "16",
                "success" => "0",
                "ivtype_id" => "1",
                "location" => "left",
                "ivsite_id" => "2"
            ]
        ];

        $response = $this->patch('/audit/'.$audit->id, $newV->toArray());
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('vasculars', [
            'audit_id' => $audit->id,
            'ivtype_id' => 1,
            'success' => 0,
        ]);

    }

    public function test_check_user_can_delete_vascular_entries()
    {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->post('/audit', [
            "incdatetime" => "2022-07-02T09:58",
            'user_id' => $user->id,
            "incnumber" => '12345678',
            "age" => "22",
            "ageunit" => "years",
            "sex" => "female",
            "ampds" => null,
            "call_type" => "4",
            "provdiag" => null,
            "outcome" => null,
            "skillsearch" => null,
            "vascular" => [
                [
                    "display" => "IV cannula left dorsum",
                    "size" => "18",
                    "success" => "1",
                    "ivtype_id" => "1",
                    "location" => "left",
                    "ivsite_id" => "2"
                ]
            ],
            "note" => null,
            "doctitle" => null,
            "docdescription" => null,
            "docformat" => null,
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('audits', [
            'user_id' => $user->id,
            'incnumber' => '12345678'
        ]);

        $audit = Audit::with('vasculars')->latest()->first();

        // dd($audit);

        $this->assertDatabaseHas('vasculars', [
            'audit_id' => $audit->id,
            'ivtype_id' => 1,
            'success' => 1
        ]);

        $newV = $audit;
        $newV->incdatetime = "2022-07-02T09:58";
        $newV->vascular = null;

        $response = $this->patch('/audit/'.$audit->id, $newV->toArray());
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('vasculars', [
            'audit_id' => $audit->id,
            'ivtype_id' => 1,
        ]);

    }

    
}
