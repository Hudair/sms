/*=========================================================================================
  File Name: auth-register.js
  Description: Auth register js file.
  ----------------------------------------------------------------------------------------
  Item Name: Ultimate SMS - Bulk SMS Application For Marketing
  Author: Codeglen
  Author URL: https://codecanyon.net/user/codeglen
==========================================================================================*/

$(function () {
    ('use strict');

    let registerMultiStepsWizard = document.querySelector('.register-multi-steps-wizard'),
        pageResetForm = $('.auth-register-form'),
        numberedStepper,
        select = $('.select2');


    // jQuery Validation
    // --------------------------------------------------------------------
    if (pageResetForm.length) {
        pageResetForm.validate({
            rules: {
                'email': {
                    required: true,
                    email: true
                },
                'password': {
                    required: true
                },
                'timezone': {
                    required: true
                },
                'locale': {
                    required: true
                }
            }
        });
    }

    // multi-steps registration
    // --------------------------------------------------------------------

    // Horizontal Wizard
    if (typeof registerMultiStepsWizard !== undefined && registerMultiStepsWizard !== null) {
        numberedStepper = new Stepper(registerMultiStepsWizard);
        let $form = $(registerMultiStepsWizard).find('form');
        $form.each(function () {
            let $this = $(this);
            $this.validate({
                rules: {
                    first_name: {
                        required: true
                    },
                    email: {
                        required: true
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                    confirm_password: {
                        required: true,
                        minlength: 8,
                        equalTo: '#password'
                    },
                    'first-name': {
                        required: true
                    },
                    'home-address': {
                        required: true
                    },
                    addCard: {
                        required: true
                    }
                },
                messages: {
                    password: {
                        required: 'Enter new password',
                        minlength: 'Enter at least 8 characters'
                    },
                    'confirm-password': {
                        required: 'Please confirm new password',
                        minlength: 'Enter at least 8 characters',
                        equalTo: 'The password and its confirm are not the same'
                    }
                }
            });
        });

        $(registerMultiStepsWizard)
            .find('.btn-next')
            .each(function () {
                $(this).on('click', function (e) {
                    var isValid = $(this).parent().siblings('form').valid();
                    if (isValid) {
                        numberedStepper.next();
                    } else {
                        e.preventDefault();
                    }
                });
            });

        $(registerMultiStepsWizard)
            .find('.btn-prev')
            .on('click', function () {
                numberedStepper.previous();
            });

        $(registerMultiStepsWizard)
            .find('.btn-submit')
            .on('click', function () {
                let isValid = $(this).parent().siblings('form').valid();
                if (isValid) {
                    alert('Submitted..!!');
                }
            });
    }

    // select2
    select.each(function () {
        let $this = $(this);
        $this.wrap('<div class="position-relative"></div>');
        $this.select2({
            // the following code is used to disable x-scrollbar when click in select input and
            // take 100% width in responsive also
            dropdownAutoWidth: true,
            width: '100%',
            dropdownParent: $this.parent()
        });
    });

    // multi-steps registration
    // --------------------------------------------------------------------
});
