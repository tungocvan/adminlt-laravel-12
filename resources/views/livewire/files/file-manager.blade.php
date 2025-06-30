<div>
    {{-- @livewire('email.attach-file')
    <div class="input-group">
        <span class="input-group-btn">
          <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
            <i class="fa fa-picture-o"></i> Choose
          </a>
        </span>
        <input id="thumbnail" class="form-control" type="text" name="filepath">
    </div>
    <div id="holder" style="margin-top:15px;max-height:100px;"></div>
    
    <hr> --}}
    <div>
       <div wire:ignore x-data="{ name: 'editor_' + Math.floor(Math.random() * 10000) }">              
               <x-adminlte-text-editor name="{{ $name }}" id="{{ $name }}"  label="{{ $label }}" label-class="text-danger"
               igroup-size="sm" placeholder="Write some text..." :config="$config"/>
               <input id="{{$name}}-content" type="text" hidden>
               
               
       </div> 
     </div>
    
    <hr> 
   
     @script
       <script>        
          
           document.addEventListener('livewire:initialized', () => {               
                var id = $wire.name;
                if(id){
                    id = "#"+id;
                }else{
                    id = "#{{ $name }}"; 

                }                
                //console.log('id:',id);
                function editorSummerNote(id) {
                       let data = ''; // Biến lưu nội dung của trình soạn thảo
                       let editor = $(id);
                       editor.on('summernote.change', function(_, contents) {
                           data = contents; 
                       });   
                       // Khi blur khỏi trình soạn thảo, cập nhật vào Livewire
                       editor.on('summernote.blur.prevent', function() {            
                           $wire.content=data
                           if ($(`${id}-content`).length) {
                                $(`${id}-content`).val(data);
                            } // Hiển thị nội dung đã nhập
                           // có thể đẩy dữ liệu lên server
                           $wire.hanlderSubmit(data).then((res) => {
                               // console.log('data:',res);
                           });


                       });
                   }
                   
              
                editorSummerNote(id)
                $.extend($.summernote.options, {
                   buttons: {
                       lfm: function(context) {
                           let ui = $.summernote.ui;
                           let button = ui.button({
                               contents: '<i class="fa fa-image"></i> LFM', // Icon Font Awesome
                               tooltip: "Chèn ảnh từ LFM",
                               click: function() {                                
                                   let route_prefix = "/laravel-filemanager";
                                   window.open(route_prefix + "?type=image", "FileManager", "width=900,height=600");
                                   window.SetUrl = function(items) {
                                       let url = items.map(item => item.url).join(",");
                                       $(id).summernote('insertImage', url);
                                   };
                               }
                           });
                           return button.render();
                       }
                   }
               });   

            //    var route_prefix = "/laravel-filemanager";
            //    $('#lfm').filemanager('file', {prefix: route_prefix});
           })
       </script>
     @endscript
</div>
