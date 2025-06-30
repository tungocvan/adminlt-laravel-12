#!/bin/bash

# Thông tin kết nối MySQL
DB_USER="tungocvan"
DB_PASSWORD="Van@2024"
DB_NAME="db_laravel_livewire"

# Kiểm tra file .env có tồn tại không
if [ ! -f .env ]; then
    echo "File .env không tồn tại. Dừng script."
    exit 1
fi

echo "File .env tồn tại. Tiếp tục thực thi script."

# Kiểm tra kết nối MySQL
if ! mysql -u$DB_USER -p$DB_PASSWORD -e "SELECT VERSION();" >/dev/null 2>&1; then
    echo "Kết nối MySQL thất bại! Kiểm tra lại thông tin đăng nhập."
    exit 1
fi

echo "Kết nối MySQL thành công."

# Kiểm tra database có tồn tại không
DB_EXISTS=$(mysql -u$DB_USER -p$DB_PASSWORD -e "SHOW DATABASES LIKE '$DB_NAME';" | grep "$DB_NAME")

if [ -z "$DB_EXISTS" ]; then
    echo "Database $DB_NAME không tồn tại. Tiến hành tạo database..."
    mysql -u$DB_USER -p$DB_PASSWORD -e "CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    echo "Database $DB_NAME đã được tạo."
else
    echo "Database $DB_NAME đã tồn tại."
fi

# Cập nhật Composer
composer update

# Thực hiện migrate và seed
php artisan migrate:fresh --seed
