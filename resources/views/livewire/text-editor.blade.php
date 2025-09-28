<div wire:ignore>
    @if ($label)
        <label class="{{ $labelClass }}">{{ $label }}</label>
    @endif

    {{-- Summernote --}}
    <x-adminlte-text-editor 
        name="{{ $name }}" 
        id="{{ $name }}" 
        :config="$config" 
        placeholder="{{ $placeholder }}" />

    {{-- Preview --}}
    {{-- <p class="mt-2">📌 <strong>Nội dung đã nhập:</strong></p>
    <div id="{{ $name }}-content" class="border p-2 mt-2 bg-light"></div> --}}
   
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function editorSummerNote(id) {
                let data = ''; 
                let editor = $('#' + id);
    
                // Lắng nghe khi nội dung thay đổi
                editor.on('summernote.change', function(_, contents) {
                    data = contents;
                    $('#' + id + '-content').html(data);
                });
    
                // Khi blur, cập nhật vào Livewire property
                editor.on('summernote.blur', function() {
                    @this.set('content', data);
                });
            }
    
            editorSummerNote("{{ $name }}");
    
            // Tạo nút LFM (Laravel File Manager)
            $.extend($.summernote.options, {
                buttons: {
                    lfm: function(context) {
                        let ui = $.summernote.ui;
                        let button = ui.button({
                            contents: '<i class="fa fa-image"></i> LFM',
                            tooltip: "Chèn ảnh từ LFM",
                            click: function() {
                                let route_prefix = "/laravel-filemanager";
                                window.open(route_prefix + "?type=image", "FileManager", "width=900,height=600");
                                window.SetUrl = function(items) {
                                    let url = items.map(item => item.url).join(",");
                                    $('#{{ $name }}').summernote('insertImage', url);
                                };
                            }
                        });
                        return button.render();
                    }
                }
            });
        });
    </script>
    
</div>



