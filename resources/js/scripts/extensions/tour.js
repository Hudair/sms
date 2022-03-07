/*=========================================================================================
	File Name: tour.js
	Description: tour
	----------------------------------------------------------------------------------------
	Item Name: Ultimate SMS - Bulk SMS Application For Marketing
	Author: Codeglen
	Author URL: https://codecanyon.net/user/codeglen
==========================================================================================*/

$(document).ready(function () {
    let tour = new Shepherd.Tour({
        classes: 'card',
        scrollTo: true,
        useModalOverlay: true,
        isCentered: true,
    })

    // tour steps
    tour.addStep('step-1', {
        text: 'Thank you for choosing Ultimate SMS!<br><br>' +
            'This small tour will guide you through some of the Ultimate SMS  features and will help you get started using the application.<br><br>' +
            'If you ever need support, please send email to <code>akasham67@gmail.com</code><br><br>' +
            'Let\'s get started!',
        buttons: [

            {
                text: "Skip",
                action: tour.complete
            },
            {
                text: 'Next',
                action: tour.next
            },
        ]
    });

    tour.addStep('step-2', {
        text: 'In order to make things easier to manage, Ultimate SMS is divided into several small sub-apps, like the <code>backend, customer, api</code><br><br>' +
            'The <code>backend</code> app is used for administrative tasks and here only the system users have access. You can create plan, customers, subscriptions, etc<br><br>' +
            'The <code>customer</code> app is used to create manage phone lists, subscribers, campaigns , and many more.<br><br>' +
            'The <code>api</code> app is used to allow custom integrations from various other apps with your own app, like customers sending sms from external systems to their lists. You can disable it any any time!<br><br>' +
            'To run ultimate sms, first, you need to create a <code>Sending Server</code>, then a <code>Plan</code>, after that, assign your created sending server on the plan. Finally, create a <code>customer</code> and assign the created plan.<br><br>' +
            'Click on next then you will find all details',
        buttons: [

            {
                text: "Skip",
                action: tour.complete
            },

            {
                text: "previous",
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            },
        ]
    });

    tour.addStep('step-3', {
        text: 'Maybe the most important thing that you have to do after you install the application is to make sure all the <code>cron jobs</code> are set properly. This is very important since without the cron jobs, the application will not be able to send any sms at all, or to do import contacts and a lot other tasks. <br><br>' +
            'For more details please go <code>Settings -> All Settings</code> menu and click on <code>Cron Jobs</code> tab',
        buttons: [

            {
                text: "Skip",
                action: tour.complete
            },

            {
                text: "previous",
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            },
        ]
    });

    tour.addStep('step-4', {
        text: '<code>Sending Servers</code> are needed in order to send out all emails from the application.<br><br>' +
            'To add a sending server please go <code>Sending -> Sending Servers</code> menu and click on <code>Add New Server</code> button.<br><br>' +
            'Finally, search your sending server and update your credentials.',
        buttons: [

            {
                text: "Skip",
                action: tour.complete
            },

            {
                text: "previous",
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            },
        ]
    });

    tour.addStep('step-5', {
        text: 'After creating a sending server you need to create a <code>Plan</code>. Where you can set your <code>plan Price</code>, <code>SMS Limit</code>, <code>Assign Sending Sending servers</code>, and <code>all other features</code><br><br>' +
            'To create a Plan please go <code>Plan -> Plans</code> menu and click on <code>Add New</code> button.<br><br>' +
            'Finally, update your all features and settings of your plan.',
        buttons: [

            {
                text: "Skip",
                action: tour.complete
            },

            {
                text: "previous",
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            },
        ]
    });
    tour.addStep('step-6', {
        text: 'When you first installed the application, you were asked to create a customer account.<br><br>' +
            'In case you haven\'t done so, please go ahead and create one from <code>Customer -> Customers</code> menu and click on <code>Add New</code> button.<br><br>' +
            'Finally, insert your all details and assign your created plan to your customer.',
        buttons: [

            {
                text: "Skip",
                action: tour.complete
            },

            {
                text: "previous",
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            },
        ]
    });

    tour.addStep('step-7', {
        text: 'Ultimate SMS is flexible and modern. Please check all features perfectly then you will find all details about ultimate sms.',
        buttons: [
            {
                text: "previous",
                action: tour.back
            },

            {
                text: "Finish",
                action: tour.complete
            },
        ]
    });

    function dismissTour() {
        if (!localStorage.getItem('shepherd-tour')) {
            localStorage.setItem('shepherd-tour', 'yes');
        }
    }

    tour.on('complete', dismissTour);

    // function to remove tour on small screen
    function displayTour() {
        window.resizeEvt;
        if ($(window).width() > 576) {
            clearTimeout(window.resizeEvt);

            // Initiate the tour
            if (!localStorage.getItem('shepherd-tour')) {
                tour.start();
            }
        } else {
            clearTimeout(window.resizeEvt);
            tour.cancel()
            window.resizeEvt = setTimeout(function () {
                alert("Tour only works for large screens!");
            }, 250);
        }
    }


    if (!localStorage.getItem('shepherd-tour')) {
        displayTour();
        $(window).resize(displayTour)
    }

});
