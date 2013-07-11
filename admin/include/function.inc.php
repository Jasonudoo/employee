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
    if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$", $p_addr))
        return true;
    else
        return false;
}
