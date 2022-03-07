<?php

namespace Database\Seeders;


use App\Models\Role;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run()
    {

        // Default password
        $defaultPassword = app()->environment('production') ? Str::random() : '12345678';
        $this->command->getOutput()->writeln("<info>Default password:</info> $defaultPassword");

        // Create super admin user
        $user     = new User();
        $role     = new Role();
        $customer = new Customer();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $user->truncate();
        $role->truncate();
        $customer->truncate();
        DB::table('role_user')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /*
         * Create roles
         */

        $superAdminRole = $role->create([
                'name'   => 'administrator',
                'status' => true,
        ]);

        foreach (
                [
                        'access backend',
                        'view customer',
                        'create customer',
                        'edit customer',
                        'delete customer',
                        'view subscription',
                        'new subscription',
                        'manage subscription',
                        'delete subscription',
                        'manage plans',
                        'create plans',
                        'edit plans',
                        'delete plans',
                        'manage currencies',
                        'create currencies',
                        'edit currencies',
                        'delete currencies',
                        'view sending_servers',
                        'create sending_servers',
                        'edit sending_servers',
                        'delete sending_servers',
                        'view keywords',
                        'create keywords',
                        'edit keywords',
                        'delete keywords',
                        'view sender_id',
                        'create sender_id',
                        'edit sender_id',
                        'delete sender_id',
                        'view blacklist',
                        'create blacklist',
                        'edit blacklist',
                        'delete blacklist',
                        'view spam_word',
                        'create spam_word',
                        'edit spam_word',
                        'delete spam_word',
                        'view administrator',
                        'create administrator',
                        'edit administrator',
                        'delete administrator',
                        'view roles',
                        'create roles',
                        'edit roles',
                        'delete roles',
                        'general settings',
                        'system_email settings',
                        'authentication settings',
                        'notifications settings',
                        'localization settings',
                        'pusher settings',
                        'view languages',
                        'new languages',
                        'manage languages',
                        'delete languages',
                        'view payment_gateways',
                        'update payment_gateways',
                        'view email_templates',
                        'update email_templates',
                        'view background_jobs',
                        'view purchase_code',
                        'manage update_application',
                        'manage maintenance_mode',
                        'view invoices',
                        'create invoices',
                        'edit invoices',
                        'delete invoices',
                        'view sms_history',
                        'view block_message',
                        'manage coverage_rates',
                ] as $name) {
            $superAdminRole->permissions()->create(['name' => $name]);
        }

//        $authorRole = $role->create([
//                'name'   => 'author',
//                'status' => true,
//        ]);
//
//        foreach (
//                [
//
//                        'access backend',
//                        'view customer',
//                        'create customer',
//                        'edit customer',
//                        'delete customer',
//                        'view subscription',
//                        'new subscription',
//                        'manage subscription',
//                        'delete subscription',
//                        'manage plans',
//                        'create plans',
//                        'edit plans',
//                        'delete plans',
//                        'manage currencies',
//                        'create currencies',
//                        'edit currencies',
//                        'delete currencies',
//                        'view sending_servers',
//                        'create sending_servers',
//                        'edit sending_servers',
//                        'delete sending_servers',
//                        'view keywords',
//                        'create keywords',
//                        'edit keywords',
//                        'delete keywords',
//                        'view sender_id',
//                        'create sender_id',
//                        'edit sender_id',
//                        'delete sender_id',
//                        'view blacklist',
//                        'create blacklist',
//                        'edit blacklist',
//                        'delete blacklist',
//                        'view spam_word',
//                        'create spam_word',
//                        'edit spam_word',
//                        'delete spam_word',
//                        'view invoices',
//                        'create invoices',
//                        'edit invoices',
//                        'delete invoices',
//                        'view sms_history',
//                        'view block_message',
//                        'manage coverage_rates',
//                ] as $name) {
//            $authorRole->permissions()->create(['name' => $name]);
//        }


        $superAdmin            = $user->create([
                'first_name'        => 'Super',
                'last_name'         => 'Admin',
                'image'             => null,
                'email'             => 'akasham67@gmail.com',
                'password'          => bcrypt($defaultPassword),
                'status'            => true,
                'is_admin'          => true,
                'locale'            => app()->getLocale(),
                'timezone'          => config('app.timezone'),
                'email_verified_at' => now(),
        ]);

        $superAdmin->api_token = $superAdmin->createToken('akasham67@gmail.com')->plainTextToken;
        $superAdmin->save();

        $superAdmin->roles()->save($superAdminRole);


//        $supervisor = $user->create([
//                'first_name'        => 'Shamim',
//                'last_name'         => 'Rahman',
//                'image'             => null,
//                'email'             => 'shamim97@gmail.com',
//                'password'          => bcrypt($defaultPassword),
//                'status'            => true,
//                'is_admin'          => true,
//                'locale'            => app()->getLocale(),
//                'timezone'          => config('app.timezone'),
//                'email_verified_at' => now(),
//        ]);
//
//        $supervisor->api_token = $supervisor->createToken('akasham67@gmail.com')->plainTextToken;
//        $supervisor->save();
//
//        $supervisor->roles()->save($authorRole);
//
//        $customers = $user->create([
//                'first_name'        => 'Codeglen',
//                'last_name'         => null,
//                'image'             => null,
//                'email'             => 'codeglen@gmail.com',
//                'password'          => bcrypt($defaultPassword),
//                'status'            => true,
//                'is_admin'          => false,
//                'is_customer'       => true,
//                'locale'            => app()->getLocale(),
//                'timezone'          => config('app.timezone'),
//                'email_verified_at' => now(),
//        ]);
//
//
//        $customers->api_token = $customers->createToken('akasham67@gmail.com')->plainTextToken;
//        $customers->save();
//
//        $customer->create([
//                'user_id'            => $customers->id,
//                'company'            => 'Codeglen',
//                'website'            => 'https://codeglen.com',
//                'address'            => 'Banasree, Rampura',
//                'city'               => 'Dhaka',
//                'postcode'           => '1219',
//                'financial_address'  => 'Banasree, Rampura',
//                'financial_city'     => 'Dhaka',
//                'financial_postcode' => '1219',
//                'tax_number'         => '21-4330267',
//                'state'              => 'Dhaka',
//                'country'            => 'Bangladesh',
//                'phone'              => '8801700000000',
//                'notifications'      => json_encode([
//                        'login'        => 'no',
//                        'sender_id'    => 'yes',
//                        'keyword'      => 'yes',
//                        'subscription' => 'yes',
//                        'promotion'    => 'yes',
//                        'profile'      => 'yes',
//                ]),
//        ]);
//
//
//        $customer_two = $user->create([
//                'first_name'        => 'Umme',
//                'last_name'         => 'Habiba',
//                'image'             => null,
//                'email'             => 'habiba@gmail.com',
//                'password'          => bcrypt($defaultPassword),
//                'status'            => true,
//                'is_admin'          => false,
//                'is_customer'       => true,
//                'locale'            => app()->getLocale(),
//                'timezone'          => config('app.timezone'),
//                'email_verified_at' => now(),
//        ]);
//
//        $customer->create([
//                'user_id'            => $customer_two->id,
//                'company'            => 'Codeglen',
//                'website'            => 'https://codeglen.com',
//                'address'            => 'Banasree, Rampura',
//                'city'               => 'Dhaka',
//                'postcode'           => '1219',
//                'financial_address'  => 'Banasree, Rampura',
//                'financial_city'     => 'Dhaka',
//                'financial_postcode' => '1219',
//                'tax_number'         => '21-4330267',
//                'state'              => 'Dhaka',
//                'country'            => 'Bangladesh',
//                'phone'              => '8801700000000',
//                'notifications'      => json_encode([
//                        'login'        => 'no',
//                        'sender_id'    => 'no',
//                        'keyword'      => 'yes',
//                        'subscription' => 'yes',
//                        'promotion'    => 'no',
//                        'profile'      => 'yes',
//                ]),
//        ]);
//
//        $customer_three = $user->create([
//                'first_name'        => 'Afeef Mohammed',
//                'last_name'         => "Sa'd",
//                'image'             => null,
//                'email'             => 'itssaad@gmail.com',
//                'password'          => bcrypt($defaultPassword),
//                'status'            => true,
//                'is_admin'          => false,
//                'is_customer'       => true,
//                'locale'            => app()->getLocale(),
//                'timezone'          => config('app.timezone'),
//                'email_verified_at' => now(),
//        ]);
//
//        $customer->create([
//                'user_id'            => $customer_three->id,
//                'company'            => 'Codeglen',
//                'website'            => 'https://codeglen.com',
//                'address'            => 'Banasree, Rampura',
//                'city'               => 'Dhaka',
//                'postcode'           => '1219',
//                'financial_address'  => 'Banasree, Rampura',
//                'financial_city'     => 'Dhaka',
//                'financial_postcode' => '1219',
//                'tax_number'         => '21-4330267',
//                'state'              => 'Dhaka',
//                'country'            => 'Bangladesh',
//                'phone'              => '8801700000000',
//                'notifications'      => json_encode([
//                        'login'        => 'no',
//                        'sender_id'    => 'no',
//                        'keyword'      => 'yes',
//                        'subscription' => 'yes',
//                        'promotion'    => 'no',
//                        'profile'      => 'yes',
//                ]),
//        ]);
//
//        $customer_four = $user->create([
//                'first_name'        => 'Abul Kashem',
//                'last_name'         => 'Shamim',
//                'image'             => null,
//                'email'             => 'kashem97@gmail.com',
//                'password'          => bcrypt($defaultPassword),
//                'status'            => true,
//                'is_admin'          => false,
//                'is_customer'       => true,
//                'locale'            => app()->getLocale(),
//                'timezone'          => config('app.timezone'),
//                'email_verified_at' => now(),
//        ]);
//
//        $customer->create([
//                'user_id'            => $customer_four->id,
//                'company'            => 'Codeglen',
//                'website'            => 'https://codeglen.com',
//                'address'            => 'Banasree, Rampura',
//                'city'               => 'Dhaka',
//                'postcode'           => '1219',
//                'financial_address'  => 'Banasree, Rampura',
//                'financial_city'     => 'Dhaka',
//                'financial_postcode' => '1219',
//                'tax_number'         => '21-4330267',
//                'state'              => 'Dhaka',
//                'country'            => 'Bangladesh',
//                'phone'              => '8801700000000',
//                'notifications'      => json_encode([
//                        'login'        => 'no',
//                        'sender_id'    => 'no',
//                        'keyword'      => 'yes',
//                        'subscription' => 'yes',
//                        'promotion'    => 'no',
//                        'profile'      => 'yes',
//                ]),
//        ]);

    }

}
