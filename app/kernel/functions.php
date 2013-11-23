<?php

/*
  speedcode-box - Laurent ASENSIO - laurent@mindescape.eu
  Copyright (C) 2011  Laurent ASENSIO

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * app/kernel/functions.php
 * 
 * speedcode-box global functions
 * 
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @version         1.0
 * @category        Helpers
 * @copyright       Copyright (c) 2011
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */

/**
 * Autoloads classes
 * @param String $class_name 
 */
function __autoload($class_name) {
    $class_file = ROOT . 'app/classes/' . strtolower($class_name) . '.class.php';
    if (file_exists($class_file)) {
        require_once($class_file);
    }
}

/**
 * Clean and secure a string to avoid XSS and SQL injections
 * @param String $data The string to clean.
 * @param boolean $html If true, the data will be HTML encoded, otherwise all tags will be stripped.
 * @return string The cleaned and securized data
 */
function str_clean($data, $html = false) {
    $cleaned_data = '';
    if (!is_array($data) && is_string($data)) {
        if (!get_magic_quotes_gpc()) {
            $cleaned_data = addslashes($data);
        }
        if (!$html) {
            $cleaned_data = trim(strip_tags(html_entity_decode($cleaned_data, ENT_QUOTES, 'UTF-8')));
        }
    }

    return $cleaned_data;
}

/**
 * Uses str_clean to clean mixed $data
 * @param String|Array $data The data to clean. [reference]
 * @param boolean $html If true, the data will be HTML encoded, otherwise not.
 * @return string|array The cleaned and secured data
 */
function sanitize($data, $html = false) {
    $sanitized_data = null;

    if (is_array($data)) {
        $sanitized_data = array();
        foreach ($data as $key => $value) {
            $sanitized_data[$key] = str_clean($value, $html);
        }
    } else {
        $sanitized_data = str_clean($data, $html);
    }

    return $sanitized_data;
}

/**
 * Compare two arrays
 * @param array $array1 Array to check
 * @param array $array2 Reference array
 * @return boolean True if $array1 is in $array2, false otherwise
 */
function array_in_array($array1, $array2) {
    // Returns true if $array1 is in $array2, false otherwise
    $same = false;
    if (is_array($array1) && is_array($array2) && count($array1) == count($array2)) {
        $same = true;
        foreach ($array1 as $key => $value) {
            if (!isset($array2[$key]) || $array2[$key] !== $value) {
                $same = false;
                break;
            }
        }
    }
    return $same;
}

/**
 * Convert SQL date format (Y-m-d) to (d/m/Y) (french)
 * @param string $date_sql Date in SQL format
 * @return string|boolean A date in (dY-m-d) format, false if format is incorrect
 */
function date_from_sql($date_sql) {
    $tmp_date = explode('-', $date_sql);
    if (count($tmp_date) == 3) {
        $out = $tmp_date[2] . '/' . $tmp_date[1] . '/' . $tmp_date[0];
    } else {
        $out = FALSE;
    }
    return $out;
}

/**
 * Convert (d/m/Y) date format to SQL format (Y-m-d)
 * @param string $date Date in (d/m/Y) format
 * @return string|boolean A date in SQL format, false if format is incorrect
 */
function date_to_sql($date) {
    $tmpDate = explode('/', $date);
    if (count($tmpDate) == 3) {
        $out = $tmpDate[2] . '-' . $tmpDate[1] . '-' . $tmpDate[0];
    } else {
        $out = FALSE;
    }
    return $out;
}

/**
 * Convert SQL datetime format (Y-m-d H:i:s) to human readable date
 * @param string $date Date in SQL format
 * @return string The date in human readable format : Monday 24 May 2010, 15:43
 * @uses $_lang The global Language object
 */
function date_to_lang($date) {
    global $_lang;

    $days = $_lang->get('days');
    $months = $_lang->get('months');

    $tmp = explode(' ', $date);
    $date = explode('-', $tmp[0]);
    $hour = '';
    if (count($tmp) > 1) {
        $time = explode(':', $tmp[1]);
        $timestamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
        $hour = date('G', $timestamp);
        $minutes = date('i', $timestamp);
        $hour = ', ' . $hour . ':' . $minutes;
    } else {
        $timestamp = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
    }
    $day = $days[date('l', $timestamp)];
    $the = date('j', $timestamp);
    $month = $months[date('F', $timestamp)];
    $year = date('Y', $timestamp);

    return $day . ' ' . $the . ' ' . $month . ' ' . $year . $hour;
}

/**
 * Check a string with preg_match for email correct format
 * @param string $email The email address to check
 * @return boolean True if email is correct, false if incorrect
 */
function is_mail($email) {
    $nonascii = "\x80-\xff"; //Les caractères Non-ASCII ne sont pas permis

    $nqtext = "[^\\\\$nonascii\015\012\"]";
    $qchar = "\\\\[^$nonascii]";

    $normuser = '[a-zA-Z0-9][a-zA-Z0-9_.-]*';
    $quotedstring = "\"(?:$nqtext|$qchar)+\"";
    $user_part = "(?:$normuser|$quotedstring)";

    $dom_mainpart = '[a-zA-Z0-9][a-zA-Z0-9._-]*\\.';
    $dom_subpart = '(?:[a-zA-Z0-9][a-zA-Z0-9._-]*\\.)*';
    $dom_tldpart = '[a-zA-Z]{2,5}';
    $domain_part = "$dom_subpart$dom_mainpart$dom_tldpart";

    $regex = "$user_part\@$domain_part";

    return preg_match("/^$regex$/", $email);
}

/**
 * Return a long unique key
 * @return string A unique ID wich is 22 characters long
 */
function get_key() {
    $key = str_replace('.', '', uniqid('', true));
    return $key;
}

/**
 * Cleans a string considering it is a filename
 * The function keep the dots and converts all other special characters
 * @param String $filename
 * @return String cleaned filename 
 */
function clean_filename($filename) {
    return utf8_encode(strtr(utf8_decode(strtolower($filename)), utf8_decode('Þßàáâãäåæçèéêë€ìíîïðñòóôõöøœùúûµüýýþÿŔŕ ?!;:^¨$£¤*%§,"\'~&/#²`|()[]}=+°@<>'), 'bsaaaaaaaceeeeeiiiidnooooooouuuuuyybyRr'));
}

/**
 * Return a random string
 * Useful for passwords, or complex keys
 * @param integer $size The desired length of the random string
 * @return string A random string composed by numbers, lowercase and 
 * uppercase letters, and special characters
 */
function str_rand($size=10) {
    $pattern = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-.*:;,=+()[]{}&#%$';
    $length = strlen($pattern) - 1;
    if (is_int($size)) {
        $rand_str = '';
        for ($i = 0; $i < $size; $i++) {
            $n = rand(0, $length);
            $rand_str .= $pattern{$n};
        }
        return $rand_str;
    }
}

/**
 * Convert an array to string human readable
 * For debug purpose
 * @param array $array The array to convert
 * @return string The string representation of the array
 */
function array_to_string($array) {
    if (is_array($array)) {
        $out = '';
        foreach ($array as $key => $value) {
            $out .= $key . ' : ' . array_to_string($value) . '<br />';
        }
    } else {
        $out = $array;
    }
    return $out;
}

/**
 * Recursive removal of slashes in array or string
 * @param string|array $mixed The value to clean
 * @return string|array The value without slashes
 */
function stripslashes_r($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = stripslashes_r($value);
        }
        return $mixed;
    } else {
        return stripslashes($mixed);
    }
}

/**
 * Recursive utf8_decode
 * @param String|Array $mixed
 * @return String|Array The utf8 decoded string
 */
function utf8_decode_r($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8_decode_r($value);
        }
        return $mixed;
    } else {
        return utf8_decode($mixed);
    }
}

/**
 * Make a redirection and exit the program
 * @param string $uri The uri to redirect to
 * @param int $error_code The error code to send to the browser
 */
function redirect($uri, $error_code = 302) {
    header('Location: ' . $uri, $error_code);
    exit;
}

/**
 * Forces the download of a file
 * @param string $file The complete path to the file to download
 * @return void
 */
function download($file, $filename) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . urlencode(basename($filename)));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Cache-Control: private', false);
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}

/**
 * Force the download of a string
 * @param string $string The string content to download
 * @return void
 */
function download_str($string, $filename) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . urlencode(basename($filename)));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Cache-Control: private', false);
    header('Content-Length: ' . strlen($string));
    echo $string;
    exit;
}

/**
 * Convert an array to a CSV formatted line
 * @param array $fields The array to convert
 * @param string $delimiter The delimiter to print between fields
 * @param string $enclosure The escape character
 * @return string A CSV formatted string
 */
function get_csv_line($fields = array(), $delimiter = ';', $enclosure = '"') {
    if ($delimiter != NULL) {
        if (strlen($delimiter) < 1) {
            trigger_error('delimiter must be a character', E_USER_WARNING);
            return false;
        } elseif (strlen($delimiter) > 1) {
            trigger_error('delimiter must be a single character', E_USER_NOTICE);
        }

        /* use first character from string */
        $delimiter = $delimiter[0];
    }

    if ($enclosure != NULL) {
        if (strlen($enclosure) < 1) {
            trigger_error('enclosure must be a character', E_USER_WARNING);
            return false;
        } elseif (strlen($enclosure) > 1) {
            trigger_error('enclosure must be a single character', E_USER_NOTICE);
        }

        /* use first character from string */
        $enclosure = $enclosure[0];
    }

    $i = 0;
    $csvline = '';
    $escape_char = '\\';
    $field_cnt = count($fields);
    $enc_is_quote = in_array($enclosure, array('"', "'"));
    reset($fields);

    foreach ($fields as $field) {

        /* enclose a field that contains a delimiter, an enclosure character, or a newline */
        if (is_string($field) && (
                strpos($field, $delimiter) !== false ||
                strpos($field, $enclosure) !== false ||
                strpos($field, $escape_char) !== false ||
                strpos($field, "\n") !== false ||
                strpos($field, "\r") !== false ||
                strpos($field, "\t") !== false ||
                strpos($field, ' ') !== false )) {

            $field_len = strlen($field);
            $escaped = 0;

            $csvline .= $enclosure;
            for ($ch = 0; $ch < $field_len; $ch++) {
                if ($field[$ch] == $escape_char && $field[$ch + 1] == $enclosure && $enc_is_quote) {
                    continue;
                } elseif ($field[$ch] == $escape_char) {
                    $escaped = 1;
                } elseif (!$escaped && $field[$ch] == $enclosure) {
                    $csvline .= $enclosure;
                } else {
                    $escaped = 0;
                }
                $csvline .= $field[$ch];
            }
            $csvline .= $enclosure;
        } else {
            $csvline .= $field;
        }

        if ($i++ != $field_cnt) {
            $csvline .= $delimiter;
        }
    }

    $csvline .= "\n";

    return $csvline;
}

/**
 * Converts an octet size into the appropriate unit
 * 
 * @param integer $octet
 * @return String The human-readable size 
 */
function get_human_size($octet) {
    $unite = array(' octets', ' Ko', ' Mo', ' Go');
    // octet
    if ($octet < 1000) {
        return $octet . $unite[0];
    } else {
        // ko
        if ($octet < 1000000) {
            $ko = round($octet / 1024, 2);
            return $ko . $unite[1];
        }
        // Mo ou Go 
        else {
            // Mo 
            if ($octet < 1000000000) {
                $mo = round($octet / (1024 * 1024), 2);
                return $mo . $unite[2];
            }
            // Go 
            else {
                $go = round($octet / (1024 * 1024 * 1024), 2);
                return $go . $unite[3];
            }
        }
    }
}

/**
 * Used to sort an array (in combination to u*sort()
 * Puts arrays prior to values
 * 
 * @param type $a
 * @param type $b
 * @return boolean
 */
function ar_compare($a, $b) {
    if ($a === $b) {
        return 0;
    }
    if (is_array($a) && is_array($b)) {
        return 0;
    }
    if (is_array($a)) {
        return -1;
    }
    return 1;
}

function parse_uri($uri, &$array) {
    $exploded_uri = explode('/', $uri);
    if (is_array($exploded_uri) && count($exploded_uri) > 0 && $exploded_uri[0] != '/') {
        if ($exploded_uri[0] == 'admin') {
            array_shift($exploded_uri);
            $array['admin'] = true;
        }
        $array['module'] = $exploded_uri[0];
        if (isset($exploded_uri[1])) {
            $array['function'] = $exploded_uri[1];
            if (count($exploded_uri) > 2) {
                array_splice($exploded_uri, 0, 2);
                foreach ($exploded_uri as $uri_param) {
                    $array['parameters'] = $uri_param;
                }
            }
        }
    }
}

function image_resize($img, $w, $h, $newfilename) {

    //Check if GD extension is loaded
    if (!extension_loaded('gd') && !extension_loaded('gd2')) {
        trigger_error("GD is not loaded", E_USER_WARNING);
        return false;
    }

    //Get Image size info
    $imgInfo = getimagesize($img);
    switch ($imgInfo[2]) {
        case 1: $im = imagecreatefromgif($img); break;
        case 2: $im = imagecreatefromjpeg($img);  break;
        case 3: $im = imagecreatefrompng($img); break;
        default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
    }

    //If image dimension is smaller, do not resize
    if ($imgInfo[0] <= $w && $imgInfo[1] <= $h) {
        $nHeight = $imgInfo[1];
        $nWidth = $imgInfo[0];
    }
    else{
        //yeah, resize it, but keep it proportional
        if ($w/$imgInfo[0] < $h/$imgInfo[1]) {
            $nWidth = $w;
            $nHeight = $imgInfo[1]*($w/$imgInfo[0]);
        }
        else{
            $nWidth = $imgInfo[0]*($h/$imgInfo[1]);
            $nHeight = $h;
        }
    }
    $nWidth = round($nWidth);
    $nHeight = round($nHeight);

    $newImg = imagecreatetruecolor($nWidth, $nHeight);

    /* Check if this image is PNG or GIF, then set if Transparent*/  
    if(($imgInfo[2] == 1) OR ($imgInfo[2]==3)){
        imagealphablending($newImg, false);
        imagesavealpha($newImg,true);
        $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
        imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
    }
    imagecopyresampled($newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);

    //Generate the file, and rename it to $newfilename
    switch ($imgInfo[2]) {
        case 1: imagegif($newImg,$newfilename); break;
        case 2: imagejpeg($newImg,$newfilename);  break;
        case 3: imagepng($newImg,$newfilename); break;
        default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
    }

    return $newfilename;
}

/**
 * If PHP < 5.3, declares the str_getcsv function
 */
if (!function_exists('str_getcsv')) {

    function str_getcsv($input, $delimiter=',', $enclosure='"', $escape=null, $eol=null) {
        $temp = fopen("php://memory", "rw");
        fwrite($temp, $input);
        fseek($temp, 0);
        $r = array();
        while (($data = fgetcsv($temp, 4096, $delimiter, $enclosure)) !== false) {
            $r[] = $data;
        }
        fclose($temp);
        return $r;
    }

}
?>