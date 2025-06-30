#!/bin/bash

# Nhập hàm từ file connect-my-sql.sh
source "./connect-my-sql.sh"

# Đặt câu lệnh SQL
sql_command="SHOW DATABASES;"

# Gọi hàm check_mysql_connection và hứng kết quả
result=$(check_mysql_connection "$sql_command")

# Kiểm tra xem có lỗi không và echo kết quả
if [[ $? -eq 0 ]]; then
    echo "Kết quả:"
    echo "$result"
else
    echo "Có lỗi xảy ra: $result"
fi