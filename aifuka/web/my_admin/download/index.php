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
		define("model","download");
		define("modelpath",TPL_ADMIN_DIR."download/");
		define("model_imagepath",download_imagepath);
		$cfg["model_name"]=  "下载资料";
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
		if($cfg["commendstr"]!=""){
			$addsql.=" and commend like '%".$cfg["commendstr"]."%' ";
		}elseif($cfg["del"]){
			$addsql.=" and del=1 ";
		}else{
			$classidsql=$cfg["classid"]!=0?" and classid in(".allclassids($cfg["classid"]).") ":"";
			$addsql.=$classidsql." and del=0 ";
		}
		$addsql.=$this->getsearsql_download();
		$numsql="select * from #@__".model.$addsql;
		$cfg["allnums"]=$db->num($numsql,"allnum");
		$thesql="select * from #@__".model.$addsql." order by sortid desc limit $beginid,$pagesize";
		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			$cfg["classname"]=gets("classname","class","lanstr='".lanstr."' and lanid=".$rs["classid"]);
			$cfg["lanid"]=$rs["lanid"];
			$cfg["sortid"]=$rs["sortid"];
			$cfg["commend"]=$rs["commend"];
			if($cfg["keyword"]!=""){
				$cfg["title"]=str_replace($cfg["keyword"],"<b class=red>".$cfg["keyword"]."</b>",$rs["title"]);
				$cfg["url"]=str_replace($cfg["keyword"],"<b class=red>".$cfg["keyword"]."</b>",$rs["url"]);
			}else{
				$cfg["title"]=left($rs["title"],$cfg["titlecutnum"]);
				$cfg["url"]=$rs["url"];
			}
			$cfg["size"]=$rs["size"];
			$cfg["type"]=$rs["type"];
			$funstr.=$this->reLabel2($loopstr);
		}
		return $funstr;
	}

	function add()
	{
		global $cfg,$db;
		$cfg["title"]=guid_str."添加".$cfg["model_name"];
		$type=$cfg["download_ext_str"];
		$size=$cfg["download_size"];
		$cfg["fileconfig"]="{type:'$type',size:$size}";
		echo $this->reLabel(modelpath."form.html");
	}

	function edit($lanid)
	{
		global $cfg,$db,$admin;
		$cfg["lanid"]=$lanid;
		$type=$cfg["download_ext_str"];
		$size=$cfg["download_size"];
		$cfg["fileconfig"]="{type:'$type',size:$size}";
		$thesql="select * from #@__".model." where lanid=".$lanid;
		$db->dosql($thesql);
		while($rs=$db->GetArray())//通过lanid访问对应数据
		{
			if($rs["lanstr"]==deflan){//共同数据部分
				$cfg["classid"]=$rs["classid"];
			}
			//独立数据部分
			$cfg["title".$rs["lanstr"]]=$rs["title"];
			$cfg["url"]=$rs["url"];
			$cfg["picurl"]=$rs["picurl"];
			$cfg["bigpic"]=$rs["bigpic"];
			$cfg["software_version".$rs["lanstr"]]=$rs["software_version"];
			$cfg["size"]=$rs["size"];
			$cfg["type"]=$rs["type"];
			$cfg["click"]=$rs["click"];
			$cfg["content".$rs["lanstr"]]=$rs["content"];
		}
		$cfg["title"]=guid_str.$cfg["model_name"]."编辑";
		echo $this->reLabel(modelpath."form.html");
	}

	function update()
	{
		global $cfg,$db,$admin;
		$now=time();
		$lanid=formatnum($_POST["lanid"],0);
		$classid=formatnum($_POST["classid"],0);
		if($classid==0)die("{err}请确定栏目是否存在！");
		$url=$_POST["url"];
		$size=$_POST["urlsize"];
		$type=$_POST["urltype"];
		$click=intval($_POST["click"]);
		$picurl=$_POST["picurl"];

		$bigpic = $_POST["picurl"];
		if ($picurl != "") {
			$smallpic = getSmallpic($picurl);
			$p = webroot . download_imagepath;
			$picSet = new picSet($p . $picurl);
			$picSet->suo($p . $smallpic, 211, 126,$cfg['pic_zoom_bgcolor']);
			$sec = $picSet->save();
			$picSet = null;
			$picurl  = $smallpic;
		}

		
		doMydb(0);
		for($i=0;$i<lannums;$i++)
		{
			$lanstr=$cfg["language"][$i];
			$title= post("title".$lanstr);
			$content = post("content".$lanstr);
			$software_version= post("software_version".$lanstr);
			if($lanid){
				$thesql="update #@__".model." set
				classid=$classid,
				title='".$title."',
				url='".$url."',
				picurl='".$picurl."',
				bigpic='".$bigpic."',
				software_version='".$software_version."',
				size='".$size."',
				type='".$type."',
                click='".$click."',
				content='".$content."',
				edittime=$now
				where lanstr='".$lanstr."' and lanid=".$lanid;
				$db->execute($thesql);
			}else{
				if($i==0){$newlanid=0;}
				$thesql="insert into #@__".model." (
				lanid,lanstr,sortid,classid,title,url,size,type,click,content,edittime,addtime,picurl,software_version,bigpic
				) values (
				$newlanid,'".$lanstr."',$newlanid,$classid,'".$title."','".$url."','".$size."','".$type."',$click,'".$content."',$now,$now,'".$picurl."','".$software_version."','".$bigpic."'
				)";

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

	function getsearsql_download()
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
				$thesql = "delete from #@__download where lanid in (" . $lanidstr . ")";
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
		$admin->adminck("download_add");
		$myclass->add();
		break;
	case "edit":
		$admin->adminck("download_edit");	
		$myclass->edit($lanid);
		break;
	case "update":
		refPage(2);
		$admin->adminck_ajax("download_edit");	
		$myclass->update();
		break;
	case "sort":
		refPage(1);
		$admin->adminck_ajax("download_edit");	
		$myclass->sort(model);
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
		$admin->adminck_ajax("download_edit");	
		$myclass->admindo($lanid);
		break;
	default:
		$myclass->def();
}
?>