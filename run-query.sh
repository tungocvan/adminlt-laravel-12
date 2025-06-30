#!/bin/bash
param1="$1"
param2="$2"
if [ -z "$param1" ]; then
  echo "Vui long nhap ten module can tao"
  exit 1
fi
php artisan db:mysql query "$param1"
