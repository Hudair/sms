/*=========================================================================================
	File Name: editor-quill.js
	Description: Quill is a modern rich text editor built for compatibility and extensibility.
==========================================================================================*/
(function (window, document, $) {
    'use strict';

    var Font = Quill.import('formats/font');
    Font.whitelist = ['sofia', 'slabo', 'roboto', 'inconsolata', 'ubuntu'];
    Quill.register(Font, true);

    // Snow Editor

    var snowEditor = new Quill('#snow-container .editor', {
        bounds: '#snow-container .editor',
        modules: {
            'formula': true,
            'syntax': true,
            'toolbar': '#snow-container .quill-toolbar'
        },
        theme: 'snow'
    });



    snowEditor.on('text-change', function(delta, oldDelta, source) {
        var html = snowEditor.container.firstChild.innerHTML;
        $("#hiddenArea").val(html);
    });

})(window, document, jQuery);
