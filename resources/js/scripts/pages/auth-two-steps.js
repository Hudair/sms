/*=========================================================================================
	File Name: auth-two-steps.js
	Description: Two Steps verification.
	----------------------------------------------------------------------------------------
Item Name: Ultimate SMS - Bulk SMS Application For Marketing
Author: Codeglen
Author URL: https://codecanyon.net/user/codeglen
==========================================================================================*/

var inputContainer = document.querySelector('.auth-input-wrapper');

// Get focus on next element after max-length reach
inputContainer.onkeyup = function (e) {
  var target = e.srcElement;
  var maxLength = parseInt(target.attributes['maxlength'].value, 10);
  var currentLength = target.value.length;

  if (e.keyCode === 8) {
    if (currentLength === 0) {
      var next = target;
      while ((next = next.previousElementSibling)) {
        if (next == null) break;
        if (next.tagName.toLowerCase() == 'input') {
          next.focus();
          break;
        }
      }
    }
  } else {
    if (currentLength >= maxLength) {
      var next = target;
      while ((next = next.nextElementSibling)) {
        if (next == null) break;
        if (next.tagName.toLowerCase() == 'input') {
          next.focus();
          break;
        }
      }
    }
  }
};

