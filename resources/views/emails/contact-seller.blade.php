@component('mail::message')
# Liên hệ bán hàng mới

**Người gửi:** {{ $seller->name ?? 'N/A' }}  
**Email:** {{ $seller->email ?? 'N/A' }}  
**Loại người dùng:** {{ $seller->user_type ?? 'N/A' }}  
**Chủ đề:** {{ $seller->subject ?? 'N/A' }}  

---

**Nội dung:**

{{ $seller->message ?? '' }}
---

@if(!empty($seller->files))
@php $files = json_decode($seller->files, true); @endphp
**File đính kèm:**  
@foreach($files as $file)
- [{{ basename($file) }}]({{ asset('storage/' . ltrim($file, '/')) }})
@endforeach
@endif




@endcomponent
