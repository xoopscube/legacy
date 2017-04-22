<?php
// $Id: xoopslists.php,v 1.1 2007/05/15 02:34:21 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: The XOOPS Project                                                 //
// URL: http://www.xoops.org/                                                //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //


if (!defined("XOOPS_LISTS_INCLUDED")) {
    define("XOOPS_LISTS_INCLUDED", 1);
    class xoopslists
    {
        public static function getTimeZoneList()
        {
            $root =& XCube_Root::getSingleton();
            if ($root->mLanguageManager !== null && !defined('_TZ_GMT0')) {
                $root->mLanguageManager->loadPageTypeMessageCatalog('timezone');
            }
            $time_zone_list = array(
                "-12" => _TZ_GMTM12,
                "-11" => _TZ_GMTM11,
                "-10" => _TZ_GMTM10,
                "-9" => _TZ_GMTM9,
                "-8" => _TZ_GMTM8,
                "-7" => _TZ_GMTM7,
                "-6" => _TZ_GMTM6,
                "-5" => _TZ_GMTM5,
                "-4.5" => _TZ_GMTM45,
                "-4" => _TZ_GMTM4,
                "-3.5" => _TZ_GMTM35,
                "-3" => _TZ_GMTM3,
                "-2" => _TZ_GMTM2,
                "-1" => _TZ_GMTM1,
                "0" => _TZ_GMT0,
                "1" => _TZ_GMTP1,
                "2" => _TZ_GMTP2,
                "3" => _TZ_GMTP3,
                "3.5" => _TZ_GMTP35,
                "4" => _TZ_GMTP4,
                "4.5" => _TZ_GMTP45,
                "5" => _TZ_GMTP5,
                "5.5" => _TZ_GMTP55,
                "5.75" => _TZ_GMTP575,
                "6" => _TZ_GMTP6,
                "6.5" => _TZ_GMTP65,
                "7" => _TZ_GMTP7,
                "8" => _TZ_GMTP8,
                "9" => _TZ_GMTP9,
                "9.5" => _TZ_GMTP95,
                "10" => _TZ_GMTP10,
                "11" => _TZ_GMTP11,
                "12" => _TZ_GMTP12,
                "13" => _TZ_GMTP13);
            return $time_zone_list;
        }

        /*
         * gets list of themes folder from themes directory
         */
        public static function &getThemesList()
        {
            $ret =& XoopsLists::getDirListAsArray(XOOPS_THEME_PATH.'/');
            return $ret;
        }

        /*
         * gets a list of module folders from the modules directory
         */
        public static function &getModulesList()
        {
            $ret =& XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH."/modules/");
            return $ret;
        }

        /*
         * gets list of name of directories inside a directory
         */
        public static function &getDirListAsArray($dirname)
        {
            $dirlist = array();
            if (is_dir($dirname) && $handle = opendir($dirname)) {
                while (false !== ($file = readdir($handle))) {
                    if (!preg_match("/^\..*$/", $file)) {
                        if (strtolower($file) != 'cvs' && is_dir($dirname.$file)) {
                            $dirlist[$file]=$file;
                        }
                    }
                }
                closedir($handle);
                asort($dirlist);
                reset($dirlist);
            }
            return $dirlist;
        }

        /*
         *  gets list of all files in a directory
         */
        public static function &getFileListAsArray($dirname, $prefix="")
        {
            $filelist = array();
            if (substr($dirname, -1) == '/') {
                $dirname = substr($dirname, 0, -1);
            }
            if (is_dir($dirname) && $handle = opendir($dirname)) {
                while (false !== ($file = readdir($handle))) {
                    if (!preg_match("/^[\.]{1,2}$/", $file) && is_file($dirname.'/'.$file)) {
                        $file = $prefix.$file;
                        $filelist[$file]=$file;
                    }
                }
                closedir($handle);
                asort($filelist);
                reset($filelist);
            }
            return $filelist;
        }

        /*
         *  gets list of image file names in a directory
         */
        public static function &getImgListAsArray($dirname, $prefix="")
        {
            $filelist = array();
            if ($handle = opendir($dirname)) {
                while (false !== ($file = readdir($handle))) {
                    if (!preg_match("/^[\.]{1,2}$/", $file) && preg_match("/(\.gif|\.jpg|\.png)$/i", $file)) {
                        $file = $prefix.$file;
                        $filelist[$file]=$file;
                    }
                }
                closedir($handle);
                asort($filelist);
                reset($filelist);
            }
            return $filelist;
        }

        /*
         *  gets list of html file names in a certain directory
        */
        public static function &getHtmlListAsArray($dirname, $prefix="")
        {
            $filelist = array();
            if ($handle = opendir($dirname)) {
                while (false !== ($file = readdir($handle))) {
                    if ((!preg_match("/^[\.]{1,2}$/", $file) && preg_match("/(\.htm|\.html|\.xhtml)$/i", $file) && !is_dir($file))) {
                        if (strtolower($file) != 'cvs' && !is_dir($file)) {
                            $file = $prefix.$file;
                            $filelist[$file] = $prefix.$file;
                        }
                    }
                }
                closedir($handle);
                asort($filelist);
                reset($filelist);
            }
            return $filelist;
        }

        /*
         *  gets list of avatar file names in a certain directory
         *  if directory is not specified, default directory will be searched
         */
        public static function &getAvatarsList($avatar_dir="")
        {
            $avatars = array();
            if ($avatar_dir != "") {
                $avatars =& XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH."/images/avatar/".$avatar_dir."/", $avatar_dir."/");
            } else {
                $avatars =& XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH."/images/avatar/");
            }
            return $avatars;
        }

        /*
         *  gets list of all avatar image files inside default avatars directory
         */
        public static function &getAllAvatarsList()
        {
            $avatars = array();
            $dirlist = array();
            $dirlist =& XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH."/images/avatar/");
            if (count($dirlist) > 0) {
                foreach ($dirlist as $dir) {
                    $avatars[$dir] =& XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH."/images/avatar/".$dir."/", $dir."/");
                }
                return $avatars;
            }
            $ret = false;
            return $ret;
        }

        /*
        *  gets list of subject icon image file names in a certain directory
        *  if directory is not specified, default directory will be searched
        */
        public static function &getSubjectsList($sub_dir="")
        {
            $subjects = array();
            if ($sub_dir != "") {
                $subjects =& XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH."/images/subject/".$sub_dir, $sub_dir."/");
            } else {
                $subjects =& XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH."/images/subject/");
            }
            return $subjects;
        }

        /*
         * gets list of language folders inside default language directory
         */
        public static function &getLangList()
        {
            $lang_list = array();
            $lang_list =& XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH."/language/");
            return $lang_list;
        }

        public static function &getCountryList()
        {
            $country_list = array(
                ""   => "-",
                "AD" => "Andorra",
                "AE" => "United Arab Emirates",
                "AF" => "Afghanistan",
                "AG" => "Antigua and Barbuda",
                "AI" => "Anguilla",
                "AL" => "Albania",
                "AM" => "Armenia",
                "AN" => "Netherlands Antilles",
                "AO" => "Angola",
                "AQ" => "Antarctica",
                "AR" => "Argentina",
                "AS" => "American Samoa",
                "AT" => "Austria",
                "AU" => "Australia",
                "AW" => "Aruba",
                "AZ" => "Azerbaijan",
                "BA" => "Bosnia and Herzegovina",
                "BB" => "Barbados",
                "BD" => "Bangladesh",
                "BE" => "Belgium",
                "BF" => "Burkina Faso",
                "BG" => "Bulgaria",
                "BH" => "Bahrain",
                "BI" => "Burundi",
                "BJ" => "Benin",
                "BM" => "Bermuda",
                "BN" => "Brunei Darussalam",
                "BO" => "Bolivia",
                "BR" => "Brazil",
                "BS" => "Bahamas",
                "BT" => "Bhutan",
                "BV" => "Bouvet Island",
                "BW" => "Botswana",
                "BY" => "Belarus",
                "BZ" => "Belize",
                "CA" => "Canada",
                "CC" => "Cocos (Keeling) Islands",
                "CF" => "Central African Republic",
                "CG" => "Congo",
                "CH" => "Switzerland",
                "CI" => "Cote D'Ivoire (Ivory Coast)",
                "CK" => "Cook Islands",
                "CL" => "Chile",
                "CM" => "Cameroon",
                "CN" => "China",
                "CO" => "Colombia",
                "CR" => "Costa Rica",
                "CS" => "Czechoslovakia (former)",
                "CU" => "Cuba",
                "CV" => "Cape Verde",
                "CX" => "Christmas Island",
                "CY" => "Cyprus",
                "CZ" => "Czech Republic",
                "DE" => "Germany",
                "DJ" => "Djibouti",
                "DK" => "Denmark",
                "DM" => "Dominica",
                "DO" => "Dominican Republic",
                "DZ" => "Algeria",
                "EC" => "Ecuador",
                "EE" => "Estonia",
                "EG" => "Egypt",
                "EH" => "Western Sahara",
                "ER" => "Eritrea",
                "ES" => "Spain",
                "ET" => "Ethiopia",
                "FI" => "Finland",
                "FJ" => "Fiji",
                "FK" => "Falkland Islands (Malvinas)",
                "FM" => "Micronesia",
                "FO" => "Faroe Islands",
                "FR" => "France",
                "FX" => "France, Metropolitan",
                "GA" => "Gabon",
                "GB" => "Great Britain (UK)",
                "GD" => "Grenada",
                "GE" => "Georgia",
                "GF" => "French Guiana",
                "GH" => "Ghana",
                "GI" => "Gibraltar",
                "GL" => "Greenland",
                "GM" => "Gambia",
                "GN" => "Guinea",
                "GP" => "Guadeloupe",
                "GQ" => "Equatorial Guinea",
                "GR" => "Greece",
                "GS" => "S. Georgia and S. Sandwich Isls.",
                "GT" => "Guatemala",
                "GU" => "Guam",
                "GW" => "Guinea-Bissau",
                "GY" => "Guyana",
                "HK" => "Hong Kong",
                "HM" => "Heard and McDonald Islands",
                "HN" => "Honduras",
                "HR" => "Croatia (Hrvatska)",
                "HT" => "Haiti",
                "HU" => "Hungary",
                "ID" => "Indonesia",
                "IE" => "Ireland",
                "IL" => "Israel",
                "IN" => "India",
                "IO" => "British Indian Ocean Territory",
                "IQ" => "Iraq",
                "IR" => "Iran",
                "IS" => "Iceland",
                "IT" => "Italy",
                "JM" => "Jamaica",
                "JO" => "Jordan",
                "JP" => "Japan",
                "KE" => "Kenya",
                "KG" => "Kyrgyzstan",
                "KH" => "Cambodia",
                "KI" => "Kiribati",
                "KM" => "Comoros",
                "KN" => "Saint Kitts and Nevis",
                "KP" => "Korea (North)",
                "KR" => "Korea (South)",
                "KW" => "Kuwait",
                "KY" => "Cayman Islands",
                "KZ" => "Kazakhstan",
                "LA" => "Laos",
                "LB" => "Lebanon",
                "LC" => "Saint Lucia",
                "LI" => "Liechtenstein",
                "LK" => "Sri Lanka",
                "LR" => "Liberia",
                "LS" => "Lesotho",
                "LT" => "Lithuania",
                "LU" => "Luxembourg",
                "LV" => "Latvia",
                "LY" => "Libya",
                "MA" => "Morocco",
                "MC" => "Monaco",
                "MD" => "Moldova",
                "MG" => "Madagascar",
                "MH" => "Marshall Islands",
                "MK" => "Macedonia",
                "ML" => "Mali",
                "MM" => "Myanmar",
                "MN" => "Mongolia",
                "MO" => "Macau",
                "MP" => "Northern Mariana Islands",
                "MQ" => "Martinique",
                "MR" => "Mauritania",
                "MS" => "Montserrat",
                "MT" => "Malta",
                "MU" => "Mauritius",
                "MV" => "Maldives",
                "MW" => "Malawi",
                "MX" => "Mexico",
                "MY" => "Malaysia",
                "MZ" => "Mozambique",
                "NA" => "Namibia",
                "NC" => "New Caledonia",
                "NE" => "Niger",
                "NF" => "Norfolk Island",
                "NG" => "Nigeria",
                "NI" => "Nicaragua",
                "NL" => "Netherlands",
                "NO" => "Norway",
                "NP" => "Nepal",
                "NR" => "Nauru",
                "NT" => "Neutral Zone",
                "NU" => "Niue",
                "NZ" => "New Zealand (Aotearoa)",
                "OM" => "Oman",
                "PA" => "Panama",
                "PE" => "Peru",
                "PF" => "French Polynesia",
                "PG" => "Papua New Guinea",
                "PH" => "Philippines",
                "PK" => "Pakistan",
                "PL" => "Poland",
                "PM" => "St. Pierre and Miquelon",
                "PN" => "Pitcairn",
                "PR" => "Puerto Rico",
                "PT" => "Portugal",
                "PW" => "Palau",
                "PY" => "Paraguay",
                "QA" => "Qatar",
                "RE" => "Reunion",
                "RO" => "Romania",
                "RU" => "Russian Federation",
                "RW" => "Rwanda",
                "SA" => "Saudi Arabia",
                "Sb" => "Solomon Islands",
                "SC" => "Seychelles",
                "SD" => "Sudan",
                "SE" => "Sweden",
                "SG" => "Singapore",
                "SH" => "St. Helena",
                "SI" => "Slovenia",
                "SJ" => "Svalbard and Jan Mayen Islands",
                "SK" => "Slovak Republic",
                "SL" => "Sierra Leone",
                "SM" => "San Marino",
                "SN" => "Senegal",
                "SO" => "Somalia",
                "SR" => "Suriname",
                "ST" => "Sao Tome and Principe",
                "SU" => "USSR (former)",
                "SV" => "El Salvador",
                "SY" => "Syria",
                "SZ" => "Swaziland",
                "TC" => "Turks and Caicos Islands",
                "TD" => "Chad",
                "TF" => "French Southern Territories",
                "TG" => "Togo",
                "TH" => "Thailand",
                "TJ" => "Tajikistan",
                "TK" => "Tokelau",
                "TM" => "Turkmenistan",
                "TN" => "Tunisia",
                "TO" => "Tonga",
                "TP" => "East Timor",
                "TR" => "Turkey",
                "TT" => "Trinidad and Tobago",
                "TV" => "Tuvalu",
                "TW" => "Taiwan",
                "TZ" => "Tanzania",
                "UA" => "Ukraine",
                "UG" => "Uganda",
                "UK" => "United Kingdom",
                "UM" => "US Minor Outlying Islands",
                "US" => "United States",
                "UY" => "Uruguay",
                "UZ" => "Uzbekistan",
                "VA" => "Vatican City State (Holy See)",
                "VC" => "Saint Vincent and the Grenadines",
                "VE" => "Venezuela",
                "VG" => "Virgin Islands (British)",
                "VI" => "Virgin Islands (U.S.)",
                "VN" => "Viet Nam",
                "VU" => "Vanuatu",
                "WF" => "Wallis and Futuna Islands",
                "WS" => "Samoa",
                "YE" => "Yemen",
                "YT" => "Mayotte",
                "YU" => "Yugoslavia",
                "ZA" => "South Africa",
                "ZM" => "Zambia",
                "ZR" => "Zaire",
                "ZW" => "Zimbabwe"
            );
            asort($country_list);
            reset($country_list);
            return $country_list;
        }

        public static function &getHtmlList()
        {
            $html_list = array(
                "a" => "&lt;a&gt;",
                "abbr" => "&lt;abbr&gt;",
                "acronym" => "&lt;acronym&gt;",
                "address" => "&lt;address&gt;",
                "b" => "&lt;b&gt;",
                "bdo" => "&lt;bdo&gt;",
                "big" => "&lt;big&gt;",
                "blockquote" => "&lt;blockquote&gt;",
                "caption" => "&lt;caption&gt;",
                "cite" => "&lt;cite&gt;",
                "code" => "&lt;code&gt;",
                "col" => "&lt;col&gt;",
                "colgroup" => "&lt;colgroup&gt;",
                "dd" => "&lt;dd&gt;",
                "del" => "&lt;del&gt;",
                "dfn" => "&lt;dfn&gt;",
                "div" => "&lt;div&gt;",
                "dl" => "&lt;dl&gt;",
                "dt" => "&lt;dt&gt;",
                "em" => "&lt;em&gt;",
                "font" => "&lt;font&gt;",
                "h1" => "&lt;h1&gt;",
                "h2" => "&lt;h2&gt;",
                "h3" => "&lt;h3&gt;",
                "h4" => "&lt;h4&gt;",
                "h5" => "&lt;h5&gt;",
                "h6" => "&lt;h6&gt;",
                "hr" => "&lt;hr&gt;",
                "i" => "&lt;i&gt;",
                "img" => "&lt;img&gt;",
                "ins" => "&lt;ins&gt;",
                "kbd" => "&lt;kbd&gt;",
                "li" => "&lt;li&gt;",
                "map" => "&lt;map&gt;",
                "object" => "&lt;object&gt;",
                "ol" => "&lt;ol&gt;",
                "samp" => "&lt;samp&gt;",
                "small" => "&lt;small&gt;",
                "strong" => "&lt;strong&gt;",
                "sub" => "&lt;sub&gt;",
                "sup" => "&lt;sup&gt;",
                "table" => "&lt;table&gt;",
                "tbody" => "&lt;tbody&gt;",
                "td" => "&lt;td&gt;",
                "tfoot" => "&lt;tfoot&gt;",
                "th" => "&lt;th&gt;",
                "thead" => "&lt;thead&gt;",
                "tr" => "&lt;tr&gt;",
                "tt" => "&lt;tt&gt;",
                "ul" => "&lt;ul&gt;",
                "var" => "&lt;var&gt;"
            );
            asort($html_list);
            reset($html_list);
            return $html_list;
        }

        public static function &getUserRankList()
        {
            $db =& Database::getInstance();
            $myts =& MyTextSanitizer::sGetInstance();
            $sql = "SELECT rank_id, rank_title FROM ".$db->prefix("ranks")." WHERE rank_special = 1";
            $ret = array();
            $result = $db->query($sql);
            while ($myrow = $db->fetchArray($result)) {
                $ret[$myrow['rank_id']] = $myts->makeTboxData4Show($myrow['rank_title']);
            }
            return $ret;
        }
    }
}
