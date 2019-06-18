<?php
namespace restphp\driver;
/**
 * PPF mysqli 常用函数封装
 * @author sofical
 * @since 2014-7-5
 */
class RestDBDriverMysqli {
	/**
	 * 链接方式：0-mysqli_connect，1-real connect
	 * @var number
	 */
	public $nConnectType = 0;
	
	/**
	 * 链接超时时间，默认5秒
	 * @var number
	 */
	public $nConnectTimeout = 5;
	
	/**
	 * 数据库查询编码设置，默认为：UTF8
	 * @var string
	 */
	public $strCharset = 'UTF8';
	
	/**
	 * 当前Mysql链接对象
	 * @var object
	 */
	public $pLink = null;
	
	/**
	 * 查询方式：MYSQLI_STORE_RESULT、MYSQLI_USE_RESULT
	 * @var number
	 */
	public $nQueryType = MYSQLI_STORE_RESULT;
	
	/**
	 * fetch_array返回结果样式设置：MYSQLI_BOTH(3)-同时产生关联和数字数组，MYSQLI_NUM(2)-数字数组，MYSQLI_ASSOC（1）-关联数组
	 * @var number
	 */
	public $nResultType = MYSQLI_BOTH;
	
	/**
	 * 错误编号
	 * @var number
	 */
	private $_nErrorNO = -100;
	
	/**
	 * 错误描述
	 * @var string
	 */
	private $_strErrorMessage = '[REST_DB_UNKNOWN_EXCEPTION]';
	
	/**
	 * 进程链接缓冲池
	 * @var array
	 */
	private static $_arrCacheLinks = array();
	
	private $_strCurrentTag = '';
	
	/**
	 * 创建数据库链接
	 * @param string $dbUser 用户名
	 * @param string $dbPass 认证密码
	 * @param string $dbName 默认链接数据库
	 * @param string $dbHost 主机地址
	 * @param integer $dbPort 数据库端口
	 * @return boolean
	 */
	public function db_connect($dbUser, $dbPass, $dbName='information_schema', $dbHost='localhost', $dbPort=3306){
	    $bFlag = false;
	
		$this->_strCurrentTag = base64_encode("{$dbUser}:{$dbPass}:{$dbHost}:{$dbPort}");
		if(isset(self::$_arrCacheLinks[$this->_strCurrentTag]) && mysqli_ping(self::$_arrCacheLinks[$this->_strCurrentTag])){
			$this->pLink = self::$_arrCacheLinks[$this->_strCurrentTag];
			$bFlag = true;
		}else{
			if(0===$this->nConnectType){
				$this->_mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
			}elseif(1==$this->nConnectType){
				$this->_real_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
			}else{
				$this->_nErrorNO = -99;
				$this->_strErrorMessage = '[REST_DB_SUPPORT_DB_LINK]';
			}
				
			if($this->pLink){
				$this->charset($this->strCharset);
				$bFlag = true;
			}else{
				if(mysqli_connect_errno()){
					$this->_nErrorNO = mysqli_connect_errno();
					$this->_strErrorMessage = mysqli_connect_error();
				}
			}
			
			self::$_arrCacheLinks[$this->_strCurrentTag] = $this->pLink;
		}
		
		if($bFlag){
			$this->select_db($dbName);
		}else{
			$this->_nErrorNO = -98;
			$this->_strErrorMessage = '[REST_DB_HOST_REFUSED]';
		}
	
		return $bFlag;
	}
	
	private function _mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort=3306){
		$this->pLink = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort) or $this->pLink=null;
	}
	
	private function _real_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort=3306){
		$this->pLink = mysqli_init();
		$this->pLink->options(MYSQLI_OPT_CONNECT_TIMEOUT, $this->nConnectTimeout);
		$this->pLink->real_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort) or $this->pLink=null;
	}
	
	/**
	 * 数据库选择
	 * @param string $dbName 数据库名
	 * @return boolean
	 */
	public function select_db($dbName){
		return mysqli_select_db($this->pLink, $dbName);
	}
	
	/**
	 * 关闭数据库链接
	 * @param boolean $bAll 是否关闭所有链接
	 * @return boolean
	 */
	public function close($bAll=false){
		if($bAll){
			if(!empty(self::$_arrCacheLinks)){
				foreach(self::$_arrCacheLinks as $strTag=>$pLink){
					if($pLink) mysqli_close($pLink);
					unset(self::$_arrCacheLinks[$strTag]);
				}
			}
		}else{
			if($this->pLink){
				mysqli_close($this->pLink);
				unset(self::$_arrCacheLinks[$this->_strCurrentTag]);
			}
		}		
	}
	
	/**
	 * 设置数据库编码
	 * @param string $charset 编码
	 */
	public function charset($charset){
		return mysqli_set_charset($this->pLink, $charset);
	}
	
	/**
	 * 获取错误编码
	 * @return number
	 */
	public function errno(){
		is_object($this->pLink) and $this->_nErrorNO = intval(($this->pLink) ? mysqli_errno($this->pLink) : mysqli_errno());
		return $this->_nErrorNO;
	}
	
	/**
	 * 获取错误信息
	 * @return string
	 */
	public function error(){
		is_object($this->pLink) and $this->_strErrorMessage = (($this->pLink) ? mysqli_error($this->pLink) : mysqli_error());
		return $this->_strErrorMessage;
	}
	
	/**
	 * MySQL 发送一条 SQL 查询（不获取 / 缓存结果）
	 * @param string $strSql
	 * @return boolean
	 */
	public function execute($strSql){
		return mysqli_real_query($this->pLink, $strSql);
	}
	
	/**
	 * 执行Sql语句并返回查询结果
	 * @param string $strSql
	 * @return resource
	 */
	public function query($strSql){
		return mysqli_query($this->pLink, $strSql, $this->nQueryType);
	}
	
	/**
	 * 获取SQL insert 主键ID
	 * @return number
	 */
	public function insert_id(){
		return mysqli_insert_id($this->pLink);
	}
	
	/**
	 * 转义 SQL 语句中使用的字符串中的特殊字符
	 * @param string $strChar 待转义字符串
	 * @return string
	 */
	public function real_escape_string($strChar){
		return mysqli_real_escape_string($this->pLink, $strChar);
	}
	
	/**
	 * 查询结果格式化为数组
	 * @param resource $queryResult 数据查询结果
	 * @return multitype: array/null
	 */
	public function fetch_array($queryResult){
		$arrResult = null;
		($queryResult) and $arrResult = mysqli_fetch_array($queryResult, $this->nResultType);
		return $arrResult;
	}
	
	/**
	 * 从结果集中取得一行作为数字数组
	 * @param resource $queryResult 数据查询结果
	 * @return multitype: array/null
	 */
	public function fetch_row($queryResult){
		$arrResult = null;
		($queryResult) and $arrResult = mysqli_fetch_row($queryResult);
		return $arrResult;
	}
	
	/**
	 * 从结果集中取得一行作为关联数组
	 * @param resource $queryResult 数据查询结果
	 * @return multitype: array/null
	 */
	public function fetch_assoc($queryResult){
		$arrResult = null;
		($queryResult) and $arrResult = mysqli_fetch_assoc($queryResult);
		return $arrResult;
	}
	
	/**
	 * 从结果集中取得列信息并作为对象返回
	 * @param resource $queryResult 数据查询结果
	 * @param number $field_offset 规定从哪个字段开始。0 指示第一个字段。如果未设置，则取回下一个字段
	 * @return object
	 */
	public function fetch_field($queryResult, $field_offset){
		$pResult = null;
		($queryResult) and $pResult = mysqli_fetch_field($queryResult, $field_offset);
		return $pResult;
	}
	
	/**
	 * 取得结果中指定字段的字段名
	 * @param resource $queryResult 数据查询结果
	 * @param number $field_offset 规定从哪个字段开始
	 * @return string
	 */
	public function field_name($queryResult, $field_offset){
		$strResult = '';
		if($queryResult){
			$i=0;
			while($pFied = $queryResult->fetch_field){
				if($i==$field_offset){
					$strResult = $pFied->name;
				}
			}
		}
		return $strResult;
	}
	
	/**
	 * 释放结果内存
	 * @param resource $queryResult 数据查询结果
	 * @return boolean
	 */
	public function free_result($queryResult){
		return mysqli_free_result($queryResult);
	}
	
	/**
	 * 当前查询影响数据行数
	 * @return number
	 */
	public function affected_rows(){
		return mysqli_affected_rows($this->pLink);
	}
	
	/**
	 * 返回最近一条查询的信息
	 * @return string
	 */
	public function query_info(){
		return mysqli_info($this->pLink);
	}
}