<?php
/********************************************************
时间：20010-1-20

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
		$admin->adminck("sys_tag");
		$this->admincache();
		loadlanXml("tag","tag_");
		if($_GET["c"]!=""){
			setcookies("page",1);
			setcookies("searchtype","");
			setcookies("keyword","");
			setcookies("orderby","");
			$cfg["page"]=formatnum($_GET["page"],1);
			$cfg["keyword"]=$_GET["keyword"];
		}else{
			$cfg["page"]=G("page",$_GET["page"],1);
			$cfg["keyword"]=G("keyword",$_GET["keyword"]);
		}
		define("model","tag");
		define("modelpath",TPL_ADMIN_DIR."tag/");
		$cfg["model_name"]=t("title");
	}
		
	function def()
	{
		global $cfg,$db;
		$opstr.="<option>--------------------------</option>\r\n";
		$opstr.="<option value='tagnum'>更新Tag涉及数量</option>\r\n";
		$opstr.="<option value='tagnums'>更新所有Tag涉及数量</option>\r\n";
		$opstr.="<option>--------------------------</option>\r\n";
		$opstr.="<option value='dels'>删除</option>\r\n";
		$cfg["admindo_option"]=$opstr;
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
		$orderby=$_GET["orderby"];
		$to=$_GET["to"];
		if($orderby!=""&&$to!="")
		{			
			$orderstr=" order by {$orderby} {$to}";
			setcookies("orderby",$orderstr);
		}else{
			$orderstr=" order by id desc";
		}		
		$thesql="select * from #@__".model.$addsql.$orderstr." limit $beginid,$pagesize";
		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			$cfg["id"]=$rs["id"];
			if($cfg["keyword"]!=""){
				$cfg["tag"]=str_replace($cfg["keyword"],"<b class=red>".$cfg["keyword"]."</b>",left($rs["tag"],50));
			}else{
				$cfg["tag"]=$rs["tag"];
			}
			$cfg["click"]=$rs["click"];
			$cfg["nums"]=$rs["nums"];
			$funstr.=$this->reLabel2($loopstr);
		}
		return $funstr;
	}
	
	function add()
	{
		global $cfg,$db;
		$cfg["pagetitle"]=guid_str."添加".$cfg["model_name"];
		echo $this->reLabel(modelpath."form.html");
	}
	
	function update()
	{
		global $cfg,$db,$admin;
		$id=formatnum($_POST["id"],0);
		$tag=$_POST["tag"];
		doMydb(0);
		if($id){
			$thesql="update #@__".model." set tag='".$tag."' where id=".$id;
			$db->execute($thesql);
		}else{
			$tag=str_replace("，",",",$tag);
			$arr=explode(",",$tag);
			if(count($arr)>1){
				$ok=0;$err=0;
				for($i=0;$i<count($arr);$i++){
					$sec=$this->addonetag($arr[$i]);
					if($sec=="Y"){$ok++;}else{$err++;}
				}
				$cfg["model_name"]="{$err}条添加失败，{$ok}条";
			}else{
				$this->addonetag($tag);
			}
		}
		doMydb(1);
		if($id){die("{ok}恭喜，".$cfg["model_name"]."编辑成功！");}else{die("{ok}恭喜，".$cfg["model_name"]."添加成功！");}
	}
	
	function addonetag($tag)
	{
		global $db;
		if($tag=="")return "N";
		$thesql="select tag from #@__tag where tag='$tag'";
		if(!$db->num($thesql)){
			$thesql2="insert into #@__".model." (tag) values ('".$tag."')";
			$db->execute($thesql2);
			return "Y";
		}
		return "N";
	}
	
	function getsearsql()
	{
		global $cfg;
		if($cfg["keyword"]=="")return;
		return " where tag like '%".$cfg["keyword"]."%' ";
	}
	
	function admindo($idstr)
	{
		global $db;
		$str=$_GET["str"];
		$fun=$_GET["fun"];
		switch ($fun)
		{
			case "dels":
				$thesql="delete from #@__tag where id in ($idstr)";
				$db->execute($thesql);
				break;
			case "tagnum":
				doMydb(0);
				$arr=explode(",",$idstr);
				for($i=0;$i<count($arr);$i++){$this->refTagNum($arr[$i]);}
				doMydb(1);
				break;
			case "tagnums":
				doMydb(0);
				$thesql="select id from #@__tag";
				$db->dosql($thesql,"f");
				while($rs=$db->GetArray("f")){
					$this->refTagNum($rs["id"]);	
				}
				doMydb(1);
				break;
		}
		die("{ok}恭喜,".$str."操作成功!");
	}
	
	function refTagNum($id)
	{
		global $db;
		$tag=$db->GetOne("select tag from #@__tag where id=".$id);
		$tag=$tag["tag"];
		$dbstr=Array("class","article","product");
		$temp=0;
		for($ii=0;$ii<count($dbstr);$ii++){
			$thesql="select tag from #@__{$dbstr[$ii]} where tag like '%{$tag}%'";
			if($db->num($thesql))$temp++;
		}		
		$thesql="update #@__tag set nums=$temp where id=$id";
		$db->execute($thesql);
	}
}

$myclass=new myclass();
$lanid=$_GET["lanid"];
switch($_GET["action"])
{
	case "add":
		$myclass->add();
		break;
	case "update":
		refPage(2);
		$myclass->update();
		break;
	case "admindo":
		refPage(1);
		$myclass->admindo($lanid);
		break;
	default:
		$myclass->def();
}
?>