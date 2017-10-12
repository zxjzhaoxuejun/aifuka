<?php
/**
 * 公共文件引用
 *
 * @version        $Id: web.config.php 1 10:33 2010年7月6日Z $
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */
include("inc.php");
include("funlist2.php");
$action=$_GET["action"];
switch($action)
{
	case "getDatanums":
		$model=$_GET["model"];
		switch($model)
		{
			case "feedback":
				$lanstrIo=$cfg["feedback_lanstr_io"]?"and lanstr='{$_GET["lanstr"]}' ":"";
				$thesql="select id from #@__feedback where locked=0 $lanstrIo";
				break;
		}
		$num=$db->num($thesql);
		die("$num");
		break;
	case "click":
		$dbname=$_GET["dbname"];
		$lanid=formatnum($_GET["lanid"],0);
		$lanstr=$_GET["lanstr"];
		$thesql="update #@__$dbname set click=click+1 where lanid=$lanid and lanstr='".$lanstr."'";
		$db->execute($thesql);
		$thesql="select click from #@__$dbname where lanid=$lanid and lanstr='".$lanstr."'";
		$click=$db->getValue($thesql,"click");
		die($click);
		break;
	case "articleguid":
		$html=$_GET["html"];
		if($html){$cfg["web_model"]="html";}else{$cfg["web_model"]="php";}
		$lanstr=$_GET["lanstr"];
		$cfg["lanstr"]=$lanstr;
		loadlanXml("article","article_");
		$classid=formatnum($_GET["classid"],0);
		$lanid=formatnum($_GET["lanid"],0);
		$thesql=getguidsql("article",$lanid,$classid,$lanstr,"article_orderby","back",1);
		if($thesql!=""){
			$thesql=str_replace("*","title,lanid,paths,filename,classidstr",$thesql);
			$db->dosql($thesql);
			if($db->nums()){
				$rs=$db->GetArray();
				$topclassid=getTopclassid($rs["classidstr"]);
				$url1="?topclassid=$topclassid&classid=$classid&id=".$rs["lanid"];
				$url2=htmlPath($lanstr).$rs["paths"].$rs["filename"];
				$url=geturl($url1,$url2);
				$temp.=t("back").":<a href='$url'>".$rs["title"]."</a>";
			}else{
				//$temp.=t("back").":".t("nothing");
			}
		}
		$thesql=getguidsql("article",$lanid,$classid,$lanstr,"article_orderby","next",1);
		if($thesql!=""){
			$thesql=str_replace("*","title,lanid,paths,filename,classidstr",$thesql);
			$db->dosql($thesql);
			if($db->nums()){
				$rs=$db->GetArray();
				$topclassid=getTopclassid($rs["classidstr"]);
				$url1="?topclassid=$topclassid&classid=$classid&id=".$rs["lanid"];
				$url2=htmlPath($lanstr).$rs["paths"].$rs["filename"];
				$url=geturl($url1,$url2);
				$temp.="<br />".t("next").":<a href='$url'>".$rs["title"]."</a>";
			}else{
				//$temp.="<br />".t("next").":".t("nothing");
			}
		}
		die($temp);
		break;
	case "productguid":
		$html=$_GET["html"];
		if($html){$cfg["web_tohtml"]=1;}else{$cfg["web_tohtml"]=0;}
		$case=$_GET["case"];
		$lanstr=$_GET["lanstr"];
		$cfg["lanstr"]=$lanstr;
		loadlanXml("product","product_");
		$classid=formatnum($_GET["classid"],0);
		$lanid=formatnum($_GET["lanid"],0);
		$thesql=getguidsql("product",$lanid,$classid,$lanstr,"product_orderby",$case,1);
		if($thesql!=""){
			$thesql=str_replace("*","title,lanid,paths,filename,classidstr",$thesql);
			$db->dosql($thesql);
			if($db->nums()){
				$rs=$db->GetArray();
				$topclassid=getTopclassid($rs["classidstr"]);
				$url1="?topclassid=$topclassid&classid=$classid&id=".$rs["lanid"];
				$url2=htmlPath($lanstr).$rs["paths"].$rs["filename"];
				$url=geturl($url1,$url2);
				$temp.=t($case."one")."：<a href='$url' title='{$rs["title"]}'>".left($rs["title"],20)."</a>";
			}else{
				$temp.=t($case."one")."："."暂无";
			}
		}
		die($temp);
		break;
	case "searchclass":
		searchclass();
		break;
}
/**
 *
 * Enter description here ...
 * @param $dbname
 * @param $lanid
 * @param $classid
 * @param $lanstr
 * @param $orderbystr
 * @param $type
 * @param $num
 */
function getguidsql($dbname,$lanid,$classid,$lanstr,$orderbystr,$type,$num=1)
{
	global $cfg,$db;
	$orderby=$cfg[$orderbystr];
	$str=str_replace("order by ","",$orderby);
	$arr=explode(",",$str);
	$s1=instr($arr[0],"desc")?">":"<";//倒序时，前N条数据应该比基数大
	$s2=$s1==">"?"<":">";//后N条数据和前N条相反
	$s=$type=="back"?$s1:$s2;
	$arr=explode(" ",$arr[0]);
	$key=$arr[0];//第一排序字段
	$thesql="select $key from #@__$dbname where lanid=$lanid and lanstr='".$lanstr."'";
	$rs=$db->GetOne($thesql);
	$value=$rs[$key];
	if($value=="")return "";
	if($type=="back"){$orderby=$cfg[$orderbystr."2"];}//若找后面的数据,应该找与基数最近,且同序;如果找前面的数据,应该为反序.
	$thesql="select * from #@__$dbname where title<>'' and del=0 and locked=0  and lanstr='$lanstr' and $key $s $value and classid in (".allclassids($classid).") $orderby limit 0,$num";
	return $thesql;
}

/**
 *
 * Enter description here ...
 */
function searchclass()
{
	global $cfg,$db;
	$model=$_GET["model"];
	$cfg["lanstr"]=$_GET["lanstr"];
	$thesql="select * from #@__class where (model like '$model%' or tpl_in like '$model%') and lanstr='{$cfg["lanstr"]}' and lid=0 order by sortid,lanid";
	$db->dosql($thesql,"sel");
	while($rs=$db->GetArray("sel"))
	{
		$temp.=$rs["lanid"]."=├".$rs["classname"]."|";
		$temp.=classSelectLoop($rs["lanid"],"　├");
	}
	echo $temp;
}


/**
 *
 * Enter description here ...
 * @param $fid
 * @param $str
 */
function classSelectLoop($fid,$str)
{
	global $cfg,$db;
	$conn=$db->linkID;
	$thesql="select * from #@__class where fid=$fid and lanstr='".$cfg["lanstr"]."' order by sortid,id";
	$thesql=$db->SetQuery($thesql);
	$result=mydb_query($thesql,$conn);
	while($rs=mydb_fetch_array($result))
	{
		$funstr.=$rs["lanid"]."=".$str.$rs["classname"]."|";
		$funstr.=classSelectLoop($rs["lanid"],"　".$str);
	}
	return $funstr;
}
?>