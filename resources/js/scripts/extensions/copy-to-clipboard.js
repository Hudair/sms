/*=========================================================================================
    File Name: copy-to-clipboard.js
    Description: Copy to clipboard
    --------------------------------------------------------------------------------------
==========================================================================================*/

var userText = $("#copy-to-clipboard-input");
var btnCopy = $("#btn-copy");

// copy text on click
btnCopy.on("click", function () {
  userText.select();
  document.execCommand("copy");
})
