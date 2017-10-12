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
		loadlanXml("download","download_");
		if($_GET["c"]!=""){
			setcookies("classid",0);
			setcookies("page",1);
			setcookies("commend","");
			setcookies("del",0);
		
			$cfg["classid"]=formatnum($_GET["classid"],0);
			$cfg["page"]=formatnum($_GET["page"],1);
			
			$cfg["del"]=formatnum($_GET["del"],0);
			
		}else{
			$cfg["classid"]=G("classid",$_GET["classid"]);
			$cfg["page"]=G("page",$_GET["page"],1);
			
			$cfg["del"]=G("del",$_GET["del"],0);
			
		}
		define("model","piece");
		define("modelpath",TPL_ADMIN_DIR."piece/");
		define("model_imagepath",download_imagepath);
		$cfg["model_name"]=  "单页碎片";
	}

	function def()
	{
		global $cfg,$db;
		$cfg["otherguidstr"]=$cfg["commendstr"]."|".$cfg["locked"]."|".$cfg["del"];
		
		$cfg['adminBatchAction'] = ' <a class="class_addchildclass twinkle" href="javascript:void(0)" onclick="adminDoType(\'dels\')" >删除</a>';



		echo $this->reLabel(modelpath."index.html");
	}
	function mylist($loopstr)
	{
		global $cfg,$db,$admin;
		$pagesize=$cfg["adminpagesize"];
		$cfg["pagesize"]=$pagesize;
		$beginid=($cfg["page"]-1)*$pagesize;
		$addsql=" where lanstr='".lanstr."'";		
		$numsql="select * from #@__".model.$addsql;
		$cfg["allnums"]=$db->num($numsql,"allnum");
		$thesql="select * from #@__".model.$addsql."order by id desc limit $beginid,$pagesize";
		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			
			$cfg["lanid"]=$rs["lanid"];			
			$cfg["title"]=left($rs["title"],$cfg["titlecutnum"]);		
			$cfg["piece_code"]=$rs["piece_code"];
			$cfg["content"]=$rs["content"];
			$funstr.=$this->reLabel2($loopstr);
		}
		return $funstr;
	}

	function add()
	{
		global $cfg,$db;
		$cfg["action_title"]= guid_str."添加".$cfg["model_name"];		
		echo $this->reLabel(modelpath."form.html");
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
				$cfg["piece_code"]=$rs["piece_code"];
				$cfg["title"]=$rs["title"];	
			}
			//独立数据部分				
			$cfg["content".$rs["lanstr"]]=$rs["content"];
		}
		$cfg["action_title"]=guid_str.$cfg["model_name"]."编辑";
		echo $this->reLabel(modelpath."form.html");
	}

	function update()
	{
		global $cfg,$db,$admin;
		$now=time();
		$lanid=formatnum($_POST["lanid"],0);
		
		$title= post("title");
		$piece_code =$_POST["piece_code"];
		
		$newlanid=0;
		doMydb(0);
		for($i=0;$i<lannums;$i++)
		{
			$lanstr=$cfg["language"][$i];
			
			$content = post("content".$lanstr);
			if($lanid){
				$thesql="update #@__".model." set				
				title='".$title."',				
                piece_code='".$piece_code."',
				content='".$content."'			
				where lanstr='".$lanstr."' and lanid=".$lanid;
				$db->execute($thesql);
				
				$thesql = "select * from #@__" . model . " where lanid=" . $lanid." and lanstr='".$lanstr."'";
				$db->dosql($thesql);
				$rs = $db->GetArray();
				if(!empty($rs)){
					$thesqls = "update #@__" . model . " set content='" . $content . "' where lanstr='" . $lanstr . "' and lanid=" . $lanid;
				}else{
					$thesqls = "insert into #@__" . model . " (lanid,title,piece_code,lanstr,content) values ($lanid,'".$title."','".$piece_code."','" . $lanstr . "','" . $content . "')";
				}
				$db->execute($thesqls);
			}else{
				if($i==0){$newlanid=0;}
				$thesql="insert into #@__".model." (lanid,lanstr,title,piece_code,content)
				 values ($newlanid,'".$lanstr."','".$title."','".$piece_code."','".$content."')";
		
				$db->execute($thesql);
				if($i==0){
					$newlanid=$db->GetLastID();
					$thesql2="update #@__".model." set lanid=$newlanid where id=".$newlanid;
					$db->execute($thesql2);
				}
			}
		}
		doMydb(1);
		if($lanid){die("{ok}恭喜，".$cfg["model_name"]."编辑成功！");}else{die("{ok}恭喜，".$cfg["model_name"]."添加成功！");}
	}

	

	function admindo($lanidstr)
	{
	 global $db;
		$str=$_GET["str"];
		$fun=$_GET["fun"];
		switch ($fun)
		{
			case "dels":
				$thesql = "delete from #@__piece where lanid in (" . $lanidstr . ")";
				$db->execute($thesql);
				//				$this->mydid($lanidstr,model,"del=1");
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
		$admin->adminck("piece_add");
		$myclass->add();
		break;
	case "edit":
		$admin->adminck("piece_edit");	
		$myclass->edit($lanid);
		break;
	case "update":
		refPage(2);
		$admin->adminck_ajax("piece_edit");	
		$myclass->update();
		break;	
	case "dbref":
		$myclass->dbref(model,models);
		break;
	
	case "guid":
		$myclass->guid(model);
		break;
	
	case "admindo":
		refPage(1);
		$admin->adminck_ajax("piece_del");	
		$myclass->admindo($lanid);
		break;
	default:
		$myclass->def();
}
?>