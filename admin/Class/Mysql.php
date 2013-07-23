<?php
if( !defined('PROJECT_START') || !PROJECT_START) die("Access Denied");
/**
 * @copyright Copyright(2012) NetWebX All Right Reserved.
 * @filesource: Mysql.php,v$
 * @package:Class
 *
 * @author WengJunFeng <jason@netwebx.com>
 * @version $Id: v 1.0 2012-06-06 Jason Exp $
 *
 * @abstract:
 */

class Mysql
{
	static private $_instance = NULL;
	static private $_db_handle = NULL;
	
	private $_real_escape = FALSE;
	private $_ready = FALSE;
	private $_transCommit = FALSE;

	private $_transCount = 0;
	private $_lastErrorCode = NULL;
	private $_lastError = NULL;
	private $_lastSql = NULL;

	private $_errorType = NULL;
	
	
	var $log = array();
	var $enableLog = FALSE;
	var $fieldNameLower = FALSE;

    /**
     * Singleton pattern implementation makes "new" unavailable
     *
     * @return void
     */
    private function __construct(){}

    /**
     * Singleton pattern implementation makes "clone" unavailable
     *
     * @return void
     */
    private function __clone(){}
    
 	static public function getInstance()
	{
		if( !self::$_instance )
		{
			self::$_instance = new Mysql();
		}
		return self::$_instance;
	}
	
	public function connect()
	{
		if( !self::$_db_handle )
		{
			$try_num = 3;
			while($try_num > 0)
			{
				self::$_db_handle = @mysql_connect(DB_HOST, DB_USER, DB_PASS, TRUE);
				if( self::$_db_handle )
				{
					register_shutdown_function(array(&$this, "close"));
					break;
				}
				$try_num--;
				sleep(1);
			}
		}
		
		if( !self::$_db_handle )
		{
			header("Content-Type:text/html;charset=utf-8");
			die("<h1>Error establishing a database connection</h1>");
		}
		
		$charset = DB_CHARSET;
		if ( defined( 'DB_COLLATE' ) && DB_COLLATE )
			$collate = DB_COLLATE;
		else
			$collate = 'utf8_general_ci';
		
		if ( !empty( $charset ) )
		{
			if ( function_exists( 'mysql_set_charset' ) )
			{
				mysql_set_charset( $charset, self::$_db_handle );
				$this->_real_escape = TRUE;
			}
			else
			{
				$sql = printf( 'SET NAMES %s', $charset );
				if ( ! empty( $collate ) )
					$sql .= printf( ' COLLATE %s', $collate );
				$this->query( $sql );
			}
		}

		mysql_select_db(DB_NAME, self::$_db_handle );
		$this->_ready = TRUE;
		
		return self::$_db_handle;
		//return self::$_instance;
	}
	
	public function close()
	{
		if ( self::$_db_handle )
		{
			mysql_close(self::$_db_handle);
		}
		self::$_db_handle = NULL;
		$this->_lastErrCode = NULL;
		$this->_lastError = NULL;
		$this->_lastSql = NULL;
		$this->_errorType = NULL;
	}

	public function startTrans()
	{
		$this->_transCount += 1;
		$this->query('START TRANSACTION');
	}

	public function completeTrans($commitOnNoErrors = TRUE)
	{
		if ($this->_transCount < 1) { return NULL; }
		$this->_transCount = 0;
		$this->_transCommit = TRUE;
		if ($commitOnNoErrors)
		{
			$this->query('COMMIT');
			return TRUE;
		}

		$this->query('ROLLBACK');
		return FALSE;
	}

	public function query($sql)
	{
		if( !$this->_ready ) return FALSE;
		
		$query = mysql_query($sql, self::$_db_handle);
		$error = mysql_errno(self::$_db_handle);
		
		if( $error )
		{
			$this->_lastError = mysql_error(self::$_db_handle);
			$this->_lastErrorCode = $error;
			$this->set_error_type($error);
			return FALSE;
		}
		
		$this->_lastSql = $sql;
		$this->rows = @mysql_num_rows($query);
	  	return $query;
	}

	public function query_fetch_array($sql)
	{
		if( !$this->_ready ) return FALSE;
		
		$query = mysql_query($sql, self::$_db_handle);
		$error = mysql_errno(self::$_db_handle);
	  	
		if($error)
		{
			$this->_lastError = mysql_error(self::$_db_handle);
			$this->_lastErrorCode = $error;
			return FALSE;
		}
		
		return $this->fetch_array($query);
	}
		
	public function query_fetch_result($sql)
	{
		if( !$this->_ready ) return FALSE;

		$query = mysql_query($sql,self::$_db_handle);
		$error = mysql_errno(self::$_db_handle);
		
	  	if($error)
		{
			$this->_lastError = mysql_error(self::$_db_handle);
			$this->_lastErrorCode = $error;
			return FALSE;
		}
		return $this->fetch_result($query);
	}
	
	public function execute($sql)
	{
		if(!self::$_db_handle) return FALSE;
		return $this->query($sql);
	}

	public function execute_proc($pbody, $binds, $package = NULL)
	{
		if(!$this->_ready) return FALSE;
		
		$sql = '';
		$args_sql = '';
		$out_sql = '';
		$out_arr = array();
		$allnum = count($binds);

		for( $i = 0; $i < $allnum; $i++ )
		{
			$item = $binds[$i];
			if( $item === NULL )
			{
				$args_sql .= "NULL,";
			}
			else
			{
				if( is_array($item) )
				{
					if( isset($item['OUT']) )
					{
						$out_arr[] = $item['OUT'];
						$args_sql .= "@{$item['OUT']},";
						$out_sql .= "@{$item['OUT']} as {$item['OUT']},";
					}
					else if( isset($item['IN_OUT'] ))
					{
						//申明变量
						$t_sql = is_string($item[0])?"set @{$item['IN_OUT']}='{$item[0]}'":"set @{$item['IN_OUT']}={$item[0]}";
						$this->query($t_sql);

						$out_arr[] = $item['IN_OUT'];
						$args_sql .= "@{$item['IN_OUT']},";
						$out_sql .= "@{$item['IN_OUT']} as {$item['IN_OUT']},";
					}
					else
					{
						$item_type = strtoupper($item[1]);
						if($type == 'BLOB' || $type == 'BLOB_FILE')
						{
							if($type == 'BLOB_FILE')
							{
								$value = $this->read_file($item[0]);
							}
						}
						$args_sql .= "'$value',";
					}
				}
				else
				{
					$args_sql .= is_string($item)?"'$item',":"$item,";
				}
			}
		}
		$args_sql = substr($args_sql,0,-1);
		$out_sql = substr($out_sql,0,-1);

		$sql = "call $pbody($args_sql)";
		$out_sql = $out_sql==''?'':"select $out_sql";
		$query = $this->query($sql);
		if($this->error())
		{
			return FALSE;
		}
		$arr = TRUE;
		if($out_sql)
		{
			$stmt = $this->query($out_sql);
			$arr = @mysql_fetch_array($stmt,MYSQL_ASSOC);
			mysql_free_result($stmt);
		}
		return $arr;
	}

	public function list_tables()
	{
		$query = $this->query('SHOW TABLES FROM ' . $db);
		$array = $this->fetch_array($query);
	}

	public function fetch_array($stmt)
	{
		if(!$this->_ready) return FALSE;

		if(!is_resource($stmt))
		{
			$this->_lastError = 'fetch_array() parameters error';
			return FALSE;
		}
		$arr = @mysql_fetch_array($stmt,MYSQL_ASSOC);
		return $arr ? array_change_key_case($arr,CASE_UPPER) : $arr;
	}

	public function fetch_result($stmt, $i=0)
	{
		return @mysql_result($stmt, $i);
	}

	public function insert_id()
	{
		if(!$this->_ready) return FALSE;
		return mysql_insert_id(self::$_db_handle);
	}
	
	public function affected_rows()
	{
		if(!$this->_ready) return FALSE;
		return mysql_affected_rows (self::$_db_handle);
	}
	
	public function getAll($stmt)
	{
		if(!$this->_ready) return FALSE;
		
		if( !is_resource($stmt) )
		{
			$stmt = $this->query($stmt);
			if(!$stmt) return FALSE;
		}
		
		$arr = array();
		while($array = $this->fetch_array($stmt))
		{
			$arr[] = $array;
		}
		return $arr;
	}

	public function getOne($sql)
	{
		if(!$this->_ready) return FALSE;

		if (is_resource($sql))
		{
			$stmt = $sql;
		}
		else
		{
			$stmt = $this->query($sql);
		}

		$row = array();
		$row = mysql_fetch_row($stmt);
		mysql_free_result($stmt);
		return isset($row[0]) ? $row[0] : NULL;
    }

	public function getRow($sql)
	{
		if (is_resource($sql))
		{
			$stmt = $sql;
		}
		else
		{
			$stmt = $this->query($sql);
		}
		$row = array();
		$row = @mysql_fetch_array($stmt,MYSQL_ASSOC);
		return $row ? array_change_key_case($row,CASE_UPPER) : $row;
	}

	public function getCol($sql, $col = 0)
	{
		if (is_resource($sql))
		{
			$stmt = $sql;
		}
		else
		{
			$stmt = $this->query($sql);
		}
		
		$data = array();
		$row = array();
		while ($row = @mysql_fetch_array($stmt,MYSQL_BOTH))
		{
			$data[] = $row[$col];
		}
		@mysql_free_result($stmt);
		return $data;
	}

	public function query_count($sql)
	{
		$search = array ("'^select[ \n\r\t].*[ \n\r\t]from[ \n\r\t]'siU","/^(.*)order[ \n\r\t]+by(.*)$/si");
		$replace = array ("select count(*) from \\1 ","\\1");
		$sql = preg_replace($search,$replace,trim($sql));
		$query = $this->query($sql);
		return $this->fetch_result($query);
	}
	
	public function page_sql($sql, $start = 1, $end = 1)
	{
		$start = $start/1;
		$end = $end/1;
		if($start == 0 && $end == 0)
		{
			return $sql;
		}
		$start -= 1;
		$end = $end - $start;
		return $sql .= " limit $start, $end";
	}

	public function table_insert($table, $arr, $commit = NULL)
	{
		$magic_quote = get_magic_quotes_gpc();
		$insert_key_sql = $insert_val_sql = $comma = '';
		foreach($arr as $key=>$val)
		{
			$insert_key_sql .= $comma.'`'.$key.'`';

			if(!is_array($val))
			{
				$val = array($val);
			}
			$value = & $val[0];
			$type = strtoupper($val[1]);
			if($type == 'BLOB' || $type == 'BLOB_FILE')
			{
				if($type == 'BLOB_FILE')
				{
					$value = $this->read_file($value);
				}
			}
			if($value === '')
			{
				$insert_val_sql .= $comma."NULL";
			}
			else
			{
				//$val = !$magic_quote ? addslashes($val) : stripslashes($val);
				$value = !$magic_quote ? addslashes($value) : $value;
				$value = mysql_escape_string($value);
				$insert_val_sql .= $comma."'".$value."'";
			}
			$comma = ', ';
		}
		
		return $this->execute('INSERT INTO '.$table.' ('.$insert_key_sql.') VALUES ('.$insert_val_sql.')');
	}

	public function table_update($table, $arr, $wh)
	{
		$sql = 'update '.$table;
		$update_str = ' set ';
		$i = 0;
		foreach($arr as $key=>$val)
		{
			if($i++>0)
			{
				$update_str .= ',';
			}
			if($val === '')
			{
				$update_str .= '`'.$key."` = NULL";
			}
			else
			{
				$val = !get_magic_quotes_gpc() ? addslashes($val) : stripslashes($val);
				$val = mysql_escape_string($val);
				$update_str .= '`'.$key."` = '".$val . "'";
			}
		}
		if($wh !== NULL)
			$update_str .= " where $wh";
		
		$sql = $sql.$update_str;
		
		return $this->execute($sql);
	}

	public function error()
	{
		return $this->_lastError;
	}
	
	public function debug($display = TRUE)
	{
		if($display)
			echo $this->_lastError;
		else return $this->_lastError;
	}
	
	public function read_file($file_name)
	{
		if(!FINE_QUOTES_RUNTIME) return file_get_contents($file_name);
		set_magic_quotes_runtime(0);
		$content = file_get_contents($file_name);
		set_magic_quotes_runtime(1);
		return $content;
	}

	public function set_error_type($code = NULL)
	{
		if($code == NULL) return;
		if($code == '1062')
		{
			$error_type = '3';
		}
		else
		{
			$error_type = '9';
		}
		$this->_errorType = $error_type;
	}

	public function get_error_type()
	{
		return $this->_errorType;
	}

	public function get_sequence($seq_name)
	{
		if( !defined("DB_SEQ_TABLE") )
		{
			return FALSE;
		}
		$seq_table = DB_SEQ_TABLE;
		$sql = "UPDATE $seq_table SET `seq_nextnum` = LAST_INSERT_ID(
			CASE
				WHEN `seq_nextnum` >= `max_value` THEN `min_value`
			ELSE
				`seq_nextnum`+ 1
			END )
			WHERE `seq_name`='$seq_name'";
		
		$result = $this->execute($sql);
		if(!$this->error() && $this->affected_rows())
		{
			return $this->insert_id();
		}
		else
		{
			return -1;
		}
	}
}