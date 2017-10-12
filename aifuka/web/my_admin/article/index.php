<?php
/**
 * 后台文章管理程序文件
 *
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */
include("../../include/inc.php");
include(incpath."funlist.php");

/**
 * 文章管理类
 * Enter description here ...
 * @author guoho
 *
 */
class myclass extends alzCms
{
	function __destruct(){$this->admincache2();}
	function __construct()
	{
		global $cfg,$admin;
		$this->admincache();
		loadlanXml("article","article_");
		if($_GET["c"]!=""){
			setcookies("classid",0);
			setcookies("page",1);
			setcookies("commend","");
			setcookies("locked",0);
			setcookies("del",0);
			setcookies("searchtype","");
			setcookies("keyword","");
			$cfg["classid"]=formatnum($_GET["classid"],0);
			$cfg["page"]=formatnum($_GET["page"],1);
			$cfg["commendstr"]=$_GET["commend"];
			$cfg["locked"]=formatnum($_GET["locked"],0);
			$cfg["del"]=formatnum($_GET["del"],0);
			$cfg["searchtype"]=$_GET["searchtype"];
			$cfg["keyword"]=$_GET["keyword"];
		}else{
			$cfg["classid"]=G("classid",$_GET["classid"]);
			$cfg["page"]=G("page",$_GET["page"],1);
			$cfg["commendstr"]=G("commend",$_GET["commend"]);
			$cfg["locked"]=G("locked",$_GET["locked"],0);
			$cfg["del"]=G("del",$_GET["del"],0);
			$cfg["searchtype"]=G("searchtype",$_GET["searchtype"]);
			$cfg["keyword"]=G("keyword",$_GET["keyword"]);
		}
		$cfg["classname"] = gets("classname", "class", "lanstr='" . lanstr . "' and lanid=" . $cfg["classid"]);

		if($cfg['classid']==7 || $cfg['classid']==33 || $cfg['classid']==77){
			$cfg['is_solution'] = 1;
		}
		define("model","article");
		define("modelpath",TPL_ADMIN_DIR."article/");
		define("model_imagepath",article_imagepath);
		define("model_vediopath",article_vediopath);
		define("models","articles");
		$cfg["model_name"]="文章管理";
	}

	function def()
	{
		global $cfg,$db;
		$cfg["otherguidstr"]=$cfg["commendstr"]."|".$cfg["locked"]."|".$cfg["del"];
		
		if ($cfg["locked"]) {
			//批量操作按钮
			$cfg['adminBatchAction'] = ' <a class="class_addchildclass twinkle" href="javascript:void(0)" onclick="adminDoType(\'dels\')" >删除</a>
                <a class="class_addchildclass twinkle" href="javascript:void(0)" onclick="adminDoType(\'dellocked\')" >审核所选</a>';				


			$addsql.=" and locked=1 and del=0";
		} elseif ($cfg["del"]) {
			$addsql.=" and del=1 ";
			//还原及彻底删除批量操作按钮
			$cfg['adminBatchAction'] = ' <a class="class_addchildclass twinkle" href="javascript:void(0)" onclick="adminDoType(\'re\')" >还原</a>
                <a class="class_addchildclass twinkle" href="javascript:void(0)" onclick="adminDoType(\'deltrue\')" >彻底删除</a>';

		} else {
			$classidsql = $cfg["classid"] != 0 ? " and classid in(" . allclassids($cfg["classid"]) . ") " : "";
			$addsql.=$classidsql . " and del=0 and locked=0 ";
			//批量操作按钮
			$cfg['adminBatchAction'] = ' <a class="class_addchildclass twinkle" href="javascript:void(0)" onclick="adminDoType(\'dels\')" >删除</a>
                ';
		}
		echo $this->reLabel(modelpath."index.html");
		
	}
	//文章列表
	function mylist($loopstr)
	{
		global $cfg,$db,$admin;
		$cfg["htmlPath"]=htmlPath();

		$pagesize=$cfg["adminpagesize"];
		$cfg["pagesize"]=$pagesize;
		$beginid=($cfg["page"]-1)*$pagesize;
		$addsql="";
		if($cfg["commendstr"]!=""){
			$addsql.=" and commend like '%".$cfg["commendstr"]."%' ";
		}elseif($cfg["locked"]){
			$addsql.=" and locked=1 and del=0";
		}elseif($cfg["del"]){
			$addsql.=" and del=1 ";
		}else{
			$classidsql=$cfg["classid"]!=0?" and classid in(".allclassids($cfg["classid"]).") ":"";
			$addsql.=$classidsql." and del=0 and locked=0 ";
		}
		$addsql.=$this->getsearsql_art();

		$numsql="select * from #@__".model." where lanstr='".lanstr."'".$addsql;
		$cfg["allnums"]=$db->num($numsql,"allnum");
		$thesql="select * from #@__".model." where lanstr='".lanstr."'".$addsql." order by sortid desc limit $beginid,$pagesize";

		$db->dosql($thesql);

		while($rs=$db->GetArray())
		{
			$cfg["classname"]=gets("classname","class","lanstr='".lanstr."' and lanid=".$rs["classid"]);
			$cfg["lanid"]=$rs["lanid"];
			$cfg["sortid"]=$rs["sortid"];
			$cfg["pic"]=$rs["picurl"]!=""?$this->viewpicurl(model_imagepath,$rs["picurl"]):"";
			if($cfg["keyword"]!=""){
				$cfg["title"]=str_replace($cfg["keyword"],"<b class=red>".$cfg["keyword"]."</b>",$rs["title"]);
			}else{
				$cfg["title"]=$rs["title"];
			}
			//取得其它语言的产品名称，
			if(lannums>1){
				$thesql = "select title from #@__article where lanid={$rs['lanid']} and lanstr='zh_cn'";
				$cfg['lang_title'] = " <span class='list-en'>[".$db->getValue($thesql, 'title')."]<span>";
			}
				
			$cfg["ishot"] = $rs["ishot"];
			$cfg["isvip"] = $rs["isvip"];
			$cfg["istop"] = $rs["istop"];
			$cfg["filestr"] = $this->getFileDownImg(model."file",$rs["file"]);
			$cfg["commend"] = $rs["commend"];
			$cfg["paths"] = $rs["paths"];
			$topclassid=gettopclassid($rs["classidstr"]);
			$cfg["theurl"]="../../?topclassid={$topclassid}&classid={$rs["classid"]}&id={$rs["lanid"]}";
			$cfg["fileurl"]=$cfg["htmlPath"].$rs["paths"].$rs["filename"];
			if(file_exists(wwwroot.$cfg["fileurl"])&&$rs["filename"]!=""){
				$cfg["filelink"]="<a href='".$cfg["fileurl"]."' target='_blank' title='浏览'><img src='".skinspath."htmlfile.gif' /></a>";
			}else{
				$cfg["filelink"]="<img src='".skinspath."tip.gif' alt='未生成' />";
			}
			$funstr.=$this->reLabel2($loopstr);
		}
		return $funstr;
	}


	//文章列表
	function newslist($recommands='')
	{
		global $cfg,$db,$admin;
		$tempArr = array();

		if($recommands)  $tempArr = explode(",",$recommands);
		$thesql="select * from #@__article where del=0 and locked=0 and lanstr='".lanstr."' ".$cfg[model."_orderby"];
		$db->dosql($thesql);
		$temp = '';
		while($rs=$db->GetArray())
		{
			$checked = @in_array($rs['lanid'], $tempArr) ? 'checked' :'';
			//取得其它语言的标题，
			if(lannums>1){
				$thesql = "select title from #@__article where lanid={$rs['lanid']} and lanstr='en'";
				$rs["title"].= " <span class='list-en'>[".$db->getValue($thesql, 'title')."]<span>";
			}
			$temp.='<tr><td><input type="checkbox" class="post_" value="'.$rs["lanid"].'" name="recommand" '.$checked.'/></td>
			<td align="left" >'.$rs["title"].'</td></tr>';

		}
		return $temp ;
	}

	function getsearsql_art()
	{
		global $cfg,$db;
		if($cfg["keyword"]=="")return;
		if($cfg["searchtype"]=="")return;

		$thesql = "select distinct(lanid) from #@__" . model . " where ".$cfg["searchtype"] . " like '%" . $cfg["keyword"] . "%' ";

		$db->dosql($thesql);
		while ($rs=$db->GetArray()){$lanidstr.=$rs["lanid"].",";}
		$lanidstr=trim($lanidstr,",");
		if($lanidstr!=""){return " and lanid in ($lanidstr) ";}else{return " and 1=2 ";}

	}

	function add()
	{
		global $cfg,$db;
		$cfg["fileconfig"]="{type:'".$cfg[model."_filetype"]."',size:".$cfg[model."_filesize"]."}";
		$cfg["title"]=guid_str."添加".$cfg["model_name"];
		$cfg["click"]=0;
		$cfg["ip"]=GetIp();
		if($cfg['field_article_relate']==1) {
			$cfg['recommandlist'] =      $this->newslist();		
			$cfg['relate_filed_table'] = $this->reLabel(TPL_ADMIN_DIR."article/relate.html");
		}
		$thesqlc = "select topclassid from #@__class where lanid=" . $cfg["classid"];
		$rsc = $db->GetOne($thesqlc);
		if($cfg["classid"]==12){
			echo $this->reLabel(modelpath."form_factory.html");
		}else if($rsc["topclassid"]==3){
			echo $this->reLabel(modelpath."form_v.html");
		}else{
			echo $this->reLabel(modelpath."form.html");
		}
		
	}

	function edit($lanid)
	{
		global $cfg,$db,$admin;
		$cfg["fileconfig"]="{type:'".$cfg[model."_filetype"]."',size:".$cfg[model."_filesize"]."}";
		$cfg["lanid"]=$lanid;
		$thesql="select * from #@__".model." where lanid=".$lanid;
		$db->dosql($thesql);
		while($rs=$db->GetArray())//通过lanid访问对应数据
		{
			if($rs["lanstr"]==deflan){//共同数据部分
				$cfg["classid"]=$rs["classid"];
				$cfg["picurl"]=$rs["bigpic"];
				$cfg["file"]=$rs["file"];
				$cfg["ip"]=$rs["ip"];

				$cfg["edittime"]= date("Y-m-d H:i:s",$rs["edittime"]);
				$cfg["pic"]=$rs["picurl"]!=""?$this->viewpicurl(model_imagepath,$rs["picurl"])." ":"";
				$cfg["filenamestr"]=$rs["paths"].$rs["filename"];
				$cfg["addtime"]=$rs["addtime"];
				$recommands = $rs["recommands"];
			}
				
			//独立数据部分
			$cfg["title".$rs["lanstr"]]=$rs["title"];
			$cfg["brief".$rs["lanstr"]]=$rs["brief"];
			$cfg["vurl".$rs["lanstr"]]=$rs["vurl"];
			$cfg["click".$rs["lanstr"]]=$rs["click"];

				
			$cfg["titles".$rs["lanstr"]]=$rs["titles"];
			$cfg["tag".$rs["lanstr"]]=$rs["tag"];
			$cfg["about".$rs["lanstr"]]=$rs["about"];
				
				
			$cfg["content".$rs["lanstr"]]=$db->getValue("select content from #@__".models." where lanid=$lanid and lanstr='".$rs["lanstr"]."'","content");
		}
		if($cfg['field_article_relate']==1) {
			$cfg['recommandlist'] = $this->newslist($recommands);
			$cfg['relate_filed_table'] = $this->reLabel(TPL_ADMIN_DIR."article/relate.html");
		}
		$cfg["title"]=guid_str.$cfg["model_name"]."编辑";
		$thesqlc = "select topclassid from #@__class where lanid=" . $cfg["classid"];
		$rsc = $db->GetOne($thesqlc);
		if($cfg["classid"]==12){
			echo $this->reLabel(modelpath."form_factory.html");
		}else if($rsc["topclassid"]==3){
			echo $this->reLabel(modelpath."form_v.html");
		}else{
			echo $this->reLabel(modelpath."form.html");
		}
	}

	/**
	 *
	 * 文章编辑
	 */
	function update()
	{
		global $cfg,$db,$admin;
		$now = time();
		$lanid=formatnum($_POST["lanid"],0);
		$classid=formatnum($_POST["classid"],0);
		if($classid==0)die("{err}请确定栏目是否存在！");
		$rs=$db->GetOne("select classidstr,paths,model from #@__class where paths<>'' and lanid=".$classid);
		if(!instr($rs["model"],"article"))die("{err}错误：当前栏目所在模型【{$rs["model"]}】内不可以添加资料！");
		$classidstr=$rs["classidstr"];
		$paths=$rs["paths"];
		$picurl=$_POST["picurl"];
		$thesqlc = "select topclassid,sortid from #@__class where lanid=" . $classid;
		$rsc = $db->GetOne($thesqlc);

		$bigpic = $_POST["picurl"];
		if ($picurl != "") {
			$smallpic = getSmallpic($picurl);
			$p = webroot . article_imagepath;
			$picSet = new picSet($p . $picurl);
			if($rsc["topclassid"]==3){
				$picSet->suo($p . $smallpic, 640,500,$cfg['pic_zoom_bgcolor']);
			}else if($rsc["topclassid"]==333333){
				$picSet->suo($p . $smallpic, 400,300,$cfg['pic_zoom_bgcolor']);
			}
			else{
				$picSet->suo($p . $smallpic, 400, 300,$cfg['pic_zoom_bgcolor']);
			}
			$sec = $picSet->save();
			$picSet = null;
			$picurl  = $smallpic;
		}

		$file=$_POST["file"];
		
		$newlanid=0;
		
		doMydb(0);
		$recommands = isset($_POST['recommand']) ? $_POST['recommand'] :  '';

		$edittime = $_POST['edittime'] ? strtotime($_POST['edittime']) : $now;

		for($i=0;$i<lannums;$i++)
		{
			$lanstr=$cfg["language"][$i];
			$lanpath=$cfg["languagepath"][$i];
			$title=post("title".$lanstr);
			$brief=post("brief".$lanstr);
			$vurl=post("vurl".$lanstr);	
			$click=formatnum($_POST["click".$lanstr],0);
			$content=post("content".$lanstr);
				
			
			$titles=post("titles".$lanstr);
			if($titles=="")  $titles=$title;
			$tag=post("tag".$lanstr);
			$about=post("about".$lanstr);
			$about = $about!="" ? $about : $brief;
				
			/**
			 * 如有其它自定义字段请在此添加, 并更新SQL语句
			 */
			$rsu = $db->GetOne("select classidstr,paths,model,classname from #@__class where paths<>'' and lanid=" . $classid." and lanstr='".$lanstr."'");
			if($rsu["classname"]!=""){	
			if($lanid){
				$addtime=$_POST["addtime"];
				if(!empty($_POST["titleen"])){
					$filename = strtolower(str_replace(" ", "-", $_POST["titleen"])) . ".html";
					$filename = str_replace("+", "", $filename);
					$filename = str_replace(":", "", $filename);
					$filename = str_replace("?", "", $filename);
					$filename = str_replace("$", "", $filename);
					$filename = str_replace(",", "", $filename);
					$filename = str_replace("-[$add]", "", $filename);
				}else{
					$filename = $this->getFilename1($_POST["titlezh_cn"]) . ".html";
					$filename = str_replace("+", "", $filename);
					$filename = str_replace("$", "", $filename);
					$filename = str_replace(":", "", $filename);
					$filename = str_replace("?", "", $filename);
					$filename = str_replace(",", "", $filename);
					$filename = str_replace("-[$add]", "", $filename);
				}
				if($i==0)
				{
					$filenameck="select id from #@__".model." where filename='".$filename."' and lanstr='".$lanstr."' and lanid<>$lanid and classid=".$classid;
					if($db->num($filenameck))$filename.=$lanid."/";
				}
				
				if($title!=""){
					$thesql12 = "select * from #@__" . model . " where lanid=" . $lanid." and lanstr='".$lanstr."'";
					$db->dosql($thesql12);
					$rs12 = $db->GetArray();
					if(empty($rs12)){
						$thesqls12 = "insert into #@__" . model . " (lanid,lanstr,title) values ($lanid,'" . $lanstr . "','" . $title . "')";
					}
					$db->execute($thesqls12);
				}
				
				
				$thesql="update #@__".model." set
				classid=$classid,
				classidstr='".$classidstr."',
				title='".$title."',
				brief='".$brief."',				
				vurl='".$vurl."',				
				paths='".$paths."',
				filename='".$filename."',
				picurl='".$picurl."',
				bigpic='".$bigpic."',
				file='".$file."',
				titles='".$titles."',
				recommands='".$recommands."',							
				tag='".$tag."',
				about='".$about."',
				click=$click,
				edittime=$edittime				
				where lanstr='".$lanstr."' and lanid=".$lanid;
				$db->execute($thesql);
				$thesql23=$thesql;
				//	echo $thesql;
				$thesql = "select * from #@__" . models . " where lanid=" . $lanid." and lanstr='".$lanstr."'";
				$db->dosql($thesql);
				$rs = $db->GetArray();
				if(!empty($rs)){
					$thesqls = "update #@__" . models . " set content='" . $content . "' where lanstr='" . $lanstr . "' and lanid=" . $lanid;
				}else{
					$thesqls = "insert into #@__" . models . " (lanid,lanstr,content) values ($lanid,'" . $lanstr . "','" . $content . "')";
				}
				$db->execute($thesqls);
				$oldpaths=wwwroot.$lanpath.$_POST["filenamestr"];
				$nowfilestr=wwwroot.$lanpath.$paths.$filename;
				if($oldpaths!=$nowfilestr&&file_exists($oldpaths)){delfile($oldpaths);}
			}else{
				if(!empty($_POST["titleen"])){
					$filename = strtolower(str_replace(" ", "-", $_POST["titleen"])) . ".html";
					$filename = str_replace("+", "", $filename);
					$filename = str_replace(":", "", $filename);
					$filename = str_replace("?", "", $filename);
					$filename = str_replace("$", "", $filename);
					$filename = str_replace(",", "", $filename);
					$filename = str_replace("-[$add]", "", $filename);
				}else{
					$filename = $this->getFilename1($_POST["titlezh_cn"]) . ".html";
					$filename = str_replace("+", "", $filename);
					$filename = str_replace(":", "", $filename);
					$filename = str_replace("$", "", $filename);
					$filename = str_replace("?", "", $filename);
					$filename = str_replace(",", "", $filename);
					$filename = str_replace("-[$add]", "", $filename);
				}
				if($i==0){
					$filenameck="select id from #@__".model." where filename='".$filename."' and lanstr='".$lanstr."' and classid=".$classid;
					$cknum=$db->num($filenameck);
				}
				$thesql="insert into #@__".model." (lanid,lanstr,sortid,classid,classidstr,title,brief,vurl,paths,filename,titles,recommands,tag,about,picurl,bigpic,locked,addtime,edittime,click,file)
				 values (
				$newlanid,'".$lanstr."',$newlanid,$classid,'".$classidstr."','".$title."','".$brief."','".$vurl."','".$paths."','".$filename."','".$titles."','".$recommands."','".$tag."','".$about."','".$picurl."','".$bigpic."',".$cfg["adminadd_locked"].",$now,$edittime,$click,'".$file."')";
				echo $thesql;
				$db->execute($thesql);
				if($i==0){
					$newlanid=$db->GetLastID();
					if(!empty($_POST["titleen"])){
						$filename = strtolower(str_replace(" ", "-", $_POST["titleen"])) . ".html";
						$filename = str_replace("+", "", $filename);
						$filename = str_replace(":", "", $filename);
						$filename = str_replace("?", "", $filename);
						$filename = str_replace("$", "", $filename);
						$filename = str_replace(",", "", $filename);
						$filename = str_replace("-[$add]", "", $filename);
					}else{
						$filename = $this->getFilename1($_POST["titlezh_cn"]) . ".html";
						$filename = str_replace("+", "", $filename);
						$filename = str_replace(":", "", $filename);
						$filename = str_replace("$", "", $filename);
						$filename = str_replace("?", "", $filename);
						$filename = str_replace(",", "", $filename);
						$filename = str_replace("-[$add]", "", $filename);
					}
					$thesql2="update #@__".model." set lanid=$newlanid,sortid=$newlanid,filename='".$filename."' where id=".$newlanid;
					$db->execute($thesql2);
				}
				$thesql2s="insert into #@__".models." (lanid,lanstr,content) values ($newlanid,'".$lanstr."','".$content."')";
				$db->execute($thesql2s);
			}
			}
		}
		doMydb(1);
		if($lanid){die("{ok}恭喜，".$cfg["model_name"]."编辑成功！");}else{die("{ok}恭喜，".$cfg["model_name"]."添加成功！");}
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
		$admin->adminck("article_add");
		$myclass->add();
		break;
	case "edit":
		$admin->adminck("article_edit");
		$myclass->edit($lanid);
		break;
	case "update":
		refPage(2);
		$admin->adminck_ajax("article_edit");
		$myclass->update();
		break;
	case "sort":
		refPage(1);
		$admin->adminck_ajax("article_edit");
		$myclass->sort(model);
		break;
	case "dbref":
		$myclass->dbref(model,models);
		break;
	case "toggle":
		refPage(1);
		$admin->adminck_ajax("article_edit");
		$myclass->toggle("article");
		break;
	case "commenddo":
		refPage(1);
		$myclass->commenddo(model,$lanid);
		break;
	case "guid":
		$myclass->guid(model);
		break;
	case "admindo":
		refPage(1);
		$admin->adminck_ajax("article_edit");
		$myclass->admindo($lanid);
		break;
	default:
		$myclass->def();
}
?>