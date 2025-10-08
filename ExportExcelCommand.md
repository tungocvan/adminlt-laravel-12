php artisan export:excel "App\Models\User"
php artisan export:excel "App\Models\User" --ids=1,2,3
php artisan export:excel "App\Models\User" --fields=id,name,email,created_at
php artisan export:excel "App\Models\User" --fields="id:ID,name:Họ tên,email:Email,created_at:Ngày tạo"
php artisan export:excel "App\Models\User" \
  --ids=1,2,3
  --fields="id:ID,name:Họ tên,email:Email" \
  --title="BÁO CÁO DANH SÁCH NGƯỜI DÙNG" \
  --footer="Người lập bảng: Nguyễn Văn A"



php artisan export:excel "App\Models\WpProduct" --ids=46,47,48 --fields="id:ID,title:Tên sản phẩm,description:Nội dung,regular_price:Giá sản phẩm" --title="BÁO CÁO DANH SÁCH SẢN PHẨM" --footer="Người lập bảng: Nguyễn Văn A"

sử dụng laravel12 + livewrie 3.1+ boostarp 4.6 tôi muốn viết 1 component như sau:
@livewire('export:excel') => nhận các tham số model, title,footer , còn phần fields sẻ hiện thị trong blade để chọn gồm 2 cột 
cột đầu tiên checkbox để chọn cột hiển thị, cố thứ 2 hiện thị tên field, cột thứ 3 là tên tiếng việt mình muốn hiển thị ! 
