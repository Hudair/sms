/*=========================================================================================
Item Name: Ultimate SMS - Bulk SMS Application For Marketing
Author: Codeglen
Author URL: https://codecanyon.net/user/codeglen
==========================================================================================*/
(function (window, document, $) {
    'use strict';

    let Font = Quill.import('formats/font');
    Font.whitelist = ['sofia', 'slabo', 'roboto', 'inconsolata', 'ubuntu'];
    Quill.register(Font, true);


    // Snow Editor

    let snowEditor = new Quill('#snow-container .editor', {
        bounds: '#snow-container .editor',
        modules: {
            formula: true,
            syntax: true,
            toolbar: '#snow-container .quill-toolbar'
        },
        theme: 'snow'
    });

    snowEditor.on('text-change', function (delta, oldDelta, source) {
        let html = snowEditor.root.innerHTML;
        $("#hiddenArea").val(html);
    });

})(window, document, jQuery);
