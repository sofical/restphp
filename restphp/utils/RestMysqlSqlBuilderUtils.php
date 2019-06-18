<?php
namespace restphp\utils;
/**
 * SQL 封装函数
 * @author sofical
 * @copyright misssofts.com
 * @since 2014-7-9
 */
class RestMysqlSqlBuilderUtils {
    private $pDbLinkCurrent;
    public function __construct($dbLink) {
        $this->pDbLinkCurrent = $dbLink;
    }

    /**
	 * 查询构造
	 * @param string $p_strTable 表名
	 * @param string $p_strOutput 获取列
	 * @param string $p_mxRule 查询条件
	 * @param string $p_strOrder 排序
	 * @param string $p_strLimit 查询范围
	 * @return string
	 */
	public function selectConstruct($p_strTable, $p_strOutput='*', $p_mxRule='', $p_strOrder='', $p_strLimit=''){
		$strRuleSub = '';
		if(is_array($p_mxRule)&&!empty($p_mxRule)){
			$strRuleSub = '';
			foreach($p_mxRule as $strKey=>$mxValue){
				if(is_float($mxValue)||is_integer($mxValue)){
					$strRuleSub .= (''==$strRuleSub) ? " {$strKey}={$mxValue} " : " AND {$strKey}={$mxValue} ";
				}else{
					if(is_array($mxValue)){
						$strRuleSub .= (''==$strRuleSub) ? $mxValue[0] : (" AND " . $mxValue[0]);
					}elseif(is_string($mxValue)){
						if(is_object($this->pDbLinkCurrent)){
							$mxValue = $this->pDbLinkCurrent->real_escape_string($mxValue);
						}else{
							$mxValue = mysql_escape_string($mxValue);
						}
						$strRuleSub .= (''==$strRuleSub) ? " {$strKey}='{$mxValue}' " : " AND {$strKey}='{$mxValue}' ";
					}
				}
			}
		}elseif(is_string($p_mxRule)){
			$strRuleSub = $p_mxRule;
		}
		
		if(''==$strRuleSub) $strSql = "SELECT {$p_strOutput} FROM {$p_strTable} {$p_strOrder} {$p_strLimit}";
		else $strSql = "SELECT {$p_strOutput} FROM {$p_strTable} WHERE {$strRuleSub} {$p_strOrder} {$p_strLimit}";
		return $strSql;
	}
	
	/**
	 * 插入SQL语句构造
	 * @param string $p_strTable 表
	 * @param array $p_arrRule 组合条件
	 * @return string
	 */
	public function insertConstruct($p_strTable, $p_arrRule){
		$strSql = '';
		if(is_array($p_arrRule) && !empty($p_arrRule)){
			$strA = '';
			$strB = '';
			foreach($p_arrRule as $strCol=>$mxValue){
				if(''==$strCol) continue;
				
				$strA .= (''==$strA) ? $strCol : ", {$strCol}";
				
				if(is_float($mxValue) || is_integer($mxValue)){
					$strB .= (''==$strB) ? " {$mxValue} " : " ,{$mxValue} ";
				}else{
					if(is_array($mxValue)){
						$strB .= (''==$strB) ? (' '.$mxValue[0]) : (', '.$mxValue[0]);
					}elseif(is_string($mxValue)){
						if(is_object($this->pDbLinkCurrent)){
							$mxValue = $this->pDbLinkCurrent->real_escape_string($mxValue);
						}else{
							$mxValue = mysql_escape_string($mxValue);
						}
						$strB .= (''==$strB) ? " '{$mxValue}' " : ", '{$mxValue}' ";
					}
				}
			}
			$strSql = "INSERT INTO {$p_strTable} ({$strA}) values ({$strB})";
		}
		return $strSql;
	}
	
	/**
	 * SQL update 语句组合
	 * @param string $p_strTable 表
	 * @param mixed $p_mxUp 更新信息，可以是key-value数组，也可以是string
	 * @param mixed $p_mxRule 更新条件，可以是key-value数组，也可以是string
	 * @return string
	 */
	public function updateConstruct($p_strTable, $p_mxUp, $p_mxRule){
		$strUp = '';
		if(is_array($p_mxUp)){
			if(!empty($p_mxUp)){
				foreach($p_mxUp as $strKey=>$mxValue){
					if(is_float($mxValue) || is_integer($mxValue)){
						$strUp .= (''==$strUp) ? " {$strKey}={$mxValue} " : ", {$strKey}={$mxValue}";
					}else{
						if(is_array($mxValue)){
							$strUp .= (''==$strUp) ? (' '.$mxValue[0]) : (', '.$mxValue[0]);
						}else{
							if(is_object($this->pDbLinkCurrent)){
								$mxValue = $this->pDbLinkCurrent->real_escape_string($mxValue);
							}else{
								$mxValue = mysql_escape_string($mxValue);
							}
							$strUp .= (''==$strUp) ? " {$strKey}='{$mxValue}' " : ", {$strKey}='{$mxValue}' ";
						}
					}
				}
			}
		}elseif(is_string($p_mxUp)){
			$strUp = $p_mxUp;
		}
		
		$strRule = '';
		if(is_array($p_mxRule)){
			if(!empty($p_mxRule)){
				foreach($p_mxRule as $strKey=>$mxValue){
					if(is_float($mxValue) || is_integer($mxValue)){
						$strRule .= (''==$strRule) ? " {$strKey}={$mxValue} " : " AND {$strKey}={$mxValue} ";
					}else{
						if(is_array($mxValue)){
							$strRule .= (''==$strRule) ? (' '.$mxValue[0]) : (' AND '.$mxValue[0]);
						}elseif(is_string($mxValue)){
							if(is_object($this->pDbLinkCurrent)){
								$mxValue = $this->pDbLinkCurrent->real_escape_string($mxValue);
							}else{
								$mxValue = mysql_escape_string($mxValue);
							}
							$strRule .= (''==$strRule) ? " {$strKey}='{$mxValue}' " : " AND {$strKey}='{$mxValue}' ";
						}
					}
				}
			}
		}elseif(is_string($p_mxRule)){
			$strRule = $p_mxRule;
		}
		
		$strSql = (''==$strRule) ? "UPDATE {$p_strTable} SET {$strUp} " : "UPDATE {$p_strTable} SET {$strUp} where {$strRule}";
		return $strSql;
	}
	
	/**
	 * SQL delete 语句组合执行
	 * @param string $p_strTable 表名
	 * @param mixed $p_mxRule
	 * @return string
	 */
	public function deleteConstruct($p_strTable, $p_mxRule){
		$strRule = '';
		if(is_array($p_mxRule)){
			if(!empty($p_mxRule)){
				foreach($p_mxRule as $strKey=>$mxValue){
					if(is_float($mxValue) || is_integer($mxValue)){
						$strRule .= (''==$strRule) ? " {$strKey}={$mxValue} " : " AND {$strKey}={$mxValue} ";
					}else{
						if(is_array($mxValue)){
							$value = $mxValue;
							$strRule .= (''==$strRule) ? (' '.$value[0]) : (' AND '.$value[0]);
						}elseif(is_string($mxValue)){
							if(is_object($this->pDbLinkCurrent)){
								$mxValue = $this->pDbLinkCurrent->real_escape_string($mxValue);
							}else{
								$mxValue = mysql_escape_string($mxValue);
							}
							$strRule .= (''==$strRule) ? " {$strKey}='{$mxValue}' " : " AND {$strKey}='{$mxValue}' ";
						}
							
					}
				}
			}
		}elseif(is_string($p_mxRule)){
			$strRule = $p_mxRule;
		}
		$strSql = (''==$strRule) ? " DELETE FROM {$p_strTable}" : "DELETE FROM {$p_strTable} WHERE {$strRule}";
		return $strSql;
	}
}