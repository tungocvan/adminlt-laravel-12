#!/bin/bash

check_mysql_connection() {
    local sql_command=$1
    # Lấy đường dẫn tuyệt đối của file .sh hiện tại
    local current_dir="$(cd "$(dirname "$0")" && pwd)"
   current_dir="$(pwd)"
   cd ..
    local env_file="$(pwd)/.env"  # Đường dẫn đến file .env

    # Kiểm tra sự tồn tại của file .env
    if [[ ! -f "$env_file" ]]; then
        echo "File .env không tồn tại. Vui lòng kiểm tra lại."
        return 1
    fi

    echo "File .env tồn tại tại đường dẫn: $env_file"

    # Đọc thông tin từ file .env
    local username=$(grep -oP 'DB_USERNAME=\K.*' "$env_file")
    local password=$(grep -oP 'DB_PASSWORD=\K.*' "$env_file")
    local hostname=$(grep -oP 'DB_HOST=\K.*' "$env_file")

    # Kiểm tra các biến có được đặt hay không
    if [[ -z "$username" || -z "$password" || -z "$hostname" ]]; then
        echo "Vui lòng kiểm tra file .env, một hoặc nhiều thông tin đang thiếu."
        return 1
    fi

    # Kiểm tra kết nối đến MySQL
    if mysql -u"$username" -p"$password" -h"$hostname" -e "EXIT" >/dev/null 2>&1; then
        # Thực hiện câu lệnh MySQL nếu có
        if [[ -n "$sql_command" ]]; then
            local result=$(mysql -u"$username" -p"$password" -h"$hostname" -e "$sql_command")
            echo "$result"  # Trả về kết quả
        fi
        return 0
    else
        echo "Kết nối đến MySQL thất bại."
        return 1
    fi
}