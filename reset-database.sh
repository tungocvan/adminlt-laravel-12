#!/bin/bash
# Thực hiện migrate và seed
php artisan migrate:fresh --seed
php artisan optimize:clear
