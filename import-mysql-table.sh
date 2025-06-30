#!/bin/bash

# Nhập thông tin kết nối MySQL từ bàn phím
read -p "DB_USER (default: tungocvan): " DB_USER
read -sp "DB_PASSWORD (default: Van@2024): " DB_PASSWORD
echo
read -p "DB_NAME (default: db_laravel_livewire): " DB_NAME
read -p "DB_FILE (tệp SQL để nhập, default: file.sql): " DB_FILE

# Gán giá trị mặc định nếu không nhập
DB_USER=${DB_USER:-tungocvan}
DB_PASSWORD=${DB_PASSWORD:-Van@2024}
DB_NAME=${DB_NAME:-db_laravel_livewire}
DB_FILE=${DB_FILE:-file.sql}

# Kiểm tra xem cơ sở dữ liệu có tồn tại không
if ! mysql -u "$DB_USER" -p"$DB_PASSWORD" -e "USE $DB_NAME;" 2>/dev/null; then
    echo "Cơ sở dữ liệu $DB_NAME không tồn tại."
    exit 1
fi

# Kiểm tra từng bảng trong tệp SQL
TABLES=$(grep -oP '(?<=CREATE TABLE `)[^`]+(?=`)' "$DB_FILE")

for TABLE in $TABLES; do
    if mysql -u "$DB_USER" -p"$DB_PASSWORD" -D "$DB_NAME" -e "DESC $TABLE;" > /dev/null 2>&1; then
        echo "Bảng $TABLE đã tồn tại."
        read -p "Bạn muốn ghi đè (y) hay bỏ qua (n)? [Mặc định: n] " CHOICE
        CHOICE=${CHOICE:-n}  # Mặc định là 'n'

        if [[ "$CHOICE" == "y" ]]; then
            echo "Đang ghi đè bảng: $TABLE"
            mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < "$DB_FILE"
        else
            echo "Bỏ qua bảng: $TABLE"
        fi
    else
        echo "Đang nhập bảng mới: $TABLE"
        mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < "$DB_FILE"
    fi
done

echo "Hoàn tất nhập tệp SQL."