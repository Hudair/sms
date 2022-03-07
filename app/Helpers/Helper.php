<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use App\Models\AppConfig;
use App\Models\Contacts;
use App\Models\Language;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;

class Helper
{

    /**
     * @return array
     */

    public static function applClasses(): array
    {
        // default data array
        $DefaultData = [
                'mainLayoutType'         => 'vertical',
                'theme'                  => 'light',
                'sidebarCollapsed'       => false,
                'navbarColor'            => '',
                'horizontalMenuType'     => 'floating',
                'verticalMenuNavbarType' => 'floating',
                'footerType'             => 'static', //footer
                'bodyClass'              => '',
                'pageHeader'             => true,
                'contentLayout'          => 'default',
                'blankPage'              => false,
                'direction'              => env('MIX_CONTENT_DIRECTION', 'ltr'),
        ];

        // if any key missing of array from custom.php file it will be merge and set a default value from dataDefault array and store in data variable
        $data = array_merge($DefaultData, config('custom.custom'));

        // All options available in the template
        $allOptions = [
                'mainLayoutType'         => ['vertical', 'horizontal'],
                'theme'                  => ['light' => 'light', 'dark' => 'dark-layout', 'semi-dark' => 'semi-dark-layout'],
                'sidebarCollapsed'       => [true, false],
                'navbarColor'            => ['bg-primary', 'bg-info', 'bg-warning', 'bg-success', 'bg-danger', 'bg-dark'],
                'horizontalMenuType'     => ['floating' => 'navbar-floating', 'static' => 'navbar-static', 'sticky' => 'navbar-sticky'],
                'horizontalMenuClass'    => ['static' => 'menu-static', 'sticky' => 'fixed-top', 'floating' => 'floating-nav'],
                'verticalMenuNavbarType' => ['floating' => 'navbar-floating', 'static' => 'navbar-static', 'sticky' => 'navbar-sticky', 'hidden' => 'navbar-hidden'],
                'navbarClass'            => ['floating' => 'floating-nav', 'static' => 'static-top', 'sticky' => 'fixed-top', 'hidden' => 'd-none'],
                'footerType'             => ['static' => 'footer-static', 'sticky' => 'fixed-footer', 'hidden' => 'footer-hidden'],
                'pageHeader'             => [true, false],
                'contentLayout'          => ['default', 'content-left-sidebar', 'content-right-sidebar', 'content-detached-left-sidebar', 'content-detached-right-sidebar'],
                'blankPage'              => [false, true],
                'sidebarPositionClass'   => ['content-left-sidebar' => 'sidebar-left', 'content-right-sidebar' => 'sidebar-right', 'content-detached-left-sidebar' => 'sidebar-detached sidebar-left', 'content-detached-right-sidebar' => 'sidebar-detached sidebar-right', 'default' => 'default-sidebar-position'],
                'contentsidebarClass'    => ['content-left-sidebar' => 'content-right', 'content-right-sidebar' => 'content-left', 'content-detached-left-sidebar' => 'content-detached content-right', 'content-detached-right-sidebar' => 'content-detached content-left', 'default' => 'default-sidebar'],
                'direction'              => ['ltr', 'rtl'],
        ];

        //if mainLayoutType value empty or not match with default options in custom.php config file then set a default value
        foreach ($allOptions as $key => $value) {
            if (array_key_exists($key, $DefaultData)) {
                if (gettype($DefaultData[$key]) === gettype($data[$key])) {
                    // data key should be string
                    if (is_string($data[$key])) {
                        // data key should not be empty
                        if (isset($data[$key]) && $data[$key] !== null) {
                            // data key should not be exist inside allOptions array's sub array
                            if ( ! array_key_exists($data[$key], $value)) {
                                // ensure that passed value should be match with any of allOptions array value
                                $result = array_search($data[$key], $value, 'strict');
                                if (empty($result) && $result !== 0) {
                                    $data[$key] = $DefaultData[$key];
                                }
                            }
                        } else {
                            // if data key not set or
                            $data[$key] = $DefaultData[$key];
                        }
                    }
                } else {
                    $data[$key] = $DefaultData[$key];
                }
            }
        }

        //layout classes
        $layoutClasses = [
                'theme'                  => $data['theme'],
                'layoutTheme'            => $allOptions['theme'][$data['theme']],
                'sidebarCollapsed'       => $data['sidebarCollapsed'],
                'verticalMenuNavbarType' => $allOptions['verticalMenuNavbarType'][$data['verticalMenuNavbarType']],
                'navbarClass'            => $allOptions['navbarClass'][$data['verticalMenuNavbarType']],
                'navbarColor'            => $data['navbarColor'],
                'horizontalMenuType'     => $allOptions['horizontalMenuType'][$data['horizontalMenuType']],
                'horizontalMenuClass'    => $allOptions['horizontalMenuClass'][$data['horizontalMenuType']],
                'footerType'             => $allOptions['footerType'][$data['footerType']],
                'sidebarClass'           => 'menu-expanded',
                'bodyClass'              => $data['bodyClass'],
                'pageHeader'             => $data['pageHeader'],
                'blankPage'              => $data['blankPage'],
                'blankPageClass'         => '',
                'contentLayout'          => $data['contentLayout'],
                'sidebarPositionClass'   => $allOptions['sidebarPositionClass'][$data['contentLayout']],
                'contentsidebarClass'    => $allOptions['contentsidebarClass'][$data['contentLayout']],
                'mainLayoutType'         => $data['mainLayoutType'],
                'direction'              => $data['direction'],
        ];

        // sidebar Collapsed
        if ($layoutClasses['sidebarCollapsed'] == 'true') {
            $layoutClasses['sidebarClass'] = "menu-collapsed";
        }

        // blank page class
        if ($layoutClasses['blankPage'] == 'true') {
            $layoutClasses['blankPageClass'] = "blank-page";
        }

        return $layoutClasses;
    }

    /**
     * @param $pageConfigs
     */

    public static function updatePageConfig($pageConfigs)
    {
        $demo = 'custom';
        if (isset($pageConfigs)) {
            if (count($pageConfigs) > 0) {
                foreach ($pageConfigs as $config => $val) {
                    Config::set('custom.'.$demo.'.'.$config, $val);
                }
            }
        }
    }

    /**
     * @return string
     */
    public static function home_route(): string
    {
        if (Gate::allows('access backend')) {
            return route('admin.home');
        }

        return route('user.home');
    }


    /**
     * @param  Request  $request
     *
     * @return bool
     */
    public static function is_admin_route(Request $request): bool
    {
        $action = $request->route()->getAction();

        return 'App\Http\Controllers\Admin' === $action['namespace'];
    }

    /**
     * @param  string  $value
     *
     * @return mixed
     */

    public static function app_config($value = '')
    {
        $conf = AppConfig::where('setting', $value)->first();

        return $conf->value;
    }

    /**
     * Get all countries.
     *
     * @return array
     */
    public static function countries(): array
    {
        $countries   = [];
        $countries[] = ['code' => 'AF', 'name' => 'Afghanistan', 'd_code' => '+93'];
        $countries[] = ['code' => 'AL', 'name' => 'Albania', 'd_code' => '+355'];
        $countries[] = ['code' => 'DZ', 'name' => 'Algeria', 'd_code' => '+213'];
        $countries[] = ['code' => 'AS', 'name' => 'American Samoa', 'd_code' => '+1'];
        $countries[] = ['code' => 'AD', 'name' => 'Andorra', 'd_code' => '+376'];
        $countries[] = ['code' => 'AO', 'name' => 'Angola', 'd_code' => '+244'];
        $countries[] = ['code' => 'AI', 'name' => 'Anguilla', 'd_code' => '+1'];
        $countries[] = ['code' => 'AG', 'name' => 'Antigua', 'd_code' => '+1'];
        $countries[] = ['code' => 'AR', 'name' => 'Argentina', 'd_code' => '+54'];
        $countries[] = ['code' => 'AM', 'name' => 'Armenia', 'd_code' => '+374'];
        $countries[] = ['code' => 'AW', 'name' => 'Aruba', 'd_code' => '+297'];
        $countries[] = ['code' => 'AU', 'name' => 'Australia', 'd_code' => '+61'];
        $countries[] = ['code' => 'AT', 'name' => 'Austria', 'd_code' => '+43'];
        $countries[] = ['code' => 'AZ', 'name' => 'Azerbaijan', 'd_code' => '+994'];
        $countries[] = ['code' => 'BH', 'name' => 'Bahrain', 'd_code' => '+973'];
        $countries[] = ['code' => 'BD', 'name' => 'Bangladesh', 'd_code' => '+880'];
        $countries[] = ['code' => 'BB', 'name' => 'Barbados', 'd_code' => '+1'];
        $countries[] = ['code' => 'BY', 'name' => 'Belarus', 'd_code' => '+375'];
        $countries[] = ['code' => 'BE', 'name' => 'Belgium', 'd_code' => '+32'];
        $countries[] = ['code' => 'BZ', 'name' => 'Belize', 'd_code' => '+501'];
        $countries[] = ['code' => 'BJ', 'name' => 'Benin', 'd_code' => '+229'];
        $countries[] = ['code' => 'BM', 'name' => 'Bermuda', 'd_code' => '+1'];
        $countries[] = ['code' => 'BT', 'name' => 'Bhutan', 'd_code' => '+975'];
        $countries[] = ['code' => 'BO', 'name' => 'Bolivia', 'd_code' => '+591'];
        $countries[] = ['code' => 'BA', 'name' => 'Bosnia and Herzegovina', 'd_code' => '+387'];
        $countries[] = ['code' => 'BW', 'name' => 'Botswana', 'd_code' => '+267'];
        $countries[] = ['code' => 'BR', 'name' => 'Brazil', 'd_code' => '+55'];
        $countries[] = ['code' => 'IO', 'name' => 'British Indian Ocean Territory', 'd_code' => '+246'];
        $countries[] = ['code' => 'VG', 'name' => 'British Virgin Islands', 'd_code' => '+1'];
        $countries[] = ['code' => 'BN', 'name' => 'Brunei', 'd_code' => '+673'];
        $countries[] = ['code' => 'BG', 'name' => 'Bulgaria', 'd_code' => '+359'];
        $countries[] = ['code' => 'BF', 'name' => 'Burkina Faso', 'd_code' => '+226'];
        $countries[] = ['code' => 'MM', 'name' => 'Burma Myanmar', 'd_code' => '+95'];
        $countries[] = ['code' => 'BI', 'name' => 'Burundi', 'd_code' => '+257'];
        $countries[] = ['code' => 'KH', 'name' => 'Cambodia', 'd_code' => '+855'];
        $countries[] = ['code' => 'CM', 'name' => 'Cameroon', 'd_code' => '+237'];
        $countries[] = ['code' => 'CA', 'name' => 'Canada', 'd_code' => '+1'];
        $countries[] = ['code' => 'CV', 'name' => 'Cape Verde', 'd_code' => '+238'];
        $countries[] = ['code' => 'KY', 'name' => 'Cayman Islands', 'd_code' => '+1'];
        $countries[] = ['code' => 'CF', 'name' => 'Central African Republic', 'd_code' => '+236'];
        $countries[] = ['code' => 'TD', 'name' => 'Chad', 'd_code' => '+235'];
        $countries[] = ['code' => 'CL', 'name' => 'Chile', 'd_code' => '+56'];
        $countries[] = ['code' => 'CN', 'name' => 'China', 'd_code' => '+86'];
        $countries[] = ['code' => 'CO', 'name' => 'Colombia', 'd_code' => '+57'];
        $countries[] = ['code' => 'KM', 'name' => 'Comoros', 'd_code' => '+269'];
        $countries[] = ['code' => 'CK', 'name' => 'Cook Islands', 'd_code' => '+682'];
        $countries[] = ['code' => 'CR', 'name' => 'Costa Rica', 'd_code' => '+506'];
        $countries[] = ['code' => 'CI', 'name' => "Côte d'Ivoire", 'd_code' => '+225'];
        $countries[] = ['code' => 'HR', 'name' => 'Croatia', 'd_code' => '+385'];
        $countries[] = ['code' => 'CU', 'name' => 'Cuba', 'd_code' => '+53'];
        $countries[] = ['code' => 'CY', 'name' => 'Cyprus', 'd_code' => '+357'];
        $countries[] = ['code' => 'CZ', 'name' => 'Czech Republic', 'd_code' => '+420'];
        $countries[] = ['code' => 'CD', 'name' => 'Democratic Republic of Congo', 'd_code' => '+243'];
        $countries[] = ['code' => 'DK', 'name' => 'Denmark', 'd_code' => '+45'];
        $countries[] = ['code' => 'DJ', 'name' => 'Djibouti', 'd_code' => '+253'];
        $countries[] = ['code' => 'DM', 'name' => 'Dominica', 'd_code' => '+1'];
        $countries[] = ['code' => 'DO', 'name' => 'Dominican Republic', 'd_code' => '+1'];
        $countries[] = ['code' => 'EC', 'name' => 'Ecuador', 'd_code' => '+593'];
        $countries[] = ['code' => 'EG', 'name' => 'Egypt', 'd_code' => '+20'];
        $countries[] = ['code' => 'SV', 'name' => 'El Salvador', 'd_code' => '+503'];
        $countries[] = ['code' => 'GQ', 'name' => 'Equatorial Guinea', 'd_code' => '+240'];
        $countries[] = ['code' => 'ER', 'name' => 'Eritrea', 'd_code' => '+291'];
        $countries[] = ['code' => 'EE', 'name' => 'Estonia', 'd_code' => '+372'];
        $countries[] = ['code' => 'ET', 'name' => 'Ethiopia', 'd_code' => '+251'];
        $countries[] = ['code' => 'FK', 'name' => 'Falkland Islands', 'd_code' => '+500'];
        $countries[] = ['code' => 'FO', 'name' => 'Faroe Islands', 'd_code' => '+298'];
        $countries[] = ['code' => 'FM', 'name' => 'Federated States of Micronesia', 'd_code' => '+691'];
        $countries[] = ['code' => 'FJ', 'name' => 'Fiji', 'd_code' => '+679'];
        $countries[] = ['code' => 'FI', 'name' => 'Finland', 'd_code' => '+358'];
        $countries[] = ['code' => 'FR', 'name' => 'France', 'd_code' => '+33'];
        $countries[] = ['code' => 'GF', 'name' => 'French Guiana', 'd_code' => '+594'];
        $countries[] = ['code' => 'PF', 'name' => 'French Polynesia', 'd_code' => '+689'];
        $countries[] = ['code' => 'GA', 'name' => 'Gabon', 'd_code' => '+241'];
        $countries[] = ['code' => 'GE', 'name' => 'Georgia', 'd_code' => '+995'];
        $countries[] = ['code' => 'DE', 'name' => 'Germany', 'd_code' => '+49'];
        $countries[] = ['code' => 'GH', 'name' => 'Ghana', 'd_code' => '+233'];
        $countries[] = ['code' => 'GI', 'name' => 'Gibraltar', 'd_code' => '+350'];
        $countries[] = ['code' => 'GR', 'name' => 'Greece', 'd_code' => '+30'];
        $countries[] = ['code' => 'GL', 'name' => 'Greenland', 'd_code' => '+299'];
        $countries[] = ['code' => 'GD', 'name' => 'Grenada', 'd_code' => '+1'];
        $countries[] = ['code' => 'GP', 'name' => 'Guadeloupe', 'd_code' => '+590'];
        $countries[] = ['code' => 'GU', 'name' => 'Guam', 'd_code' => '+1'];
        $countries[] = ['code' => 'GT', 'name' => 'Guatemala', 'd_code' => '+502'];
        $countries[] = ['code' => 'GN', 'name' => 'Guinea', 'd_code' => '+224'];
        $countries[] = ['code' => 'GW', 'name' => 'Guinea-Bissau', 'd_code' => '+245'];
        $countries[] = ['code' => 'GY', 'name' => 'Guyana', 'd_code' => '+592'];
        $countries[] = ['code' => 'HT', 'name' => 'Haiti', 'd_code' => '+509'];
        $countries[] = ['code' => 'HN', 'name' => 'Honduras', 'd_code' => '+504'];
        $countries[] = ['code' => 'HK', 'name' => 'Hong Kong', 'd_code' => '+852'];
        $countries[] = ['code' => 'HU', 'name' => 'Hungary', 'd_code' => '+36'];
        $countries[] = ['code' => 'IS', 'name' => 'Iceland', 'd_code' => '+354'];
        $countries[] = ['code' => 'IN', 'name' => 'India', 'd_code' => '+91'];
        $countries[] = ['code' => 'ID', 'name' => 'Indonesia', 'd_code' => '+62'];
        $countries[] = ['code' => 'IR', 'name' => 'Iran', 'd_code' => '+98'];
        $countries[] = ['code' => 'IQ', 'name' => 'Iraq', 'd_code' => '+964'];
        $countries[] = ['code' => 'IE', 'name' => 'Ireland', 'd_code' => '+353'];
        $countries[] = ['code' => 'IL', 'name' => 'Israel', 'd_code' => '+972'];
        $countries[] = ['code' => 'IT', 'name' => 'Italy', 'd_code' => '+39'];
        $countries[] = ['code' => 'JM', 'name' => 'Jamaica', 'd_code' => '+1'];
        $countries[] = ['code' => 'JP', 'name' => 'Japan', 'd_code' => '+81'];
        $countries[] = ['code' => 'JO', 'name' => 'Jordan', 'd_code' => '+962'];
        $countries[] = ['code' => 'KZ', 'name' => 'Kazakhstan', 'd_code' => '+7'];
        $countries[] = ['code' => 'KE', 'name' => 'Kenya', 'd_code' => '+254'];
        $countries[] = ['code' => 'KI', 'name' => 'Kiribati', 'd_code' => '+686'];
        $countries[] = ['code' => 'XK', 'name' => 'Kosovo', 'd_code' => '+381'];
        $countries[] = ['code' => 'KW', 'name' => 'Kuwait', 'd_code' => '+965'];
        $countries[] = ['code' => 'KG', 'name' => 'Kyrgyzstan', 'd_code' => '+996'];
        $countries[] = ['code' => 'LA', 'name' => 'Laos', 'd_code' => '+856'];
        $countries[] = ['code' => 'LV', 'name' => 'Latvia', 'd_code' => '+371'];
        $countries[] = ['code' => 'LB', 'name' => 'Lebanon', 'd_code' => '+961'];
        $countries[] = ['code' => 'LS', 'name' => 'Lesotho', 'd_code' => '+266'];
        $countries[] = ['code' => 'LR', 'name' => 'Liberia', 'd_code' => '+231'];
        $countries[] = ['code' => 'LY', 'name' => 'Libya', 'd_code' => '+218'];
        $countries[] = ['code' => 'LI', 'name' => 'Liechtenstein', 'd_code' => '+423'];
        $countries[] = ['code' => 'LT', 'name' => 'Lithuania', 'd_code' => '+370'];
        $countries[] = ['code' => 'LU', 'name' => 'Luxembourg', 'd_code' => '+352'];
        $countries[] = ['code' => 'MO', 'name' => 'Macau', 'd_code' => '+853'];
        $countries[] = ['code' => 'MK', 'name' => 'Macedonia', 'd_code' => '+389'];
        $countries[] = ['code' => 'MG', 'name' => 'Madagascar', 'd_code' => '+261'];
        $countries[] = ['code' => 'MW', 'name' => 'Malawi', 'd_code' => '+265'];
        $countries[] = ['code' => 'MY', 'name' => 'Malaysia', 'd_code' => '+60'];
        $countries[] = ['code' => 'MV', 'name' => 'Maldives', 'd_code' => '+960'];
        $countries[] = ['code' => 'ML', 'name' => 'Mali', 'd_code' => '+223'];
        $countries[] = ['code' => 'MT', 'name' => 'Malta', 'd_code' => '+356'];
        $countries[] = ['code' => 'MH', 'name' => 'Marshall Islands', 'd_code' => '+692'];
        $countries[] = ['code' => 'MQ', 'name' => 'Martinique', 'd_code' => '+596'];
        $countries[] = ['code' => 'MR', 'name' => 'Mauritania', 'd_code' => '+222'];
        $countries[] = ['code' => 'MU', 'name' => 'Mauritius', 'd_code' => '+230'];
        $countries[] = ['code' => 'YT', 'name' => 'Mayotte', 'd_code' => '+262'];
        $countries[] = ['code' => 'MX', 'name' => 'Mexico', 'd_code' => '+52'];
        $countries[] = ['code' => 'MD', 'name' => 'Moldova', 'd_code' => '+373'];
        $countries[] = ['code' => 'MC', 'name' => 'Monaco', 'd_code' => '+377'];
        $countries[] = ['code' => 'MN', 'name' => 'Mongolia', 'd_code' => '+976'];
        $countries[] = ['code' => 'ME', 'name' => 'Montenegro', 'd_code' => '+382'];
        $countries[] = ['code' => 'MS', 'name' => 'Montserrat', 'd_code' => '+1'];
        $countries[] = ['code' => 'MA', 'name' => 'Morocco', 'd_code' => '+212'];
        $countries[] = ['code' => 'MZ', 'name' => 'Mozambique', 'd_code' => '+258'];
        $countries[] = ['code' => 'NA', 'name' => 'Namibia', 'd_code' => '+264'];
        $countries[] = ['code' => 'NR', 'name' => 'Nauru', 'd_code' => '+674'];
        $countries[] = ['code' => 'NP', 'name' => 'Nepal', 'd_code' => '+977'];
        $countries[] = ['code' => 'NL', 'name' => 'Netherlands', 'd_code' => '+31'];
        $countries[] = ['code' => 'AN', 'name' => 'Netherlands Antilles', 'd_code' => '+599'];
        $countries[] = ['code' => 'NC', 'name' => 'New Caledonia', 'd_code' => '+687'];
        $countries[] = ['code' => 'NZ', 'name' => 'New Zealand', 'd_code' => '+64'];
        $countries[] = ['code' => 'NI', 'name' => 'Nicaragua', 'd_code' => '+505'];
        $countries[] = ['code' => 'NE', 'name' => 'Niger', 'd_code' => '+227'];
        $countries[] = ['code' => 'NG', 'name' => 'Nigeria', 'd_code' => '+234'];
        $countries[] = ['code' => 'NU', 'name' => 'Niue', 'd_code' => '+683'];
        $countries[] = ['code' => 'NF', 'name' => 'Norfolk Island', 'd_code' => '+672'];
        $countries[] = ['code' => 'KP', 'name' => 'North Korea', 'd_code' => '+850'];
        $countries[] = ['code' => 'MP', 'name' => 'Northern Mariana Islands', 'd_code' => '+1'];
        $countries[] = ['code' => 'NO', 'name' => 'Norway', 'd_code' => '+47'];
        $countries[] = ['code' => 'OM', 'name' => 'Oman', 'd_code' => '+968'];
        $countries[] = ['code' => 'PK', 'name' => 'Pakistan', 'd_code' => '+92'];
        $countries[] = ['code' => 'PW', 'name' => 'Palau', 'd_code' => '+680'];
        $countries[] = ['code' => 'PS', 'name' => 'Palestine', 'd_code' => '+970'];
        $countries[] = ['code' => 'PA', 'name' => 'Panama', 'd_code' => '+507'];
        $countries[] = ['code' => 'PG', 'name' => 'Papua New Guinea', 'd_code' => '+675'];
        $countries[] = ['code' => 'PY', 'name' => 'Paraguay', 'd_code' => '+595'];
        $countries[] = ['code' => 'PE', 'name' => 'Peru', 'd_code' => '+51'];
        $countries[] = ['code' => 'PH', 'name' => 'Philippines', 'd_code' => '+63'];
        $countries[] = ['code' => 'PL', 'name' => 'Poland', 'd_code' => '+48'];
        $countries[] = ['code' => 'PT', 'name' => 'Portugal', 'd_code' => '+351'];
        $countries[] = ['code' => 'PR', 'name' => 'Puerto Rico', 'd_code' => '+1'];
        $countries[] = ['code' => 'QA', 'name' => 'Qatar', 'd_code' => '+974'];
        $countries[] = ['code' => 'CG', 'name' => 'Republic of the Congo', 'd_code' => '+242'];
        $countries[] = ['code' => 'RE', 'name' => 'Réunion', 'd_code' => '+262'];
        $countries[] = ['code' => 'RO', 'name' => 'Romania', 'd_code' => '+40'];
        $countries[] = ['code' => 'RU', 'name' => 'Russia', 'd_code' => '+7'];
        $countries[] = ['code' => 'RW', 'name' => 'Rwanda', 'd_code' => '+250'];
        $countries[] = ['code' => 'BL', 'name' => 'Saint Barthélemy', 'd_code' => '+590'];
        $countries[] = ['code' => 'SH', 'name' => 'Saint Helena', 'd_code' => '+290'];
        $countries[] = ['code' => 'KN', 'name' => 'Saint Kitts and Nevis', 'd_code' => '+1'];
        $countries[] = ['code' => 'MF', 'name' => 'Saint Martin', 'd_code' => '+590'];
        $countries[] = ['code' => 'PM', 'name' => 'Saint Pierre and Miquelon', 'd_code' => '+508'];
        $countries[] = ['code' => 'VC', 'name' => 'Saint Vincent and the Grenadines', 'd_code' => '+1'];
        $countries[] = ['code' => 'WS', 'name' => 'Samoa', 'd_code' => '+685'];
        $countries[] = ['code' => 'SM', 'name' => 'San Marino', 'd_code' => '+378'];
        $countries[] = ['code' => 'ST', 'name' => 'São Tomé and Príncipe', 'd_code' => '+239'];
        $countries[] = ['code' => 'SA', 'name' => 'Saudi Arabia', 'd_code' => '+966'];
        $countries[] = ['code' => 'SN', 'name' => 'Senegal', 'd_code' => '+221'];
        $countries[] = ['code' => 'RS', 'name' => 'Serbia', 'd_code' => '+381'];
        $countries[] = ['code' => 'SC', 'name' => 'Seychelles', 'd_code' => '+248'];
        $countries[] = ['code' => 'SL', 'name' => 'Sierra Leone', 'd_code' => '+232'];
        $countries[] = ['code' => 'SG', 'name' => 'Singapore', 'd_code' => '+65'];
        $countries[] = ['code' => 'SK', 'name' => 'Slovakia', 'd_code' => '+421'];
        $countries[] = ['code' => 'SI', 'name' => 'Slovenia', 'd_code' => '+386'];
        $countries[] = ['code' => 'SB', 'name' => 'Solomon Islands', 'd_code' => '+677'];
        $countries[] = ['code' => 'SO', 'name' => 'Somalia', 'd_code' => '+252'];
        $countries[] = ['code' => 'ZA', 'name' => 'South Africa', 'd_code' => '+27'];
        $countries[] = ['code' => 'KR', 'name' => 'South Korea', 'd_code' => '+82'];
        $countries[] = ['code' => 'ES', 'name' => 'Spain', 'd_code' => '+34'];
        $countries[] = ['code' => 'LK', 'name' => 'Sri Lanka', 'd_code' => '+94'];
        $countries[] = ['code' => 'LC', 'name' => 'St. Lucia', 'd_code' => '+1'];
        $countries[] = ['code' => 'SD', 'name' => 'Sudan', 'd_code' => '+249'];
        $countries[] = ['code' => 'SR', 'name' => 'Suriname', 'd_code' => '+597'];
        $countries[] = ['code' => 'SZ', 'name' => 'Swaziland', 'd_code' => '+268'];
        $countries[] = ['code' => 'SE', 'name' => 'Sweden', 'd_code' => '+46'];
        $countries[] = ['code' => 'CH', 'name' => 'Switzerland', 'd_code' => '+41'];
        $countries[] = ['code' => 'SY', 'name' => 'Syria', 'd_code' => '+963'];
        $countries[] = ['code' => 'TW', 'name' => 'Taiwan', 'd_code' => '+886'];
        $countries[] = ['code' => 'TJ', 'name' => 'Tajikistan', 'd_code' => '+992'];
        $countries[] = ['code' => 'TZ', 'name' => 'Tanzania', 'd_code' => '+255'];
        $countries[] = ['code' => 'TH', 'name' => 'Thailand', 'd_code' => '+66'];
        $countries[] = ['code' => 'BS', 'name' => 'The Bahamas', 'd_code' => '+1'];
        $countries[] = ['code' => 'GM', 'name' => 'The Gambia', 'd_code' => '+220'];
        $countries[] = ['code' => 'TL', 'name' => 'Timor-Leste', 'd_code' => '+670'];
        $countries[] = ['code' => 'TG', 'name' => 'Togo', 'd_code' => '+228'];
        $countries[] = ['code' => 'TK', 'name' => 'Tokelau', 'd_code' => '+690'];
        $countries[] = ['code' => 'TO', 'name' => 'Tonga', 'd_code' => '+676'];
        $countries[] = ['code' => 'TT', 'name' => 'Trinidad and Tobago', 'd_code' => '+1'];
        $countries[] = ['code' => 'TN', 'name' => 'Tunisia', 'd_code' => '+216'];
        $countries[] = ['code' => 'TR', 'name' => 'Turkey', 'd_code' => '+90'];
        $countries[] = ['code' => 'TM', 'name' => 'Turkmenistan', 'd_code' => '+993'];
        $countries[] = ['code' => 'TC', 'name' => 'Turks and Caicos Islands', 'd_code' => '+1'];
        $countries[] = ['code' => 'TV', 'name' => 'Tuvalu', 'd_code' => '+688'];
        $countries[] = ['code' => 'UG', 'name' => 'Uganda', 'd_code' => '+256'];
        $countries[] = ['code' => 'UA', 'name' => 'Ukraine', 'd_code' => '+380'];
        $countries[] = ['code' => 'AE', 'name' => 'United Arab Emirates', 'd_code' => '+971'];
        $countries[] = ['code' => 'GB', 'name' => 'United Kingdom', 'd_code' => '+44'];
        $countries[] = ['code' => 'US', 'name' => 'United States', 'd_code' => '+1'];
        $countries[] = ['code' => 'UY', 'name' => 'Uruguay', 'd_code' => '+598'];
        $countries[] = ['code' => 'VI', 'name' => 'US Virgin Islands', 'd_code' => '+1'];
        $countries[] = ['code' => 'UZ', 'name' => 'Uzbekistan', 'd_code' => '+998'];
        $countries[] = ['code' => 'VU', 'name' => 'Vanuatu', 'd_code' => '+678'];
        $countries[] = ['code' => 'VA', 'name' => 'Vatican City', 'd_code' => '+39'];
        $countries[] = ['code' => 'VE', 'name' => 'Venezuela', 'd_code' => '+58'];
        $countries[] = ['code' => 'VN', 'name' => 'Vietnam', 'd_code' => '+84'];
        $countries[] = ['code' => 'WF', 'name' => 'Wallis and Futuna', 'd_code' => '+681'];
        $countries[] = ['code' => 'YE', 'name' => 'Yemen', 'd_code' => '+967'];
        $countries[] = ['code' => 'ZM', 'name' => 'Zambia', 'd_code' => '+260'];
        $countries[] = ['code' => 'ZW', 'name' => 'Zimbabwe', 'd_code' => '+263'];

        return $countries;
    }


    /**
     * get timezone list
     *
     * @return array
     * @throws Exception
     */
    public static function timezoneList(): array
    {
        $timezoneIdentifiers = DateTimeZone::listIdentifiers();
        $utcTime             = new DateTime('now', new DateTimeZone('UTC'));

        $tempTimezones = [];
        foreach ($timezoneIdentifiers as $timezoneIdentifier) {
            $currentTimezone = new DateTimeZone($timezoneIdentifier);

            $tempTimezones[] = [
                    'offset'     => (int) $currentTimezone->getOffset($utcTime),
                    'identifier' => $timezoneIdentifier,
            ];
        }
        usort($tempTimezones, function ($a, $b) {
            return ($a['offset'] == $b['offset'])
                    ? strcmp($a['identifier'], $b['identifier'])
                    : $a['offset'] - $b['offset'];
        });

        $timezoneList = [];
        foreach ($tempTimezones as $tz) {
            $sign                            = ($tz['offset'] > 0) ? '+' : '-';
            $offset                          = gmdate('H:i', abs($tz['offset']));
            $timezoneList[$tz['identifier']] = '(UTC '.$sign.$offset.') '.
                    $tz['identifier'];
        }

        return $timezoneList;
    }


    /**
     * Check if exec() function is available.
     *
     * @return bool
     */

    public static function exec_enabled(): bool
    {
        try {
            // make a small test
            exec('ls');

            return function_exists('exec') && ! in_array('exec', array_map('trim', explode(', ', ini_get('disable_functions'))));
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * application menu
     *
     * @return array[]
     */
    public static function menuData(): array
    {
        return [
                "admin"    => [
                        [
                                "url"    => url(config('app.admin_path')."/dashboard"),
                                'slug'   => config('app.admin_path')."/dashboard",
                                "name"   => "Dashboard",
                                "i18n"   => "Dashboard",
                                "icon"   => "feather icon-home",
                                "access" => "access backend",
                        ],
                        [
                                "url"     => "",
                                "name"    => "Customer",
                                "icon"    => "feather icon-users",
                                "i18n"    => "Customer",
                                "access"  => "view customer|view subscription",
                                "submenu" => [
                                        [
                                                "url"    => url(config('app.admin_path')."/customers"),
                                                'slug'   => config('app.admin_path')."/customers",
                                                "name"   => "Customers",
                                                "i18n"   => "Customers",
                                                "access" => "view customer",
                                        ],
                                        [
                                                "url"    => url(config('app.admin_path')."/subscriptions"),
                                                'slug'   => config('app.admin_path')."/subscriptions",
                                                "name"   => "Subscriptions",
                                                "i18n"   => "Subscriptions",
                                                "access" => "view subscription",
                                        ],
                                ],
                        ],
                        [
                                "url"     => "",
                                "name"    => "Plan",
                                "i18n"    => "Plan",
                                "icon"    => "feather icon-credit-card",
                                "access"  => "manage plans|manage currencies",
                                "submenu" => [
                                        [
                                                "url"    => url(config('app.admin_path')."/plans"),
                                                'slug'   => config('app.admin_path')."/plans",
                                                "name"   => "Plans",
                                                "i18n"   => "Plans",
                                                "access" => "manage plans",
                                        ],
                                        [
                                                "url"    => url(config('app.admin_path')."/currencies"),
                                                'slug'   => config('app.admin_path')."/currencies",
                                                "name"   => "Currencies",
                                                "i18n"   => "Currencies",
                                                "access" => "manage currencies",
                                        ],
                                ],
                        ],
                        [
                                "url"     => "",
                                "name"    => "Sending",
                                "icon"    => "feather icon-send",
                                "i18n"    => "Sending",
                                "access"  => "view sender_id|view keywords|view sending_servers|view phone_numbers|view tags",
                                "submenu" => [
                                        [
                                                "url"    => url(config('app.admin_path')."/sending-servers"),
                                                'slug'   => config('app.admin_path')."/sending-servers",
                                                "name"   => "Sending Servers",
                                                "i18n"   => "Sending Servers",
                                                "access" => "view sending_servers",
                                        ],
                                        [
                                                "url"    => url(config('app.admin_path')."/senderid"),
                                                'slug'   => config('app.admin_path')."/senderid",
                                                "name"   => "Sender ID",
                                                "i18n"   => "Sender ID",
                                                "access" => "view sender_id",
                                        ],
                                        [
                                                "url"    => url(config('app.admin_path')."/phone-numbers"),
                                                'slug'   => config('app.admin_path')."/phone-numbers",
                                                "name"   => "Numbers",
                                                "i18n"   => "Numbers",
                                                "access" => "view phone_numbers",
                                        ],
                                        [
                                                "url"    => url(config('app.admin_path')."/keywords"),
                                                'slug'   => config('app.admin_path')."/keywords",
                                                "name"   => "Keywords",
                                                "i18n"   => "Keywords",
                                                "access" => "view keywords",
                                        ],
                                        [
                                                "url"    => url(config('app.admin_path')."/tags"),
                                                'slug'   => config('app.admin_path')."/tags",
                                                "name"   => "Template Tags",
                                                "i18n"   => "Template Tags",
                                                "access" => "view tags",
                                        ],
                                ],
                        ],
                        [
                                "url"     => "",
                                "name"    => "Security",
                                "i18n"    => "Security",
                                "icon"    => "feather icon-shield",
                                "access"  => "view blacklist|view spam_word",
                                "submenu" => [
                                        [
                                                "url"    => url(config('app.admin_path')."/blacklists"),
                                                'slug'   => config('app.admin_path')."/blacklists",
                                                "name"   => "Blacklist",
                                                "i18n"   => "Blacklist",
                                                "access" => "view blacklist",
                                        ],
                                        [
                                                "url"    => url(config('app.admin_path')."/spam-word"),
                                                'slug'   => config('app.admin_path')."/spam-word",
                                                "name"   => "Spam Word",
                                                "i18n"   => "Spam Word",
                                                "access" => "view spam_word",
                                        ],
                                ],
                        ],
                        [
                                "url"     => "",
                                "name"    => "Administrator",
                                "i18n"    => "Administrator",
                                "icon"    => "feather icon-user",
                                "access"  => "view administrator|view roles",
                                "submenu" => [
                                        [
                                                "url"    => url(config('app.admin_path')."/administrators"),
                                                'slug'   => config('app.admin_path')."/administrators",
                                                "name"   => "Administrators",
                                                "i18n"   => "Administrators",
                                                "access" => "view administrator",
                                        ],
                                        [
                                                "url"    => url(config('app.admin_path')."/roles"),
                                                'slug'   => config('app.admin_path')."/roles",
                                                "name"   => "Admin Roles",
                                                "i18n"   => "Admin Roles",
                                                "access" => "view roles",
                                        ],
                                ],
                        ],
                        [
                                "url"     => "",
                                "name"    => "Settings",
                                "i18n"    => "Settings",
                                "icon"    => "feather icon-settings",
                                "access"  => "general settings|view languages|view payment_gateways|view email_templates|manage update_application",
                                "submenu" => [
                                        [
                                                "url"    => url(config('app.admin_path')."/settings"),
                                                'slug'   => config('app.admin_path')."/settings",
                                                "name"   => "All Settings",
                                                "i18n"   => "All Settings",
                                                "access" => "general settings",
                                        ],
                                        [
                                                "url"    => url(config('app.admin_path')."/languages"),
                                                'slug'   => config('app.admin_path')."/languages",
                                                "name"   => "Language",
                                                "i18n"   => "Language",
                                                "access" => "view languages",
                                        ],
                                        [
                                                "url"    => url(config('app.admin_path')."/payment-gateways"),
                                                'slug'   => config('app.admin_path')."/payment-gateways",
                                                "name"   => "Payment Gateways",
                                                "i18n"   => "Payment Gateways",
                                                "access" => "view payment_gateways",
                                        ],
                                        [
                                                "url"    => url(config('app.admin_path')."/email-templates"),
                                                'slug'   => config('app.admin_path')."/email-templates",
                                                "name"   => "Email Templates",
                                                "i18n"   => "Email Templates",
                                                "access" => "view email_templates",
                                        ],
                                        [
                                                "url"    => url(config('app.admin_path')."/update-application"),
                                                'slug'   => config('app.admin_path')."/update-application",
                                                "name"   => "Update Application",
                                                "i18n"   => "Update Application",
                                                "access" => "manage update_application",
                                        ],
                                ],
                        ],
                        [
                                "url"     => "",
                                "name"    => "Reports",
                                "i18n"    => "Reports",
                                "icon"    => "feather icon-bar-chart-2",
                                "access"  => "view invoices|view sms_history",
                                "submenu" => [
                                        [
                                                "url"    => url(config('app.admin_path')."/invoices"),
                                                'slug'   => config('app.admin_path')."/invoices",
                                                "name"   => "All Invoices",
                                                "i18n"   => "All Invoices",
                                                "access" => "view invoices",
                                        ],
                                        [
                                                "url"    => url(config('app.admin_path')."/reports"),
                                                'slug'   => config('app.admin_path')."/reports",
                                                "name"   => "SMS History",
                                                "i18n"   => "SMS History",
                                                "access" => "view sms_history",
                                        ],
                                ],
                        ],
                        [
                                "url"    => url(config('app.admin_path')."/plugins"),
                                'slug'   => config('app.admin_path')."/plugins",
                                "name"   => "Plugins",
                                "i18n"   => "Plugins",
                                "icon"   => "feather icon-package",
                                "access" => "access backend",
                        ],
                ],
                "customer" => [
                        [
                                "url"    => url("dashboard"),
                                'slug'   => "dashboard",
                                "name"   => "Dashboard",
                                "i18n"   => "Dashboard",
                                "icon"   => "feather icon-home",
                                "access" => "access_backend",
                        ],
                        [
                                "url"     => "",
                                "name"    => "Reports",
                                "i18n"    => "Reports",
                                "icon"    => "feather icon-bar-chart-2",
                                "access"  => "view_reports",
                                "submenu" => [
                                        [
                                                "url"    => url("reports/all"),
                                                'slug'   => "reports/all",
                                                "name"   => "All Messages",
                                                "i18n"   => "All Messages",
                                                "access" => "view_reports",
                                        ],
                                        [
                                                "url"    => url("reports/received"),
                                                'slug'   => "reports/received",
                                                "name"   => "Received Messages",
                                                "i18n"   => "Received Messages",
                                                "access" => "view_reports",
                                        ],
                                        [
                                                "url"    => url("reports/sent"),
                                                'slug'   => "reports/sent",
                                                "name"   => "Sent Messages",
                                                "i18n"   => "Sent Messages",
                                                "access" => "view_reports",
                                        ],
                                        [
                                                "url"    => url("reports/campaigns"),
                                                'slug'   => "reports/campaigns",
                                                "name"   => "Campaigns",
                                                "i18n"   => "Campaigns",
                                                "access" => "view_reports",
                                        ],
                                ],
                        ],
                        [
                                "url"    => url("contacts"),
                                'slug'   => "contacts",
                                "name"   => "Contacts",
                                "i18n"   => "Contacts",
                                "icon"   => "feather icon-user",
                                "access" => "view_contact_group|create_contact_group|update_contact_group|delete_contact_group|view_contact|create_contact|update_contact|delete_contact",
                        ],
                        [
                                "url"     => "",
                                "name"    => "Sending",
                                "icon"    => "feather icon-send",
                                "i18n"    => "Sending",
                                "access"  => "create_sending_servers|view_numbers|view_keywords|view_sender_id|sms_template",
                                "submenu" => [
                                        [
                                                "url"    => url("sending-servers"),
                                                'slug'   => "sending-servers",
                                                "name"   => "Sending Servers",
                                                "i18n"   => "Sending Servers",
                                                "access" => "create_sending_servers",
                                        ],
                                        [
                                                "url"    => url("senderid"),
                                                'slug'   => "senderid",
                                                "name"   => "Sender ID",
                                                "i18n"   => "Sender ID",
                                                "access" => "view_sender_id",
                                        ],
                                        [
                                                "url"    => url("numbers"),
                                                'slug'   => "numbers",
                                                "name"   => "Numbers",
                                                "i18n"   => "Numbers",
                                                "access" => "view_numbers",
                                        ],
                                        [
                                                "url"    => url("keywords"),
                                                'slug'   => "keywords",
                                                "name"   => "Keywords",
                                                "i18n"   => "Keywords",
                                                "access" => "view_keywords",
                                        ],
                                        [
                                                "url"    => url("templates"),
                                                'slug'   => "templates",
                                                "name"   => "SMS Template",
                                                "i18n"   => "SMS Template",
                                                "access" => "sms_template",
                                        ],
                                ],
                        ],
                        [
                                "url"    => url("blacklists"),
                                'slug'   => "blacklists",
                                "name"   => "Blacklist",
                                "i18n"   => "Blacklist",
                                "icon"   => "feather icon-shield",
                                "access" => "view_blacklist|create_blacklist|update_blacklist|delete_blacklist",
                        ],
                        [
                                "url"     => "",
                                "name"    => "SMS",
                                "i18n"    => "SMS",
                                "icon"    => "feather icon-message-square",
                                "access"  => "sms_campaign_builder|sms_quick_send|sms_bulk_messages",
                                "submenu" => [
                                        [
                                                "url"    => url("sms/campaign-builder"),
                                                'slug'   => "sms/campaign-builder",
                                                "name"   => "Campaign Builder",
                                                "i18n"   => "Campaign Builder",
                                                "access" => "sms_campaign_builder",
                                        ],
                                        [
                                                "url"    => url("sms/quick-send"),
                                                'slug'   => "sms/quick-send",
                                                "name"   => "Quick Send",
                                                "i18n"   => "Quick Send",
                                                "access" => "sms_quick_send",
                                        ],
                                        [
                                                "url"    => url("sms/import"),
                                                'slug'   => "sms/import",
                                                "name"   => "Send Using File",
                                                "i18n"   => "Send Using File",
                                                "access" => "sms_bulk_messages",
                                        ],
                                ],
                        ],
                        [
                                "url"     => "",
                                "name"    => "Voice",
                                "i18n"    => "Voice",
                                "icon"    => "feather icon-phone-call",
                                "access"  => "voice_campaign_builder|voice_quick_send|voice_bulk_messages",
                                "submenu" => [
                                        [
                                                "url"    => url("voice/campaign-builder"),
                                                'slug'   => "voice/campaign-builder",
                                                "name"   => "Campaign Builder",
                                                "i18n"   => "Campaign Builder",
                                                "access" => "voice_campaign_builder",
                                        ],
                                        [
                                                "url"    => url("voice/quick-send"),
                                                'slug'   => "voice/quick-send",
                                                "name"   => "Quick Send",
                                                "i18n"   => "Quick Send",
                                                "access" => "voice_quick_send",
                                        ],
                                        [
                                                "url"    => url("voice/import"),
                                                'slug'   => "voice/import",
                                                "name"   => "Send Using File",
                                                "i18n"   => "Send Using File",
                                                "access" => "voice_bulk_messages",
                                        ],
                                ],
                        ],
                        [
                                "url"     => "",
                                "name"    => "MMS",
                                "i18n"    => "MMS",
                                "icon"    => "feather icon-image",
                                "access"  => "mms_campaign_builder|mms_quick_send|mms_bulk_messages",
                                "submenu" => [
                                        [
                                                "url"    => url("mms/campaign-builder"),
                                                'slug'   => "mms/campaign-builder",
                                                "name"   => "Campaign Builder",
                                                "i18n"   => "Campaign Builder",
                                                "access" => "mms_campaign_builder",
                                        ],
                                        [
                                                "url"    => url("mms/quick-send"),
                                                'slug'   => "mms/quick-send",
                                                "name"   => "Quick Send",
                                                "i18n"   => "Quick Send",
                                                "access" => "mms_quick_send",
                                        ],
                                        [
                                                "url"    => url("mms/import"),
                                                'slug'   => "mms/import",
                                                "name"   => "Send Using File",
                                                "i18n"   => "Send Using File",
                                                "access" => "mms_bulk_messages",
                                        ],
                                ],
                        ],
                        [
                                "url"     => "",
                                "name"    => "WhatsApp",
                                "i18n"    => "WhatsApp",
                                "icon"    => "feather icon-message-circle",
                                "access"  => "whatsapp_campaign_builder|whatsapp_quick_send|whatsapp_bulk_messages",
                                "submenu" => [
                                        [
                                                "url"    => url("whatsapp/campaign-builder"),
                                                'slug'   => "whatsapp/campaign-builder",
                                                "name"   => "Campaign Builder",
                                                "i18n"   => "Campaign Builder",
                                                "access" => "whatsapp_campaign_builder",
                                        ],
                                        [
                                                "url"    => url("whatsapp/quick-send"),
                                                'slug'   => "whatsapp/quick-send",
                                                "name"   => "Quick Send",
                                                "i18n"   => "Quick Send",
                                                "access" => "whatsapp_quick_send",
                                        ],
                                        [
                                                "url"    => url("whatsapp/import"),
                                                'slug'   => "whatsapp/import",
                                                "name"   => "Send Using File",
                                                "i18n"   => "Send Using File",
                                                "access" => "whatsapp_bulk_messages",
                                        ],
                                ],
                        ],
                        [
                                "url"    => url("chat-box"),
                                'slug'   => "chat-box",
                                "name"   => "Chat Box",
                                "i18n"   => "Chat Box",
                                "icon"   => "feather icon-slack",
                                "access" => "chat_box",
                        ],

                        [
                                "url"    => url("developers"),
                                'slug'   => "developers",
                                "name"   => "Developers",
                                "i18n"   => "Developers",
                                "icon"   => "feather icon-terminal",
                                "access" => "developers",
                        ],
                ],
        ];
    }

    public static function languages()
    {
        $lang_count  = Language::where('status', 1)->count();
        $availLocale = Session::get('available_languages');

        if ( ! isset($availLocale) || count($availLocale) !== $lang_count) {
            $availLocale = Language::where('status', 1)->cursor()->map(function ($lang) {
                return [
                        'name'     => $lang->name,
                        'code'     => $lang->code,
                        'iso_code' => $lang->iso_code,
                ];
            })->toArray();

            session()->put('available_languages', $availLocale);
        }

        return $availLocale;
    }


    /**
     * make round robin
     *
     * @param  array  $teams
     * @param  int|null  $rounds
     * @param  bool  $shuffle
     * @param  int|null  $seed
     *
     * @return array
     */
    public static function makeRoundRobin(array $teams, int $rounds = null, bool $shuffle = true, int $seed = null): array
    {
        $teamCount = count($teams);

        if ($teamCount < 2) {
            return [];
        }
        //Account for odd number of teams by adding a bye
        if ($teamCount % 2 === 1) {
            array_push($teams, null);
            $teamCount += 1;
        }
        if ($shuffle) {
            //Seed shuffle with random_int for better randomness if seed is null
            try {
                srand($seed ?? random_int(PHP_INT_MIN, PHP_INT_MAX));
            } catch (Exception $e) {
            }
            shuffle($teams);
        } elseif ( ! is_null($seed)) {
            //Generate friendly notice that seed is set but shuffle is set to false
            trigger_error('Seed parameter has no effect when shuffle parameter is set to false');
        }
        $halfTeamCount = $teamCount / 2;
        if ($rounds === null) {
            $rounds = $teamCount - 1;
        }
        $schedule = [];
        for ($round = 1; $round <= $rounds; $round += 1) {
            foreach ($teams as $key => $team) {
                if ($key >= $halfTeamCount) {
                    break;
                }
                $team1 = $team;
                $team2 = $teams[$key + $halfTeamCount];
                //Home-away swapping
                $matchup            = $round % 2 === 0 ? [$team1, $team2] : [$team2, $team1];
                $schedule[$round][] = $matchup;
            }

            $itemCount = count($teams);

            if ($itemCount < 3) {
                return [];
            }
            $lastIndex = $itemCount - 1;
            /**
             * Though not technically part of the round-robin algorithm, odd-even
             * factor differentiation included to have intuitive behavior for arrays
             * with an odd number of elements
             */
            $factor          = (int) ($itemCount % 2 === 0 ? $itemCount / 2 : ($itemCount / 2) + 1);
            $topRightIndex   = $factor - 1;
            $topRightItem    = $teams[$topRightIndex];
            $bottomLeftIndex = $factor;
            $bottomLeftItem  = $teams[$bottomLeftIndex];
            for ($i = $topRightIndex; $i > 0; $i -= 1) {
                $teams[$i] = $teams[$i - 1];
            }
            for ($i = $bottomLeftIndex; $i < $lastIndex; $i += 1) {
                $teams[$i] = $teams[$i + 1];
            }
            $teams[1]          = $bottomLeftItem;
            $teams[$lastIndex] = $topRightItem;
        }

        return $schedule;
    }


    /**
     * voice regions
     *
     * @return string[]
     */
    public static function voice_regions(): array
    {
        return [
                "de-DE" => "German, Germany",
                "en-AU" => "English, Australia",
                "en-GB" => "English, UK",
                "en-US" => "English, US",
                "es-ES" => "Spanish, Spain",
                "es-MX" => "Spanish, Mexico",
                "es-US" => "Spanish, US",
                "fr-CA" => "French, Canada",
                "fr-FR" => "French, France",
                "is-IS" => "Icelandic, Iceland",
                "it-IT" => "Italian, Italy",
                "ja-JP" => "Japanese, Japan",
                "ko-KR" => "Korean, Korea",
                "nl-NL" => "Dutch, Netherlands",
                "pl-PL" => "Polish, Poland",
                "pt-BR" => "Portuguese, Brazil",
                "ro-RO" => "Romanian, Romania",
                "ru-RU" => "Russian, Russia",
                "zh-CN" => "Chinese (Mandarin",
                "da-DK" => "Danish, Denmark",
                "en-IN" => "English, Indian",
                "cy-GB" => "Welsh, Wales",
                "nb-NO" => "Norwegian, Norway",
                "pt-PT" => "Portuguese, Portugal",
                "sv-SE" => "Swedish, Sweden",
                "tr-TR" => "Turkish, Turkey",
                "el-GR" => "Greek, Greece",
                "zh-HK" => "Chinese, Hong",
                "id-ID" => "Indonesian, Indonesia",
                "vi-VN" => "Vietnamese, Vietnam",
                "th-TH" => "Thai, Thailand",
                "ta-IN" => "Tamil, India",
                "ms-MY" => "Malay, Malaysia",
        ];
    }

    public static function greetingMessage()
    {
        /* This sets the $time variable to the current hour in the 24 hour clock format */
        $time = date("H");
        /* If the time is less than 1200 hours, show good morning */
        if ($time < "12") {
            return __('locale.labels.greeting_message', [
                    'time' => __('locale.labels.good_morning'),
                    'name' => auth()->user()->displayName(),
            ]);
        } elseif ($time >= "12" && $time < "17") {
            return __('locale.labels.greeting_message', [
                    'time' => __('locale.labels.good_afternoon'),
                    'name' => auth()->user()->displayName(),
            ]);
        } elseif ($time >= "17" && $time < "19") {
            return __('locale.labels.greeting_message', [
                    'time' => __('locale.labels.good_evening'),
                    'name' => auth()->user()->displayName(),
            ]);
        } else {
            return __('locale.labels.greeting_message', [
                    'time' => __('locale.labels.good_night'),
                    'name' => auth()->user()->displayName(),
            ]);
        }
    }

    public static function contactName($number)
    {
        $contact = Contacts::where('phone', $number)->first();

        if ($contact && $contact->first_name != null) {
            return $contact->first_name.' '.$contact->last_name;
        }

        return $number;
    }

}
