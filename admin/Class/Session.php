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
class Session extends Base {
    
    private $_table = "";
    public $Login = FALSE;
    public $UserInfo = FALSE;
    
    public function __construct() {
        parent::__construct();
        $this->_table = $this->DB->sessions;
        $this->_initSession();
    }
    
    private function _initSession() {
        //global $_COOKIE,$_SERVER;
        
        if (isset($_COOKIE[CUSER_USERNAME])) {
            $this->set("sessions_user_name", $_COOKIE[CUSER_USERNAME]);
        } else {
        	$this->set("sessions_user_name", "");
            $this->Login = FALSE;
        }
        
        if (isset($_COOKIE[CSESSIONID])) {
            $this->set("sessions", $_COOKIE[CSESSIONID]);
        } else {
        	$this->set("sessions", "");
            $this->Login = FALSE;
        }

        $this->set("sessions_ipadr", $_SERVER['REMOTE_ADDR']);
		$agent = checkUserAgentString($_SERVER['HTTP_USER_AGENT']);
		//print_r($agent);
		$this->set("sessions_browser", $agent[0]);
		$this->set("sessions_browser_ver", $agent[1]);
		$this->set("sessions_os", $agent[2]);

    }
    
	/**
	 * verify the session is expired or correct
	 * @return 
	 */
    public function verify() {
		$this->cleanExpiredSessions();

        if ( trim($this->_data['sessions_user_name']) == "" || trim($this->_data['sessions']) == ""){
            $this->Login = FALSE;
            return;
        }
		$user = new User();
        if ( !$this->UserInfo = $user->getUserInfoByName($this->_data['sessions_user_name']) ) {
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
    private function isSessionExist() {
    	$sql = sprintf("SELECT count(1) AS cnt FROM `%s` WHERE `sessions` = '%s'", $this->_table, $this->_data['sessions']);
        $result = $this->DB->get_var($sql);
        $result = intval($result);
        if (!$result){
            return false;
        } else return true;
    }
	
	/**
	 * clean the expired sesssion from the session table
	 * the default session will be expired for 24 hour since last login
	 * and if the session is set to the option of stay login, the session 
	 * will be not expired.
	 * 
	 * @return 
	 */
    private function cleanExpiredSessions() {
		$expired = time() - SESSION_EXPIRED;
		$sql = sprintf("DELETE FROM `%s` WHERE `session_last` <= %b AND `sessions_lock` = 'N'", $this->_table, $expired);
		$this->DB->query($sql);
    }

    public function deleteLockSession() {
    	$escape_time = time() - SESSION_LOCK_EXPIRED;
		$sql = sprintf("DELETE `%s` WHERE `session_lock` = 'Y' AND `session_last` <= %s", $this->_table, $escape_time);
        $this->DB->query($sql);
        return;
    }
	
	/**
	 * 
	 * @return 
	 */
    public function createSession() {
        srand((double) microtime() * 1000000);
        $sessionid = "";
        $random_char = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", 
							"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", 
							"k", "l", "m", "n", "o", "p", "q", "r", "s", "t", 
							"u", "v", "w", "x", "y", "z", "A", "B", "C", "D", 
							"E", "F", "G", "H", "I", "J", "K", "L", "M", "N", 
							"O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
        for ($i = 0; $i < 9; $i++) {
            $sessionid .= $random_char[rand(0, 61)];
        }
        $sessionid = md5($sessionid);
		
		if( !isset($this->_data['sessions_lock']) ) $this->_data['sessions_lock'] = "N";
		$sql = sprintf("INSERT INTO `%s` SET `sessions` = '%s', `sessions_user_name` = '%s', `sessions_ipadr` = '%s',
						`sessions_browser` = '%s', `sessions_browser_ver` = '%s', `sessions_os` = '%s', `sessions_lock` = '%s',
						`sessions_login` = %s, `sessions_last` = %s",
						$this->_table, $sessionid, $this->_data['sessions_user_name'], $this->_data['sessions_ipadr'],
						$this->_data['sessions_browser'], $this->_data['sessions_browser_ver'], $this->_data['sessions_os'],
						$this->_data['sessions_lock'], time(), time());
		
		$this->DB->query($sql);
        return $sessionid;
    }
	
    public function updateLastTime() {
    	$sql = sprintf("UPDATE `%s` SET `session_last` = %s WHERE `sessions` = '%s'", 
    					$this->_table, 
    					time(), 
    					$this->_data['sessions']);
        $this->DB->query($sql);
    }

    public function getLockSession() {
    	$sql = sprintf("SELECT count(1) as cnt FROM `%s` WHERE `session_lock` = 'Y' AND `session_ipadr` = '%s'", 
    					$this->_table,
    					$this->_data['session_ipadr']);
    	$result = $this->DB->get_var($sql);
    	$result = intval($result);
		return $result;
    }
}
?>
