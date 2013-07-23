<?php
if( !defined('PROJECT_START') || !PROJECT_START) die("Access Denied");
/**
 * @copyright Copyright(2011) All Right Reserved.
 * @filesource: form.lib.php,v$
 * @package: library
 *
 * @author Jason Williams <jasonudoo@gmail.com>
 * @version $Id: v 1.0 2011-05-20 Jason Exp $
 *
 * @abstract: 
 */

class WebForm 
{
	protected $_dbh = NULL;
	public $ready = FALSE;
	
	private $_data = array();
	
	public function __construct()
	{
		
	}
	
	public function set($name, $value)
	{
		$this->_data[$name] = $value;
	}
	
	public function get($name)
	{
		if( isset($this->_data[$name]) )
		{
			return $this->_data[$name];
		}
		return NULL;
	}
	
	public function insert()
	{
		$field_data['usr_first_name'] = $this->_data['In_First_Name'];
		$field_data['usr_last_name'] = $this->_data['In_Last_Name'];
		$field_data['usr_company'] = $this->_data['In_Company_Name'];
		$field_data['usr_work_email'] = $this->_data['In_Email'];
		$field_data['usr_phone'] = $this->_data['In_Phone'];
		$field_data['usr_phone_extent'] = $this->_data['In_Phone_Extent'];
		$field_data['usr_job_title'] = $this->_data['In_Job_Title'];
		$field_data['usr_created_date'] = "now()";
		$field_data['usr_ip_addr'] = $_SERVER['REMOTE_ADDR'];
		$field_data['usr_option1'] = $this->_data['In_Option_1'];
		$field_data['usr_option2'] = $this->_data['In_Opiton_2'];
		$field_data['usr_option3'] = $this->_data['In_Option_3'];
		$field_data['usr_from_page'] = $this->_data['In_Page'];
		
		if( isset($this->_data['In_X1']) )
		{
			$field_data['usr_subid'] = $this->_data['In_X1'];
		}
		
		if( isset($this->_data['In_X2']) )
		{
			$field_data['usr_sub_id_2'] = $this->_data['In_X2'];
		}
		
		if( isset($this->_data['In_X3']) )
		{
			$field_data['campaign_id'] = $this->_data['In_X3'];
		}
		
		if( isset($this->_data['In_When_Finance']) )
		{
			$field_data['usr_when_financing'] = $this->_data['In_When_Finance'];
		}
		
		if( isset($this->_data['In_Annual_Revenue']) )
		{
			$field_data['usr_annual_revenue'] = $this->_data['In_Annual_Revenue'];
		}
		
		if( isset($this->_data['In_Amount_Finance']) )
		{
			$field_data['usr_amount_financing'] = $this->_data['In_Amount_Finance'];
		}
				
		$fields = array();
		foreach($field_data as $key => $value)
		{
			$value = mysql_real_escape_string($value);
			if($key == "usr_created_date")
			{
				$fields[] = "`$key` = now()";
			}
			else
			{
				$fields[] = "`$key` = '".$value."'";
			}

		}
		$sql = "INSERT INTO tbl_userinfo SET ".join(",", $fields);
		//echo $sql;
		$result = $this->query($sql);
		
		return mysql_insert_id($this->_dbh);
	}
	
	public function select($id = 0)
	{
		$sql = "SELECT * FROM tbl_userinfo WHERE usr_insert_silverpop_status = 'NO' AND id = %d LIMIT 1";
		$sql = sprintf($sql, $id);
		$result = $this->query($sql);
		
		$rs = $this->fetch_array($result);
		return $rs;
	}
	
	public function update($id = 0, $contact_id = 0)
	{
		$sql = "UPDATE tbl_userinfo SET usr_insert_silverpop_status = 'YES', usr_recipient_id = %d  WHERE id = %d LIMIT 1";
		$sql = sprintf($sql, $contact_id, $id);
		$this->query($sql);
		
		return TRUE;
	}
	
	public function validate_email($email)
	{
		if( !is_email($email) )
		{
			return FALSE;
		}
		/*
		$sql = sprintf("SELECT count(1) as CNT FROM tbl_userinfo WHERE usr_work_email = '%s'", mysql_real_escape_string($email));
		$result = $this->query($sql);
		
		$rs = $this->fetch_array($result);
		if((int)$rs['CNT'] > 0 )
		{
			return FALSE;
		}
		*/
		return TRUE;
	}
	
	public function validate_firstname($val)
	{
		if(trim($val) == "")
		{
			return FALSE;
		}
		return $this->_checkString($val);	
	}
	
	public function validate_lastname($val)
	{
		if( trim($val) == "" )
		{
			return FALSE;
		}
		return $this->_checkString($val);
	}
	
	public function validate_company($val)
	{
		if( trim($val) == "" )
		{
			return FALSE;
		}
		return $this->_checkString($val);
	}
	
	public function validate_phone($val)
	{
		if( trim($val) == "" )
		{
			return FALSE;
		}
		
		if( !eregi('^[0-9]{3}-?[0-9]{3}-?[0-9]{4}-?[0-9]{0,6}$', $val) )
		{
			return FALSE;
		}
		return TRUE;
	}
	
	public function validate_jobtitle($val)
	{
		if( trim($val) == "" )
		{
			return FALSE;	
		}
		return $this->_checkString($val);
	}
	
	public function connect()
	{
		if ( defined('DB_CHARSET') )
		{
			$charset = DB_CHARSET;
		}

		if ( defined('DB_COLLATE') )
		{
			$collate = DB_COLLATE;
		}

		$try_num = 5;
		$dbhost = DB_HOST;
		$dbuser = DB_USER;
		$dbname = DB_NAME;
		$dbpassword = DB_PASSWORD;
		while($try_num > 0){
			$this->_dbh = @mysql_connect($dbhost, $dbuser, $dbpassword, true);
			if($this->_dbh){
				register_shutdown_function(array(&$this, "close"));
				break;
			}
			$try_num--;
			sleep(1);
		}
		$this->ready = true;

		if ( !empty($charset) ) 
		{
			if ( function_exists('mysql_set_charset') ) {
				mysql_set_charset($charset, $this->_dbh);
				$this->real_escape = true;
			} else {
				$collation_query = "SET NAMES '{$charset}'";
				if ( !empty($collate) )
					$collation_query .= " COLLATE '{$collate}'";
				$this->query($collation_query);
			}
		}

		mysql_select_db($dbname, $this->_dbh);
	}
	
	public function close()
	{
		mysql_close($this->_dbh);
		return TRUE;
	}
	
	public function query($sql)
	{
		if( !$this->ready )
		{
			return FALSE;
		}
		$result = mysql_query($sql, $this->_dbh);
		return $result;		
	}

	public function fetch_array($result)
	{
		if(!$result)
		{
			return FALSE;
		}
		
		$rs = mysql_fetch_assoc($result);
		return $rs;
	}
	
	private function _checkString($val)
	{
		if( strlen(trim($val)) <= 0 )
		{
			return FALSE;	
		}
		
		return TRUE;
	}
}