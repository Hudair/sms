/*=========================================================================================
    File Name: checkout.js
    Description: checkout payments
    ----------------------------------------------------------------------------------------
   Item Name: Ultimate SMS - Bulk SMS Application For Marketing
   Author: Codeglen
   Author URL: https://codecanyon.net/user/codeglen
==========================================================================================*/
$(document).ready(function () {
  "use strict";

  let sidebarShop = $(".sidebar-shop"),
    shopOverlay = $(".shop-content-overlay"),
    sidebarToggler = $(".shop-sidebar-toggler"),
    gridViewBtn = $(".grid-view-btn"),
    listViewBtn = $(".list-view-btn"),
    ecommerceProducts = $("#ecommerce-products"),
    cart = $(".cart");


  // show sidebar
  sidebarToggler.on("click", function () {
    sidebarShop.toggleClass("show");
    shopOverlay.toggleClass("show");
  });

  // remove sidebar
  $(".shop-content-overlay, .sidebar-close-icon").on("click", function () {
    sidebarShop.removeClass("show");
    shopOverlay.removeClass("show");
  })


  /***** CHANGE VIEW *****/
  // Grid View
  gridViewBtn.on("click", function () {
    ecommerceProducts.removeClass("list-view").addClass("grid-view");
    listViewBtn.removeClass("active");
    gridViewBtn.addClass("active");
  });

  // List View
  listViewBtn.on("click", function () {
    ecommerceProducts.removeClass("grid-view").addClass("list-view");
    gridViewBtn.removeClass("active");
    listViewBtn.addClass("active");
  });

  // For View in cart
  cart.on("click", function () {
    let $this = $(this),
      addToCart = $this.find(".add-to-cart"),
      viewInCart = $this.find(".view-in-cart");
    if (addToCart.is(':visible')) {
      addToCart.addClass("d-none");
      viewInCart.addClass("d-inline-block");
    }
    else {
      window.location.href = viewInCart.attr('href');
    }
  });

  $(".view-in-cart").on('click', function (e) {
    e.preventDefault();
  });

  // Checkout Wizard
  let checkoutWizard = $(".checkout-tab-steps"),
    checkoutValidation = checkoutWizard.show();
  if (checkoutWizard.length > 0) {
    $(checkoutWizard).steps({
      headerTag: "h6",
      bodyTag: "fieldset",
      transitionEffect: "fade",
      titleTemplate: '<span class="step">#index#</span> #title#',
      enablePagination: false,
      onStepChanging: function (event, currentIndex, newIndex) {
        // allows to go back to previous step if form is
        if (currentIndex > newIndex) {
          return true;
        }
        // Needed in some cases if the user went back (clean up)
        if (currentIndex < newIndex) {
          // To remove error styles
          checkoutValidation.find(".body:eq(" + newIndex + ") label.error").remove();
          checkoutValidation.find(".body:eq(" + newIndex + ") .error").removeClass("error");
        }
        // check for valid details and show notification accordingly
        if (currentIndex === 1 && Number($(".form-control.required").val().length) < 1) {
          toastr.warning('Error', 'Please Enter Valid Details', { "positionClass": "toast-top-right" });
        }
        checkoutValidation.validate().settings.ignore = ":disabled,:hidden";
        return checkoutValidation.valid();
      },
    });
    // to move to next step on place order and save address click
    $(".place-order, .delivery-address").on("click", function () {
      $(".checkout-tab-steps").steps("next", {});
    });
  }
})
// on window resize hide sidebar
$(window).on("resize", function () {
  if ($(window).outerWidth() >= 991) {
    $(".sidebar-shop").removeClass("show");
    $(".shop-content-overlay").removeClass("show");
  }
});
