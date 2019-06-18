<?php
namespace restphp\driver;
use restphp\exception\RestException;
use restphp\utils\RestMysqlSqlBuilderUtils;
use restphp\i18n\RestLangUtils;


/**
 * PPFK Mysql DB Driver
 * @author sofical
 * @copyright misssofts.com
 * @since 2014-7-6
 */
class RestDbMysql {
	/**
	 * 当前数据库操作对
	 * @var pp_mysqli
	 */
	public $db;
	
	/**
	 * 当前数据库链接
	 * @var pp_mysqli::pLink
	 */
	public $link;
	
	/**
	 * 当前会话的数据库链接池
	 */
	public static $arrDbCache = array();
	
	/**
	 * MySql数据库链接方式
	 * @var array
	 */
	public static $arrDbLinkType = array(
			0	=> 'restphp\driver\RestDBDriverMysql',
			1	=> 'restphp\driver\RestDBDriverMysqli'
    );
	
	/**
	 * 当前运行数据库信息
	 * @var array
	 */
	public $arrDbInfo = array();

    /**
     * 数据库配置
     * @var array|mixed
     */
	private static $arrDBCnf = array();
	
	/**
	 * @param string $dbTag：数据库配置标识
	 */
	function __construct($dbTag){
	    self::$arrDBCnf = isset($GLOBALS['_DB_MYSQL']) ? $GLOBALS['_DB_MYSQL']: array();
		$this->arrDbInfo = isset(self::$arrDBCnf[$dbTag]) ? self::$arrDBCnf[$dbTag] : array();
		$this->_link();
	}
	
	/**
	 * 链接
	 */
	private function _link(){
		if(empty($this->arrDbInfo)){
		    throw new RestException("[REST_DB_CONFIG_NOT_EXISTS]");
		}else{
			$strDbTag = md5(serialize($this->arrDbInfo));
				
			$bRlink = true;
			if(isset(self::$arrDbCache[$strDbTag]) && is_object(self::$arrDbCache[$strDbTag])){
				$bRlink = false;
				$this->db = self::$arrDbCache[$strDbTag];
			}
				
			if($bRlink){
				//使用数据库类型
				$nDbType = isset($this->arrDbInfo['dbtype']) ? intval($this->arrDbInfo['dbtype']) : 0;
		
				//创建数据库对象
				$this->db = new self::$arrDbLinkType[$nDbType]();
		
				//初始参数设置
				$this->db->nConnectType = isset($this->arrDbInfo['linktype']) ? $this->arrDbInfo['linktype'] : 0;
				$this->db->strCharset = isset($this->arrDbInfo['charset']) ? $this->arrDbInfo['charset'] : 'UTF8';
				if(1==$nDbType){
					isset($this->arrDbInfo['querytype']) and $this->db->nQueryType = $this->arrDbInfo['querytype'];
					(1==$this->db->nConnectType && isset($this->arrDbInfo['timeout'])) and $this->db->nConnectTimeout = 5;
				}
				isset($this->arrDbInfo['fetchtype']) and $this->db->nResultType = intval($this->arrDbInfo['fetchtype']);
		
				//端口初始化
				$nDbPort = isset($this->arrDbInfo['dbport']) ? $this->arrDbInfo['dbport'] : 3306;
				if(!$this->db->db_connect($this->arrDbInfo['dbuser'], $this->arrDbInfo['dbpass'], $this->arrDbInfo['dbname'], $this->arrDbInfo['dbhost'], $nDbPort)){
					echo $this->db->errno() . ":" . RestLangUtils::replace($this->db->error());
					exit();
				}
			}
		}
		
		$this->link = $this->db->pLink;
	}
	
	/**
	 * 关闭所有数据库链接
	 * @param boolean $bAll 是否关闭所有链接
	 * @return boolean
	 */
	public function close($bAll=false){
		return $this->db->close($bAll);
	}
	
	public function __call($method, $argument = ''){
		try{
			if($argument){
				$result = call_user_func_array(array($this->current_redis_handler(), $method), $argument);
			}else{
				$result = call_user_func(array($this->current_redis_handler(), $method));
			}
		}catch(Exception $e){
			$result = NULL;
		}
		
		return $result;
	}
	
	/**
	 * 执行SQL语句
	 * @param string $strSql SQL语句
	 * @return boolean
	 */
	public function execute($strSql){
		$mxRes = $this->db->execute($strSql);
		$mxRes or $this->_debugMsgAdd($strSql);
		return $mxRes;
	}
	
	/**
	 * 执行Sql并获取返回结果
	 * @param strng $strSql SQL语句
	 * @return resource
	 */
	public function query($strSql){
		$mxRes = $this->db->query($strSql);
		$mxRes or $this->_debugMsgAdd($strSql);
		return $mxRes;
	}
	
	/**
	 * 获取SQL insert 主键ID
	 * @return number
	 */
	public function insert_id(){
		return $this->db->insert_id();
	}
	
	/**
	 * 查询并获取数组结果第一行
	 * @param string $strSql SQL语句
	 * @return array 查询结果
	 */
	public function result($strSql){
		$arrResult = array();
		$rData = $this->query($strSql);
		($rData) and $arrResult = $this->db->fetch_array($rData);
		return $arrResult;
	}
	
	/**
	 * 查询并获得数组结果
	 * @param string $strSqlSQL语句
	 * @return array
	 */
	public function results($strSql){
		$arrResult = array();
		$rData = $this->query($strSql);
		if($rData){
			while($arrRs = $this->db->fetch_array($rData)){
				$arrResult[] = $arrRs;
			}
		}
		return $arrResult;
	}
	
	/**
	 * 启动事务
	 */
	public function startTran(){
		$this->query('START TRANSACTION');
	}
	
	/**
	 * 提交事务
	 */
	public function commitTran(){
		$this->query('COMMIT');
	}
	
	/**
	 * 事务回滚
	 */
	public function rollBackTran(){
		$this->query('ROLLBACK');
	}
	
	/**
	 * 数据操作影响列
	 * @return number
	 */
	public function affected_rows(){
		return $this->db->affected_rows();
	}
	
	/**
	 * 释放查询结果
	 * @param resource $p_oQuery
	 */
	public function free($rData){
		return $this->db->free_result($rData);
	}
	
	/**
	 * 字符串安全转换
	 * @param string $p_strColumnValue
	 * @return string
	 */
	public function real_escape_string($p_strColumnValue){
		return $this->db->real_escape_string($p_strColumnValue);
	}
	
	/**
	 * SQL Debug Add
	 */
	private function _debugMsgAdd($strSql){
		//pp_static::$strDebugMessage.='<br /><br />MySQL Error: Error NO. - '.$this->db->errno().", Description - ".$this->db->error().'<br /> On execute SQL:'.$strSql;
	}
	
	
	/***
	 * =======================================================
	 * ↓↓↓↓↓↓↓↓		以下为快捷封装	↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
	 * =======================================================
	 */
	/**
	 * SQL 组合对象
	 * @var RestMysqlSqlBuilderUtils.
	 */
	public static $pSqlBuild;
	/**
	 * SQL 组合对象
	 * @return pp_sqlbuild
     * @return RestMysqlSqlBuilderUtils.
	 */
	public function pSqlBuild(){
		is_object(self::$pSqlBuild) or self::$pSqlBuild = new RestMysqlSqlBuilderUtils($this->link);
		return self::$pSqlBuild;
	}
	
	/**
	 * 查询组合
	 * @param array $p_arrParams 组合参数解释： 
	 * 		table-表名，
	 * 		output-获取列,
	 * 		rule-Where部分条件,
	 * 		order-排序,limit查询范围限制,
	 * 		cahcetype-缓存方式（0-文件缓存，1-redis, 2-memcache, 3-mongo），默认为0
	 *		timeout-缓存过期时时：0表示使用永久缓存，小于0表示不使用缓存，正数表示缓存时长，单位秒，默认不使用缓存
	 *		debug-是否断点输出SQL
	 * @return array
	 */
	public function select($p_arrParams){
		$strTable	= isset($p_arrParams['table']) ? $p_arrParams['table'] : '';
		$strOutput	= isset($p_arrParams['output']) ? $p_arrParams['output'] : ' * ';
		$mxRule		= isset($p_arrParams['rule']) ? $p_arrParams['rule'] : '';
		$strOrder	= isset($p_arrParams['order']) ? $p_arrParams['order'] : '';
		$strLimit	= isset($p_arrParams['limit']) ? $p_arrParams['limit'] : '';
		$nTimeout	= isset($p_arrParams['timeout']) ? intval($p_arrParams['timeout']) : -1;
		$bDebug		= isset($p_arrParams['debug']) ? $p_arrParams['debug'] : false;
		$nCacheType	= isset($p_arrParams['cahcetype']) ? intval($p_arrParams['cahcetype']) : 0;

		$strSql = $this->pSqlBuild()->selectConstruct($strTable, $strOutput, $mxRule, $strOrder, $strLimit);
		($bDebug) and die($strSql);
		if(-1<$nTimeout){
			$arrInfo = $this->select_cache($strSql, $nCacheType, $nTimeout);
		}else{
			$arrInfo = $this->results($strSql);
		}
		
		return $arrInfo;
	}
	
	/**
	 * 带缓存的SQL查询
	 * @param string $p_strSql SQL查询语句
	 * @param integer $p_nCacheType 缓存类型：（0-文件缓存，1-redis, 2-memcache, 3-mongodb），默认为0
	 * @param integer $p_nTimeout 超时时间：0表示使用永久缓存，小于0表示不使用缓存，正数表示缓存时长，单位秒，默认不使用缓存
	 * @return array
	 */
	public function select_cache($p_strSql, $p_nCacheType, $p_nTimeout){
		$arrCacheFuncs = array('_select_cache_file', '_select_cache_redis', '_select_cache_memcache', '_select_cache_mongo');
		$p_nCacheType = intval($p_nCacheType);
		
		return isset($arrCacheFuncs[$p_nCacheType]) ? $this->$arrCacheFuncs[$p_nCacheType]($p_strSql, intval($p_nTimeout)) : $this->results($p_strSql);
	}
	
	/**
	 * 查询SQL文件缓存
	 * @param string $strSql SQL语句
	 * @param integer $p_nTimeout 超时时间，为0表示永久缓存
	 * @return array 查询结果
	 */
	private function _select_cache_file($p_strSql, $p_nTimeout){
		$arrInfo = array();
		
		$arrObj = pp_static::$arrRunObjConfig[PPFK_RUN_OBJ];
		$strLib = $arrObj['app_cache'].DIRECTORY_SEPARATOR.(isset($arrObj['sql_file_cache_path']) ? $arrObj['sql_file_cache_path'] : '_sql_index').DIRECTORY_SEPARATOR;
		$strSqlCacheFile = $strLib.md5($p_strSql).'.cache';
		unset($arrObj);
		unset($strLib);
		
		$bRecache = true;
		0===$p_nTimeout and $bRecache = false;
		
		if($bRecache && file_exists($strSqlCacheFile)){
			$nFileTime = filemtime($strSqlCacheFile);
			
			$nTime = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
			($nTime-$nFileTime)<=$p_nTimeout and $bRecache = false;
		}
		
		if($bRecache){
			$arrInfo = $this->results($p_strSql);
			file_put_contents($strSqlCacheFile, serialize($arrInfo));
		}else{
			$arrInfo = unserialize(file_get_contents($strSqlCacheFile));
		}
		
		return $arrInfo;
	}
	
	/**
	 * 带Redis缓存的SQL查询
	 * @param string $strSql SQL语句
	 * @param integer $p_nTimeout 超时时间
	 * @return array
	 */
	private function _select_cache_redis($strSql, $p_nTimeout){
		$arrInfo = array();
		
		$arrObj = pp_static::$arrRunObjConfig[PPFK_RUN_OBJ];
		$arrConf = isset($arrObj['sql_redis_cache']) ? $arrObj['sql_redis_cache'] : array();
		unset($arrObj);
		
		if(empty($arrConf)){
			_pp_error_debug('-99', 'Undefined Redis server information！', __FILE__.':'.__CLASS__.'::'.__FUNCTION__, __LINE__);
		}else{
			pp_static::$arrRdsCnf['_sql_redis_cache'] = $arrConf;
			$strKey = md5($strSql);
			
			$pRedis = new pp_redis('_sql_redis_cache');
			$nDb = isset($arrConf['db']) ? intval($arrConf['db']) : 0;
			$pRedis->redis->select($nDb);
			$mxInfoCache = $pRedis->get($strKey);
			if(empty($mxInfoCache)){
				$arrInfo = $this->results($strSql);
				$pRedis->set($strKey, serialize($arrInfo), $p_nTimeout);
			}else{
				$arrInfo = unserialize($mxInfoCache);
			}
			unset($pRedis);
		}
		
		return $arrInfo;
	}
	
	/**
	 * 带Memcache缓存的SQL查询
	 * @param string $strSql SQL语句
	 * @param integer $p_nTimeout 超时时间
	 * @return array
	 */
	private function _select_cache_memcache($p_strSql, $p_nTimeout){
		$arrInfo = array();
		
		$arrObj = pp_static::$arrRunObjConfig[PPFK_RUN_OBJ];
		$arrConf = isset($arrObj['sql_memcache_cache']) ? $arrObj['sql_memcache_cache'] : array();
		unset($arrObj);
		
		if(empty($arrConf)){
			_pp_error_debug('-99', 'Undefined Memcached server information！', __FILE__.':'.__CLASS__.'::'.__FUNCTION__, __LINE__);
		}else{
			pp_static::$arrMemCnf['_sql_memcache_cache'] = $arrConf;
			$strKey = md5($p_strSql);
			
			$pMemcache = new pp_memcache('_sql_memcache_cache');
			$mxInfoCache = $pMemcache->get($strKey);
			if(empty($mxInfoCache)){
				$arrInfo = $this->results($p_strSql);
				if(0===$p_nTimeout){
					$pMemcache->memcache->set($strKey, serialize($arrInfo));
				}else{
					$pMemcache->set($strKey, serialize($arrInfo), MEMCACHE_COMPRESSED, $p_nTimeout);
				}
			}else{
				$arrInfo = unserialize($mxInfoCache);
			}
			unset($pMemcache);
		}
		
		return $arrInfo;
	}
	
	/**
	 * 带Mongo缓存的SQL查询
	 * @param string $strSql SQL语句
	 * @param integer $p_nTimeout 超时时间
	 * @return array
	 */
	private function _select_cache_mongo($p_strSql, $p_nTimeout){
		$arrInfo = array();
		
		$arrObj = pp_static::$arrRunObjConfig[PPFK_RUN_OBJ];
		$strConf = isset($arrObj['sql_memcache_cache']) ? trim($arrObj['sql_mongodb_cache']) : '';
		unset($arrObj);
		
		if(''===$strConf){
			_pp_error_debug('-99', 'Undefined Mongo server information！', __FILE__.':'.__CLASS__.'::'.__FUNCTION__, __LINE__);
		}else{
			$strDb = '_SQL_Cache_DB';
			$strRule = md5($p_strSql);
			$strTable = '_SQL_Cache_TAB_'.substr($strRule, 0, 16);
			$pMongo = new pp_mongo($strConf);
			if($pMongo->bFlag){
				$pMongo->connect();
				$pMongo->selectDb($strDb);
				$arrCacheInfo = $pMongo->findOne($strTable, array('_md5'=>$strRule));
				
				$bRecache = true;
				$bAdd = true;
				$nTime = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
				if(!empty($arrCacheInfo)){
					$bAdd = false;
					$nCacheTime = $arrCacheInfo['_time'];
					
					if(0>=$p_nTimeout || ($nTime-$nCacheTime)<=$p_nTimeout){
						$bRecache = false;
						$arrInfo = unserialize($arrCacheInfo['_info']);
					}
				}
				
				if($bRecache){
					$arrInfo = $this->results($p_strSql);
					
					if($bAdd){
						$pMongo->insert($strTable, array(
								'_md5'	=> $strRule,
								'_time'	=> $nTime,
								'_info'	=> serialize($arrInfo)
						));
					}else{
						$pMongo->update($strTable, array('_md5'=>$strRule), array(
								'_md5'	=> $strRule,
								'_time'	=> $nTime,
								'_info'	=> serialize($arrInfo)
						));
					}
				}
			}else{
				_pp_error_debug('-98', 'Mongo server refused！'.$pMongo->error, __FILE__.':'.__CLASS__.'::'.__FUNCTION__, __LINE__);
			}
			
			unset($pMongo);
		}
		
		return $arrInfo;
	}
	
	/**
	 * 插入数据
	 * @param string $p_strTable 表名
	 * @param array $p_arrRule 组合条件
	 * @param boolean $p_bDebug 是否断点调试
	 * @return boolean
	 */
	public function insert($p_strTable, $p_arrRule, $p_bDebug=false){
		$strSql = $this->pSqlBuild()->insertConstruct($p_strTable, $p_arrRule);
		($p_bDebug) and die($strSql);
		return $this->execute($strSql);
	}
	
	/**
	 * 更新数据
	 * @param string $p_strTable 表名
	 * @param mixed $p_mxUp 更新数据
	 * @param mixed $p_mxRule 更新条件
	 * @param boolean $p_bDebug 是否断点调试
	 * @return boolean
	 */
	public function update($p_strTable, $p_mxUp, $p_mxRule, $p_bDebug=false){
		$strSql = $this->pSqlBuild()->updateConstruct($p_strTable, $p_mxUp, $p_mxRule);
		($p_bDebug) and die($strSql);
		return $this->execute($strSql);
	}
	
	/**
	 * 删除数据
	 * @param string $p_strTable 表名
	 * @param mixed $p_mxRule 删除条件
	 * @param boolean $p_bDebug 是否断点调试
	 * @return boolean
	 */
	public function delete($p_strTable, $p_mxRule, $p_bDebug=false){
		$strSql = $this->pSqlBuild()->deleteConstruct($p_strTable, $p_mxRule);
		($p_bDebug) and die($strSql);
		return $this->execute($strSql);
	}
}