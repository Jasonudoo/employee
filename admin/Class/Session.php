<?php
if( !defined('PROJECT_START') || !PROJECT_START) die("Access Denied");
/**
 * @copyright Copyright(2010) NetWebX All Right Reserved.
 * @filesource: Session.php,v$
 * @package: Class
 *
 * @author Jason Williams <jason@netwebx.com>
 * @version $Id: v 1.0 2010-05-20 Jason Exp $
 *
 * @abstract:
 */
class Session extends Base
{
    public $Login = FALSE;
    public $UserInfo = FALSE;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTable('tbl_sessions');
        $this->_initSession();
    }
    
    private function _initSession()
    {
        if (isset($_COOKIE[CUSER_USERNAME])) {
            $this->set("SESSIONS_USER_NAME", $_COOKIE[CUSER_USERNAME]);
        } else {
        	$this->set("SESSIONS_USER_NAME", "");
            $this->Login = FALSE;
        }
        
        if (isset($_COOKIE[CSESSIONID])) {
            $this->set("SESSIONS", $_COOKIE[CSESSIONID]);
        } else {
        	$this->set("SESSIONS", "");
            $this->Login = FALSE;
        }

        $this->set("SESSIONS_IPADR", $_SERVER['REMOTE_ADDR']);
		//$agent = checkUserAgentString($_SERVER['HTTP_USER_AGENT']);
		//$this->set("SESSIONS_BROWSER", $agent[0]);
		//$this->set("SESSIONS_BROWSER_VER", $agent[1]);
		//$this->set("SESSIONS_OS", $agent[2]);
        $this->set("SESSIONS_BROWSER", "");
        $this->set("SESSIONS_BROWSER_VER", "");
        $this->set("SESSIONS_OS", "");
        
    }
    
	/**
	 * verify the session is expired or correct
	 * @return
	 */
    public function verify()
    {
		$this->cleanExpiredSessions();

        if ( trim($this->get('SESSIONS_USER_NAME')) == "" || trim($this->get('SESSIONS')) == ""){
            $this->Login = FALSE;
            return;
        }
		$user = new User();
        if ( !$this->UserInfo = $user->getUserInfoByName($this->get('SESSIONS_USER_NAME')) ) {
            $this->Login = FALSE;
            return;
        }
        if (!$this->isSessionExist()) {
            $this->Login = FALSE;
            return;
        }
        $this->updateLastTime();
        $this->Login = TRUE;
    }
	
	/**
	 * check the session is exists or not
	 * @return
	 */
    private function isSessionExist()
    {
    	$sql = sprintf("SELECT count(1) AS cnt FROM `%s` WHERE `SESSIONS` = '%s'",
    	            $this->getTable(),
    	            $this->get('SESSIONS'));
        $qry = $this->getDb()->query($sql);
        $result = $this->getDb()->fetch_result($qry);
        $result = intval($result);
        
        if (!$result)
        {
            return FALSE;
        }
        
        return TRUE;
    }
	
	/**
	 * clean the expired sesssion from the session table
	 * the default session will be expired for 24 hour since last login
	 * and if the session is set to the option of stay login, the session
	 * will be not expired.
	 *
	 * @return
	 */
    private function cleanExpiredSessions()
    {
		$expired = time() - SESSION_EXPIRED;
		$sql = sprintf("DELETE FROM `%s` WHERE `SESSIONS_LAST` <= %b AND `SESSIONS_LOCK` = 'N'",
		            $this->getTable(), $expired);
		$this->getDb()->query($sql);
    }

    public function deleteLockSession()
    {
    	$escape_time = time() - SESSION_LOCK_EXPIRED;
		$sql = sprintf("DELETE `%s` WHERE `SESSIONS_LOCK` = 'Y' AND `SESSIONS_LAST` <= %s",
		            $this->getTable(), $escape_time);
        $this->getDb()->query($sql);
    }
	
	/**
	 *
	 * @return
	 */
    public function createSession()
    {
        srand((double) microtime() * 1000000);
        $sessionid = "";
        $random_char = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
							"a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
							"k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
							"u", "v", "w", "x", "y", "z", "A", "B", "C", "D",
							"E", "F", "G", "H", "I", "J", "K", "L", "M", "N",
							"O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
        for ($i = 0; $i < 9; $i++)
        {
            $sessionid .= $random_char[rand(0, 61)];
        }
        $sessionid = md5($sessionid);
		
		if( is_null($this->get('SESSIONS_LOCK')) ) $this->set('SESSIONS_LOCK', 'N');
		
		$sql = sprintf("INSERT INTO `%s` SET `SESSIONS` = '%s',
		                `SESSIONS_USER_NAME` = '%s',
		                `SESSIONS_IPADR` = '%s',
						`SESSIONS_BROWSER` = '%s',
		                `SESSIONS_BROWSER_VER` = '%s',
		                `SESSIONS_OS` = '%s',
		                `SESSIONS_LOCK` = '%s',
						`SESSIONS_LOGIN` = %s,
		                `SESSIONS_LAST` = %s",
						$this->getTable(),
		                $sessionid,
		                $this->get('SESSIONS_USER_NAME'),
		                $this->get('SESSIONS_IPADR'),
						$this->get('SESSIONS_BROWSER'),
		                $this->get('SESSIONS_BROWSER_VER'),
		                $this->get('SESSIONS_OS'),
						$this->get('SESSIONS_LOCK'),
		                time(),
		                time());
		
		$this->getDb()->query($sql);
        return $sessionid;
    }
	
    public function updateLastTime()
    {
    	$sql = sprintf("UPDATE `%s` SET `SESSIONS_LAST` = %s WHERE `SESSIONS` = '%s'",
    					$this->getTable(),
    					time(),
    					$this->get('SESSIONS'));
        $this->getDb()->query($sql);
    }

    public function getLockSession()
    {
    	$sql = sprintf("SELECT count(1) as cnt FROM `%s` WHERE `SESSIONS_LOCK` = 'Y' AND `SESSIONS_IPADR` = '%s'",
    					$this->getTable(),
    					$this->get('SESSIONS_IPADR'));
    	$qry = $this->getDb()->query($sql);
    	$result = $this->getDb()->fetch_result($qry);
    	$result = intval($result);
		return $result;
    }
}
?>
