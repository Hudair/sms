@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/katex.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/monokai-sublime.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.bubble.css')) }}">
@endsection


<div id="snow-wrapper">
    <div id="snow-container">
        <div class="quill-toolbar">
                                                <span class="ql-formats">
                                                    <select class="ql-header">
                                                      <option value="1">Heading</option>
                                                      <option value="2">Subheading</option>
                                                      <option selected>Normal</option>
                                                    </select>

                                                    <select class="ql-font">
                                                      <option selected>Sailec Light</option>
                                                      <option value="sofia">Sofia Pro</option>
                                                      <option value="slabo">Slabo 27px</option>
                                                      <option value="roboto">Roboto Slab</option>
                                                      <option value="inconsolata">Inconsolata</option>
                                                      <option value="ubuntu">Ubuntu Mono</option>
                                                    </select>
                                              </span>

            <span class="ql-formats">
                                                    <button class="ql-bold"></button>
                                                    <button class="ql-italic"></button>
                                                    <button class="ql-underline"></button>
                                                </span>

            <span class="ql-formats">
                                                    <button class="ql-list" value="ordered"></button>
                                                    <button class="ql-list" value="bullet"></button>
                                                </span>

            <span class="ql-formats">
                                                    <button class="ql-link"></button>
                                                    <button class="ql-image"></button>
                                                    <button class="ql-video"></button>
                                                </span>

            <span class="ql-formats">
                                                    <button class="ql-formula"></button>
                                                    <button class="ql-code-block"></button>
                                                </span>

            <span class="ql-formats">
                                                    <button class="ql-clean"></button>
                                                </span>
        </div>

        <div class="editor">
            {!! $content !!}
            <textarea name="text" style="display:none" id="hiddenArea"></textarea>
        </div>

    </div>
</div>


@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/jquery.steps.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection

@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/editors/editor-quill.js')) }}"></script>
    <script>
        var myEditor = document.querySelector('.editor')
        var html = myEditor.children[0].innerHTML;
        $("#hiddenArea").val(html);
    </script>

@endsection
