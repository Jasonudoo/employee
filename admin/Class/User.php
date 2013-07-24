<?php
if (! defined('PROJECT_START') || ! PROJECT_START)
    die("Access Denied");

/**
 *
 * @copyright Copyright(2012) NetWebX All Right Reserved.
 * @filesource : User.php,v$
 * @package :Class
 *
 * @author WengJunFeng <jason@netwebx.com>
 * @version $Id: v 1.0 2012-06-11 Jason Exp $
 *
 * @abstract :
 */
class User extends Base
{

    public function __construct ()
    {
        parent::__construct();
        $this->setTable("tbl_user");
    }

    /**
     * check the user exists or not by user name
     *
     * @param object $p_value
     *            -- the user name string
     * @return BOOLEAN false -- not exists
     *         true -- exists
     */
    public function isExistsByUserName ($p_value)
    {
        if (empty($p_value))
            return FALSE;
        $sql = sprintf("SELECT count(1) AS cnt FROM %s WHERE USER_ID = '%s' LIMIT 1",
                $this->getTable(),
                $p_value);
        $qry = $this->getDb()->query($sql);
        $cnt = $this->getDb()->fetch_result($qry);
        
        if (intval($cnt) == 1)
            return TRUE;
        
        return FALSE;
    }

    public function login ($p_name, $p_passwd)
    {
        if (empty($p_name) || empty($p_passwd)) {
            return FALSE;
        }
        $sql = sprintf(
                "SELECT * FROM `%s` WHERE `USER_ID` = '%s' AND `USER_PASSWORD` = '%s' LIMIT 1",
                $this->getTable(), $p_name, $p_passwd);
        
        $userInfo = $this->getDb()->getAll($sql);
        if (count($userInfo) > 0) {
            $session = new Session();
            $session->set("SESSIONS_USER_NAME", $userInfo[0]['USER_ID']);
            $session_id = $session->createSession();
            setcookie(CSESSIONID, $session_id, time() + SESSION_EXPIRED);
            setcookie(CUSER_USERNAME, $userInfo[0]['USER_ID'], time() + SESSION_EXPIRED);
            
            return $userInfo[0];
        }
        return FALSE;
    }

    public function getUserInfoByName ($p_name)
    {
        $result = FALSE;
        if (empty($p_name)) {
            return $result;
        }
        $sql = sprintf("SELECT * FROM `%s` WHERE `USER_ID` = '%s' LIMIT 1",
                $this->getTable(), $p_name);
        $result = $this->getDb()->getAll($sql);
        
        if (count($result) > 0) {
            return $result[0];
        }
        
        return $result;
    }

    public function signout ()
    {
        setcookie(CSESSIONID, "", 0);
        setcookie(CUSER_USERNAME, "", 0);
        if (! headers_sent()) {
            header("Location: index.php");
        }
    }

    public function add ()
    {}

    public function update ()
    {}

    public function checkVerifyCode ()
    {}

    public function updateAccountInfo ()
    {}
}