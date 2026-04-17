<textarea class="form-control richTextBox" name="{{ $row->field }}" id="richtext{{ $row->field }}">
    {{ old($row->field, $dataTypeContent->{$row->field} ?? '') }}
</textarea>

@push('javascript')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var additionalConfig = {
                selector: 'textarea.richTextBox[name="{{ $row->field }}"]',
                menubar: "file edit view insert format tools table help",
                selector: 'textarea.richTextBox',
                // base_url: $('meta[name="assets-path"]').attr('content') + '?path=js/',
                skin: 'oxide',
                min_height: 600,
                resize: true,
                plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons accordion',
                extended_valid_elements: 'input[id|name|value|type|class|style|required|placeholder|autocomplete|onclick]',
                quickbars_selection_toolbar: "bold italic | quicklink h2 h3 blockquote quickimage quicktable",
                toolbar_mode: "sliding",
                contextmenu: "link image table",
                relative_urls: false, // Necessary so uploaded images don't get a relative path but an URL instead.
                remove_script_host: false,
                // file_picker_types: 'image',
                file_picker_callback: (callback, value, meta) => {

                    if (meta.filetype == 'image' || meta.filetype == 'media') {
                        var input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*,video/*');

                        input.onchange = function() {
                            var formdata = new FormData();
                            formdata.append('image', this.files[0]);
                            formdata.append('type_slug', $('#upload_type_slug').val());

                            // Show loader
                            $('#voyager-loader').css('z-index', 10000);
                            $('#voyager-loader').fadeIn();
                            $.ajax({
                                    type: 'post',
                                    url: '{{ route('panelMedia.upload') }}',
                                    data: formdata,
                                    enctype: 'multipart/form-data',
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                })
                                .done((result) => {
                                    callback(result);
                                })
                                .always(() => {
                                    $('#voyager-loader').fadeOut();
                                    $('#voyager-loader').css('z-index', 99);
                                });
                        }

                        input.click();
                    }
                },
                toolbar: 'undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl',
                image_caption: true,
                image_title: true,
                init_instance_callback: function(editor) {
                    if (typeof tinymce_init_callback !== "undefined") {
                        tinymce_init_callback(editor);
                    }
                },
                setup: function(editor) {
                    if (typeof tinymce_setup_callback !== "undefined") {
                        tinymce_setup_callback(editor);
                    }
                }
            }
            tinymce.init(additionalConfig);
        });
    </script>
@endpush
