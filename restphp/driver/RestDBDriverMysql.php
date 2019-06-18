<?php
namespace restphp\driver;
/**
 * Mysql数据库操作封装
 * @author sofical
 * @copyright misssofts.com
 * @since 2014-7-5
 */
class RestDBDriverMysql {
	/**
	 * 链接方式：0-mysql_connect，1-mysql_pconnect
	 * @var number
	 */
	public $nConnectType = 0;
	
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
	 * fetch_array返回结果样式设置：MYSQL_BOTH(3)-同时产生关联和数字数组，MYSQL_NUM(2)-数字数组，MYSQL_ASSOC（1）-关联数组
	 * @var number
	 */
	public $nResultType = MYSQL_BOTH;
	
	/**
	 * 错误编号
	 * @var number
	 */
	private $_nErrorNO = -100;
	
	/**
	 * 错误描述
	 * @var string
	 */
	private $_strErrorMessage = '_PPFK_DB_UNEXCEPTION_';
	
	/**
	 * 进程链接缓冲池
	 * @var array
	 */
	private static $_arrCacheLinks = array();
	
	/**
	 * 创建数据库链接
	 * @param string $dbUser 用户名
	 * @param string $dbPass 认证密码
	 * @param string $dbName 默认链接数据库
	 * @param string $dbHost 主机地址
	 * @param number $dbPort 数据库端口
	 * @return boolean
	 */
	public function db_connect($dbUser, $dbPass, $dbName='information_schema', $dbHost='localhost', $dbPort=3306){
		$bFlag = false;
		
		$strLinkTag = base64_encode("{$dbUser}:{$dbPass}:{$dbHost}:{$dbPort}");
		if(isset(self::$_arrCacheLinks[$strLinkTag])){
			$this->pLink = self::$_arrCacheLinks[$strLinkTag];
		}else{
			$dbServer = "{$dbHost}:{$dbPort}";
			if(0===$this->nConnectType){
				$this->_mysql_connect($dbServer, $dbUser, $dbPass);
			}elseif(1==$this->nConnectType){
				$this->_mysql_pconnect($dbServer, $dbUser, $dbPass);
			}else{
				$this->_nErrorNO = -99;
				$this->_strErrorMessage = '_PPFK_DB_UNSUPPORT_DB_LINK_';
			}
			
			if($this->pLink) $this->charset($this->strCharset);
			self::$_arrCacheLinks[$strLinkTag] = $this->pLink;
		}
		
		if($this->pLink){
			$this->select_db($dbName) and $bFlag = true;
		}else{
			$this->_nErrorNO = -98;
			$this->_strErrorMessage = '_PPFK_DB_HOST_REFUSED_';
		}
		
		return $bFlag;
	}
	
	private function _mysql_connect($dbServer, $dbUser, $dbPass){
		$this->pLink = mysql_connect($dbServer, $dbUser, $dbPass) or $this->pLink=null;
	}
	
	private function _mysql_pconnect($dbServer, $dbUser, $dbPass){
		$this->pLink = mysql_pconnect($dbServer, $dbUser, $dbPass) or $this->pLink=null;
	}
	
	/**
	 * 数据库选择
	 * @param string $dbName 数据库名
	 * @return boolean
	 */
	public function select_db($dbName){
		return mysql_select_db($dbName, $this->pLink);
	}
	
	/**
	 * 关闭数据库链接
	 * @param boolean $bAll 是否关闭所有数据库链接
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

		return true;
	}
	
	/**
	 * 设置数据库编码
	 * @param string $charset 编码
	 */
	public function charset($charset){
		return $this->query("SET names {$charset}");
	}
	
	/**
	 * 获取错误编码
	 * @return number
	 */
	public function errno(){
		is_object($this->pLink) and $this->_nErrorNO = intval(($this->pLink) ? mysql_errno($this->pLink) : mysql_errno());
		return $this->_nErrorNO;
	}
	
	/**
	 * 获取错误信息
	 * @return string
	 */
	public function error(){
		is_object($this->pLink) and $this->_strErrorMessage = (($this->pLink) ? mysql_error($this->pLink) : mysql_error());
		return $this->_strErrorMessage;
	}
	
	/**
	 * MySQL 发送一条 SQL 查询（不获取 / 缓存结果）
	 * @param string $strSql
	 * @return resource
	 */
	public function execute($strSql){
		return mysql_unbuffered_query($strSql, $this->pLink);
	}
	
	/**
	 * 执行Sql语句
	 * @param string $strSql
	 * @return resource
	 */
	public function query($strSql){
		return mysql_query($strSql, $this->pLink);
	}
	
	/**
	 * 获取SQL insert 主键ID
	 * @return number
	 */
	public function insert_id(){
		return mysql_insert_id($this->pLink);
	}
	
	/**
	 * 转义 SQL 语句中使用的字符串中的特殊字符
	 * @param string $strChar 待转义字符串
	 * @return string
	 */
	public function real_escape_string($strChar){
		return mysql_real_escape_string($strChar, $this->pLink);
	}
	
	/**
	 * 查询结果格式化为数组
	 * @param resource $queryResult 数据查询结果
	 * @return multitype: array/null
	 */
	public function fetch_array($queryResult){
		$arrResult = null;
		is_resource($queryResult) and $arrResult = mysql_fetch_array($queryResult, $this->nResultType);
		return $arrResult;
	}
	
	/**
	 * 从结果集中取得一行作为数字数组
	 * @param resource $queryResult 数据查询结果
	 * @return multitype: array/null
	 */
	public function fetch_row($queryResult){
		$arrResult = null;
		is_resource($queryResult) and $arrResult = mysql_fetch_row($queryResult);
		return $arrResult;
	}
	
	/**
	 * 从结果集中取得一行作为关联数组
	 * @param resource $queryResult 数据查询结果
	 * @return multitype: array/null
	 */
	public function fetch_assoc($queryResult){
		$arrResult = null;
		is_resource($queryResult) and $arrResult = mysql_fetch_assoc($queryResult);
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
		is_resource($queryResult) and $pResult = mysql_fetch_field($queryResult, $field_offset);
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
		is_resource($queryResult) and $strResult = mysql_field_name($queryResult, $field_offset);
		return $strResult;
	}
	
	/**
	 * 释放结果内存
	 * @param resource $queryResult 数据查询结果
	 * @return boolean
	 */
	public function free_result($queryResult){
		return mysql_free_result($queryResult);
	}
	
	/**
	 * 当前查询影响数据行数
	 * @return number
	 */
	public function affected_rows(){
		return mysql_affected_rows($this->pLink);
	}
	
	/**
	 * 返回最近一条查询的信息
	 * @return string
	 */
	public function query_info(){
		return mysql_info($this->pLink);
	}
}