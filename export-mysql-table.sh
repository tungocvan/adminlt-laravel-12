#!/bin/bash

# Nhập thông tin kết nối MySQL từ bàn phím
read -p "DB_USER (default: tungocvan): " DB_USER
read -sp "DB_PASSWORD (default: Van@2024): " DB_PASSWORD
echo
read -p "DB_NAME (default: db_laravel_livewire): " DB_NAME
read -p "DB_FILE (default: file.sql): " DB_FILE

# Gán giá trị mặc định nếu không nhập
DB_USER=${DB_USER:-tungocvan}
DB_PASSWORD=${DB_PASSWORD:-Van@2024}
DB_NAME=${DB_NAME:-db_laravel_livewire}
DB_FILE=${DB_FILE:-file.sql}

# Nhập tham số cho kiểu xuất
read -p "Nhập 0 để xuất cấu trúc bảng (no data) hoặc 1 để xuất cấu trúc và dữ liệu (add-drop-table) (default: 0): " EXPORT_OPTION
EXPORT_OPTION=${EXPORT_OPTION:-0}

# Chọn tham số mysqldump dựa trên nhập vào
if [ "$EXPORT_OPTION" -eq 1 ]; then
    DUMP_OPTION="--add-drop-table"
else
    DUMP_OPTION="--no-data"
fi

# Kiểm tra số lượng tham số
if [ "$#" -lt 1 ]; then
    echo "Vui lòng nhập ít nhất một tên bảng."
    exit 1
fi

# Khởi tạo file DB_FILE (xoá nội dung nếu đã tồn tại)
> "$DB_FILE"

# Xuất từng bảng
for TABLE in "$@"; do
    # Kiểm tra xem bảng có tồn tại không
    if mysql -u "$DB_USER" -p"$DB_PASSWORD" -D "$DB_NAME" -e "DESC $TABLE;" > /dev/null 2>&1; then
        echo "Đang xuất bảng: $TABLE"
        mysqldump $DUMP_OPTION -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" "$TABLE" >> "$DB_FILE"
    else
        echo "Bảng $TABLE không tồn tại, bỏ qua."
    fi
done

echo "Hoàn tất xuất bảng."