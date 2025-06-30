<div x-data="emailEditor">
    <div class="form-group d-flex flex-row">
        <div class="d-flex flex-row" style="width:95%;position:relative">
            <input id="toEmail" class="form-control" placeholder="To:" wire:model="to" x-on:blur="checkTo"  x-model="to">
            <i x-show="to.length > 0" x-on:click="to = ''" class="fas fa-times" style="position: absolute; right:10px; top:10px;cursor: pointer;"></i>
        </div>        
        <button x-on:click="isCc = !isCc" class="btn-sm btn-outline-danger mx-1">Cc</button>
        <button x-on:click="isBcc = !isBcc" class="btn-sm btn-outline-danger mx-1">Bcc</button>
    </div>
    <div class="form-group" x-show="isCc">
        <input class="form-control" placeholder="Cc:" wire:model="cc">
    </div>
    <div class="form-group" x-show="isBcc">
        <input class="form-control" placeholder="Bcc:" wire:model="bcc">
    </div>
    <div class="form-group">
        <input class="form-control" placeholder="Subject:" wire:model="subject">
    </div>
    @livewire('files.file-manager', ['name' => 'email', 'label' => 'Nhập nội dung', 'height' => '300'])

    <div class="form-group">
        <div class="btn btn-default btn-file">      
           <i class="fas fa-paperclip"></i> <span>Attachment</span> 
          <input wire:model="photos" type="file" id="photoInput"  x-on:change="uploadFiles" multiple>          
        </div>
                  
        <template x-if="attachmentNames.length > 0">
            <template x-for="(item, index) in attachmentNames">    
                <div class="mx-2 attach-item" style="display: contents">
                    <i class="fas fa-paperclip" x-text="item"></i>
                    <button x-on:click="deleteItem(index)" class="btn-sm btn-outline-danger">X</button>
                </div>
            </template>
        </template> 
        <p class="help-block">Max. 10MB</p>
    </div>

    <button type="button" class="btn btn-primary" 
            x-on:click="save" 
            x-bind:disabled="isSending || isTo">
        <span x-show="!isSending">Send Mail</span>
        <span x-show="isSending">Sending...</span>
    </button>

    <p x-show="message" x-text="message" class="alert alert-info mt-2"></p>
</div>
@script
<script>
    Alpine.data('emailEditor', () => ({
            attachmentName: 'Attachment', // Biến lưu tên file
            attachmentNames: [], // Biến lưu tên file
            isSending: false, // Trạng thái gửi email        
            isTo:true,
            to:'',
            isCc:false,
            isBcc:false,
            message: '', // Thông báo kết quả
            
            checkTo(){
                to = $('#toEmail').val();
                if(to.length > 5){
                    this.isTo = false
                }else{
                    this.isTo = true
                }
                
            },
            save() {
                this.isSending = true; // Khóa nút gửi
                this.message = ''; // Xóa thông báo cũ
                let data = document.querySelector('#email-content').value;      
                $wire.call('SendMail', data).then((res) => {
                    console.log('res:', res);
                    this.message = res; // Hiển thị kết quả trả về
                    }).catch((error) => {
                        console.error('Lỗi gửi mail:', error);
                        this.message = 'Gửi mail thất bại!';
                    }).finally(() => {
                        this.isSending = false; // Mở khóa nút gửi
                    });
                },
            uploadFiles() {
                let fileInput = document.querySelector('#photoInput');
                if (!fileInput || fileInput.files.length === 0) {
                    alert('Vui lòng chọn file!');
                    return;
                }
        
                let files = Array.from(fileInput.files); // Chuyển NodeList thành mảng

                $wire.uploadMultiple('photos', files, (uploadedFilename) => {                       
                        $wire.call('saveFiles').then(() => {
                            files.forEach(file => {
                                this.attachmentNames.push(file.name)
                            })
                        });

                    }, (error) => {
                        console.error('Lỗi upload:', error);
                    });
            },
            deleteItem(index){
                console.log('index:',index);
                this.attachmentNames.splice(index,1)
                $wire.deleteIndex(index);
            }

        })
        );
    
 
</script>
@endscript