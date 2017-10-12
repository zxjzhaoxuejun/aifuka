<?php
/**
 * 数据库类文件
 *  此数据库抽象类 来自 Dedecms
 * @version        $Id: sql.class.php 1 10:33 2010年7月6日Z $
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */


include(incpath."web_conn.php");

$db = new $cfg["mydb_type"]();


/**
 *
 * Enter description here ...
 * @author guoho
 *
 */

class Mysql
{
	var $linkID;
	var $dbHost;
	var $dbUser;
	var $dbPwd;
	var $dbName;
	var $dbPrefix;
	var $result;
	var $queryString;
	var $isClose;
	var $safeCheck;
	var $prefix;

	//用外部定义的变量初始类，并连接数据库
	function __construct($pconnect=false,$nconnect=true)
	{
		$this->isClose = false;
		$this->safeCheck = false;
		$this->prefix="#@__";
		if($nconnect){$this->Init($pconnect);}
	}

	function Init($pconnect=false)        //初始化
	{
		global $cfg;
		$this->linkID = 0;
		$this->queryString = '';
		if($cfg["mydb_local"]){
			$this->dbHost   =  "localhost";
			$this->dbUser   =  "root";
			$this->dbPwd    =  "";
			$this->dbName   =  "tdmphpcms";
			$this->dbPrefix =  "alz_";
		}else{
			$this->dbHost   =  $cfg["mydb_dbhost"];
			$this->dbUser   =  $cfg["mydb_dbuser"];
			$this->dbPwd    =  $cfg["mydb_dbpwd"];
			$this->dbName   =  $cfg["mydb_dbname"];
			$this->dbPrefix =  $cfg["mydb_dbprefix"];
		}
		$this->result["me"] = 0;      //'me' 为记录集游标，用于区分不同的查询
		$this->Open($pconnect);
	}

	//用指定参数初始数据库信息
	function SetSource($host,$username,$pwd,$dbname,$dbprefix="")
	{
		global $cfg;
		$this->dbHost = $host;
		$this->dbUser = $username;
		$this->dbPwd = $pwd;
		$this->dbName = $dbname;
		$this->dbPrefix = $dbprefix!=""?$dbprefix:$cfg["dbprefix"];
		$this->result["me"] = 0;
	}

	//连接数据库
	function Open($pconnect=false)
	{
		global $db;
		if($db&&!$db->isClose){
			$this->linkID=$db->linkID;
		}else{
			if(!$pconnect){
				$this->linkID = @mysql_connect($this->dbHost,$this->dbUser,$this->dbPwd);
			}else{
				$this->linkID = @mysql_pconnect($this->dbHost,$this->dbUser,$this->dbPwd);
			}
		}

		//处理错误，成功连接则选择数据库
		if(!$this->linkID){
			$this->DisplayError("错误警告：<font color='red'>连接数据库失败，可能数据库密码不对或数据库服务器出错！<br />数据库配置文件：web/include/web_conn.php</font>");
			exit();
		}
		@mysql_select_db($this->dbName);
		mysql_query("set names 'utf8'");
		return true;
	}

	//设置SQL语句，会自动把SQL语句里的#@__替换为$this->dbPrefix(在配置文件中为$cfg_dbprefix)
	function SetQuery($sql)
	{
		$prefix=$this->prefix;
		$sql = str_replace($prefix,$this->dbPrefix,$sql);
		$this->queryString = $sql;
		return $sql;
	}

	//为了防止采集等需要较长运行时间的程序超时，在运行这类程序时设置系统等待和交互时间
	function SetLongLink()
	{
		@mysql_query("SET interactive_timeout=3600, wait_timeout=3600 ;", $this->linkID);
	}

	/**
	 *
	 * 执行SQL语句
	 * @param $sql
	 */
	function execute($sql='')
	{
		if($db->isClose){
			$this->Open();
			$db->isClose = false;
		}
		if(!empty($sql)){
			$this->SetQuery($sql);
		}
               
		if($this->safeCheck)
		CheckSql($this->queryString,'update');
              
		return mysql_query($this->queryString,$this->linkID);
	}

	function getNums($sql=''){
		if($db->isClose){
			$this->Open(false);$db->isClose = false;
		}
		if(!empty($sql)){
			$this->SetQuery($sql);
		}
		mysql_query($this->queryString,$this->linkID);
		return mysql_affected_rows($this->linkID);
	}

	//执行一个不返回结果的SQL语句
	function dosql($sql='',$id="me"){
		if($db->isClose){$this->Open(false);$db->isClose = false;}
		if(!empty($sql)){$this->SetQuery($sql);}

		if($this->safeCheck){CheckSql($this->queryString);}    //SQL语句安全检查
		//$t1 = execTime();
		$this->result[$id] = mysql_query($this->queryString,$this->linkID);
		//$queryTime = execTime()-$t1;
		//if($queryTime>0.05){echo $this->queryString."--{$queryTime}<hr />\r\n";}
		if($this->result[$id]===false){$this->DisplayError(mysql_error()." <br />Error sql: <font color='red'>".$this->queryString."</font>");}
	}

	function getvalue($sql,$keystr)
	{
		$restr=$this->GetOne($sql);
		return $restr[$keystr];
	}

	//执行一个SQL语句,返回前一条记录或仅返回一条记录
	function GetOne($sql='',$acctype=MYSQL_ASSOC)

	{
		if($db->isClose){$this->Open(false);$db->isClose = false;}
		if(!empty($sql)){
			$this->SetQuery($sql);
		}
		$this->dosql($this->queryString,"one");
		$arr = $this->GetArray("one",$acctype);
		if(!is_array($arr)){
			return '';
		}else{
			@mysql_free_result($this->result["one"]);   //mysql_free_result(data)释放结果内存。
			return($arr);
		}
	}

	function nums($id="me")
	{
		if($this->result[$id]==0){
			return -1;
		}else{
			return mysql_num_rows($this->result[$id]);  //返回结果集中行的数目。
		}
	}

	function num($sql="",$id="me")
	{
		if(!empty($sql)){$this->SetQuery($sql);}
		$this->dosql($this->queryString,$id);
		return mysql_num_rows($this->result[$id]);
	}

	function GetLastID(){return mysql_insert_id($this->linkID);}  //mysql_insert_id() 函数返回上一步 INSERT 操作产生的 ID。

	function FreeResult($id="me"){
		@mysql_free_result($this->result[$id]);
	}

	function FreeResultAll()
	{
		if(!is_array($this->result)){return '';}
		foreach($this->result as $kk => $vv){if($vv){@mysql_free_result($vv);}}
	}

	//返回当前的一条记录并把游标移向下一记录
	function GetArray($id="me",$acctype=MYSQL_ASSOC)
	{
		if($this->result[$id]==0){
			return false;
		}else{
			return mysql_fetch_array($this->result[$id],$acctype);  //从结果集中取得一行作为关联数组(MYSQL_ASSOC)
		}
	}

	function GetObject($id="me")
	{
		if($this->result[$id]==0){
			return false;
		}else{
			return mysql_fetch_object($this->result[$id]);  //mysql_fetch_object() 函数从结果集中取得一行作为对象。
		}
	}

	//检测是否存在某数据表
	function IsTable($tbname)
	{
		$this->result[0] = mysql_query("SHOW TABLES FROM ".$this->dbName);  // mysql_list_tables列出 数据库中的表。
		while ($row = mysql_fetch_array($this->result[0]))
		{
			if(strtolower($row[0])==strtolower($this->dbPrefix.$tbname))
			{
				mysql_freeresult($this->result[0]);
				return true;
			}
		}
		mysql_freeresult($this->result[0]);
		return false;
	}

	/**
	 * 
	 * 获取 MySQL版本。
	 * @param unknown_type $isformat
	 */
	function GetVersion($isformat=true)
	{
		global $db;
		if($db->isClose){$this->Open(false);$db->isClose = false;}
		$rs = mysql_query("SELECT VERSION()",$this->linkID);
		$row = mysql_fetch_array($rs);
		$temp = $row[0];
		mysql_free_result($rs);
		if($isformat)
		{
			$temps = explode(".",trim($temp));
			$temp = number_format($temps[0].".".$temps[1],2);     //number_format(number,decimals) number必需。要格式化的数字。如果未设置其他参数，则数字会被格式化为不带小数点且以逗号 (,) 作为分隔符。decimals可选。规定多少个小数。如果设置了该参数，则使用点号 (.) 作为小数点来格式化数字。
		}
		return $temp;
	}

    /**
     * 
     * 取得数据表中的字段。
     * @param $dbname
     */
	function getDatabaseIdstr($dbname)
	{
		//echo(" $dbname ");
		$fields = mysql_list_fields($this->dbName,$this->dbPrefix.$dbname,$this->linkID);
		$full_tablename = $this->dbPrefix.$dbname;
		//列出 MySQL 结果中的字段。
		
		$columns = $this->GetOne("select count(*) as counts from Information_schema.columns  
						where table_schema='{$this->dbName}' and table_Name = '$full_tablename';");
		//print_r($columns);exit;
		$columns = $columns['counts'];
		//$columns=mysql_num_fields($fields);    //取得结果集中字段的数目
		//echo(" $columns ");
		$idselect="";
		for($i=0;$i<$columns;$i++){
			$id=mysql_field_name($fields,$i); //mysql_field_name(data,field_offset)取得结果中指定字段的字段名。field_offset 指示从哪个字段开始返回。0 指示第一个字段
			//echo("$i<$columns $fields , $i = $id|||");if($i=5)exit();
			if($id!="id"&&$id!="lanstr"){$idselect.=$id.",";}
		}
		
		$idselect=trim($idselect,",");
		//exit();
		return $idselect;
	}

	//获取字段详细信息
	function GetFieldObject($id="me")
	{
		return mysql_fetch_field($this->result[$id]);   //从结果集中取得列信息并作为对象返回。
	}

	function DisplayError($msg){
		global $cfg;
		$emsg = "<div><h3>".$cfg["version"]." Error Warning!</h3>\r\n";
		$emsg .= "<div style='line-helght:160%;font-size:14px;color:green'>\r\n";
		$emsg .= "<div style='color:blue'>Error page: <font color='red'>".$this->GetCurUrl()."</font></div>\r\n";
		$emsg .= "<div>Error infos: {$msg}</div>\r\n";
		$emsg .= "</div></div>\r\n";
		echo $emsg;
	}

	//获得当前的脚本网址
	function GetCurUrl()
	{
		if(!empty($_SERVER["REQUEST_URI"])){
			$scriptName = $_SERVER["REQUEST_URI"];
			$nowurl = $scriptName;
		}else{
			$scriptName = $_SERVER["PHP_SELF"];
			if(empty($_SERVER["QUERY_STRING"])) {
				$nowurl = $scriptName;
			}else{
				$nowurl = $scriptName."?".$_SERVER["QUERY_STRING"];
			}
		}
		return $nowurl;
	}

	function err(){return mysql_error();}
}


/**
 *
 * SQLite 数据库抽象类
 * @author guoho
 *
 */
class Sqlite
{
	var $prefix;
	var $linkID;
	var $dbPrefix;
	var $result;
	var $queryString;

	function __construct()
	{
		$this->prefix="#@__";
		$this->dbPrefix =  "alz_";
		$this->linkID = sqlite_open(webroot."#tdm.db") or die ("ERROR: Cannot open database");
	}

	function SetQuery($sql)
	{
		$prefix=$this->prefix;
		$sql = str_replace($prefix,$this->dbPrefix,$sql);
		$sql = str_replace("\\\"","\"",$sql); //str_replace函数间的在后面的之中替换前面的
		$this->queryString = $sql;
		return $sql;
	}

	function SetLongLink()
	{
		//@sqlite_query("SET interactive_timeout=3600, wait_timeout=3600 ;", $this->linkID);
	}

	function execute($sql='')
	{
		if(!empty($sql)){$this->SetQuery($sql);}
		if($this->safeCheck)CheckSql($this->queryString,'update');
		return sqlite_unbuffered_query($this->linkID,$this->queryString);
	}

	function getNums($sql=''){
		if(!empty($sql)){$this->SetQuery($sql);}
		sqlite_query($this->linkID,$this->queryString);
		return sqlite_changes($this->linkID);
	}

	function dosql($sql='',$id="me"){
		if(!empty($sql)){
			$this->SetQuery($sql);
		}
		if($this->safeCheck){
			CheckSql($this->queryString);
		}
		$this->result[$id] = sqlite_query($this->linkID,$this->queryString);
		if($this->result[$id]===false){$this->DisplayError(sqlite_last_error($this->linkID)." <br />Error sql: <font color='red'>".$this->queryString."</font>");}
	}

	function getvalue($sql,$keystr)
	{
		$restr=$this->GetOne($sql);
		return $restr[$keystr];
	}

	function GetOne($sql='',$acctype=MYSQL_ASSOC)
	{
		if(!empty($sql)){
			if(!eregi("limit",$sql)) $this->SetQuery(eregi_replace("[,;]$",'',trim($sql))." limit 0,1;");
			else $this->SetQuery($sql);
		}
		$this->dosql($this->queryString,"one");
		$arr = $this->GetArray("one",$acctype);
		return $arr;
	}

	function nums($id="me")
	{
		if($this->result[$id]==0){
			return -1;
		}else{
			return sqlite_num_rows($this->result[$id]);
		}
	}

	function num($sql="",$id="me")
	{
		if(!empty($sql)){$this->SetQuery($sql);}
		$this->dosql($this->queryString,$id);
		return sqlite_num_rows($this->result[$id]);
	}

	function GetLastID(){return sqlite_last_insert_rowid($this->linkID);}

	function FreeResult($id="me"){sqlite_query($this->linkID,"VACUUM");}

	function FreeResultAll(){sqlite_query($this->linkID,"VACUUM");}

	function GetArray($id="me",$acctype=MYSQL_ASSOC)
	{
		if($this->result[$id]==0){
			return false;
		}else{
			return sqlite_fetch_array($this->result[$id],$acctype);
		}
	}

	function GetObject($id="me")
	{
		if($this->result[$id]==0){
			return false;
		}else{
			return sqlite_fetch_object($this->result[$id]);
		}
	}

	function IsTable($tbname)
	{
		return true;
	}

	function GetVersion($isformat=true)
	{
		$rs = sqlite_query($this->linkID,"SELECT sqlite_version()");
		$row = sqlite_fetch_array($rs);
		return $row[0];
	}

	function getDatabaseIdstr($dbname)
	{
		$fields=sqlite_fetch_column_types($this->dbPrefix.$dbname,$this->linkID);
		$idselect="";
		foreach ($fields as $name=>$value){
			if($name!="id"&&$name!="lanstr"){$idselect.=$name.",";}
		}
		$idselect=trim($idselect,",");
		return $idselect;
	}

	function GetFieldObject($id="me")
	{
		return sqlite_fetch_field($this->result[$id]);
	}

	function DisplayError($msg){
		global $cfg;
		$emsg = "<div><h3>".$cfg["version"]." Error Warning!</h3>\r\n";
		$emsg .= "<div style='line-helght:160%;font-size:14px;color:green'>\r\n";
		$emsg .= "<div style='color:blue'>Error page: <font color='red'>".$this->GetCurUrl()."</font></div>\r\n";
		$emsg .= "<div>Error infos: {$msg}</div>\r\n";
		$emsg .= "</div></div>\r\n";
		echo $emsg;
	}

	function GetCurUrl()
	{
		if(!empty($_SERVER["REQUEST_URI"])){
			$scriptName = $_SERVER["REQUEST_URI"];
			$nowurl = $scriptName;
		}else{
			$scriptName = $_SERVER["PHP_SELF"];
			if(empty($_SERVER["QUERY_STRING"])) {
				$nowurl = $scriptName;
			}else{
				$nowurl = $scriptName."?".$_SERVER["QUERY_STRING"];
			}
		}
		return $nowurl;
	}

	function err(){return sqlite_error();}
}

//SQL语句过滤程序

function CheckSql($db_string,$querytype='select')
{
	global $cfg_cookie_encode,$db;
	$clean = '';
	$error='';
	$old_pos = 0;
	$pos = -1;
	$log_file = incpath.time().'_safe.txt';
	$userIP = GetIP();
	$getUrl = $db->GetCurUrl();

	//如果是普通查询语句，直接过滤一些特殊语法
	if($querytype=='select')
	{
		$notallow1 = "[^0-9a-z@\._-]{1,}(union|sleep|benchmark|load_file|outfile)[^0-9a-z@\.-]{1,}";
		if(eregi($notallow1,$db_string))
		{
			fputs(fopen($log_file,'a+'),"$userIP||$getUrl||$db_string||SelectBreak\r\n");
			exit("<font size='5' color='red'>Safe Alert: Request Error step 1 !</font>");
		}
	}

	//完整的SQL检查
	while (true)
	{
		$pos = strpos($db_string, '\'', $pos + 1);
		if ($pos === false)
		{
			break;
		}
		$clean .= substr($db_string, $old_pos, $pos - $old_pos);
		while (true)
		{
			$pos1 = strpos($db_string, '\'', $pos + 1);
			$pos2 = strpos($db_string, '\\', $pos + 1);
			if ($pos1 === false){
				break;
			}elseif($pos2 == false || $pos2 > $pos1){
				$pos = $pos1;
				break;
			}
			$pos = $pos2 + 1;
		}
		$clean .= '$s$';
		$old_pos = $pos + 1;
	}
	$clean .= substr($db_string, $old_pos);
	$clean = trim(strtolower(preg_replace(array('~\s+~s' ), array(' '), $clean)));

	//老版本的Mysql并不支持union，常用的程序里也不使用union，但是一些黑客使用它，所以检查它
	if (strpos($clean, 'union') !== false && preg_match('~(^|[^a-z])union($|[^[a-z])~s', $clean) != 0)
	{
		$fail = true;
		$error="union detect";
	}
	//发布版本的程序可能比较少包括--,#这样的注释，但是黑客经常使用它们
	elseif (strpos($clean, '/*') > 2 || strpos($clean, '--') !== false || strpos($clean, '#') !== false)
	{
		$fail = true;
		$error="comment detect";
	}

	//这些函数不会被使用，但是黑客会用它来操作文件，down掉数据库
	elseif (strpos($clean, 'sleep') !== false && preg_match('~(^|[^a-z])sleep($|[^[a-z])~s', $clean) != 0)
	{
		$fail = true;
		$error="slown down detect";
	}
	elseif (strpos($clean, 'benchmark') !== false && preg_match('~(^|[^a-z])benchmark($|[^[a-z])~s', $clean) != 0)
	{
		$fail = true;
		$error="slown down detect";
	}
	elseif (strpos($clean, 'load_file') !== false && preg_match('~(^|[^a-z])load_file($|[^[a-z])~s', $clean) != 0)
	{
		$fail = true;
		$error="file fun detect";
	}
	elseif (strpos($clean, 'into outfile') !== false && preg_match('~(^|[^a-z])into\s+outfile($|[^[a-z])~s', $clean) != 0)
	{
		$fail = true;
		$error="file fun detect";
	}

	//老版本的MYSQL不支持子查询，我们的程序里可能也用得少，但是黑客可以使用它来查询数据库敏感信息
	elseif (preg_match('~\([^)]*?select~s', $clean) != 0)
	{
		$fail = true;
		$error="sub select detect";
	}
	if (!empty($fail)){
		fputs(fopen($log_file,'a+'),"$userIP||$getUrl||$db_string||$error\r\n");
		exit("<font size='5' color='red'>Safe Alert: Request Error step 2!</font>");
	}else{
		return $db_string;
	}
}
?>