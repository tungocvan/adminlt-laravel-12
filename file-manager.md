https://unisharp.github.io/laravel-filemanager/installation
composer require unisharp/laravel-filemanager
composer require intervention/image-laravel
 php artisan vendor:publish --tag=lfm_config
 php artisan vendor:publish --tag=lfm_public
 ls -l /var/www/adminlt/public/storage
 php artisan storage:unlink
 php artisan storage:link
 php artisan route:clear
 php artisan config:clear
Edit APP_URL in .env (tên domain)
Edit routes/web.php
Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
     \UniSharp\LaravelFilemanager\Lfm::routes();
 });

