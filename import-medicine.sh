#!/bin/bash
php artisan clean:table medicines
php artisan migrate
php artisan import:danhmucthuoc
