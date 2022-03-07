<?php

    namespace Database\Seeders;


    use Illuminate\Database\Seeder;
    use App\Models\EmailTemplates;

    class EmailTemplateSeeder extends Seeder
    {
        /**
         * Run the database seeders.
         *
         * @return void
         */
        public function run()
        {

            EmailTemplates::truncate();

            $templates = [
                [
                    'name'    => 'Customer Registration',
                    'slug'    => 'customer_registration',
                    'subject' => 'Welcome to {app_name}',
                    'content' => 'Hi {first_name} {last_name},
                                      Welcome to {app_name}! This message is an automated reply to your User Access request. Login to your User panel by using the details below:
                                      {login_url}
                                      Email: {email_address}
                                      Password: {password}',
                    'status'  => true,
                ],
                [
                    'name'    => 'Customer Registration Verification',
                    'slug'    => 'registration_verification',
                    'subject' => 'Registration Verification From {app_name}',
                    'content' => 'Hi {first_name} {last_name},
                                      Welcome to {app_name}! This message is an automated reply to your account verification request. Click the following url to verify your account:
                                      {verification_url}',
                    'status'  => true,
                ],
                [
                    'name'    => 'Password Reset',
                    'slug'    => 'password_reset',
                    'subject' => '{app_name} New Password',
                    'content' => 'Hi {first_name} {last_name},
                                      Password Reset Successfully! This message is an automated reply to your password reset request. Login to your account to set up your all details by using the details below:
                                      {login_url}
                                      Email: {email_address}
                                      Password: {password}',
                    'status'  => true,
                ],
                [
                    'name'    => 'Forgot Password',
                    'slug'    => 'forgot_password',
                    'subject' => '{app_name} password change request',
                    'content' => 'Hi {first_name} {last_name},
                                      Password Reset Successfully! This message is an automated reply to your password reset request. Click this link to reset your password:
                                      {forgot_password_link}
                                      Notes: Until your password has been changed, your current password will remain valid. The Forgot Password Link will be available for a limited time only.',
                    'status'  => true,
                ],
                [
                    'name'    => 'Login Notification',
                    'slug'    => 'login_notification',
                    'subject' => 'Your {app_name} Login Information',
                    'content' => 'Hi,
                                      You successfully logged in to {app_name} on {time} from ip {ip_address}.  If you did not login, please contact our support immediately.',
                    'status'  => true,
                ],
                [
                    'name'    => 'Customer Registration Notification',
                    'slug'    => 'registration_notification',
                    'subject' => 'New customer registered to {app_name}',
                    'content' => 'Hi,
                                      New customer named {first_name} {last_name} registered. Login to your portal to show details.
                                      {customer_profile_url}',
                    'status'  => true,
                ],
                [
                    'name'    => 'Sender ID Notification',
                    'slug'    => 'sender_id_notification',
                    'subject' => 'New sender id requested to {app_name}',
                    'content' => 'Hi,
                                      New sender id {sender_id} requested. Login to your portal to show details.
                                      {sender_id_url}',
                    'status'  => true,
                ],
                [
                    'name'    => 'Subscription Notification',
                    'slug'    => 'subscription_notification',
                    'subject' => 'New subscription to {app_name}',
                    'content' => 'Hi,
                                      New subscription made on {app_name}. Login to your portal to show details.
                                      {invoice_url}',
                    'status'  => true,
                ],
                [
                    'name'    => 'Keyword purchase Notification',
                    'slug'    => 'keyword_purchase_notification',
                    'subject' => 'New keyword sale on {app_name}',
                    'content' => 'Hi,
                                      New keyword sale made on {app_name}. Login to your portal to show details.
                                      {keyword_url}',
                    'status'  => true,
                ],
                [
                    'name'    => 'Phone number purchase Notification',
                    'slug'    => 'number_purchase_notification',
                    'subject' => 'New phone number sale on {app_name}',
                    'content' => 'Hi,
                                      New phone number sale made on {app_name}. Login to your portal to show details.
                                      {number_url}',
                    'status'  => true,
                ],
//                [
//                    'name'    => 'Ticket For Customer',
//                    'slug'    => 'ticket_customer',
//                    'subject' => 'New Ticket From {app_name}',
//                    'content' => 'Hi {first_name} {last_name},
//                                      Thank you for stay with us! This is a Support Ticket For Yours.. Login to your account to view  your support tickets details:
//                                      {ticket_url}
//                                      Ticket ID: {ticket_id}
//                                      Ticket Subject: {ticket_subject}
//                                      Message: {message}
//                                      Created By: {create_by}',
//                    'status'  => true,
//                ],
//                [
//                    'name'    => 'Reply Ticket',
//                    'slug'    => 'reply_ticket',
//                    'subject' => 'Reply to Ticket [TID-{ticket_id}]',
//                    'content' => 'Hi {first_name} {last_name},
//                                      Thank you for stay with us! This is a Support Ticket Reply. Login to your account to view  your support ticket reply details:
//                                      {ticket_url}
//                                      Ticket ID: {ticket_id}
//                                      Ticket Subject: {ticket_subject}
//                                      Message: {message}
//                                      Replied By: {reply_by}
//                                      Should you have any questions in regards to this support ticket or any other tickets related issue, please feel free to contact the Support department by creating a new ticket from your Customer/User Portal.',
//                    'status'  => true,
//                ],
//                [
//                    'name'    => 'Ticket For Admin',
//                    'slug'    => 'ticket_admin',
//                    'subject' => 'New Ticket From {app_name} Customer',
//                    'content' => 'Hi {department_name}
//                                         {ticket_url}
//                                         Ticket ID: {ticket_id}
//                                         Ticket Subject: {ticket_subject}
//                                         Message: {message}
//                                         Created By: {create_by}
//                                         Waiting for your quick response.',
//                    'status'  => true,
//                ],
            ];

            foreach ($templates as $tp) {
                EmailTemplates::create($tp);
            }
        }

    }
