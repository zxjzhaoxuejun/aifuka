<?php

/**
 *  网站栏目管理
 */
include("../../include/inc.php");
include(incpath . "funlist.php");


class myclass extends alzCms {

	function __construct() {
		global $cfg, $admin;
		
		$this->admincache();
		loadlanXml("class", "class_");

		define("model", "class");


		$cfg["template"] = "";
		//formatnum:检测变量是否为数字或数字字符串
		$cfg["get_fid"] = formatnum($_GET["fid"], 0);
		if ($_GET["c"] != "") {
			setcookies("classlistid", 0);
			$cfg["bigclass"] = 1;
			$cfg["classlistid"] = 0;
		} else {
			$cfg["bigclass"] = 0;
			$cfg["classlistid"] = formatnum(getcookies("classlistid"), 0);
		}
		$cfg["layerstr"] = "　　";
		if ($cfg["bigclass"]) {
			$cfg["class_pic"] = 0;
			$cfg["class_banner"] = 0;
		}
		
		//超级管理员专用表单
		if (getsession("admin_username") == $cfg["superusername"]) {
			$cfg["class_template"] = 1;
			$cfg["class_model"] = 1;
			$cfg["is_superadmin"] = 1;
		}
		else {
			$cfg["class_template"] = 0;
			$cfg["class_model"] = 0;
		}		

		$lanid = $_GET["lanid"];
		switch ($_GET["action"]) {
			case "add":
				$admin->adminck("class_add");
				$this->add();
				break;
			case "edit":
				$admin->adminck("class_edit");
				$this->edit($lanid);			
				break;
			case "del":
				refPage(1);
				$admin->adminck("class_del");
				$this->del($lanid);
				break;
			case "update":
				refPage(2);
				$admin->adminck_ajax("class_add");
				$this->update();
				break;
			case "sort":
				refPage(1);
				$admin->adminck_ajax("class_add");
				$this->sort("class");
				break;
			case "guid":
				$this->guid_class();
				break;
			case "getallclassids":
				$this->getallclassids();
				break;
			case "getclassidstr":
				$this->getclassidstr();
				break;
			case "getlayer":
				$this->getlayer($lanid);
				break;
			case "dbref":
				$this->dbref("class");
				break;
			
			case "toggle":
				$admin->adminck_ajax("class_edit");
				refPage(1);
				$this->toggle("class");
				break;			
			
			case "setgoto":
				$this->setgoto($lanid);
				break;
			case "admindo":
				refPage(1);
				$admin->adminck_ajax("class_edit");
				$this->admindo($lanid);
				break;
			default:
				$this->def();
		}
	}

	function __destruct() {
		$this->admincache2();
	}

	function admindo($lanidstr) {
		$str = $_GET["str"];
		$fun = $_GET["fun"];
		switch ($fun) {
			case "dels":
				$this->mydid($lanidstr, model, "del=1");
				break;
			case "re":
				$this->mydid($lanidstr, model, "del=0");
				break;
			case "reall":
				$this->mydidall(model, "del=0");
				break;
			case "deltrue":
				$this->deltrue(model, $lanidstr);
				break;
			case "delclear":
				$this->deltrue(model);
				break;
			case "locked":
				$this->mydid($lanidstr, model, "locked=1");
				break;
			case "dellocked":
				$this->mydid($lanidstr, model, "locked=0");
				break;
			case "delalllocked":
				$this->mydidall(model, "locked=0");
				break;
		}
		die("{ok}恭喜," . $str . "操作成功!");
	}

	/**
	 *
	 * 获取分类 Config 信息
	 * @param $fid
	 */
	function config($fid) {
		global $db, $cfg;
		$fid = formatnum($fid, 0);
		$thesql = "select model from #@__class where lanid=" . $fid;
		$rs = $db->GetOne($thesql);
		if ($fid > 0) {
			if ($rs['model'] == 'product')
			$cfg["viewclassdata"] = '<a href="' . adminpath . 'product/?classid=' . $fid . '" class="class_addchildclass  twinkle">查看所有产品</a>';
			else

			if ($rs['model'] == 'article')
			$cfg["viewclassdata"] = '<a href="' . adminpath . 'article/?classid=' . $fid . '" class="class_addchildclass  twinkle">查看所有文章</a>';

		}
		$config = getcache("class_" . $fid);


		$cfg["model"] = $rs["model"];
		$cfg["class_layer"] = formatnum($config["layer"], 1);
		$cfg["class_commend"] = $config["commend"];
		$cfg["class_urlgoto"] = $config["urlgoto"];
		$cfg["class_pic"] = $config["pic"];
		$cfg["class_banner"] = $config["banner"];
		$cfg["class_shortname"] = $config["shortname"];
		$cfg["class_desc"] = $config["desc"];
		

	}

	function getlayer($fid) {
		global $db, $cfg;
		$fid = formatnum($fid, 0);
		$config = getcache("class_" . $fid);
		rw($config);
		die(formatnum($config["layer"], 2));
	}

	function def() {
		global $cfg, $db;					
		if ($cfg["get_fid"]) {
			$this->config($cfg["get_fid"]);
			if ($cfg["class_inpage"] && $cfg["myadmin_username"] != $cfg["superusername"]) {
				$cfg["addchildclass"] = "";
			} else {
				$cfg["addchildclass"] = "<a href=\"?action=add&topclassid=" . $cfg["get_fid"] . "\" class=\"class_addchildclass twinkle\">添加子栏目</a>";
			}
		} else {//默认配置
			$cfg["addmainclass"] = "<a href=\"?action=add&main=1\" class=\"class_addchildclass twinkle\">添加主栏目</a>";
			if (getsession("admin_username") != $cfg["superusername"]) {
				$cfg["addmainclass"] = "";
			}
			$cfg["class_dbname"] = "#@__class";
			$cfg["class_layer"] = 10;			
			$cfg["class_urlgoto"] = 0;
			$cfg["class_pic"] = 0;
			$cfg["class_banner"] = 1;			
			$cfg["class_desc"] = 0;
			
		}
		$cfg["commend_io"] = $cfg["class_commend"] ? "" : "false";
		$cfg["pictitle"] = $cfg["class_pic"] ? "<td class='cellb'>图片</td>" : "";
		$cfg["bannertitle"] = $cfg["class_banner"] ? "<td>Banner</td>" : "";
		$cfg["numstitle"] = $cfg["class_datanums"] ? "<td>数量</td>" : "";
		$cfg["lanid"] = 0;
		echo $this->reLabel(TPL_ADMIN_DIR."class/index.html");
	}

	function classlist($loopstr) {
		global $cfg, $db;
		$funstr = $this->classlistLoop($cfg["get_fid"], "", $loopstr);
		if ($funstr != "")
		$cfg["have_record"] = true;
		return $funstr;
	}

	function getModelName($model) {
		global $cfg;

		$arr1 = explode("|", $cfg["model_str{$cfg["lanstr"]}"]);

		$arr2 = explode("|", $cfg["model_key"]);

		for ($i = 0; $i < count($arr2); $i++) {
			if ($model == $arr2[$i])
			return $arr1[$i];
		}
	}

	function classlistLoop($fid, $str, $loopstr) {
		global $cfg, $db;
		$conn = $db->linkID;
		$thesql = "select * from #@__class where fid=$fid and lanstr='" . lanstr . "' and lid<=" . $cfg["class_layer"] . " order by sortid,id";
		$thesql = $db->SetQuery($thesql);
		$result = mydb_query($thesql, $conn);
		while ($rs = mydb_fetch_array($result)) {		
			$cfg["model"] = $this->getModelName($rs["model"]);
			$cfg["lanid"] = $rs["lanid"];
			$cfg["sortid"] = $rs["sortid"];
			$cfg["lid"] = $rs["lid"];
			$cfg["botnav"] = $rs["botnav"];
			$cfg["shortname"] = $rs["shortname"];

			$cfg["topclassid"] = $rs["topclassid"];
			$cfg["main"] = $rs["lid"] ? "" : "&main=1";
			if ($cfg["class_pic"]) {
				$pic = $rs["picurl"] != "" ? $this->viewpicurl(class_imagepath, $rs["picurl"]) : "";
			}
			if ($cfg["class_banner"]) {
				$banner = $rs["banner"] != "" ? $this->viewpicurl(class_imagepath, $rs["banner"]) : "";
			}
			$cfg["picurl"] = $cfg["class_pic"] ? "<td>" . $pic . "</td>" : "";
			$cfg["banner"] = $cfg["class_banner"] ? "<td>" . $banner . "</td>" : "";
			$b1 = $rs["pid"] ? "<b>" : "";
			$b2 = $rs["pid"] ? "</b>" : "";
			$cat = $db->GetOne("select * from #@__class where lanid=".intval($rs["lanid"])." and lanstr='en'");
			$cfg["classname"] = $str . $b1 . "<img src='" . skinspath . "jian.gif' align='absmiddle' />" . "<a href='javascript:classlistto(" . $rs["lanid"] . "," . $cfg["topclassid"] . "," . $cfg["lid"] . ");'>" . $rs["classname"] . $b2 . "</a>";
			
			$thesql2 = "select id from #@__class where fid in(" . allclassids($cfg["lanid"]) . ") and lid<=" . $cfg["class_layer"] . " and lanstr='" . lanstr . "'";

			$nums = $db->num($thesql2, "class");
			if ($nums > 0) {
				$cfg["chclassnums"] = "<span class='ccc' title='子栏目：$nums'>(" . $nums . ")</span>";
			} else {
				$cfg["chclassnums"] = "";
			}
			if ($cfg["class_datanums"] && $db->IsTable($cfg["class_dbname"])) {
				$nums = @$db->num("select id from #@__" . $cfg["class_dbname"] . " where classid in(" . allclassids($cfg["lanid"]) . ") and lanstr='" . lanstr . "'", "class");
				$cfg["nums"] = "<td><b class=\"red\">$nums</b></td>";
			} else {
				$cfg["nums"] = "";
			}
			$cfg["classdel"] = $rs["locked"] ? "" : "<a class=\"class_del\" href=\"javascript:did('?action=del&lanid=" . $rs["lanid"] . "&db=&paths=" . $rs["paths"] . "');\">[删除]</a>";

			if ($rs['fid'] == 0 && getsession("admin_username") != $cfg["superusername"]) {
				$cfg["classdel"]  = "";
			}


			if ($rs['model'] == 'article' ||  $rs['model'] == 'feedback'  || $rs['model'] == 'product' || $rs['model'] == 'job' || $rs['model'] == 'download') {
				if($rs['model'] != 'feedback')
					$cfg['classdata'] = '<a class="class_edit" href="' . adminpath . $rs['model'] . '/?action=add&classid=' . $rs['lanid'] . '">[添加数据]</a>  ';
				else
					$cfg['classdata'] = '';
					
				$cfg['classdata'].='<a class="class_edit" href="' . adminpath . $rs['model'] . '/?classid=' . $rs['lanid'] . '&c=1">[查看本栏目数据]</a>';
			} else {
				//$cfg['classdata'] = '<a class="class_edit" href="?action=edit&lanid=' . $rs['lanid'] . '&lid=' . $rs['lid'] . '&topclassid=' . $rs['topclassid'] . '">[查看本栏目数据]</a>';
				$cfg['classdata'] = '';
			}


			if ($rs["model"] != "-") {
				$cfg["path"] = str_replace("/", "", $rs["path"]);
				$cfg["fileurl"] = htmlPath() . $rs["paths"];
				if (file_exists(wwwroot . $cfg["fileurl"])) {
					$cfg["filelink"] = "<a href='" . $cfg["fileurl"] . "' target='_blank' title='浏览'><img src='" . skinspath . "htmlfile.gif' /></a>";
				} else {
					$cfg["filelink"] = "<img src='" . skinspath . "tip.gif' alt='未生成' />";
				}
			} else {
				$cfg["filelink"] = "-";
				$cfg["path"] = "-";
			}

			$funstr.=$this->reLabel2($loopstr);
			if (!$cfg["bigclass"])
			$funstr.=$this->classlistLoop($rs["lanid"], $cfg["layerstr"] . $str, $loopstr);
		}
		return $funstr;
	}

	function get_template_option($dir, $template){
		global $cfg;
		$list = mydir(template.TPL_FRONT_DIR.$cfg['front_theme'].'/'.$dir);
		foreach ($list as $val){
			if($val == '.' || $val == '..') continue;
			$val = $dir.'/'.$val;
			$option.= $val==$template ? "<option value='$val' selected>$val</option>" : "<option value='$val'>$val</option>" ;
		}

		return $option;
			
	}
	// 添加页面
	function add() {
		global $cfg, $db;
		$this->config($_GET["topclassid"]);
		$cfg["topclassid"] = $_GET["topclassid"];
		$cfg["title"] = guid_str . t(2);
		if ($_GET["main"]) {
			//主栏目
			$cfg["class_banner"] = 1;
			$cfg["config_io"] = 1;
			$cfg["class_mainclass"] = 1;			
			
		} else {
			//子栏目
			
			$cfg["config_io"] = 0;
			$cfg["class_mainclass"] = 0;
			$cfg["class_locked"] = 0;
			$cfg["class_banner"] = 0;		
			$cfg["class_path"] = 1;
			$cfg["class_jump"] = 0;
		}
					
		$cat = $db->GetOne("select * from #@__class where lanid=".intval($_GET["topclassid"])." and lanstr='".lanstr."'");
		
		$cfg['template_main_option'] = $this->get_template_option('main',$cat["template_main"]);
		$cfg['template_list_option'] = $this->get_template_option('list', $cat["template_list"]);
		$cfg['template_detail_option'] = $this->get_template_option('detail', $cat["template_detail"]);

		$cfg["classname"] = $cat["classname"];	
			
		$cfg["locked"] = 0;
		
	
		$cfg["class"] = getcookies("fid_lid");
		echo $this->reLabel(TPL_ADMIN_DIR."class/form.html");
	}

	//编辑页面
	function edit($lanid) {
		global $cfg, $db, $admin;		
		$configid = $_GET["topclassid"] ? $_GET["topclassid"] : $lanid;		
		$this->config($configid);	
		
		if ($_GET["lid"] == 0) {
			$cfg["class_config"] = 1;
			$cfg["config_io"] = 1;
			$cfg["class_mainclass"] = 1;			
			$cfg["class_locked"] = 1;
		} else {
			$cfg["class_config"] = 0;
			$cfg["config_io"] = 0;
			$cfg["class_mainclass"] = 0;
			$cfg["class_locked"] = 0;
			$cfg["class_path"] = 1;
			$cfg["class_jump"] = 0;
			$cfg["class_banner"] = 0;			
		}
		

		$cfg["lanid"] = $lanid;
		$thesql = "select * from #@__class where lanid=" . $lanid;
		$db->dosql($thesql);
		while ($rs = $db->GetArray()) {//通过lanid访问对应数据
			if ($rs["lanstr"] == deflan) {//共同数据部分
				$cfg["pid"] = $rs["pid"];
				$cfg["topclassid"] = $rs["topclassid"];
				$cfg["fid"] = $rs["fid"];
				$cfg["model"] = $rs["model"];
				$cfg["lid"] = $rs["lid"];
				$cfg["locked"] = $rs["locked"];
				$cfg["classidstr"] = $rs["classidstr"];
				$cfg["path"] = str_replace("/", "", $rs["path"]);
				$cfg["paths"] = $rs["paths"]; //编辑保存时和原来路径比对，若不相同，需要移动文件
				
				
				
				$cfg["class"] = $rs["fid"] . "|" . $rs["lid"];
				$cfg["delclass"] = $rs["lanid"] . "|" . ($rs["lid"] + 1);

					

				$cfg['template_main_option'] = $this->get_template_option('main',$rs["template_main"]);
				$cfg['template_list_option'] = $this->get_template_option('list', $rs["template_list"]);
				$cfg['template_detail_option'] = $this->get_template_option('detail', $rs["template_detail"]);
			}
			//独立数据部分
			//$cfg["picurl" . $rs["lanstr"]] = $rs["bigpic"];
			$cfg["picurl"] = $rs["bigpic"];
			$cfg["classname" . $rs["lanstr"]] = $rs["classname"];
			$cfg["shortname" . $rs["lanstr"]] = $rs["shortname"];
			$cfg["brief" . $rs["lanstr"]] = $rs["brief"];
			$cfg["banner" . $rs["lanstr"]] = $rs["banner"];
			$cfg["urlgoto" . $rs["lanstr"]] = $rs["urlgoto"];
			$cfg["content" . $rs["lanstr"]] = $rs["content"];
			
			$cfg["titles" . $rs["lanstr"]] = $rs["titles"];
			$cfg["tag" . $rs["lanstr"]] = $rs["tag"];
			$cfg["about" . $rs["lanstr"]] = $rs["about"];
		}		

		echo $this->reLabel(TPL_ADMIN_DIR."class/form.html");
		
	}

	function update() {
		global $cfg, $db, $admin;

		$now = time();
		$lanid = formatnum($_POST["lanid"], 0); // 分类ID。
		$pid = formatnum($_POST["pid"], 0);  //是否主栏目
		
		$classidstr = 0;
		$topclassid = intval($_POST["topclassid"]); //顶级分类
		
		$model = $_POST["model"]; //模型
		$template_main = $_POST["template_main"]; //框架模板
		$template_list = $_POST["template_list"]; //列表页模板
		$template_detail = $_POST["template_detail"];  //详情页模板


		
		$class = explode("|", $_POST["class"]);  //选择的父级分类参数
		$fid = intval($class[0]);  //父级分类
		$lid = intval($class[1]); //分类层数
		
		if ($lanid) {//编辑			
			$classidstr = $fid == intval($_POST["oldfid"]) ? $_POST["classidstr"] : $this->getIdStr($fid, $lanid);	
			$oldpath = $_POST["paths"];
		}
		
		$locked = $_POST["locked"];	//锁定分类	
		$path = $_POST["path"];
		$path = str_replace(" ", "", $path);
		$path = strtolower($path);
		
			
		//分类设置 config 部分
		if ($_POST["configIo"]) {
			$config["layer"] = $_POST["class_layer"];			
			$config["urlgoto"] = $_POST["class_urlgoto"];
			$config["pic"] = $_POST["class_pic"];
			$config["banner"] = $_POST["class_banner"];
			$config["desc"] = $_POST["class_desc"];
			$config["shortname"] = intval($_POST["class_shortname"]);

		}
		$fpath = $db->getValue("select paths from #@__class where paths<>'' and lanid=$fid", "paths");

		if ($path == "") {
			
			if(!empty($_POST["classnameen"])){
				$path = strtolower(str_replace(" ", "-", $_POST["classnameen"])) . "/";
				$paths = strtolower(str_replace(" ", "-", $_POST["classnameen"])) . "/";
			}else{
				$path = $this->getFilename1($_POST["classnamezh_cn"]) . "/";
				$paths = $this->getFilename1($_POST["classnamezh_cn"]) . "/";
			}
		} else {
			$path.="/";
			$paths = $path;
		}
		
			//分类列表图
		$picurl = $_POST["picurl"];

		$bigpic = $_POST["picurl"];
		if ($picurl != "") {
			$smallpic = getSmallpic($picurl);
			$p = webroot . class_imagepath;
			$picSet = new picSet($p . $picurl);
			$picSet->suo($p . $smallpic, $cfg['picsize_class_list_w'], $cfg['picsize_class_list_h'],$cfg['pic_zoom_bgcolor']);
			$sec = $picSet->save();
			$picSet = null;
			$picurl  = $smallpic;
		}
		
		doMydb(0);
		for ($i = 0; $i < lannums; $i++) {
		
			$lanstr = $cfg["language"][$i];
		
			$classname = post("classname" . $lanstr);
			$shortname = post("shortname" . $lanstr);
			if ($shortname == "") $shortname = $classname;
			
			$banner = post("banner" . $lanstr);			
			$brief = post("brief" . $lanstr);
			$urlgoto = $_POST["urlgoto" . $lanstr];
			$content = post("content" . $lanstr);
			
			$titles = post("titles" . $lanstr);
			if ($titles == "")	$titles = $classname;			
			$tag = post("tag" . $lanstr);
			$about = post("about" . $lanstr);
			$about = $about!="" ? $about : $brief;
			
			if ($lanid) {//编辑 用语言来循环
				if ($i == 0) {
					/*$nums = $db->num("select id from #@__class where paths='" . $paths . "' and lanid<>$lanid");
					if ($nums)
					die("{err}存在相同路径！");*/
					if ($_POST["configIo"])
					setcache("class_" . $lanid, $config);
				}
				
				$thesql = "select * from #@__" . model . " where lanid=" . $lanid." and lanstr='".$lanstr."'";
				$db->dosql($thesql);
				$rs = $db->GetArray();
				if(empty($rs)){
					$thesqls = "insert into #@__" . model . " (lanid,lanstr,titles) values ($lanid,'" . $lanstr . "','" . $titles . "')";
					$db->execute($thesqls);
				}
				
				$thesql = "update #@__class set
				model='" . $model . "',
				template_main='" . $template_main . "',
				template_list='" . $template_list . "',
				template_detail='" . $template_detail . "',
				fid=$fid,
				lid=$lid,
				pid=$pid,
				classidstr='" . $classidstr . "',
				topclassid=$topclassid,
				classname='" . $classname . "',
				shortname='" . $shortname . "',
				brief='" . $brief . "',
				locked=$locked,
				urlgoto='" . $urlgoto . "',
				path='" . $path . "',
				paths='" . $paths . "',
				picurl='" . $picurl . "',
				bigpic='" . $bigpic . "',
				banner='" . $banner . "',
				content='" . $content . "',
				titles='" . $titles . "',				
				tag='" . $tag . "',
				about='" . $about . "'					
				where lanstr='" . $lanstr . "' and lanid=" . $lanid;
				echo $thesql;	
				$db->execute($thesql);
				/*if ($oldpath != $paths) {
					$this->safepath($paths);
					for ($s = 0; $s < lannums; $s++) {
						$htmlPaths = wwwroot . htmlPath($cfg["language"][$s]);
						$oldpaths = $htmlPaths . $oldpath;
						$newpaths = $htmlPaths . $paths;
						if (file_exists($newpaths)) {
							die("{err}存在相同路径！");
						}
						if (file_exists($oldpaths)) {
							rename($oldpaths, $newpaths);
						}
					}
				}*/
			} else {
				if ($i == 0) {
					$this->safepath($paths);
					$newlanid = 0;
					setcookies("fid_lid", $class[0] . "|" . $class[1]);
					/*$nums = $db->num("select id from #@__class where paths='" . $paths . "'");
					if ($nums)
					die("{err}存在相同路径！");*/
				}
				$thesql = "insert into #@__class (				lanid,lanstr,model,template_main,template_list,template_detail,fid,lid,sortid,topclassid,classidstr,classname,shortname,brief,
				path,paths,urlgoto,tag,about,content,picurl,bigpic,banner,locked,pid,titles
				) values (
				$newlanid,'" . $lanstr . "','" . $model . "','" . $template_main . "','" . $template_list . "','" . $template_detail . "',$fid,$lid,$newlanid,'" . $topclassid . "','" . $classidstr . "','" . $classname . "','" . $shortname . "','" . $brief . "',
				'" . $path . "','" . $paths . "','" . $urlgoto . "','" . $tag . "','" . $about . "','" . $content . "','" . $picurl . "','".$bigpic . "','" . $banner . "',$locked,$pid,'" . $titles . "')";
								
				$db->execute($thesql);				
				if ($i == 0) {
					$newlanid = $db->GetLastID();					
					if( $topclassid==0 )   $topclassid = $newlanid;					
					$classidstr = $fid>0 ? $this->getIdStr($fid, $newlanid) : $newlanid;
				
						
					if ($_POST["path"] == "") {
						if(!empty($_POST["classnameen"])){
							$path = strtolower(str_replace(" ", "-", $_POST["classnameen"])) . "/";
							$paths = strtolower(str_replace(" ", "-", $_POST["classnameen"])) . "/";
						}else{
							$path = $this->getFilename1($_POST["classnamezh_cn"]) . "/";
							$paths = $this->getFilename1($_POST["classnamezh_cn"]) . "/";
						}
					}
					$thesql2 = "update #@__class set lanid=" . $newlanid . ",sortid=$newlanid,classidstr='" . $classidstr . "',topclassid='" . $topclassid . "',path='" . $path . "',paths='" . $paths . "' where id=" . $newlanid;					
					$db->execute($thesql2);
					if ($_POST["configIo"]) setcache("class_" . $newlanid, $config);
				}
			}
		}
		
		
		doMydb(1);
		if ($lanid) {
			die("{ok}恭喜，栏目编辑成功！");
		} else {
			die("{ok}恭喜，栏目创建成功！");
		}
	}

	/**
	 *  根据父级分类获取分类ID字符串。
	 */
	function getIdStr($fid, $lanid){
		global $cfg, $db;
				
		$classidstr = $db->getValue("select classidstr from #@__class where lanid=$fid and lanstr='{$cfg["lanstr"]}'", 'classidstr');
		
		return $classidstr.','.$lanid;
	}

	function setting_page()
	{
		global $cfg;
		if($cfg["myadmin_username"] == $cfg["superusername"])
		return $this->reLabel(TPL_ADMIN_DIR."class/setting.html");
	}


	function safepath($path) {
		global $cfg;
		$path = str_replace("/", "", $path);
		$paths = "|" . $path . "|";
		if (instr($cfg["class_notpath"], $paths)) {
			die("{err}栏目路径 $path 为系统禁止使用的路径，请更换！");
		}
	}
	
	/**
	 * 删除栏目操作
	 */
	function del($lanid) {
		global $cfg, $db, $admin;
		
		$rs = $db->GetOne("select locked,model from #@__class where lanid=" . $lanid);
		$locked = $rs["locked"];
		if ($locked)
		die("{err}此栏目已被锁定,请不要非法操作!");
		
		$num = $db->num("select id from #@__class where fid=" . $lanid);
		if ($num > 0)
		die("{err}此栏目有子栏目存在,不可以删除!");
		
		if ($rs['model']!='onepage') {
			$num = $db->num("select id from #@__".$rs['model']." where classid in(" . allclassids($lanid) . ")");
			if ($num > 0)
			die("{err}此栏目有文件存在,不可以删除!");
		}
		$thesql = "delete from #@__class where lanid=" . $lanid;
		$db->execute($thesql);
		if ($cfg["class_deltype"]) {
			for ($i = 0; $i < lannums; $i++) {
				$htmlPaths = htmlPath($cfg["language"][$i]);
				$paths = wwwroot . $htmlPaths . $_GET["paths"];
				if (file_exists($paths)) {
					delfile($paths);
				}
			}
		}
		die("{ok}" . lang("delok"));
	}
	
	/**
	 * 父级分类选项列表
	 */
	function classSelect() {
		global $cfg, $db;
		$cfg["class_alz"] = $_GET["main"];
		$funstr = "<select name='class' id='class' class='post_'>\r\n";
		if ($cfg["class_alz"]) {
			//主栏目
			$funstr.="<option value='0|0'>网站主栏目</option>\r\n";
		} else {
			$topclassid = formatnum($_GET["topclassid"], 0);
			if ($topclassid != 0) {
				$topclassname = $db->getValue("select classname from #@__class where lanid=$topclassid and lanstr='" . lanstr . "'", "classname");
				$funstr.="<option value='$topclassid|1'>" . $topclassname . "</option>\r\n";
			}
			$funstr.=$this->classSelectLoop($topclassid, "├ ");
		}
		$funstr.="</select>\r\n";
		return $funstr;
	}
	/**
	 * 更多子栏目
	 */
	function classSelectLoop($fid, $str) {
		global $cfg, $db;
		$conn = $db->linkID;
		$thesql = "select * from #@__class where fid=$fid and lanstr='" . lanstr . "' order by sortid,id";
		$thesql = $db->SetQuery($thesql);
		$result = mydb_query($thesql, $conn);
		while ($rs = mydb_fetch_array($result)) {
			$lid = $rs["lid"];
			if ($lid < $cfg["class_layer"]) {
				$lanid = $rs["lanid"];
				$value = $lanid . "|" . ($lid + 1);
				$funstr.="<option value='$value'>" . $str . $rs["classname"] . "</option>\r\n";
				$funstr.=$this->classSelectLoop($lanid, "　" . $str);
			} else {
				return $funstr;
			}
		}
		return $funstr;
	}

	function guid_class() {
		global $cfg, $db;
		$classidstr = $_GET["classidstr"];
		$a = $_GET["a"];
		$db->dosql("select topclassid,lanid,classname from #@__class where lanstr='" . lanstr . "' and lanid in($classidstr) order by lid");
		while ($rs = $db->GetArray()) {
			if ($a == "") {
				$temp.=guid_str . "<a href='javascript:classlistto(" . $rs["lanid"] . "," . $rs["topclassid"] . ",0);'>" . $rs["classname"] . "</a>";
			} else {
				$temp.=guid_str . $rs["classname"];
			}
		}
		die($temp);
	}

	function getclassidstr() {
		global $cfg, $db;
		$lanid = $_GET["lanid"];
		$classidstr = $db->getvalue("select classidstr from #@__class where lanid=" . $lanid, "classidstr");
		die($classidstr);
	}

	function getallclassids() {
		$lanid = $_GET["lanid"];
		die(allclassids($lanid));
	}
	
	/**
	 * 模型选项列表
	 */
	function modellist() {
		global $cfg, $admin;
		$temp = "<select name='model' id='model' class='post_'>";
		$keyarr = explode("|", $cfg["model_key"]);
		$modelarr = explode("|", $cfg["model_str" . lanstr]);
		for ($i = 0; $i < count($keyarr); $i++) {			
			$temp.="<option value='$keyarr[$i]'>" . $modelarr[$i] . "【" . $keyarr[$i] . "】</option>\r\n";
		}
		$temp.="</select>\r\n";
		return $temp;
	}

}

$myclass = new myclass();
?>