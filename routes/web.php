<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
// use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\CkeditorController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\GoogleController;
use App\Events\MessageSent;

use Illuminate\Support\Facades\Http;

use App\Http\Controllers\StudentController;
Route::get('/tra-cuu-ho-so', function(){
    return view('students.lookup');
})->name('students.lookup');

Route::get('/students/search', [StudentController::class, 'index'])->name('students.index');
Route::post('/students/search', [StudentController::class, 'search'])->name('students.search');

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/send-message', function () {
    // Gửi event sang Socket.IO
    $data = ['user' => 'Laravel', 'message' => 'Xin chào từ Laravel 🚀'];

    // Dùng socket.io-client phát sự kiện
    // -> nhưng tốt nhất dùng package pusher-like cho Laravel
    //Log::info("Test gửi tin nhắn:", $data);

    return response()->json($data);
});

Route::get('/', [HomeController::class, 'index']);





Auth::routes();
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('roles/permission',[RoleController::class,'permission'])->name('roles.permission');
Route::post('roles/permission',[RoleController::class,'storePermission'])->name('roles.store-permission');
Route::group(['middleware' => ['auth']], function() {
    Route::resource('admin/roles', RoleController::class);
    Route::resource('admin/users', UserController::class);
    Route::get('lang/{lang}', function ($lang) {
        if (!in_array($lang, ['en', 'vi'])) {
            abort(400);
        }
        session()->put('locale', $lang);
        app()->setLocale($lang);
        return redirect()->back();
    })->name('change.lang');

});


Route::middleware(['auth'])->prefix('/admin')->name('admin.')->group(function(){
    Route::get('/profile', [ProfileController::class,'index'])->name('profile');
    Route::patch('/profile', [ProfileController::class,'update'])->name('profile-update');
    Route::put('/profile', [ProfileController::class,'updatePassword'])->name('profile-password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile-destroy');
    
});

Route::get('user-notify', [NotificationsController::class, 'index']);
Route::get('notifications/get',[NotificationsController::class, 'getNotificationsData'])->name('notifications.get');
Route::get('language/get',[NotificationsController::class, 'getLanguage'])->name('language.get');
Route::get('navbar/search',[SearchController::class,'showNavbarSearchResults']);
Route::post('navbar/search',[SearchController::class,'showNavbarSearchResults']);


Route::get('ckeditor', [CkeditorController::class, 'index']);
Route::post('ckeditor/upload', [CkeditorController::class, 'upload'])->name('ckeditor.upload');
Route::post('ckeditor', [CkeditorController::class, 'store'])->name('ckeditor.store');


Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

// Route::get('/mail-test', function () {
//     Mail::raw('Gửi thử mail từ Laravel', function ($msg) {
//         $msg->to('tungocvan@gmail.com')->subject('Test Mail');
//     });

//     return 'Đã gửi mail!';
// });

Route::get('demo-search', [SearchController::class, 'index']);
Route::get('autocomplete', [SearchController::class, 'autocomplete'])->name('autocomplete');

Route::controller(GoogleController::class)->group(function(){
    Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('auth/google/callback', 'handleGoogleCallback');
});


Route::post('/submit-form', [App\Http\Controllers\FormController::class, 'store'])->name('form.store');

Route::get('/test-broadcast', function () {
    broadcast(new MessageSent('Tin nhắn test từ route'));
    return 'Gửi xong!';
});