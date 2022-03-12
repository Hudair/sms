<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

/**
 * @method static where(string $string, $name)
 * @method static truncate()
 * @method static create(array|string[] $conf)
 */
class AppConfig extends Model
{
    /**
     * model database
     *
     * @var string
     */
    protected $table = 'app_config';

    /**
     * mass fillable value
     *
     * @var string[]
     */
    protected $fillable = ['setting', 'value'];

    /**
     * default settings
     *
     * @return array
     */
    public function defaultSettings(): array
    {
        return [
                [
                        'setting' => 'app_name',
                        'value'   => 'Ultimate SMS',
                ],
                [
                        'setting' => 'app_title',
                        'value'   => 'Bulk SMS Application For Marketing',
                ],
                [
                        'setting' => 'app_keyword',
                        'value'   => 'ultimate sms, codeglen, bulk sms, sms, sms marketing, laravel, framework',
                ],
                [
                        'setting' => 'license',
                        'value'   => '',
                ],
                [
                        'setting' => 'license_type',
                        'value'   => 'Regular license',
                ],
                [
                        'setting' => 'valid_domain',
                        'value'   => 'yes',
                ],
                [
                        'setting' => 'from_email',
                        'value'   => 'akasham67@gmail.com',
                ],
                [
                        'setting' => 'from_name',
                        'value'   => 'Ultimate SMS',
                ],
                [
                        'setting' => 'company_address',
                        'value'   => 'House#11, Block#B, <br>Rampura<br>Banasree Project<br>Dhaka<br>1219<br>Bangladesh',
                ],
                [
                        'setting' => 'software_version',
                        'value'   => '3.0.1',
                ],
                [
                        'setting' => 'footer_text',
                        'value'   => 'Copyright &copy; Codeglen - 2020',
                ],
                [
                        'setting' => 'app_logo',
                        'value'   => 'images/logo/1e4fd743756c6c73940e089cf853b602.png',
                ],
                [
                        'setting' => 'app_favicon',
                        'value'   => 'images/logo/428eedaaee070f72c0a4f14aa08be0c4.png',
                ],
                [
                        'setting' => 'country',
                        'value'   => 'Bangladesh',
                ],
                [
                        'setting' => 'timezone',
                        'value'   => 'Asia/Dhaka',
                ],
                [
                        'setting' => 'app_stage',
                        'value'   => 'live',
                ],
                [
                        'setting' => 'maintenance_mode',
                        'value'   => true,
                ],
                [
                        'setting' => 'maintenance_mode_message',
                        'value'   => 'We\'re undergoing a bit of scheduled maintenance.',
                ],
                [
                        'setting' => 'maintenance_mode_end',
                        'value'   => 'Jan 5, 2021 15:37:25',
                ],
                [
                        'setting' => 'php_bin_path',
                        'value'   => '/usr/bin/php',
                ],
                [
                        'setting' => 'driver',
                        'value'   => 'default',
                ],
                [
                        'setting' => 'host',
                        'value'   => 'smtp.gmail.com',
                ],
                [
                        'setting' => 'username',
                        'value'   => 'user@example.com',
                ],
                [
                        'setting' => 'password',
                        'value'   => 'testpassword',
                ],
                [
                        'setting' => 'port',
                        'value'   => '587',
                ],
                [
                        'setting' => 'encryption',
                        'value'   => 'tls',
                ],
                [
                        'setting' => 'date_format',
                        'value'   => 'jS M y',
                ],
                [
                        'setting' => 'language',
                        'value'   => '1',
                ],
                [
                        'setting' => 'client_registration',
                        'value'   => true,
                ],
                [
                        'setting' => 'registration_verification',
                        'value'   => true,
                ],
                [
                        'setting' => 'two_factor',
                        'value'   => false,
                ],
                [
                        'setting' => 'two_factor_send_by',
                        'value'   => 'email',
                ],
                [
                        'setting' => 'captcha_in_login',
                        'value'   => false,
                ],
                [
                        'setting' => 'captcha_in_client_registration',
                        'value'   => false,
                ],
                [
                        'setting' => 'captcha_site_key',
                        'value'   => '6Lfp3ugUAAAAANwwZcKZ9qfOS4ha-Wla15B4IGVh',
                ],
                [
                        'setting' => 'captcha_secret_key',
                        'value'   => '6Lfp3ugUAAAAAFW1exmw0I4C8K33mhkLdraWF8PA',
                ],
                [
                        'setting' => 'login_with_facebook',
                        'value'   => false,
                ],
                [
                        'setting' => 'facebook_client_id',
                        'value'   => '',
                ],
                [
                        'setting' => 'facebook_client_secret',
                        'value'   => '',
                ],
                [
                        'setting' => 'login_with_twitter',
                        'value'   => false,
                ],
                [
                        'setting' => 'twitter_client_id',
                        'value'   => '',
                ],
                [
                        'setting' => 'twitter_client_secret',
                        'value'   => '',
                ],
                [
                        'setting' => 'login_with_google',
                        'value'   => false,
                ],
                [
                        'setting' => 'google_client_id',
                        'value'   => '',
                ],
                [
                        'setting' => 'google_client_secret',
                        'value'   => '',
                ],
                [
                        'setting' => 'login_with_github',
                        'value'   => false,
                ],
                [
                        'setting' => 'github_client_id',
                        'value'   => '',
                ],
                [
                        'setting' => 'github_client_secret',
                        'value'   => '',
                ],
                [
                        'setting' => 'notification_sms_gateway',
                        'value'   => '5eddfce2b68e6',
                ],
                [
                        'setting' => 'notification_sender_id',
                        'value'   => config('app.name'),
                ],
                [
                        'setting' => 'notification_phone',
                        'value'   => '8801721970168',
                ],
                [
                        'setting' => 'notification_from_name',
                        'value'   => config('app.name'),
                ],
                [
                        'setting' => 'notification_email',
                        'value'   => 'akasham67@gmail.com',
                ],
                [
                        'setting' => 'sender_id_notification_email',
                        'value'   => true,
                ],
                [
                        'setting' => 'sender_id_notification_sms',
                        'value'   => true,
                ],
                [
                        'setting' => 'user_registration_notification_email',
                        'value'   => true,
                ],
                [
                        'setting' => 'user_registration_notification_sms',
                        'value'   => false,
                ],
                [
                        'setting' => 'subscription_notification_email',
                        'value'   => true,
                ],
                [
                        'setting' => 'subscription_notification_sms',
                        'value'   => false,
                ],
                [
                        'setting' => 'keyword_notification_email',
                        'value'   => true,
                ],
                [
                        'setting' => 'keyword_notification_sms',
                        'value'   => false,
                ],
                [
                        'setting' => 'phone_number_notification_email',
                        'value'   => true,
                ],
                [
                        'setting' => 'phone_number_notification_sms',
                        'value'   => false,
                ],
                [
                        'setting' => 'block_message_notification_email',
                        'value'   => false,
                ],
                [
                        'setting' => 'block_message_notification_sms',
                        'value'   => false,
                ],
                [
                        'setting' => 'unsubscribe_message',
                        'value'   => 'Reply Stop to unsubscribe',
                ],
                [
                        'setting' => 'custom_script',
                        'value'   => '',
                ],

        ];
    }


    /**
     * updateLogo
     *
     * @param $file
     * @param  null  $name
     *
     * @return bool
     */
    public static function uploadFile($file, $name = null): bool
    {
        $path        = 'images/logo/';
        $upload_path = public_path($path);

        if ( ! file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $md5file = md5_file($file);

        $filename = $md5file.'.'.$file->getClientOriginalExtension();
        $img      = Image::make($file->getRealPath());
        if ($name == 'app_logo') {
            $img->fit(150, 26, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });
        } else {
            $img->fit(32, 32, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });
        }

        $img->save($upload_path.$filename);
        $file_location = $path.$filename;

        AppConfig::where('setting', $name)->update([
                'value' => $file_location,
        ]);

        AppConfig::setEnv(strtoupper($name), $file_location);

        return true;
    }


    /**
     * Update setting one line.
     *
     * @param $key
     * @param $value
     */
    public static function setEnv($key, $value)
    {
        $file_path = base_path('.env');
        $data      = file($file_path);
        $data      = array_map(function ($data) use ($key, $value) {
            return stristr($data, $key) ? "$key=\"$value\"\n" : $data;
        }, $data);

        // Write file
        $env_file = fopen($file_path, 'w') or die('Unable to open file!');
        fwrite($env_file, implode('', $data));
        fclose($env_file);
    }

    /**
     * get default notifications value
     *
     * @return string[]
     */
    public static function notificationsValues(): array
    {
        return [
                'sender_id_notification_email',
                'sender_id_notification_sms',
                'user_registration_notification_email',
                'user_registration_notification_sms',
                'subscription_notification_email',
                'subscription_notification_sms',
                'keyword_notification_email',
                'keyword_notification_sms',
                'phone_number_notification_email',
                'phone_number_notification_sms',
                'block_message_notification_email',
                'block_message_notification_sms',
        ];
    }
}
