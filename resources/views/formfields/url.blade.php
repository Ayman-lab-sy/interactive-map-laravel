<input id="url_{{$row->field}}" @if($row->required == 1) required @endif type="text" class="form-control" name="{{ $row->field }}" placeholder="{{ old($row->field, $options->placeholder ?? $row->getTranslatedAttribute('display_name')) }}" {!! isBreadSlugAutoGenerator($options) !!} value="{{ old($row->field, $dataTypeContent->{$row->field} ?? $options->default ?? '') }}">

       <script>
        document.querySelector("#url_{{$row->field}}").addEventListener("input", (ev) => {
            if(!ev.target.value.startsWith('"')) {
                ev.target.value = '"'+ev.target.value
            }
        })
        </script>
