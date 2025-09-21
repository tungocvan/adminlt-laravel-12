export function createLfmButton(id) {
    return function (context) {
        let ui = $.summernote.ui;
        let button = ui.button({
            contents: '<i class="fa fa-image"></i> LFM',
            tooltip: "Chèn ảnh từ LFM",
            click: function () {
                let route_prefix = "/laravel-filemanager";
                window.open(route_prefix + "?type=image", "FileManager", "width=900,height=600");
                window.SetUrl = function(items) {
                    let url = items.map(item => item.url).join(",");
                    $('#' + id).summernote('insertImage', url);
                };
            }
        });
        return button.render();
    }
}

export function initSummernote(id, height, livewire) {
    console.log('initSummernote');
    $('#' + id).summernote({
        height: height,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['lfm']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        buttons: {
            lfm: createLfmButton(id)
        },
        callbacks: {
            onChange: function(contents) {
                if (livewire) {
                    livewire.set(id, contents);
                }
            }
        }
    });
}
