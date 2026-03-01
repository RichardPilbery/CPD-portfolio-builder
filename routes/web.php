<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SwotController;
use App\Http\Controllers\PdpController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/cookie', [HomeController::class, 'cookie']);
Route::get('/about', [HomeController::class, 'about']);
Route::get('/ksf', [HomeController::class, 'ksf']);
Route::get('/hcpc', [HomeController::class, 'hcpc']);
Route::get('/pdps', [HomeController::class, 'pdps']);
Route::get('/cpd', [HomeController::class, 'cpd']);

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
    Route::post('/portfolio', [PortfolioController::class, 'store']);
    Route::get('/portfolio/ksf/{print?}', [PortfolioController::class, 'ksf']);
    Route::get('/portfolio/printksf', [PortfolioController::class, 'printksf']);
    Route::get('/portfolio/clf/{print?}', [PortfolioController::class, 'clf']);
    Route::get('/portfolio/printclf', [PortfolioController::class, 'printclf']);
    // Should place wildcard routes i.e. those with {} below hard set ones apparently!
    Route::get('/portfolio/step3', [PortfolioController::class, 'step3']);
    Route::post('/portfolio/step3', [PortfolioController::class, 'savestep3']);
    Route::get('/portfolio/step4/{audit}', [PortfolioController::class, 'step4']);
    Route::get('/portfolio/profile/{whichbit}/{audit}', [PortfolioController::class, 'downloadprofile']);
    Route::get('/portfolio/create', [PortfolioController::class, 'create']);
    Route::patch('/portfolio/{portfolio}', [PortfolioController::class, 'update']);
    Route::get('/portfolio/search/{query}', [PortfolioController::class, 'search']);
    Route::get('/portfolio/{portfolio}', [PortfolioController::class, 'show']);
    Route::get('/portfolio/{portfolio}/edit', [PortfolioController::class, 'edit']);
    Route::delete('/portfolio/{portfolio}', [PortfolioController::class, 'destroy']);
    Route::get('/portfolio/{portfolio}/print', [PortfolioController::class, 'print']);
    Route::get('/portfolio/{portfolio}/download', [PortfolioController::class, 'downloadentry']);

    Route::post('/portfolio/{portfolio}/documents', [DocumentController::class, 'create']);

    Route::resource('documents', DocumentController::class);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download']);

    Route::get('user/clients', [UserController::class, 'admin_clients']);
    Route::get('user/ksf', [UserController::class, 'ksf']);
    Route::post('user/ksf', [UserController::class, 'ksfupdate']);
    Route::get('user/clf', [UserController::class, 'clf']);
    Route::post('user/clf', [UserController::class, 'clfupdate']);
    Route::get('/user/search/{query}', [UserController::class, 'search']);
    Route::post('/user/migrateP', [UserController::class, 'processMigrationP']);
    Route::post('/user/migrateA', [UserController::class, 'processMigrationA']);
    Route::get('/user/migrate-portfolio', [UserController::class, 'migrateP']);
    Route::get('/user/{user}/{profile}/edit', [UserController::class, 'edit']);
    Route::get('/user/migrate-audit/{olduserid}/{user_id}', [UserController::class, 'migrateA']);
    Route::delete('/user/{user}', [UserController::class, 'admin_destroy']);

    Route::patch('user/{user}', [UserController::class, 'update']);
    // If updating a document, don't include portfolio wildcard
    // Instead get refernec from documnet (portfolio_id)

    Route::get('/audit/airway', [AuditController::class, 'airway'])->name('audit.airway');
    Route::get('/audit/summary', [AuditController::class, 'summary'])->name('audit.summary');
    Route::post('/audit/airway', [AuditController::class, 'downloadairway']);
    Route::post('/audit/download', [AuditController::class, 'download']);
    Route::resource('audit', AuditController::class);
    Route::get('/audit/downloadlog/{logtype}', [AuditController::class, 'downloadlog']);
    Route::get('/audit/search/{query}', [AuditController::class, 'search']);
    Route::get('/audit/{audit}/print', [AuditController::class, 'print']);
    Route::get('/audit/{audit}/download', [AuditController::class, 'downloadentry']);

    Route::resource('summary', SummaryController::class);
    Route::post('/summary/print', [SummaryController::class, 'print']);
    Route::get('/summary/create/{profile}', [SummaryController::class, 'create']);
    Route::get('/summary/{summary}/edit/{profile}', [SummaryController::class, 'edit']);

    Route::resource('swot', SwotController::class);
    Route::get('/pdp/print', [PDPController::class, 'print']);
    Route::resource('pdp', PdpController::class);
    Route::patch('/pdp/{pdp}/{toggle}', [PDPController::class, 'update']);

    Route::get('/admin', [UserController::class, 'admin_user']);
});


require __DIR__.'/auth.php';
