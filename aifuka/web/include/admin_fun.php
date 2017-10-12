<?php
/**
 * 后台管理公共函数库。
 *
 * @version        $Id: web.config.php 1 10:33 2010年7月6日Z $
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */


$admin = new admin();
$app=new application();
$cfg["myadmin_username"]=getsession("admin_username");
$cfg["myadmin_userid"]=formatnum(getsession("admin_userid"),1);
if(!$admin->OL()){
	header("location:".adminpath."admin_login.php");
}

/**
 *
 * Enter description here ...
 * @param $case
 */
function refPage($case=0){
	switch($case){
		case 2://清除来路缓存，用于内容编辑
			$url=getcookies("rightcomeurl");
				
			$admincachfile = admincachepath.md5(str_replace("http://","",$url)).".inc";
			if(is_file($admincachfile)){unlink($admincachfile);}
			break;
		case 1://清除当前缓存，用于不跳页设置表单
			$url= getcookies("rightlocation");

			$admincachfile=admincachepath.md5(str_replace("http://","",$url)).".inc";
			if(is_file($admincachfile)){unlink($admincachfile);}
			break;
		default://刷新当前页面，返回地址
			$url = getcookies("rightlocation");
			$admincachfile = admincachepath.md5(str_replace("http://","",$url)).".inc";
			if(is_file($admincachfile)){
				unlink($admincachfile);
			}
			die($url);
	}
}




/**
 *
 * 管理员IP地址验证。
 *
 */
function adminipck()
{//后台主页面调用一次就可以了.
	global $cfg,$admin;
	$not=false;
	$ip=GetIP();
	if(!checkIp($ip,$cfg["adminiplist"]))$not=1;
	if($cfg["myadmin_username"]==$cfg["superusername"])$not=0;
	if($cfg["myadmin_username"]=="")$not=0;
	if($not){
		$admin->quit();
		sucgotos("<p>您的IP不是合法的管理IP！</p>",webpath,10);
	}
}

/**
 *   配置文件写入相应语言版本的配置文件
 * 
 */
function writeconfigfile()
{
	global $db,$cfg;
	$db->dosql("select * from #@__cfg where keystr not like 'mydb_%' and keystr not like '%_textcache' order by keystr");
	//exit("fdafsd");
	$funstr.="<?php\r\n";
	while($rs=$db->GetArray())
	{
		$funstr.=writeconfigfile2($rs["keystr"],$rs["valuestr"]);
	}
	$funstr.="?>";
	
	file_put_contents(incpath."web_config.php",$funstr);
	for($i=0;$i<lannums;$i++){
		$thelan=$cfg["language"][$i];
		$funstr="<?php\r\n";
		$db->dosql("select * from #@__language where model='text' and lanstr='".$thelan."'");
		while($rs=$db->GetArray())
		{
			$content=delcrlf($rs["content"]);
			$funstr.=writeconfigfile2($rs["tag"],$content);
		}
		$funstr.="?>";
		file_put_contents(incpath."language_".$thelan.".php",$funstr);
	}
}

/**
 *
 * 获取配置信息 HTML
 * @param $keystr
 * @param $valuestr
 */
function writeconfigfile2($keystr,$valuestr)
{
	if(is_numeric($valuestr)){
		$funstr.="\$cfg[\"".$keystr."\"]=".$valuestr.";\r\n";
	}else{
		$str=delcrlf($valuestr);
		$str=str_replace("\"","\\\"",$str);
		if($str=="true"||$str=="false"){
			$funstr.="\$cfg[\"".$keystr."\"]=".$str.";\r\n";
		}else{
			$funstr.="\$cfg[\"".$keystr."\"]=\"".$str."\";\r\n";
		}
	}
	return $funstr;
}


/**
 *
 * 将系统配置文件写入 JS 文件。
 */
function writeconfigjs()
{
	global $db,$cfg;
	$alz = new alzCms();
	$arr = explode(".",$cfg["htmlIndex"]);
	for($i=0;$i<lannums;$i++)
	{
		$funstr="var lan=1;\r\n";
		$funstr.="var lanstr=\"".$cfg["language"][$i]."\";\r\n";
		$funstr.="var lanxmlpath=\"".lanxmlpathjs."\";\r\n";
		$funstr.="var weburl=\"".$cfg["weburl".$cfg["language"][$i]]."\";\r\n";
		$funstr.="var company=\"".$cfg["company".$cfg["language"][$i]]."\";\r\n";
		$funstr.="var htmlname=\"".$arr[0]."\";\r\n";
		$funstr.="var suffix=\".".$arr[1]."\";\r\n";
		$funstr.=$alz->reLabel("admin/admin_jsconfig.html")."\r\n";
		$db->dosql("select * from #@__language where model='text' and lanstr='".$cfg["language"][$i]."'");
		while($rs=$db->GetArray())
		{
			$content=delcrlf($rs["content"]);
			$content = str_replace("\"","\\\"",$content);
			$funstr.="var ".$rs["tag"]."=\"".$content."\";\r\n";
		}
		file_put_contents(webroot."js/inc_".$cfg["language"][$i].".js",$funstr);
	}
}

/**
 *
 * 将配置文件写入 Cache .
 */
function writeconfigtextcache()
{
	global $db,$cfg;
	$db->dosql("select * from #@__cfg where keystr like '%_textcache'");
	while($rs=$db->GetArray()){
		file_put_contents(temppath.str_replace("_textcache","",$rs["keystr"]).".inc",$rs["valuestr"]);
	}
}
?>