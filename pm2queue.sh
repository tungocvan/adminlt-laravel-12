#!/bin/bash

# Chạy từ thư mục hiện tại
cd /var/www/adminlt
php artisan config:clear
pm2 start queue-worker.sh --name laravel-queue
