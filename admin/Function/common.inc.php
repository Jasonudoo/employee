<?php
if( !defined('PROJECT_START') || !PROJECT_START) die("Access Denied");
/**
 * @copyright Copyright(2011) All Right Reserved.
 * @filesource: post.php,v$
 * @package: library
 *
 * @author Jason Williams <jasonudoo@gmail.com>
 * @version $Id: v 1.0 2011-05-20 Jason Exp $
 *
 * @abstract:
 */

/**
 * @function daddslashes
 * @return string
 */
function daddslashes($string, $force = 0) {
    !defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
    if (!MAGIC_QUOTES_GPC || $force) {
        if (is_array($string)) {
            foreach ($string as $key=>$val) {
                $string[$key] = daddslashes($val, $force);
            }
        } else {
            $string = addslashes($string);
        }
    }
    return $string;
}

function is_email($p_addr) {
    if (preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$", $p_addr))
        return true;
    else
        return false;
}

function checkUserAgentString($p_value) {
    $userAgent = array();
    $agent = $p_value;
    $products = array();

    $pattern = "([^/[:space:]]*)"."(/([^[:space:]]*))?"."([[:space:]]*\[[a-zA-Z][a-zA-Z]\])?"."[[:space:]]*"."(\\((([^()]|(\\([^()]*\\)))*)\\))?"."[[:space:]]*";

    while (strlen($agent) > 0) {
        if ($l = preg_match($pattern, $agent, $a)) {
            // product, version, comment
            array_push($products, array($a[1], $a[3], $a[6]));
            $agent = substr($agent, $l);
        } else {
            $agent = "";
        }
    }
    // Directly catch these
    foreach ($products as $product) {
        switch (strtolower($product[0])) {
            case 'firefox':
            case 'netscape':
            case 'safari':
            case 'camino':
            case 'mosaic':
            case 'galeon':
            case 'opera':
            case 'chrome':
                $userAgent[0] = $product[0];
                $userAgent[1] = $product[1];
                break;
        }
        if (count($userAgent) > 0) {
            break;
        }
    }
    if (count($userAgent) == 0) {
        // Mozilla compatible (MSIE, konqueror, etc)
        if ($products[0][0] == 'Mozilla' && !strncmp($products[0][2], 'compatible;', 11)) {
            $userAgent = array();
            if ($cl = ereg("compatible; ([^ ]*)[ /]([^;]*).*", $products[0][2], $ca)) {
                $userAgent[0] = $ca[1];
                $userAgent[1] = $ca[2];
            } else {
                $userAgent[0] = $products[0][0];
                $userAgent[1] = $products[0][1];
            }
        } else {
            $userAgent = array();
            $userAgent[0] = $products[0][0];
            $userAgent[1] = $products[0][1];
        }
    }
    // Get runing OS and version
    $oslist = array(
            // Match user agent string with operating systems
            'Windows 3.11'=>'Win16', 'Windows 95'=>'(Windows 95)|(Win95)|(Windows_95)', 'Windows 98'=>'(Windows 98)|(Win98)', 'Windows 2000'=>'(Windows NT 5.0)|(Windows 2000)', 'Windows XP'=>'(Windows NT 5.1)|(Windows XP)', 'Windows Server 2003'=>'(Windows NT 5.2)', 'Windows Vista'=>'(Windows NT 6.0)', 'Windows 7'=>'(Windows NT 7.0)', 'Windows NT 4.0'=>'(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)', 'Windows ME'=>'Windows ME', 'Open BSD'=>'OpenBSD', 'Sun OS'=>'SunOS', 'Linux'=>'(Linux)|(X11)', 'Mac OS'=>'(Mac_PowerPC)|(Macintosh)', 'QNX'=>'QNX', 'BeOS'=>'BeOS', 'OS/2'=>'OS/2', 'Search Bot'=>'(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)');
    // Loop through the array of user agents and matching operating systems
    foreach ($oslist as $CurrOS=>$Match) {
        // Find a match
        if (preg_match("/" . $Match . "/i", $products[0][2])) {
            // We found the correct match
            break;
        }
    }
    $userAgent[2] = $CurrOS;
    return $userAgent;
}
