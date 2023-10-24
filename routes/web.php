<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Service\SMTPController;
use App\Http\Controllers\Service\RecipientController;
use App\Http\Controllers\Service\EmailTemplateController;
use App\Http\Controllers\Service\MailController;
use App\Http\Controllers\DashboardController;

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


Route::get('/dashboard',[DashboardController::Class,'index'])->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';


Route::group(['namespace' => 'Service','prefix' => 'service','middleware' => 'auth'], function() {

    Route::get('/smtp/add-smtp', [SMTPController::Class,'create'])->name('add-new-smtp');
    Route::post('/save-smtp', [SMTPController::Class,'store'])->name('save-smtp');
    Route::get('/smtp/list-all', [SMTPController::Class,'index'])->name('list-all');
    Route::get('/smtp/{id}/edit', [SMTPController::Class,'edit'])->name('edit-smtp');
    Route::put('/update-smtp/{id}', [SMTPController::Class,'update'])->name('update-smtp');
    Route::post('/smtp/test', [SMTPController::Class,'test_smtp'])->name('test-smtp');
    Route::post('/smtp/test-smtp-by-id', [SMTPController::Class,'test_smtp_by_id'])->name('test-smtp-by-id');
    Route::delete('/smtp/delete/{id}',[SMTPController::Class,'destroy'])->name('delete-smtp');

    Route::get('/recipient/upload-form', [RecipientController::Class,'upload_form'])->name('upload-recipients-form');
    Route::get('/recipient/list-all', [RecipientController::Class,'index'])->name('recipients-list');
    Route::post('/recipient/upload', [RecipientController::Class,'upload'])->name('recipients-upload');
    Route::delete('/recipient/delete/{id}',[RecipientController::Class,'destroy'])->name('delete-recipients');

    Route::get('/template/add-template', [EmailTemplateController::Class,'create'])->name('add-new-template');
    Route::post('/template/save-template', [EmailTemplateController::Class,'store'])->name('save-template');
    Route::get('/template/list-all', [EmailTemplateController::Class,'index'])->name('list-all-template');
    Route::get('/template/{id}/edit', [EmailTemplateController::Class,'edit'])->name('edit-template');
    Route::put('/update-template/{id}', [EmailTemplateController::Class,'update'])->name('update-template');
    Route::get('/show-template/{id}', [EmailTemplateController::Class,'show'])->name('show-template');
    Route::delete('/template/delete/{id}',[EmailTemplateController::Class,'destroy'])->name('delete-template');
    Route::post('/template/upload-image', [EmailTemplateController::Class,'upload_image'])->name('upload-image');

    Route::get('/email/create-email', [MailController::Class,'create_email'])->name('create-email');
    Route::get('/email/list-all-email', [MailController::Class,'list_all_email'])->name('list-all-emails');
    Route::post('/email/send-email', [MailController::Class,'send_email'])->name('send-email');
    Route::post('/email/prepare-email', [MailController::Class,'prepare_to_send_email'])->name('prepare-email');
});