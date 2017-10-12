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
		if($_GET["c"]!=""){
			setcookies("classid",0);
			setcookies("page",1);
			setcookies("commend","");
			setcookies("del",0);
			setcookies("searchtype","");
			setcookies("keyword","");
			$cfg["classid"]=formatnum($_GET["classid"],0);
			$cfg["page"]=formatnum($_GET["page"],1);
			$cfg["commendstr"]=$_GET["commend"];
			$cfg["del"]=formatnum($_GET["del"],0);
			$cfg["searchtype"]=$_GET["searchtype"];
			$cfg["keyword"]=$_GET["keyword"];
		}else{
			$cfg["classid"]=G("classid",$_GET["classid"]);
			$cfg["page"]=G("page",$_GET["page"],1);
			$cfg["commendstr"]=G("commend",$_GET["commend"]);
			$cfg["del"]=G("del",$_GET["del"],0);
			$cfg["searchtype"]=G("searchtype",$_GET["searchtype"]);
			$cfg["keyword"]=G("keyword",$_GET["keyword"]);
		}
		$cfg["model_name"] = '链接关键词';
		define("model","keylink");
		define("modelpath",TPL_ADMIN_DIR."keylink/");
	//	$cfg["model_name"]=t("title");
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
		$addsql.=$this->getsearsql();
		$numsql="select * from #@__".model.$addsql;
		$cfg["allnums"]=$db->num($numsql,"allnum");
		$thesql="select * from #@__".model.$addsql.$cfg[model."_orderby"]." limit $beginid,$pagesize";
		
		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			$cfg["lanid"]=$rs["lanid"];
			$cfg["word"]=$rs["word"];
			$cfg["url"]=$rs["url"];
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
		echo $this->reLabel(modelpath."form.html");
	}
	
	function edit($lanid)
	{
		global $cfg,$db,$admin;
		$cfg["lanid"]=$lanid;
		$thesql="select * from #@__".model." where lanid=".$lanid;
		$rs = $db->GetOne($thesql);
		$cfg["lanid"]=$rs["lanid"];
		$cfg["word"]=$rs["word"];
		$cfg["url"]=$rs["url"];
			
		$cfg["title"]=guid_str."链接关键词编辑";
		echo $this->reLabel(modelpath."form.html");
	}	
	
	function update()
	{
		global $cfg,$db,$admin;
		$lanid=formatnum($_POST["lanid"],0);
		
		doMydb(0);
		
			
			$word=$_POST["word"];
			$url=$_POST["url"];
			
			if($lanid){
				$thesql="update #@__".model." set				
				word='".$word."',url='".$url."' where lanid=".$lanid;
				$db->execute($thesql);
			}else{
				if($i==0){$newlanid=0;}
				$thesql="insert into #@__".model." (word,url) values ('".$word."','".$url."')";
				$db->execute($thesql);
				
			}
		
		doMydb(1);
		if($lanid){die("{ok}恭喜，".$cfg["model_name"]."编辑成功！");}else{die("{ok}恭喜，".$cfg["model_name"]."添加成功！");}
	}
	
	function getsearsql()
	{
		global $cfg,$db;
		if($cfg["keyword"]=="")return;
		if($cfg["searchtype"]=="")return;
		return " and ".$cfg["searchtype"]." like '%".$cfg["keyword"]."%' ";
	}
	
	function admindo($lanidstr)
	{
		$str=$_GET["str"];
		$fun=$_GET["fun"];
		switch ($fun)
		{
			case "dels":
				$this->mydid($lanidstr,model,"del=1");
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
		$admin->adminck("keylink_add");
		$myclass->add();
		break;
	case "edit":
		$admin->adminck("keylink_edit");
		$myclass->edit($lanid);
		break;	
	case "update":
		refPage(2);
		$admin->adminck_ajax("keylink_edit");
		$myclass->update();
		break;
	case "dbref":
		$myclass->dbref(model,models);
		break;
	case "commenddo":
		refPage(1);
		$myclass->commenddo(model,$lanid);
		break;
	case "guid":
		$myclass->guid(model);
		break;
	case "read":
		$myclass->read($lanid);
		break;
	case "admindo":
		refPage(1);
		$admin->adminck_ajax("keylink_del");
		$myclass->admindo($lanid);
		break;
	default:
		$myclass->def();
}
?>