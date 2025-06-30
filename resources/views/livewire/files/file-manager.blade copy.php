<div>
     <div class="input-group">
        <span class="input-group-btn">
          <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
            <i class="fa fa-picture-o"></i> Choose
          </a>
        </span>
        <input id="thumbnail" class="form-control" type="text" name="filepath">
      </div>
      <div id="holder" style="margin-top:15px;max-height:100px;"></div>
      <hr>
      {{-- summernote basic --}}
      <form  wire:submit.prevent="hanlderSubmit">
            <div id="editorContainer" wire:ignore>
                <!-- Trình soạn thảo Summernote -->
                <x-adminlte-text-editor name="teBasic" id="teBasic"></x-adminlte-text-editor>            
                <p class="mt-2">📌 <strong>Nội dung đã nhập:</strong></p>
                <div id="teBasic-content" class="border p-2 mt-2 bg-light"></div>
            </div>
            <button type="submit">Save</button>
      </form>

      {{-- summernote basic insert button lfm --}} 

      <div>
        <div wire:ignore> 
                @php
                $config = [
                    "height" => "100",
                    "toolbar" => [
                        // [groupName, [list of button]]
                        ['style', ['style']],
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video','lfm']],
                        ['view', ['fullscreen', 'codeview', 'help']],
                    ],
                ]
                @endphp
                <x-adminlte-text-editor name="teConfig" label="WYSIWYG Editor" label-class="text-danger"
                    igroup-size="sm" placeholder="Write some text..." :config="$config"/>


                <p class="mt-2">📌 <strong>Nội dung đã nhập:</strong></p>
                <div id="teConfig-content" class="border p-2 mt-2 bg-light"></div>
        </div> 
      </div>


      @script
        <script>            
              
              

            document.addEventListener('livewire:initialized', () => {
                var route_prefix = "/laravel-filemanager";
                $('#lfm').filemanager('file', {prefix: route_prefix});   
            })
            document.addEventListener('livewire:initialized', () => {
                 function editorSummerNote(id) {
                        let data = ''; // Biến lưu nội dung của trình soạn thảo
                        let editor = $(id);
                        editor.on('summernote.change', function(_, contents) {
                            data = contents; // Cập nhật nội dung vào biến
                            // có thể lưu data lên database ở đây
                            $(`${id}-content`).html(data); // Hiển thị nội dung đã nhập
                            // $('#editorContainer input:text').val(data); // Hiển thị nội dung đã nhập
                            
                            

                        });   
                        // Khi blur khỏi trình soạn thảo, cập nhật vào Livewire
                        editor.on('summernote.blur.prevent', function() {            
                            $wire.content=data
                        });
                    }
                editorSummerNote('#teBasic')
                editorSummerNote('#teConfig')
                
            })

            document.addEventListener('livewire:initialized', () => {
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
                                        $('#teConfig').summernote('insertImage', url);
                                    };
                                }
                            });
                            return button.render();
                        }
                    }
                });
            })
        </script>
      @endscript
</div>
