<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class Countries extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {

        $c = new Country();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $c->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $countries   = [];
        $countries[] = ['iso_code' => 'AF', 'name' => 'Afghanistan', 'country_code' => '93'];
        $countries[] = ['iso_code' => 'AL', 'name' => 'Albania', 'country_code' => '355'];
        $countries[] = ['iso_code' => 'DZ', 'name' => 'Algeria', 'country_code' => '213'];
        $countries[] = ['iso_code' => 'AS', 'name' => 'American Samoa', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'AD', 'name' => 'Andorra', 'country_code' => '376'];
        $countries[] = ['iso_code' => 'AO', 'name' => 'Angola', 'country_code' => '244'];
        $countries[] = ['iso_code' => 'AI', 'name' => 'Anguilla', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'AG', 'name' => 'Antigua', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'AR', 'name' => 'Argentina', 'country_code' => '54'];
        $countries[] = ['iso_code' => 'AM', 'name' => 'Armenia', 'country_code' => '374'];
        $countries[] = ['iso_code' => 'AW', 'name' => 'Aruba', 'country_code' => '297'];
        $countries[] = ['iso_code' => 'AU', 'name' => 'Australia', 'country_code' => '61'];
        $countries[] = ['iso_code' => 'AT', 'name' => 'Austria', 'country_code' => '43'];
        $countries[] = ['iso_code' => 'AZ', 'name' => 'Azerbaijan', 'country_code' => '994'];
        $countries[] = ['iso_code' => 'BH', 'name' => 'Bahrain', 'country_code' => '973'];
        $countries[] = ['iso_code' => 'BD', 'name' => 'Bangladesh', 'country_code' => '880'];
        $countries[] = ['iso_code' => 'BB', 'name' => 'Barbados', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'BY', 'name' => 'Belarus', 'country_code' => '375'];
        $countries[] = ['iso_code' => 'BE', 'name' => 'Belgium', 'country_code' => '32'];
        $countries[] = ['iso_code' => 'BZ', 'name' => 'Belize', 'country_code' => '501'];
        $countries[] = ['iso_code' => 'BJ', 'name' => 'Benin', 'country_code' => '229'];
        $countries[] = ['iso_code' => 'BM', 'name' => 'Bermuda', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'BT', 'name' => 'Bhutan', 'country_code' => '975'];
        $countries[] = ['iso_code' => 'BO', 'name' => 'Bolivia', 'country_code' => '591'];
        $countries[] = ['iso_code' => 'BA', 'name' => 'Bosnia and Herzegovina', 'country_code' => '387'];
        $countries[] = ['iso_code' => 'BW', 'name' => 'Botswana', 'country_code' => '267'];
        $countries[] = ['iso_code' => 'BR', 'name' => 'Brazil', 'country_code' => '55'];
        $countries[] = ['iso_code' => 'IO', 'name' => 'British Indian Ocean Territory', 'country_code' => '246'];
        $countries[] = ['iso_code' => 'VG', 'name' => 'British Virgin Islands', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'BN', 'name' => 'Brunei', 'country_code' => '673'];
        $countries[] = ['iso_code' => 'BG', 'name' => 'Bulgaria', 'country_code' => '359'];
        $countries[] = ['iso_code' => 'BF', 'name' => 'Burkina Faso', 'country_code' => '226'];
        $countries[] = ['iso_code' => 'MM', 'name' => 'Burma Myanmar', 'country_code' => '95'];
        $countries[] = ['iso_code' => 'BI', 'name' => 'Burundi', 'country_code' => '257'];
        $countries[] = ['iso_code' => 'KH', 'name' => 'Cambodia', 'country_code' => '855'];
        $countries[] = ['iso_code' => 'CM', 'name' => 'Cameroon', 'country_code' => '237'];
        $countries[] = ['iso_code' => 'CA', 'name' => 'Canada', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'CV', 'name' => 'Cape Verde', 'country_code' => '238'];
        $countries[] = ['iso_code' => 'KY', 'name' => 'Cayman Islands', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'CF', 'name' => 'Central African Republic', 'country_code' => '236'];
        $countries[] = ['iso_code' => 'TD', 'name' => 'Chad', 'country_code' => '235'];
        $countries[] = ['iso_code' => 'CL', 'name' => 'Chile', 'country_code' => '56'];
        $countries[] = ['iso_code' => 'CN', 'name' => 'China', 'country_code' => '86'];
        $countries[] = ['iso_code' => 'CO', 'name' => 'Colombia', 'country_code' => '57'];
        $countries[] = ['iso_code' => 'KM', 'name' => 'Comoros', 'country_code' => '269'];
        $countries[] = ['iso_code' => 'CK', 'name' => 'Cook Islands', 'country_code' => '682'];
        $countries[] = ['iso_code' => 'CR', 'name' => 'Costa Rica', 'country_code' => '506'];
        $countries[] = ['iso_code' => 'CI', 'name' => "Côte d'Ivoire", 'country_code' => '225'];
        $countries[] = ['iso_code' => 'HR', 'name' => 'Croatia', 'country_code' => '385'];
        $countries[] = ['iso_code' => 'CU', 'name' => 'Cuba', 'country_code' => '53'];
        $countries[] = ['iso_code' => 'CY', 'name' => 'Cyprus', 'country_code' => '357'];
        $countries[] = ['iso_code' => 'CZ', 'name' => 'Czech Republic', 'country_code' => '420'];
        $countries[] = ['iso_code' => 'CD', 'name' => 'Democratic Republic of Congo', 'country_code' => '243'];
        $countries[] = ['iso_code' => 'DK', 'name' => 'Denmark', 'country_code' => '45'];
        $countries[] = ['iso_code' => 'DJ', 'name' => 'Djibouti', 'country_code' => '253'];
        $countries[] = ['iso_code' => 'DM', 'name' => 'Dominica', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'DO', 'name' => 'Dominican Republic', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'EC', 'name' => 'Ecuador', 'country_code' => '593'];
        $countries[] = ['iso_code' => 'EG', 'name' => 'Egypt', 'country_code' => '20'];
        $countries[] = ['iso_code' => 'SV', 'name' => 'El Salvador', 'country_code' => '503'];
        $countries[] = ['iso_code' => 'GQ', 'name' => 'Equatorial Guinea', 'country_code' => '240'];
        $countries[] = ['iso_code' => 'ER', 'name' => 'Eritrea', 'country_code' => '291'];
        $countries[] = ['iso_code' => 'EE', 'name' => 'Estonia', 'country_code' => '372'];
        $countries[] = ['iso_code' => 'ET', 'name' => 'Ethiopia', 'country_code' => '251'];
        $countries[] = ['iso_code' => 'FK', 'name' => 'Falkland Islands', 'country_code' => '500'];
        $countries[] = ['iso_code' => 'FO', 'name' => 'Faroe Islands', 'country_code' => '298'];
        $countries[] = ['iso_code' => 'FM', 'name' => 'Federated States of Micronesia', 'country_code' => '691'];
        $countries[] = ['iso_code' => 'FJ', 'name' => 'Fiji', 'country_code' => '679'];
        $countries[] = ['iso_code' => 'FI', 'name' => 'Finland', 'country_code' => '358'];
        $countries[] = ['iso_code' => 'FR', 'name' => 'France', 'country_code' => '33'];
        $countries[] = ['iso_code' => 'GF', 'name' => 'French Guiana', 'country_code' => '594'];
        $countries[] = ['iso_code' => 'PF', 'name' => 'French Polynesia', 'country_code' => '689'];
        $countries[] = ['iso_code' => 'GA', 'name' => 'Gabon', 'country_code' => '241'];
        $countries[] = ['iso_code' => 'GE', 'name' => 'Georgia', 'country_code' => '995'];
        $countries[] = ['iso_code' => 'DE', 'name' => 'Germany', 'country_code' => '49'];
        $countries[] = ['iso_code' => 'GH', 'name' => 'Ghana', 'country_code' => '233'];
        $countries[] = ['iso_code' => 'GI', 'name' => 'Gibraltar', 'country_code' => '350'];
        $countries[] = ['iso_code' => 'GR', 'name' => 'Greece', 'country_code' => '30'];
        $countries[] = ['iso_code' => 'GL', 'name' => 'Greenland', 'country_code' => '299'];
        $countries[] = ['iso_code' => 'GD', 'name' => 'Grenada', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'GP', 'name' => 'Guadeloupe', 'country_code' => '590'];
        $countries[] = ['iso_code' => 'GU', 'name' => 'Guam', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'GT', 'name' => 'Guatemala', 'country_code' => '502'];
        $countries[] = ['iso_code' => 'GN', 'name' => 'Guinea', 'country_code' => '224'];
        $countries[] = ['iso_code' => 'GW', 'name' => 'Guinea-Bissau', 'country_code' => '245'];
        $countries[] = ['iso_code' => 'GY', 'name' => 'Guyana', 'country_code' => '592'];
        $countries[] = ['iso_code' => 'HT', 'name' => 'Haiti', 'country_code' => '509'];
        $countries[] = ['iso_code' => 'HN', 'name' => 'Honduras', 'country_code' => '504'];
        $countries[] = ['iso_code' => 'HK', 'name' => 'Hong Kong', 'country_code' => '852'];
        $countries[] = ['iso_code' => 'HU', 'name' => 'Hungary', 'country_code' => '36'];
        $countries[] = ['iso_code' => 'IS', 'name' => 'Iceland', 'country_code' => '354'];
        $countries[] = ['iso_code' => 'IN', 'name' => 'India', 'country_code' => '91'];
        $countries[] = ['iso_code' => 'ID', 'name' => 'Indonesia', 'country_code' => '62'];
        $countries[] = ['iso_code' => 'IR', 'name' => 'Iran', 'country_code' => '98'];
        $countries[] = ['iso_code' => 'IQ', 'name' => 'Iraq', 'country_code' => '964'];
        $countries[] = ['iso_code' => 'IE', 'name' => 'Ireland', 'country_code' => '353'];
        $countries[] = ['iso_code' => 'IL', 'name' => 'Israel', 'country_code' => '972'];
        $countries[] = ['iso_code' => 'IT', 'name' => 'Italy', 'country_code' => '39'];
        $countries[] = ['iso_code' => 'JM', 'name' => 'Jamaica', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'JP', 'name' => 'Japan', 'country_code' => '81'];
        $countries[] = ['iso_code' => 'JO', 'name' => 'Jordan', 'country_code' => '962'];
        $countries[] = ['iso_code' => 'KZ', 'name' => 'Kazakhstan', 'country_code' => '7'];
        $countries[] = ['iso_code' => 'KE', 'name' => 'Kenya', 'country_code' => '254'];
        $countries[] = ['iso_code' => 'KI', 'name' => 'Kiribati', 'country_code' => '686'];
        $countries[] = ['iso_code' => 'XK', 'name' => 'Kosovo', 'country_code' => '381'];
        $countries[] = ['iso_code' => 'KW', 'name' => 'Kuwait', 'country_code' => '965'];
        $countries[] = ['iso_code' => 'KG', 'name' => 'Kyrgyzstan', 'country_code' => '996'];
        $countries[] = ['iso_code' => 'LA', 'name' => 'Laos', 'country_code' => '856'];
        $countries[] = ['iso_code' => 'LV', 'name' => 'Latvia', 'country_code' => '371'];
        $countries[] = ['iso_code' => 'LB', 'name' => 'Lebanon', 'country_code' => '961'];
        $countries[] = ['iso_code' => 'LS', 'name' => 'Lesotho', 'country_code' => '266'];
        $countries[] = ['iso_code' => 'LR', 'name' => 'Liberia', 'country_code' => '231'];
        $countries[] = ['iso_code' => 'LY', 'name' => 'Libya', 'country_code' => '218'];
        $countries[] = ['iso_code' => 'LI', 'name' => 'Liechtenstein', 'country_code' => '423'];
        $countries[] = ['iso_code' => 'LT', 'name' => 'Lithuania', 'country_code' => '370'];
        $countries[] = ['iso_code' => 'LU', 'name' => 'Luxembourg', 'country_code' => '352'];
        $countries[] = ['iso_code' => 'MO', 'name' => 'Macau', 'country_code' => '853'];
        $countries[] = ['iso_code' => 'MK', 'name' => 'Macedonia', 'country_code' => '389'];
        $countries[] = ['iso_code' => 'MG', 'name' => 'Madagascar', 'country_code' => '261'];
        $countries[] = ['iso_code' => 'MW', 'name' => 'Malawi', 'country_code' => '265'];
        $countries[] = ['iso_code' => 'MY', 'name' => 'Malaysia', 'country_code' => '60'];
        $countries[] = ['iso_code' => 'MV', 'name' => 'Maldives', 'country_code' => '960'];
        $countries[] = ['iso_code' => 'ML', 'name' => 'Mali', 'country_code' => '223'];
        $countries[] = ['iso_code' => 'MT', 'name' => 'Malta', 'country_code' => '356'];
        $countries[] = ['iso_code' => 'MH', 'name' => 'Marshall Islands', 'country_code' => '692'];
        $countries[] = ['iso_code' => 'MQ', 'name' => 'Martinique', 'country_code' => '596'];
        $countries[] = ['iso_code' => 'MR', 'name' => 'Mauritania', 'country_code' => '222'];
        $countries[] = ['iso_code' => 'MU', 'name' => 'Mauritius', 'country_code' => '230'];
        $countries[] = ['iso_code' => 'YT', 'name' => 'Mayotte', 'country_code' => '262'];
        $countries[] = ['iso_code' => 'MX', 'name' => 'Mexico', 'country_code' => '52'];
        $countries[] = ['iso_code' => 'MD', 'name' => 'Moldova', 'country_code' => '373'];
        $countries[] = ['iso_code' => 'MC', 'name' => 'Monaco', 'country_code' => '377'];
        $countries[] = ['iso_code' => 'MN', 'name' => 'Mongolia', 'country_code' => '976'];
        $countries[] = ['iso_code' => 'ME', 'name' => 'Montenegro', 'country_code' => '382'];
        $countries[] = ['iso_code' => 'MS', 'name' => 'Montserrat', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'MA', 'name' => 'Morocco', 'country_code' => '212'];
        $countries[] = ['iso_code' => 'MZ', 'name' => 'Mozambique', 'country_code' => '258'];
        $countries[] = ['iso_code' => 'NA', 'name' => 'Namibia', 'country_code' => '264'];
        $countries[] = ['iso_code' => 'NR', 'name' => 'Nauru', 'country_code' => '674'];
        $countries[] = ['iso_code' => 'NP', 'name' => 'Nepal', 'country_code' => '977'];
        $countries[] = ['iso_code' => 'NL', 'name' => 'Netherlands', 'country_code' => '31'];
        $countries[] = ['iso_code' => 'AN', 'name' => 'Netherlands Antilles', 'country_code' => '599'];
        $countries[] = ['iso_code' => 'NC', 'name' => 'New Caledonia', 'country_code' => '687'];
        $countries[] = ['iso_code' => 'NZ', 'name' => 'New Zealand', 'country_code' => '64'];
        $countries[] = ['iso_code' => 'NI', 'name' => 'Nicaragua', 'country_code' => '505'];
        $countries[] = ['iso_code' => 'NE', 'name' => 'Niger', 'country_code' => '227'];
        $countries[] = ['iso_code' => 'NG', 'name' => 'Nigeria', 'country_code' => '234'];
        $countries[] = ['iso_code' => 'NU', 'name' => 'Niue', 'country_code' => '683'];
        $countries[] = ['iso_code' => 'NF', 'name' => 'Norfolk Island', 'country_code' => '672'];
        $countries[] = ['iso_code' => 'KP', 'name' => 'North Korea', 'country_code' => '850'];
        $countries[] = ['iso_code' => 'MP', 'name' => 'Northern Mariana Islands', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'NO', 'name' => 'Norway', 'country_code' => '47'];
        $countries[] = ['iso_code' => 'OM', 'name' => 'Oman', 'country_code' => '968'];
        $countries[] = ['iso_code' => 'PK', 'name' => 'Pakistan', 'country_code' => '92'];
        $countries[] = ['iso_code' => 'PW', 'name' => 'Palau', 'country_code' => '680'];
        $countries[] = ['iso_code' => 'PS', 'name' => 'Palestine', 'country_code' => '970'];
        $countries[] = ['iso_code' => 'PA', 'name' => 'Panama', 'country_code' => '507'];
        $countries[] = ['iso_code' => 'PG', 'name' => 'Papua New Guinea', 'country_code' => '675'];
        $countries[] = ['iso_code' => 'PY', 'name' => 'Paraguay', 'country_code' => '595'];
        $countries[] = ['iso_code' => 'PE', 'name' => 'Peru', 'country_code' => '51'];
        $countries[] = ['iso_code' => 'PH', 'name' => 'Philippines', 'country_code' => '63'];
        $countries[] = ['iso_code' => 'PL', 'name' => 'Poland', 'country_code' => '48'];
        $countries[] = ['iso_code' => 'PT', 'name' => 'Portugal', 'country_code' => '351'];
        $countries[] = ['iso_code' => 'PR', 'name' => 'Puerto Rico', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'QA', 'name' => 'Qatar', 'country_code' => '974'];
        $countries[] = ['iso_code' => 'CG', 'name' => 'Republic of the Congo', 'country_code' => '242'];
        $countries[] = ['iso_code' => 'RE', 'name' => 'Réunion', 'country_code' => '262'];
        $countries[] = ['iso_code' => 'RO', 'name' => 'Romania', 'country_code' => '40'];
        $countries[] = ['iso_code' => 'RU', 'name' => 'Russia', 'country_code' => '7'];
        $countries[] = ['iso_code' => 'RW', 'name' => 'Rwanda', 'country_code' => '250'];
        $countries[] = ['iso_code' => 'BL', 'name' => 'Saint Barthélemy', 'country_code' => '590'];
        $countries[] = ['iso_code' => 'SH', 'name' => 'Saint Helena', 'country_code' => '290'];
        $countries[] = ['iso_code' => 'KN', 'name' => 'Saint Kitts and Nevis', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'MF', 'name' => 'Saint Martin', 'country_code' => '590'];
        $countries[] = ['iso_code' => 'PM', 'name' => 'Saint Pierre and Miquelon', 'country_code' => '508'];
        $countries[] = ['iso_code' => 'VC', 'name' => 'Saint Vincent and the Grenadines', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'WS', 'name' => 'Samoa', 'country_code' => '685'];
        $countries[] = ['iso_code' => 'SM', 'name' => 'San Marino', 'country_code' => '378'];
        $countries[] = ['iso_code' => 'ST', 'name' => 'São Tomé and Príncipe', 'country_code' => '239'];
        $countries[] = ['iso_code' => 'SA', 'name' => 'Saudi Arabia', 'country_code' => '966'];
        $countries[] = ['iso_code' => 'SN', 'name' => 'Senegal', 'country_code' => '221'];
        $countries[] = ['iso_code' => 'RS', 'name' => 'Serbia', 'country_code' => '381'];
        $countries[] = ['iso_code' => 'SC', 'name' => 'Seychelles', 'country_code' => '248'];
        $countries[] = ['iso_code' => 'SL', 'name' => 'Sierra Leone', 'country_code' => '232'];
        $countries[] = ['iso_code' => 'SG', 'name' => 'Singapore', 'country_code' => '65'];
        $countries[] = ['iso_code' => 'SK', 'name' => 'Slovakia', 'country_code' => '421'];
        $countries[] = ['iso_code' => 'SI', 'name' => 'Slovenia', 'country_code' => '386'];
        $countries[] = ['iso_code' => 'SB', 'name' => 'Solomon Islands', 'country_code' => '677'];
        $countries[] = ['iso_code' => 'SO', 'name' => 'Somalia', 'country_code' => '252'];
        $countries[] = ['iso_code' => 'ZA', 'name' => 'South Africa', 'country_code' => '27'];
        $countries[] = ['iso_code' => 'KR', 'name' => 'South Korea', 'country_code' => '82'];
        $countries[] = ['iso_code' => 'ES', 'name' => 'Spain', 'country_code' => '34'];
        $countries[] = ['iso_code' => 'LK', 'name' => 'Sri Lanka', 'country_code' => '94'];
        $countries[] = ['iso_code' => 'LC', 'name' => 'St. Lucia', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'SD', 'name' => 'Sudan', 'country_code' => '249'];
        $countries[] = ['iso_code' => 'SR', 'name' => 'Suriname', 'country_code' => '597'];
        $countries[] = ['iso_code' => 'SZ', 'name' => 'Swaziland', 'country_code' => '268'];
        $countries[] = ['iso_code' => 'SE', 'name' => 'Sweden', 'country_code' => '46'];
        $countries[] = ['iso_code' => 'CH', 'name' => 'Switzerland', 'country_code' => '41'];
        $countries[] = ['iso_code' => 'SY', 'name' => 'Syria', 'country_code' => '963'];
        $countries[] = ['iso_code' => 'TW', 'name' => 'Taiwan', 'country_code' => '886'];
        $countries[] = ['iso_code' => 'TJ', 'name' => 'Tajikistan', 'country_code' => '992'];
        $countries[] = ['iso_code' => 'TZ', 'name' => 'Tanzania', 'country_code' => '255'];
        $countries[] = ['iso_code' => 'TH', 'name' => 'Thailand', 'country_code' => '66'];
        $countries[] = ['iso_code' => 'BS', 'name' => 'The Bahamas', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'GM', 'name' => 'The Gambia', 'country_code' => '220'];
        $countries[] = ['iso_code' => 'TL', 'name' => 'Timor-Leste', 'country_code' => '670'];
        $countries[] = ['iso_code' => 'TG', 'name' => 'Togo', 'country_code' => '228'];
        $countries[] = ['iso_code' => 'TK', 'name' => 'Tokelau', 'country_code' => '690'];
        $countries[] = ['iso_code' => 'TO', 'name' => 'Tonga', 'country_code' => '676'];
        $countries[] = ['iso_code' => 'TT', 'name' => 'Trinidad and Tobago', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'TN', 'name' => 'Tunisia', 'country_code' => '216'];
        $countries[] = ['iso_code' => 'TR', 'name' => 'Turkey', 'country_code' => '90'];
        $countries[] = ['iso_code' => 'TM', 'name' => 'Turkmenistan', 'country_code' => '993'];
        $countries[] = ['iso_code' => 'TC', 'name' => 'Turks and Caicos Islands', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'TV', 'name' => 'Tuvalu', 'country_code' => '688'];
        $countries[] = ['iso_code' => 'UG', 'name' => 'Uganda', 'country_code' => '256'];
        $countries[] = ['iso_code' => 'UA', 'name' => 'Ukraine', 'country_code' => '380'];
        $countries[] = ['iso_code' => 'AE', 'name' => 'United Arab Emirates', 'country_code' => '971'];
        $countries[] = ['iso_code' => 'GB', 'name' => 'United Kingdom', 'country_code' => '44'];
        $countries[] = ['iso_code' => 'US', 'name' => 'United States', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'UY', 'name' => 'Uruguay', 'country_code' => '598'];
        $countries[] = ['iso_code' => 'VI', 'name' => 'US Virgin Islands', 'country_code' => '1'];
        $countries[] = ['iso_code' => 'UZ', 'name' => 'Uzbekistan', 'country_code' => '998'];
        $countries[] = ['iso_code' => 'VU', 'name' => 'Vanuatu', 'country_code' => '678'];
        $countries[] = ['iso_code' => 'VA', 'name' => 'Vatican City', 'country_code' => '39'];
        $countries[] = ['iso_code' => 'VE', 'name' => 'Venezuela', 'country_code' => '58'];
        $countries[] = ['iso_code' => 'VN', 'name' => 'Vietnam', 'country_code' => '84'];
        $countries[] = ['iso_code' => 'WF', 'name' => 'Wallis and Futuna', 'country_code' => '681'];
        $countries[] = ['iso_code' => 'YE', 'name' => 'Yemen', 'country_code' => '967'];
        $countries[] = ['iso_code' => 'ZM', 'name' => 'Zambia', 'country_code' => '260'];
        $countries[] = ['iso_code' => 'ZW', 'name' => 'Zimbabwe', 'country_code' => '263'];

        foreach ($countries as $country) {
            Country::create([
                    'name'         => $country['name'],
                    'iso_code'     => $country['iso_code'],
                    'country_code' => $country['country_code'],
                    'status'       => true,
            ]);
        }

    }

}
