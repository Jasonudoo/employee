<?php
if( !defined('PROJECT_START') || !PROJECT_START) die("Access Denied");
/**
 * @copyright Copyright(2012) NetWebX All Right Reserved.
 * @filesource: Base.php,v$
 * @package:Class
 *
 * @author WengJunFeng <jason@netwebx.com>
 * @version $Id: v 1.0 2012-06-06 Jason Exp $
 *
 * @abstract:
 */

class Base
{
	protected $_db = NULL;
	protected $_table = NULL;
	private $_data = NULL;
	
	public function __construct()
	{
		$this->_getDbHandler();
	}
	
	private function _getDbHandler()
	{
		if( is_null($this->_db) )
		{
			$this->_db = Mysql::getInstance();
			$this->_db->connect();
		}
		return $this->_db;
	}
	
	public function setTable($table)
	{
		$this->_table = $table;
	}
	
	public function getTable()
	{
		return $this->_table;
	}
	
	public function setData($key, $value)
	{
		$this->_data[$key] = $value;
	}
	
	public function getData($key)
	{
		return isset($this->_data[$key]) ? $this->_data[$key] : NULL;
	}

	protected function _insert($value, $debug = FALSE)
	{
		if( !is_array($value) )
		{
			return FALSE;
		}
		if( !$table = $this->getTable() )
		{
			return FALSE;
		}
		$sql = "INSERT INTO " . $table . " SET ";
		$set_values = array();
		
		foreach($value as $key => $val)
		{
			if( isset($key) )
			{
				$set_values[] = "`$key` = '" . mysql_escape_string($val) . "'";
			}
		}
		$sql .= join(", ", $set_values);
		
		if( $debug )
		{
			echo "SQL : <pre>" . $sql . "</pre>";
			return FALSE;
		}
		
		$this->_db->query($sql);
		
		return $this->_db->insert_id();
	}
	
	protected function _update($value, $where, $debug = FALSE)
	{
		if( !is_array($value) )
		{
			return FALSE;
		}
		
		if( !$table = $this->_getTableName() )
		{
			return FALSE;
		}
		
		$sql = "UPDATE " . $table . " SET ";
		$set_values = array();
		
		foreach($value as $key => $val)
		{
			if( isset($key) )
			{
				$set_values[] = "`$key` = '" . mysql_escape_string($val) . "'";
			}
		}
		$sql .= join(", ", $set_values);
		
		if( !is_array($where) )
		{
			$wc = array();
			foreach($where as $fld => $condition)
			{
				$wc[] = "`$fld` = '" . mysql_escape_string($condition) . "'";
			}
			$sql .= " WHERE " . join(" AND ", $wc);
		}
		else
		{
			$sql .= " WHERE " . $wc;
		}
		
		return $this->_query($sql, self::$DBUPDATE, NULL, $debug);
		
	}
	
	protected function _delete($where, $debug = FALSE)
	{
		if( !isset($where) )
		{
			return FALSE;
		}
		if( !$table = $this->_getTableName() )
		{
			return FALSE;
		}
		
		$sql = "DELETE FROM " . $table ." WHERE " . $where;
		
		return $this->_query($sql, self::$DBDELETE, NULL, $debug);
	}

	protected function _compareTime($time1, $time2)
	{
		$time1 = $this->_getTimestamp($time1);
		$time2 = $this->_getTimestamp($time2);

		if( $time1 > $time2 )
		{
			return 1;
		}
		
		return 0;
	}
	
	private function _getTimestamp($time)
	{
		$year = substr($time, 0, 4);
		$month = substr($time, 4, 2);
		$day = substr($time, 6, 2);
		$hour = substr($time, 8, 2);
		$minute = substr($time, 10, 2);
		$second = substr($time, 12, 2);
		
		$time = mktime($hour, $minute, $second, $month, $day, $year);
		
		return $time;
	}

	static public function getStringLength($string)
	{
		preg_match_all("/./us", $string, $match);
		// 返回单元个数
		return count($match[0]);
	}

	//$sourcestr 是要处理的字符串
	
	//$cutlength 为截取的长度(即字数)
	
	static public function subString($sourcestr,$cutlength)
	{
		$returnstr='';
		$i=0;
		$n=0;
		//字符串的字节数
		$str_length = strlen($sourcestr);
		
		while (($n<$cutlength) and ($i<=$str_length))
		{
			$temp_str = substr($sourcestr, $i, 1);
			//得到字符串中第$i位字符的ascii码
			$ascnum=Ord($temp_str);
			if ($ascnum>=224)    //如果ASCII位高与224，
			{
				$returnstr=$returnstr.substr($sourcestr,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
				$i=$i+3;            //实际Byte计为3
				$n++;            //字串长度计1
			}
			elseif ($ascnum>=192) //如果ASCII位高与192，
			{
				$returnstr=$returnstr.substr($sourcestr,$i,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
				$i=$i+2;            //实际Byte计为2
				$n++;            //字串长度计1
			}
			elseif ($ascnum>=65 && $ascnum<=90) //如果是大写字母，
			{
				$returnstr=$returnstr.substr($sourcestr,$i,1);
				$i=$i+1;            //实际的Byte数仍计1个
				$n++;            //但考虑整体美观，大写字母计成一个高位字符
			}
			else                //其他情况下，包括小写字母和半角标点符号，
			{
				$returnstr=$returnstr.substr($sourcestr,$i,1);
				$i=$i+1;            //实际的Byte数计1个
				$n=$n+0.5;        //小写字母和半角标点等与半个高位字符宽...
			}
		}
		return $returnstr;
	}
}