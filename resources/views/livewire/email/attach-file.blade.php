<div x-data="attachFile">
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
</div>
@script
<script>
    Alpine.data('attachFile', () => ({            
            attachmentNames: [], // Biến lưu tên file   
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
