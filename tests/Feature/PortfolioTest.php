<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Portfolio;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;

class PortfolioTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_check_guest_user_cannot_access_dashboard() {
        $response = $this->get('/portfolio/');
        $response->assertRedirect('/login');

        $response = $this->get('/home/');
        $response->assertRedirect('/login');
    }

    public function test_check_user_can_create_new_portfolio_entry()
    {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $test_title = 'Test title';

        $response = $this->post('/portfolio', [
                "actdate" => '2022-01-01',
                "title" => $test_title,
                "description" => 'Fake description',
                "benefit" => 'Fake benefit',
                "activity_id" => 3,
                "profile" => 0,
                'start'  => '2022-01-01T12:30',
                'end' => '2022-01-01T13:00',
                'user_id' => $user->id
            ]);

            //dd($response);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('portfolios', [
            'user_id' => $user->id,
            'title' => $test_title
        ]);

    }

    public function test_check_user_cannot_access_another_users_portfolio_entry()
    {
        $user = User::factory()->create();
        $portfolio = Portfolio::factory()->create(['user_id'=>$user->id]);

        $test_title = $portfolio->title;

        $this->assertDatabaseHas('portfolios', [
            'user_id' => $user->id,
            'title' => $test_title
        ]);

        $user2 = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user2->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $response = $this->get('/portfolio/'.$portfolio->id);
        $response->assertStatus(403);

        //return true;

    }

    public function test_check_user_can_update_portfolio_entry() {

        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $portfolio = Portfolio::factory()->create(['user_id'=>$user->id]);

        $test_title = $portfolio->title;

        $this->assertDatabaseHas('portfolios', [
            'user_id' => $user->id,
            'title' => $test_title
        ]);

        $newP = $portfolio;

        $newP->title = "Updated title";
        $newP->description = "Updated description";
        $newP->start = '2022-01-01T12:30';
        $newP->end =  '2022-01-01T13:00';

        $response = $this->patch('/portfolio/'.$portfolio->id, $newP->toArray());

        $this->assertDatabaseHas('portfolios', [
            'title' => $newP->title,
            'description' => $newP->description
        ]);

        

    }

    public function test_check_user_can_delete_portfolio_entry() {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $portfolio = Portfolio::factory()->create(['user_id'=>$user->id]);
        $response = $this->delete('/portfolio/'.$portfolio->id);

        $this->assertDatabaseMissing('portfolios', [
            'id' => $portfolio->id
        ]);


    }

    public function test_check_user_can_delete_a_portfolio_entry_with_a_document() {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $portfolio = Portfolio::factory()->create(['user_id'=>$user->id]);

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
            'subject_id'    => $portfolio->id,
            'subject_type'  => "App\Models\Portfolio",
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
            'subject_type' => 'App\Models\Portfolio',
            'subject_id' => $portfolio->id,
            'user_id' => $user->id
        ]);

        $response = $this->delete('/portfolio/'.$portfolio->id);

        $this->assertDatabaseMissing('portfolios', [
            'id' => $portfolio->id
        ]);

        Storage::assertMissing($fullpath);

        Storage::delete($fullpath);
        Storage::deleteDirectory("store/".$user->id);
    }

    public function test_check_user_can_delete_a_portfolio_entry_with_a_document_and_not_delete_other_documents() {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $portfolio = Portfolio::factory()->create(['user_id'=>$user->id]);
        $portfolio2 = Portfolio::factory()->create(['user_id'=>$user->id]);

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
            'subject_id'    => $portfolio->id,
            'subject_type'  => "App\Models\Portfolio",
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
            'subject_id'    => $portfolio2->id,
            'subject_type'  => "App\Models\Portfolio",
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
            'subject_type' => 'App\Models\Portfolio',
            'subject_id' => $portfolio->id,
            'user_id' => $user->id
        ]);

        $response = $this->delete('/portfolio/'.$portfolio->id);

        $this->assertDatabaseMissing('portfolios', [
            'id' => $portfolio->id
        ]);

        $this->assertDatabaseHas('documents', [
            'id' => $document2->id,
            'subject_type' => 'App\Models\Portfolio',
            'subject_id' => $portfolio2->id,
            'user_id' => $user->id
        ]);

        Storage::assertMissing($fullpath);
        Storage::assertExists($fullpath2);

        Storage::delete($fullpath);
        Storage::delete($fullpath2);
        Storage::deleteDirectory("store/".$user->id);
    }

    public function test_check_user_cannot_delete_another_users_portfolio_entry() {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $portfolio = Portfolio::factory()->create(['user_id'=>$user->id]);
        $portfolio2 = Portfolio::factory()->create(['user_id'=>$user2->id]);

        $response = $this->delete('/portfolio/'.$portfolio2->id);

        $response->assertStatus(403);

        $this->assertDatabaseHas('portfolios', [
            'id' => $portfolio2->id,
        ]);

        $this->assertDatabaseHas('portfolios', [
            'id' => $portfolio->id
        ]);


    }

    public function test_check_user_cannot_delete_another_users_portfolio_entry_with_a_document() {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $portfolio = Portfolio::factory()->create(['user_id'=>$user->id]);
        $portfolio2 = Portfolio::factory()->create(['user_id'=>$user2->id]);

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
            'subject_id'    => $portfolio->id,
            'subject_type'  => "App\Models\Portfolio",
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
        $fullpath2 = "store/".$user2->id."/".md5($orig_filename2).".jpg";
        $path2 = Storage::copy($test_file_path2, $fullpath2);
        $filesize2 = Storage::size($test_file_path2);

        $document2 = Document::factory()->create([
            'user_id'       => $user2->id,
            'subject_id'    => $portfolio2->id,
            'subject_type'  => "App\Models\Portfolio",
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
            'subject_type' => 'App\Models\Portfolio',
            'subject_id' => $portfolio->id,
            'user_id' => $user->id
        ]);

        $response = $this->delete('/portfolio/'.$portfolio2->id);

        $response->assertStatus(403);

        $this->assertDatabaseHas('portfolios', [
            'id' => $portfolio2->id
        ]);

        $this->assertDatabaseHas('documents', [
            'id' => $document2->id,
            'subject_type' => 'App\Models\Portfolio',
            'subject_id' => $portfolio2->id,
            'user_id' => $user2->id
        ]);

        Storage::assertExists($fullpath2);

        Storage::delete($fullpath);
        Storage::delete($fullpath2);
        Storage::deleteDirectory("store/".$user->id);
        Storage::deleteDirectory("store/".$user2->id);
    }

    public function test_check_portfolio_entry_can_have_comptencies_added() {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->post('/portfolio', [
                "actdate" => '2022-01-01',
                "title" => 'Test title with comptency',
                "description" => 'Fake description',
                "benefit" => 'Fake benefit',
                "activity_id" => 3,
                "profile" => 0,
                'start'  => '2022-01-01T12:30',
                'end' => '2022-01-01T13:00',
                'user_id' => $user->id,
                'comp' => [
                    "KSF-1" => "Core 1: Communication",
                    "CLF-9" => "L3.1: Managing services: Planning"
                ],
            ]);

        // dd($response->all());

        // dd($portfolio);

        $response->assertSessionHasNoErrors();

        $portfolio = Portfolio::latest()->first();

        // Log::debug($portolio);

        $this->assertDatabaseHas('ksf_portfolio', [
            'ksf_id' => 1,
            'portfolio_id' => $portfolio->id
        ]);

        $this->assertDatabaseHas('clf_portfolio', [
            'clf_id' => 9,
            'portfolio_id' => $portfolio->id
        ]);

    }

    public function test_check_user_can_update_competencies_in_a_portfolio_entry() {

        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $response = $this->post('/portfolio', [
            "actdate" => '2022-01-01',
            "title" => 'Test title with comptency',
            "description" => 'Fake description',
            "benefit" => 'Fake benefit',
            "activity_id" => 3,
            "profile" => 0,
            'start'  => '2022-01-01T12:30',
            'end' => '2022-01-01T13:00',
            'user_id' => $user->id,
            'comp' => [
                "KSF-1" => "Core 1: Communication",
                "CLF-9" => "L3.1: Managing services: Planning"
            ],
        ]);

        $response->assertSessionHasNoErrors();

        $portfolio = Portfolio::latest()->first();

        // Log::debug($portolio);

        $this->assertDatabaseHas('ksf_portfolio', [
            'ksf_id' => 1,
            'portfolio_id' => $portfolio->id
        ]);

        $this->assertDatabaseHas('clf_portfolio', [
            'clf_id' => 9,
            'portfolio_id' => $portfolio->id
        ]);

        $newP = $portfolio;

        $newP->title = "Updated title";
        $newP->description = "Updated description";
        $newP->start = '2022-01-01T12:30';
        $newP->end =  '2022-01-01T13:00';
        $newP->comp = [
            "KSF-2" => "Test",
            "CLF-10" => "Test"
        ];

        $response = $this->patch('/portfolio/'.$portfolio->id, $newP->toArray());

        $this->assertDatabaseHas('ksf_portfolio', [
            'ksf_id' => 2,
            'portfolio_id' => $portfolio->id
        ]);

        $this->assertDatabaseHas('clf_portfolio', [
            'clf_id' => 10,
            'portfolio_id' => $portfolio->id
        ]);

        $this->assertDatabaseMissing('ksf_portfolio', [
            'ksf_id' => 1,
            'portfolio_id' => $portfolio->id
        ]);

        $this->assertDatabaseMissing('clf_portfolio', [
            'clf_id' => 9,
            'portfolio_id' => $portfolio->id
        ]);

        

    }

    public function test_check_competencies_are_deleted_when_portfolio_entry_is_deleted() {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->post('/portfolio', [
                "actdate" => '2022-01-01',
                "title" => 'Test title with comptency',
                "description" => 'Fake description',
                "benefit" => 'Fake benefit',
                "activity_id" => 3,
                "profile" => 0,
                'start'  => '2022-01-01T12:30',
                'end' => '2022-01-01T13:00',
                'user_id' => $user->id,
                'comp' => [
                    "KSF-1" => "Core 1: Communication",
                    "CLF-9" => "L3.1: Managing services: Planning"
                ],
            ]);

        $response->assertSessionHasNoErrors();

        $portfolio = Portfolio::latest()->first();

        // Log::debug($portolio);

        $this->assertDatabaseHas('ksf_portfolio', [
            'ksf_id' => 1,
            'portfolio_id' => $portfolio->id
        ]);

        $this->assertDatabaseHas('clf_portfolio', [
            'clf_id' => 9,
            'portfolio_id' => $portfolio->id
        ]);

        $response = $this->delete('/portfolio/'.$portfolio->id);

        $this->assertDatabaseMissing('portfolios', [
            'id' => $portfolio->id
        ]);

        $this->assertDatabaseMissing('ksf_portfolio', [
            'ksf_id' => 1,
            'portfolio_id' => $portfolio->id
        ]);

        $this->assertDatabaseMissing('clf_portfolio', [
            'clf_id' => 9,
            'portfolio_id' => $portfolio->id
        ]);



    }
}
