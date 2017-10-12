<?php
/********************************************************
时间：2009-10-21

程序：胡思文
********************************************************/
include("../../include/inc.php");
include(incpath."funlist.php");

class myclass extends alzCms
{
	function __destruct(){$this->admincache2();}
	function __construct()
	{
		global $cfg,$admin;		
		$this->admincache();
		loadlanXml("links","links_");
		
		$cfg["classid"]=formatnum($_GET["classid"],0);
		if($_GET["c"]!=""){
			setcookies("classid",0);
			setcookies("page",1);
			setcookies("commend","");
			setcookies("del",0);
			setcookies("searchtype","");
			setcookies("keyword","");
			
			$cfg["page"]=formatnum($_GET["page"],1);
			
			$cfg["del"]=formatnum($_GET["del"],0);
			$cfg["searchtype"]=$_GET["searchtype"];
			$cfg["keyword"]=$_GET["keyword"];
		}else{			
			$cfg["page"]=G("page",$_GET["page"],1);		
			$cfg["del"]=G("del",$_GET["del"],0);
			$cfg["searchtype"]=G("searchtype",$_GET["searchtype"]);
			$cfg["keyword"]=G("keyword",$_GET["keyword"]);
		}
		define("model","links");
		define("modelpath",TPL_ADMIN_DIR."links/");
		define("model_imagepath",links_imagepath);
		$cfg["model_name"]= "友情链接";
	}
	
	function def()
	{
		global $cfg,$db;
		$cfg["otherguidstr"]=$cfg["commendstr"]."|".$cfg["locked"]."|".$cfg["del"];		
		echo $this->reLabel(modelpath."index.html");
	}
	function mylist($loopstr)
	{
		global $cfg,$db,$admin;
		$pagesize=$cfg["adminpagesize"];
		$cfg["pagesize"]=$pagesize;
		$beginid=($cfg["page"]-1)*$pagesize;
		$addsql=" where lanstr='".lanstr."'";
		if($cfg["classid"]>0) {
			$classidsql=  $cfg["classid"]!=0?" and classid in(".allclassids($cfg["classid"]).") ":"";
			$addsql.=$classidsql;
		}
		$addsql.=$this->getsearsql_link();
		$numsql="select * from #@__".model.$addsql;
		$cfg["allnums"]=$db->num($numsql,"allnum");
		$thesql="select * from #@__".model.$addsql." order by sortid desc limit $beginid,$pagesize";

		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			$cfg["classname"]=gets("classname","class","lanstr='".lanstr."' and lanid=".$rs["classid"]);
			$cfg["lanid"]=$rs["lanid"];
			$cfg["sortid"]=$rs["sortid"];		
			if($cfg["keyword"]!=""){
				$cfg["title"]=str_replace($cfg["keyword"],"<b class=red>".$cfg["keyword"]."</b>",$rs["title"]);
				$cfg["url"]=str_replace($cfg["keyword"],"<b class=red>".$cfg["keyword"]."</b>",$rs["url"]);
			}else{
				$cfg["title"]=left($rs["title"],$cfg["titlecutnum"]);
				$cfg["url"]=$rs["url"];
			}
			$cfg["pic"]=$this->viewpicurl(model_imagepath,$rs["logo"]);
			$funstr.=$this->reLabel2($loopstr);
		}
		return $funstr;
	}
	
	function add()
	{
		global $cfg,$db;
		$cfg["title"]=guid_str."添加".$cfg["model_name"];
		$cfg["click"]=0;
		$cfg["ip"]=GetIp();		
		if($cfg["classid"]==138){
			echo $this->reLabel(modelpath."form1.html");
		}else{
			echo $this->reLabel(modelpath."form.html");
		}
	}
	
	function edit($lanid)
	{
		global $cfg,$db,$admin;
		$cfg["lanid"]=$lanid;
		$thesql="select * from #@__".model." where lanid=".$lanid;
		$db->dosql($thesql);
		while($rs=$db->GetArray())//通过lanid访问对应数据
		{
			if($rs["lanstr"]==deflan){//共同数据部分
				$cfg["classid"]=$rs["classid"];
			}
			//独立数据部分
			$cfg["title".$rs["lanstr"]]=$rs["title"];
			$cfg["url".$rs["lanstr"]]=$rs["url"];
			$cfg["logo"] = str_replace("small/","", $rs["logo"]);
			$cfg["content".$rs["lanstr"]]=$rs["content"];
		}
		$cfg["title"]=guid_str.$cfg["model_name"]."编辑";
		if($cfg["classid"]==138){
			echo $this->reLabel(modelpath."form1.html");
		}else{
			echo $this->reLabel(modelpath."form.html");
		}
	}	
	
	function update()
	{
		global $cfg,$db,$admin;
		
		$lanid=formatnum($_POST["lanid"],0);
		$classid=formatnum($_POST["classid"],0);
		if($classid==0)die("{err}请确定栏目是否存在！");
		
		$bigpic =$_POST["logo"];
		
		if ($bigpic != "") {
            $smallpic = getSmallpic($bigpic);
            $p = webroot . model_imagepath;
            $picSet = new picSet($p . $bigpic);
            $picSet->suo($p . $smallpic, $cfg['picsize_link_w'], $cfg['picsize_link_h'],$cfg['pic_zoom_bgcolor']);
            $sec = $picSet->save();
            $picSet = null;
        }
		$logo = $smallpic;
		
		doMydb(0);
		for($i=0;$i<lannums;$i++)
		{
			$lanstr=$cfg["language"][$i];
			$title=post("title".$lanstr);
			$url=$_POST["url".$lanstr];			
			$content=post("content".$lanstr);
			if($lanid){
				$thesql="update #@__".model." set
				classid=$classid,
				title='".$title."',
				url='".$url."',
				logo='".$logo."',
				content='".$content."'				
				where lanstr='".$lanstr."' and lanid=".$lanid;
				$db->execute($thesql);
				$thesql = "select * from #@__" . models . " where lanid=" . $lanid." and lanstr='".$lanstr."'";
				$db->dosql($thesql);
				$rs = $db->GetArray();
				if(empty($rs)){
					$thesqls = "insert into #@__" . models . " (lanid,lanstr,sortid,classid,title,url,logo,content,del) values ($newlanid,'".$lanstr."',$newlanid,$classid,'".$title."','".$url."','".$logo."','".$content."',0)";
					$db->execute($thesqls);
				}
			}else{
				if($i==0){$newlanid=0;}
				$thesql="insert into #@__".model." (lanid,lanstr,sortid,classid,title,url,logo,content,del) 
				values ($newlanid,'".$lanstr."',$newlanid,$classid,'".$title."','".$url."','".$logo."','".$content."',0)";
				$db->execute($thesql);
				
				if($i==0){
					$newlanid=$db->GetLastID();
					$thesql2="update #@__".model." set lanid=$newlanid,sortid=$newlanid where id=".$newlanid;
					$db->execute($thesql2);
				}
			}
		}
		doMydb(1);
		if($lanid){die("{ok}恭喜，".$cfg["model_name"]."编辑成功！");}else{die("{ok}恭喜，".$cfg["model_name"]."添加成功！");}
	}
	
	function getsearsql_link()
	{
		global $cfg,$db;
		if($cfg["keyword"]=="")return;
		if($cfg["searchtype"]=="")return;
		return " and ".$cfg["searchtype"]." like '%".$cfg["keyword"]."%' ";
	}
	
	function admindo($lanidstr)
	{
		 global $db;
		$str=$_GET["str"];
		$fun=$_GET["fun"];
		switch ($fun)
		{
			case "dels":
				$thesql = "delete from #@__links where lanid in (" . $lanidstr . ")";
                $db->execute($thesql);
			//	$this->mydid($lanidstr,model,"del=1");
				break;
			case "re":
				$this->mydid($lanidstr,model,"del=0");
				break;
			case "reall":
				$this->mydidall(model,"del=0");
				break;
			case "deltrue":
				$this->deltrue(model,$lanidstr);
				break;
			case "delclear":
				$this->deltrue(model);
				break;
			case "locked":
				$this->mydid($lanidstr,model,"locked=1");
				break;
			case "dellocked":
				$this->mydid($lanidstr,model,"locked=0");
				break;
			case "delalllocked":
				$this->mydidall(model,"locked=0");
				break;
		}
		die("{ok}恭喜,".$str."操作成功!");
	}	
}

$myclass=new myclass();
$lanid=$_GET["lanid"];
switch($_GET["action"])
{
	case "add":
		//$admin->adminck("keylink_add");
		$myclass->add();
		break;
	case "edit":
		//$admin->adminck("links_edit");
		$myclass->edit($lanid);
		break;	
	case "update":
		refPage(2);
		//$admin->adminck_ajax("links_edit");
		$myclass->update();
		break;
	case "sort":
		refPage(1);
	$admin->adminck_ajax("links_edit");
		$myclass->sort(model);
		break;
	case "dbref":
		$myclass->dbref(model,models);
		break;
	case "guid":
		$myclass->guid(model);
		break;	
	case "admindo":
		refPage(1);
		//$admin->adminck_ajax("links_del");
		$myclass->admindo($lanid);
		break;
	default:
		$myclass->def();
}
?>