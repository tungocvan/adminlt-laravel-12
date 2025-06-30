<?php

namespace App\Livewire\Email;
use Illuminate\Support\Facades\Mail;
use Livewire\WithFileUploads;
use Livewire\Component;

class EmailMessage extends Component
{
    use WithFileUploads;
    public $photo;
    public $photos; // Mảng file upload
    public $uploadedFiles = []; // Lưu tên file sau khi upload
    public $pathDirectory= 'uploads';
    public $messages = []; // Thông báo
    public $to;
    public $cc;
    public $bcc;
    public $subject;
 
    public function deleteIndex($index){
        array_splice($this->uploadedFiles,$index,1);
        //dd($this->uploadedFiles);
    }
    public function saveFiles()
    {
        // Kiểm tra xem có file nào không
        if (empty($this->photos)) {
            $this->messages[] = "Vui lòng chọn file!";
            return;
        }

        // Validate tất cả các file
        $this->validate([
            'photos.*' => 'file|max:10240', // Mỗi file tối đa 10MB
        ]);

        foreach ($this->photos as $index => $photo) {
            if (!isset($this->uploadedFiles[$index])) {
                $this->uploadImage($index);
            }
        }

        //dd($this->uploadedFiles); // "uploads/cccd-matsau.jpg"

        // Gửi sự kiện về client báo upload thành công
        // $this->dispatchBrowserEvent('upload-success', ['files' => $this->uploadedFiles]);
    }
    public function uploadImage($index)
    {
        if (!isset($this->photos[$index]) || isset($this->uploadedFiles[$index])) return;

        $photo = $this->photos[$index];
        $filename = $photo->getClientOriginalName();

        // Lưu file vào storage/public/uploads
        $path = $photo->storeAs($this->pathDirectory, $filename, 'public');

        // Lưu trạng thái đã upload
        $this->uploadedFiles[$index] = $path;
        $this->messages[] = "Ảnh '{$filename}' đã upload thành công!";
    }
  
    public function SendMail($data)
{
    $attachments = [];

    // Kiểm tra file đính kèm
    if (!empty($this->uploadedFiles)) {
        foreach ($this->uploadedFiles as $file) {
            $filePath = storage_path("app/public/" . $file);
            if (file_exists($filePath)) {
                $attachments[] = $filePath;
            }
        }
    }

    // Chuyển danh sách email thành mảng nếu là chuỗi
    $to = is_string($this->to) ? array_map('trim', explode(';', $this->to)) : $this->to;
    $to = array_filter($to, function ($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    });

    if (empty($to)) {
        $this->dispatchBrowserEvent('alert', ['message' => 'Email gửi đi không hợp lệ']);
        return;
    }

    
    // Kiểm tra CC
    $cc = [];
    if (!empty($this->cc)) {
        $cc = is_string($this->cc) ? array_map('trim', explode(';', $this->cc)) : $this->cc;
        $cc = array_filter($cc, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });
    }

    // Kiểm tra BCC
    $bcc = [];
    if (!empty($this->bcc)) {
        $bcc = is_string($this->bcc) ? array_map('trim', explode(';', $this->bcc)) : $this->bcc;
        $bcc = array_filter($bcc, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });
    }

    // Nội dung và tiêu đề email
    $content = $data ?? '<h3>This is test mail</h3>';
    $subject = $this->subject ?? 'Email sent from tungocvan1@gmail.com';

    try {
        Mail::html($content, function ($message) use ($to, $cc, $bcc, $subject, $attachments) {
            $message->to($to);
            if (!empty($cc)) $message->cc($cc);
            if (!empty($bcc)) $message->bcc($bcc);
            $message->subject($subject);

            // Đính kèm file
            foreach ($attachments as $file) {
                $message->attach($file);
            }
        });

        return 'Đã gửi mail thành công';
    } catch (\Exception $e) {
        return 'Đã gửi mail thất bại: ' . $e->getMessage();
    }
}


    public function render()
    {
        return view('livewire.email.email-message');
    }
}
