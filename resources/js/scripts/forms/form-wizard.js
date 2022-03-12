/*=========================================================================================
    File Name: form-wizard.js
    Description: wizard steps page specific js
    ----------------------------------------------------------------------------------------
    Item Name: Ultimate SMS - Bulk SMS Application For Marketing
    Author: Codeglen
    Author URL: https://codecanyon.net/user/codeglen
==========================================================================================*/

$(function () {
    'use strict';

    let bsStepper = document.querySelectorAll('.bs-stepper'),
        select = $('.select2'),
        modernWizard = document.querySelector('.modern-wizard-example');

    // Adds crossed class
    if (typeof bsStepper !== undefined && bsStepper !== null) {
        for (let el = 0; el < bsStepper.length; ++el) {
            bsStepper[el].addEventListener('show.bs-stepper', function (event) {
                let index = event.detail.indexStep;
                let numberOfSteps = $(event.target).find('.step').length - 1;
                let line = $(event.target).find('.step');

                // The first for loop is for increasing the steps,
                // the second is for turning them off when going back
                // and the third with the if statement because the last line
                // can't seem to turn off when I press the first item. ¯\_(ツ)_/¯

                for (let i = 0; i < index; i++) {
                    line[i].classList.add('crossed');

                    for (let j = index; j < numberOfSteps; j++) {
                        line[j].classList.remove('crossed');
                    }
                }
                if (event.detail.to === 0) {
                    for (let k = index; k < numberOfSteps; k++) {
                        line[k].classList.remove('crossed');
                    }
                    line[0].classList.remove('crossed');
                }
            });
        }
    }

    // Basic Select2 select
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

    // Modern Wizard
    // --------------------------------------------------------------------
    if (typeof modernWizard !== undefined && modernWizard !== null) {
        let modernStepper = new Stepper(modernWizard, {
            linear: false
        });
        $(modernWizard)
            .find('.btn-next')
            .on('click', function () {
                modernStepper.next();
            });
        $(modernWizard)
            .find('.btn-prev')
            .on('click', function () {
                modernStepper.previous();
            });
    }

});
