<?php
/********************************************************
 时间：2009-10-5

 程序：胡思文
 ********************************************************/
include("include/inc.php");
include(incpath."funlist2.php");

/**
 *
 * Enter description here ...
 * @author Administrator
 *
 */
class search extends alzCms
{
	function __construct(){
		global $cfg,$db;
		$cfg["target"]=" target=\"_blank\"";
		$topclassid=$_GET["topclassid"];
		$classid=$_GET["classid"];
		$cfg["lanstr"] =$_GET["lanstr"];

		if($cfg["lanstr"]=="" || ($cfg["lanstr"]!="en" && $cfg["lanstr"]!="zh_cn") )	$cfg["lanstr"] = $cfg["deflan2"];

		$cfg["htmlPath"]=htmlPath();
		if($topclassid!=""||$classid!="")header("location:index.php?topclassid=$topclassid&classid=$classid&lanstr=$lanstr");
		//	loadlanxml("search","search_","s");
		$cfg["classidstr"]="search";
		$cfg["classstr"]="<a>".t(8,"s")."</a>";

		
		//设置前台模板目录 		
		if($cfg['is_multi_theme']){
			$cfg["tpl_id"] = G("tpl_id",$_GET["tpl_id"]);
			$cfg['front_theme'] = $cfg["tpl_id"]  && in_array($cfg["tpl_id"] , mydir(template.TPL_FRONT_DIR)) ? $cfg["tpl_id"] : $cfg['front_theme'];
		}
		define("FRONT_THEME_DIR", TPL_FRONT_DIR.$cfg['front_theme'].'/');
		
		
		$cfg["page_title"] = $this->_lang('search')."-".$cfg["webname".$cfg["lanstr"]];
		$cfg["page_keywords"] = $rs["tag"]."|".$cfg["webkeywords".$cfg["lanstr"]];
		$cfg["page_description"] = $rs["about"]."|".$cfg["webdescription".$cfg["lanstr"]];

		$classid=G("classid",$_POST["classid"]);
		$model= G("model",$_POST["model"]);
		
		$model=$m= G("model",$_GET["m"]);
		//$type=G("type",$_POST["type"]);
		$keyword=G("keywords",$_POST["keywords"]);
		$cfg["model"]=$model;
		$cfg["keyword"]=$keyword;
		if($cfg["keyword"]==""){
			$cfg["thesql"]="select * from #@__product where del=0 and locked=0 and lanstr='".$cfg["lanstr"]."' ".$cfg["product_orderby"];
		}else{
			$cfg["thesql"]="select * from #@__product where del=0 and locked=0 and lanstr='".$cfg["lanstr"]."' and (title like '%$keyword%' or product_sn like '%$keyword%' or tag like '%$keyword%' or brief like '%$keyword%' )".$cfg["product_orderby"];
		}
		
		if($m=="article"){
			if($cfg["keyword"]==""){
				$cfg["thesql2"]="select * from #@__article where del=0 and locked=0 and classid in (368,354) and lanstr='".$cfg["lanstr"]."'  ".$cfg["article_orderby"];
				}else{
				$cfg["thesql2"]="select * from #@__article where del=0 and locked=0 and classid in (368,354) and lanstr='".$cfg["lanstr"]."'  and (title like '%$keyword%' or tag like '%$keyword%' or brief like '%$keyword%' )".$cfg["article_orderby"];
			}
		}
		$cfg["classid"]=2;
		$cfg['topclassid'] = 2;
		
		//Banner条
		$banner = $db->GetOne("select banner from #@__class where lanid=" . $cfg["topclassid"] . " and lanstr='" . $cfg["lanstr"] . "'");
		$cfg["classbanner"] = $banner["banner"];
		
		

		$cfg["page"]=1;		
		
		$cfg["modelcontent"]=$this->reLabel(FRONT_THEME_DIR."list/product.html");	
		$content=$this->reLabel(FRONT_THEME_DIR."main/product.html");
		echo $content;
	}

	/**
	 *
	 * 产品搜索
	 * @param $loopstr
	 */
	function prolist($loopstr)
	{
		global $cfg,$db;
		if($cfg["model"]!="product")$cfg["cssid"]="2";
		//	loadlanXml("product","product_");
		$pagesize=$cfg["productpagesize"];
		$cfg["pagesize"]=$pagesize;
		$beginid=($cfg["page"]-1)*$pagesize;
		$thesql=$cfg["thesql"];
		$db->dosql($thesql);
		$cfg["allnums"]=$db->nums();
		//$orderstr=$cfg["product_orderby"];
		$limitstr=" limit $beginid,$pagesize";
		$thesql.=$limitstr;

		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			$cfg["title"]=str_replace($cfg["keyword"],"<font style='color:red;'>".$cfg["keyword"]."</font>",$rs["title"]);
			$cfg["brief"]=str_replace($cfg["keyword"],"<font style='color:red;'>".$cfg["keyword"]."</font>",$rs["brief"]);
			
			$cfg["product_sn"]=  $rs['product_sn'];
			$cfg["brief"]=left($cfg["brief"],250);
			$cfg["filestr"]=$this->getFileDownImg("productfile",$rs["file"]);
			$p=webpath.product_imagepath;

			//设置产品的默认图片
			$cfg["picurl"] = webpath."userfiles/default/product_list.jpg";
			
			

			if(trim($rs["smallpic"]) && file_exists(webroot.product_imagepath.$rs["smallpic"])){
				$cfg["picurl"]=$p.$rs["smallpic"];
			}
			$topclassid=getTopclassid($rs["classidstr"]);
			$url1="index.php?topclassid=$topclassid&classid=".$rs["classid"];
			$url2=$cfg["htmlPath"].$rs["paths"];
			$cfg["classurl"]=geturl($url1,$url2);
			$cfg["classname"]=gets("classname","class","lanid={$rs["classid"]} and lanstr='{$cfg["lanstr"]}'");
			$url1="index.php?topclassid=$topclassid&classid={$rs["classid"]}&id=".$rs["lanid"];
			$url2=$cfg["htmlPath"].$rs["paths"].$rs["filename"];
			$cfg["theurl"]=geturl($url1,$url2);
			$temp.=$this->reLabel2($loopstr);
		}

		if($cfg['allnums']<1){
			$temp = $this->reLabel(FRONT_THEME_DIR."piece/norecord.html");	
		}

		return $temp;
	}

	/**
	 *
	 * 文章搜索
	 * @param $loopstr
	 */
	function articlelist($loopstr)
	{
		global $cfg,$db;
		if($cfg["model"]!="product")$cfg["cssid"]="2";
		//loadlanXml("product","product_");
		$pagesize=$cfg["productpagesize"];
		$cfg["pagesize"]=$pagesize;
		$beginid=($cfg["page"]-1)*$pagesize;
		$thesql=$cfg["thesql2"];
		$db->dosql($thesql);
		$cfg["allnums"]=$db->nums();
		//$orderstr=$cfg["product_orderby"];
		$limitstr=" limit $beginid,$pagesize";
		$thesql.=$limitstr;
		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			$cfg["title"]= str_replace($cfg["keyword"],"<font style='color:red;'>".$cfg["keyword"]."</font>",$rs["title"]);
			$cfg["brief"]=str_replace($cfg["keyword"],"<font style='color:red;'>".$cfg["keyword"]."</font>",$rs["brief"]);

			$cfg["brief"]=left($cfg["brief"],88);
			$cfg["time"]=date($cfg["article_formattime{$cfg["lanstr"]}"],$rs["edittime"]);
				
			$topclassid=getTopclassid($rs["classidstr"]);
			$url1="index.php?topclassid=$topclassid&classid=".$rs["classid"];
			$url2=$cfg["htmlPath"].$rs["paths"];
			$cfg["classurl"]=geturl($url1,$url2);
			$cfg["classname"]=gets("classname","class","lanid={$rs["classid"]} and lanstr='{$cfg["lanstr"]}'");
			$url1="index.php?topclassid=$topclassid&classid={$rs["classid"]}&id=".$rs["lanid"];
			$url2=$cfg["htmlPath"].$rs["paths"].$rs["filename"];
			$cfg["theurl"]=geturl($url1,$url2);
			$temp.=$this->reLabel2($loopstr);
		}

		if($cfg['allnums']<1){
			$temp = $this->reLabel(FRONT_THEME_DIR."piece/norecord_article.html");	
		}

		return $temp;
	}
}
/**
 *
 * Enter description here ...
 * @var unknown_type
 */
$search=new search();
?>