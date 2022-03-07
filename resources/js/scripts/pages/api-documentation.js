/*=========================================================================================
	File Name: api-documentation.js
	Description: show hide div for api documentation.
------------------------------------------------------------------------------
    Item Name: Ultimate SMS - Bulk SMS Application For Marketing
    Author: Codeglen
    Author URL: https://codecanyon.net/user/codeglen
==========================================================================================*/

$(document).ready(function () {

        let featureDescription = $('.features_description .title');
        featureDescription.hide();

        $("#contacts-api-div").show();

        function setFeature(feature) {
            featureDescription.each(function () {
                if (this !== feature)
                    $(this).hide();
            });
            $('#' + feature).toggle();
        }

        $("#features li").click(function (e) {
            e.preventDefault();
            setFeature(this.id + '-div')
        });
    }
)
