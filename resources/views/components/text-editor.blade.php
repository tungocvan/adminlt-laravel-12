<div wire:ignore>
    @if ($label)
        <label class="{{ $labelClass }}">{{ $label }}</label>
    @endif
    <x-adminlte-text-editor name="{{ $name }}" id="{{ $name }}" :config="$config" placeholder="Nhập nội dung..." />
    <p class="mt-2">📌 <strong>Nội dung đã nhập:</strong></p>
    <div id="{{ $name }}-content" class="border p-2 mt-2 bg-light"></div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function editorSummerNote(id) {
            console.log('id:',id);
            let data = ''; // Biến lưu nội dung của trình soạn thảo
            let editor = $('#' + id);
            editor.on('summernote.change', function(_, contents) {
                data = contents; // Cập nhật nội dung vào biến
                console.log('data:',data);
                $('#' + id + '-content').html(data); // Hiển thị nội dung đã nhập
            });

            // Khi blur khỏi trình soạn thảo, cập nhật vào Livewire nếu có
            editor.on('summernote.blur', function() {            
                @this.set(id, data);
            });
        }
        
        editorSummerNote("{{ $name }}");
    });

    // Tạo nút chèn ảnh từ Laravel File Manager (LFM)
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
</script>
@endpush
