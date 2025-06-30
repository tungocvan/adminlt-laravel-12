#!/bin/bash

# Đọc file .env
if [ -f .env ]; then
    export $(grep -v '^#' .env | xargs)
else
    echo "File .env không tồn tại."
    exit 1
fi

# Lấy thông tin từ biến môi trường
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
DB_HOST=${DB_HOST:-localhost}  # Mặc định là localhost nếu không có giá trị trong .env

# Kiểm tra kết nối đến MySQL
mysql -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SELECT 1;" > /dev/null 2>&1

if [ $? -eq 0 ]; then
    echo "Kết nối đến MySQL thành công!"
else
    echo "Kết nối đến MySQL thất bại!"
fi