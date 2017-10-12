<?php
/**
 * 网站产品管理， 如有其它自定义字段的 需求 edit函数 及 update 函数 编辑页面及
 */
include("../../include/inc.php");
include(incpath . "funlist.php");

class myclass extends alzCms {

	function __destruct() {
		$this->admincache2();
	}
	function __construct() {
		global $cfg, $admin;
		
		$this->admincache();
		loadlanXml("product", "product_");
		if ($_GET["c"] != "") {
			setcookies("classid", 0);
			setcookies("page", 1);
			setcookies("commend", "");
			setcookies("locked", 0);
			setcookies("del", 0);
			setcookies("searchtype", "");
			setcookies("keyword", "");
			$cfg["classid"] = formatnum($_GET["classid"], 0);
			$cfg["page"] = formatnum($_GET["page"], 1);
			$cfg["commendstr"] = $_GET["commend"];
			$cfg["locked"] = formatnum($_GET["locked"], 0);
			$cfg["del"] = formatnum($_GET["del"], 0);
			$cfg["searchtype"] = $_GET["searchtype"];
			$cfg["keyword"] = $_GET["keyword"];
		} else {
			$cfg["classid"] = G("classid", $_GET["classid"]);
			$cfg["page"] = G("page", $_GET["page"], 1);
			$cfg["commendstr"] = G("commend", $_GET["commend"]);
			$cfg["locked"] = G("locked", $_GET["locked"], 0);
			$cfg["del"] = G("del", $_GET["del"], 0);
			$cfg["searchtype"] = G("searchtype", $_GET["searchtype"]);
			$cfg["keyword"] = G("keyword", $_GET["keyword"]);
		}
		
		$cfg['curpath'] = '';
		
		
		define("model", "product");
		define("modelpath", TPL_ADMIN_DIR."product/");
		define("model_imagepath", product_imagepath);
		define("models", "products");
		$cfg["model_name"] = "产品";
	}
	/**
	 *
	 * 显示产品列表
	 */
	function def() {
		global $cfg, $db;
		$cfg["otherguidstr"] = $cfg["commendstr"] . "|" . $cfg["locked"] . "|" . $cfg["del"];

		if ($cfg["locked"]) {
			//批量操作按钮
			$cfg['adminBatchAction'] = ' <a class="class_addchildclass" href="javascript:void(0)" onclick="adminDoType(\'dels\')" >删除</a>
                <a class="class_addchildclass" href="javascript:void(0)" onclick="adminDoType(\'dellocked\')" >审核所选</a>';				


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
                <a class="class_addchildclass twinkle" href="javascript:void(0)" onclick="adminDoType(\'locked\')" >取消审核</a>
				   <a class="class_addchildclass twinkle" href="javascript:void(0)" onclick="adminDoType(\'tohtml-product-listandfile\')" >发布</a>';		

		}
		echo $this->reLabel(modelpath . "index.html");
	}

	function mylist($loopstr) {
		global $cfg, $db, $admin;

		$pagesize = $cfg["adminpagesize"];
		$cfg["pagesize"] = $pagesize;
		$beginid = ($cfg["page"] - 1) * $pagesize;

		$addsql = "";
		if ($cfg["commendstr"] != "") {
			$addsql.=" and commend like '%" . $cfg["commendstr"] . "%' ";
		} elseif ($cfg["locked"]) {


			$addsql.=" and locked=1 and del=0";
		} elseif ($cfg["del"]) {
			$addsql.=" and del=1 ";


		} else {
			$classidsql = $cfg["classid"] != 0 ? " and classid in(" . allclassids($cfg["classid"]) . ") " : "";
			$addsql.=$classidsql . " and del=0 and locked=0 ";
		}
		$cfg['adminBatchAction'];

		$cfg["admindo_option"] = $opstr;
		$addsql.=$this->getsearsql_pro();
		$numsql = "select * from #@__" . model . " where lanstr='" . lanstr . "' " . $addsql;
		$cfg["allnums"] = $db->num($numsql, "allnum");
		$thesql = "select * from #@__" . model . " where lanstr='" . lanstr . "' " . $addsql. " order by sortid desc limit $beginid,$pagesize";
		$db->dosql($thesql);
		while ($rs = $db->GetArray()) {
			$cfg["classname"] = gets("classname", "class", "lanstr='" . lanstr . "' and lanid=" . $rs["classid"]);
			$cfg["lanid"] = $rs["lanid"];
			$cfg["sortid"] = $rs["sortid"];
			$cfg["product_sn"] = $rs["product_sn"];
			$cfg["pic"] = $rs["smallpic"] != "" ? $this->viewpicurl(model_imagepath, $rs["smallpic"], $rs["bigpic"]) : "-";
				
			if ($cfg["keyword"] != "") {
				$cfg["title"] = str_replace($cfg["keyword"], "<b class=red>" . $cfg["keyword"] . "</b>", $rs["title"]);
			} else {
				$cfg["title"] = $rs["title"];
			}
			//取得其它语言的产品名称，
			if(lannums>1){
				$thesql = "select title from #@__product where lanid={$rs['lanid']} and lanstr='zh_cn'";
				$cfg['lang_title'] = " <span class='list-en'>[".$db->getValue($thesql, 'title')."]<span>";
			}
			$cfg['is_hot'] = $rs["is_hot"];
			$cfg['is_new'] = $rs["is_new"];
			$cfg["isvip"] = $rs["isvip"];
			$cfg["filestr"] = $this->getFileDownImg(model . "file", $rs["file"]);
			$cfg["commend"] = $rs["commend"];
			$cfg["paths"] = $rs["paths"];
			$topclassid = gettopclassid($rs["classidstr"]);
			$cfg["theurl"] = "../../?topclassid={$topclassid}&classid={$rs["classid"]}&id={$rs["lanid"]}";
			$htmlPath = htmlPath();
			$cfg["fileurl"] = $htmlPath . $rs["paths"] . $rs["filename"];
			if (file_exists(wwwroot . $cfg["fileurl"]) && $rs["filename"] != "") {
				$cfg["filelink"] = "<a href='" . $cfg["fileurl"] . "' target='_blank' title='浏览'><img src='" . skinspath . "htmlfile.gif' /></a>";
			} else {
				$cfg["filelink"] = "<img src='" . skinspath . "tip.gif' alt='未生成' />";
			}
			$funstr.=$this->reLabel2($loopstr);
		}
		return $funstr;
	}

	function getsearsql_pro() {
		global $cfg, $db;
		if ($cfg["keyword"] == "")
		return;
		if ($cfg["searchtype"] == "")
		return;

		$thesql = "select distinct(lanid) from #@__" . model . " where ".$cfg["searchtype"] . " like '%" . $cfg["keyword"] . "%' ";

		$db->dosql($thesql);
		while ($rs = $db->GetArray()) {
			$lanidstr.=$rs["lanid"] . ",";
		}
		$lanidstr = trim($lanidstr, ",");
		if ($lanidstr != "") {
			return " and lanid in ($lanidstr) ";
		} else {
			return " and 1=2 ";
		}
	}
	
	//关联文章列表
	function download_list($downloads='')
	{
		global $cfg,$db,$admin;
		$tempArr = array();

		if($downloads)  $tempArr = explode(",",$downloads);
		$thesql="select * from #@__download where del=0 and locked=0 and lanstr='".lanstr."' order by sortid desc";
		$db->dosql($thesql);
		$temp = '';
		while($rs=$db->GetArray())
		{
			$checked = @in_array($rs['lanid'], $tempArr) ? 'checked' :'';
			
			//取得其它语言的产品名称，
			if(lannums>1){
				$thesql = "select title from #@__download where lanid={$rs['lanid']} and lanstr='en'";
				$rs["title"].= " <span class='list-en'>[".$db->getValue($thesql, 'title')."]<span>";
			}
			
			$temp.='<tr><td><input type="checkbox" class="post_" value="'.$rs["lanid"].'" name="download" '.$checked.'/></td>
			<td align="left" >'.$rs["title"].'</td></tr>';

		}
		return $temp ;
	}
	
	//关联文章或产品列表
	function recommend_list($recommands='')
	{
		global $cfg,$db,$admin;
		$tempArr = array();

		if($recommands)  $tempArr = explode(",",$recommands);
		$thesql="select * from #@__product where del=0 and locked=0 and lanstr='".lanstr."' order by sortid desc";
		$db->dosql($thesql);
		$temp = '';
		while($rs=$db->GetArray())
		{
			$checked = @in_array($rs['lanid'], $tempArr) ? 'checked' :'';
			
			//取得其它语言的产品名称，
			if(lannums>1){
				$thesql = "select title from #@__product where lanid={$rs['lanid']} and lanstr='en'";
				$rs["title"].= " <span class='list-en'>[".$db->getValue($thesql, 'title')."]<span>";
			}
			
			$temp.='<tr><td><input type="checkbox" class="post_" value="'.$rs["lanid"].'" name="recommand" '.$checked.'/></td>
			<td align="left" >'.$rs["title"].'</td></tr>';

		}
		return $temp ;
	}
	/**
	 *
	 * 添加单个产品
	 */
	function add() {
		global $cfg, $db;
		$cfg["fileconfig"] = "{type:'" . $cfg[model . "_filetype"] . "',size:" . $cfg[model . "_filesize"] . ",addmore:true}";
		//$cfg["title"]=guid_str."添加".$cfg["model_name"];
		$cfg["click"] = 0;
		/**
		 * 是否是测试版
		 */
		if($cfg['is_test_version']){
			$numsql = "select * from #@__product where locked=0 and lanstr='".deflan."' ";
			$num = $db->num($numsql, "nums");
			if($num > 30) {
				echo $this->reLabel(modelpath . "limit.html");
				exit;
			}
		}
		
		
		//是否显示推荐产品字段
		if($cfg['field_product_recommands']==1) {
			$cfg['recommandlist'] = $this->recommend_list();
			$cfg['recommands_filed_table'] = $this->reLabel(TPL_ADMIN_DIR."product/field_recommands.html");
		}
		//资料下载
		if($cfg['field_product_downloads']==1) {		
			$cfg['downloadlist'] = $this->download_list();
			$cfg['downloads_filed_table'] = $this->reLabel(TPL_ADMIN_DIR."product/field_downloads.html");
		}
		
		//产品特征
		if($cfg['field_product_standard']==1) {		
			$cfg['standard_filed_table'] = $this->reLabel(TPL_ADMIN_DIR."product/field_standard.html");
		}
		
		//产品参数
		if($cfg['field_product_parameter']==1) {		
			$cfg['parameter_filed_table'] = $this->reLabel(TPL_ADMIN_DIR."product/field_parameter.html");
		}
		
		//其它描述
		if($cfg['field_product_other']==1) {				
			$cfg['other_filed_table'] = $this->reLabel(TPL_ADMIN_DIR."product/field_other.html");
		}
		
		$thesqlc = "select topclassid,fid from #@__class where lanid=" . $cfg["classid"];
		$rsc = $db->GetOne($thesqlc);
		//if($cfg["classid"]==322222221||$cfg["fid"]==31||$cfg["classid"]==25||$cfg["fid"]==25){
		//	echo $this->reLabel(modelpath."form_video.html");
		//}else if($cfg["classid"]==33335){
		//	echo $this->reLabel(modelpath."form_certificate.html");
		//}else{
			echo $this->reLabel(modelpath."form.html");
		//}
	}


	function getmodel() {
		global $db;
		$classid = formatnum($_GET["classid"], 0);
		$model = gets("model", "class", "lanid=" . $classid, "");
		die($model);
	}

	function edit($lanid) {
		global $cfg, $db, $admin;
		$cfg["fileconfig"] = "{type:'" . $cfg[model . "_filetype"] . "',size:" . $cfg[model . "_filesize"] . ",addmore:true}";
		$cfg["lanid"] = $lanid;
		$thesql = "select * from #@__" . model . " where lanid=" . $lanid;
		$db->dosql($thesql);
		while ($rs = $db->GetArray()) {//通过lanid访问对应数据
			if ($rs["lanstr"] == deflan) {//共同数据部分
				$cfg["classid"] = $rs["classid"];
				$cfg["picurl"] = $rs["bigpic"];
				$cfg["file"] = $rs["file"];
				$cfg["product_sn"] = $rs["product_sn"];
				$cfg["price"] = $rs["price"];
				$cfg["click"] = $rs["click"];
				$cfg["edittime"] = $rs["edittime"];
				$cfg["pic"] = $rs["bigpic"] != "" ? $this->viewpicurl(model_imagepath, $rs["bigpic"],$rs["smallpic"]) : "";
				$cfg["filenamestr"] = $rs["paths"] . $rs["filename"];
				$cfg["addtime"] = $rs["addtime"];
				
				$arr = unserialize($rs["gallery"]);
				$galleryStr = "";
				if ($rs["gallery"]) {
					foreach ($arr as $tmp) {
						$galleryStr.=$tmp["source"] . "\n";
						$galleryStr_file.=$tmp["source"] . "|";
					}
				}
				$recommands = $rs["recommands"];
				$downloads = $rs["downloads"];
				$cfg["gallery"] = $galleryStr;
				$cfg["gallery_file"] = $galleryStr_file; 
				
				$recommands = $rs["recommands"];
				$downloads = $rs["downloads"];
			}
			//独立数据部分
			//通用字段
			$cfg["title" . $rs["lanstr"]] = addslashes($rs["title"]);
			$cfg["brief" . $rs["lanstr"]] = $rs["brief"];
			//$cfg["click" . $rs["lanstr"]] = $rs["click"];
			$cfg["minititle" . $rs["lanstr"]] = $rs["minititle"];
			$cfg["lanzi" . $rs["lanstr"]] = $rs["lanzi"];
			$cfg["huizi" . $rs["lanstr"]] = $rs["huizi"];
			$cfg["titles" . $rs["lanstr"]] = $rs["titles"];
			$cfg["tag" . $rs["lanstr"]] = $rs["tag"];
			$cfg["about" . $rs["lanstr"]] = $rs["about"];
			$cfg["content" . $rs["lanstr"]] = $db->getValue("select content from #@__" . models . " where lanid=$lanid and lanstr='" . $rs["lanstr"] . "'", "content");


			//其它自定义字段
			$cfg["standard" . $rs["lanstr"]] = $rs["standard"];			
			$cfg["parameter" . $rs["lanstr"]] = $rs["parameter"];
			$cfg["other" . $rs["lanstr"]] = $rs["other"];		

		}
		if($cfg["gallery"]){
			$garr = unserialize($cfg["gallery"]);		
			if( dirname($arr[0]['source']) !='.') {
				$cfg['curpath'] = dirname($arr[0]['source']).'/';
				$cfg['product_imagepath'] = product_imagepath.$cfg['curpath'];
			}
		}
		$cfg["title"] = guid_str . $cfg["model_name"] . "编辑";
		//是否显示推荐产品字段
		if($cfg['field_product_recommands']==1) {
			$cfg['recommandlist'] = $this->recommend_list($recommands);
			$cfg['recommands_filed_table'] = $this->reLabel(TPL_ADMIN_DIR."product/field_recommands.html");
		}
		//资料下载
		if($cfg['field_product_downloads']==1) {		
			$cfg['downloadlist'] = $this->download_list($downloads);
			$cfg['downloads_filed_table'] = $this->reLabel(TPL_ADMIN_DIR."product/field_downloads.html");
		}
		
		//产品特征
		if($cfg['field_product_standard']==1) {		
			$cfg['standard_filed_table'] = $this->reLabel(TPL_ADMIN_DIR."product/field_standard.html");
		}
		
		//产品参数
		if($cfg['field_product_parameter']==1) {		
			$cfg['parameter_filed_table'] = $this->reLabel(TPL_ADMIN_DIR."product/field_parameter.html");
		}
		
		//其它描述
		if($cfg['field_product_other']==1) {				
			$cfg['other_filed_table'] = $this->reLabel(TPL_ADMIN_DIR."product/field_other.html");
		}
		$thesqlc = "select topclassid from #@__class where lanid=" . $cfg["classid"];
		$rsc = $db->GetOne($thesqlc);
		//if($cfg["classid"]==31||$cfg["fid"]==31||$cfg["classid"]==25||$cfg["fid"]==25){
		//	echo $this->reLabel(modelpath."form_video.html");
		//}else if($cfg["classid"]==33335){
		//	echo $this->reLabel(modelpath."form_certificate.html");
		//}else{
			echo $this->reLabel(modelpath."form.html");
		//}
	}

	/**
	 *
	 * 更新单个产品
	 */
	function update() {
		global $cfg, $db, $admin;
		$now = time();

		$lanid = formatnum($_POST["lanid"], 0);
		$classid = formatnum($_POST["classid"], 0);
		if ($classid == 0)
		die("{err}请确定栏目是否存在！");
		$rs = $db->GetOne("select classidstr,paths,model from #@__class where paths<>'' and lanid=" . $classid);
		if (!instr($rs["model"], "product"))
		die("{err}错误：当前栏目所在模型【{$rs["model"]}】内不可以添加资料！");
		$classidstr = $rs["classidstr"];
		$paths = $rs["paths"];
		
		//附件
		$file = isset($_POST["file"]) ? $_POST["file"] : "";
		//关联产品
		$recommands = isset($_POST['recommand']) ? $_POST['recommand'] :  '';
		//关联下载资料。
		$downloads = isset($_POST['download']) ? $_POST['download'] :  '';

		$product_sn = $_POST['product_sn'];
		$price = floatval($_POST['price']);
		$click = formatnum($_POST["click"], 0);
			
		//产品属性
		//$is_hot = $_POST['is_hot'] ? $_POST['is_hot'] : 0;
		//$is_new = $_POST['is_new'] ? $_POST['is_new'] : 0 ;
			
		//产品相册 需求时启用，需在
		$galleryStr = "";

		$_POST["morepic"] = str_replace("\r","", $_POST["morepic"]);
		$gallery = explode("\n", $_POST["morepic"]);
		$galleryArr = array();


		if (trim($_POST["morepic"])) {
			foreach ($gallery as $key=>$tmp) {
				$p = webroot . model_imagepath;
				if(is_file($p . $tmp)) {
					$picSet = new picSet($p . $tmp);
					$tmp = trim($tmp);
					if($key==0){
						//生成产品列表图
						$bigpic = $tmp;
						$smallpic = getPicName($tmp,'list');
						$thesqlc = "select sortid,fid from #@__class where lanid=" . $classid;
						$rsc = $db->GetOne($thesqlc);
						if($rsc["sortid"]==31||$rsc["fid"]==31||$rsc["sortid"]==25||$rsc["fid"]==25){
							$picSet->suo($p . $smallpic, 400,300,$cfg['pic_zoom_bgcolor']);
						}else if($rsc["topclassid"]==3333){
							$picSet->suo($p . $smallpic, 400,300,$cfg['pic_zoom_bgcolor']);
						}
						else{
							$picSet->suo($p . $smallpic, $cfg['picsize_product_list_w'], $cfg['picsize_product_list_h'],$cfg['pic_zoom_bgcolor']);
						}
						//$picSet->suo($p . $smallpic, $cfg['picsize_product_list_w'], $cfg['picsize_product_list_h'],$cfg['pic_zoom_bgcolor']);
						$sec = $picSet->save();

					}
					if($key==0){
						//生成头部滚动图
						$bigpic = $tmp;
						$midpic = getPicName($tmp,'mid');
						$picSet->suo($p . $midpic, 141, 122,$cfg['pic_zoom_bgcolor']);
						$sec = $picSet->save();

					}

						
					$row["thumb"] = getPicName($tmp,'thumb');
					$row["image"] = getPicName($tmp,'middle');

					$row["source"] =  $tmp;
					$row["original"] =  getPicName($tmp,'big');
					$galleryArr[] = $row;


						
					//生成小图
					$picSet->suo($p . $row["thumb"], $cfg['picsize_product_thumb_w'], $cfg['picsize_product_thumb_h'],$cfg['pic_zoom_bgcolor']);
					$sec = $picSet->save();

					//生成中等图
					$picSet->suo($p . $row["image"], $cfg['picsize_product_mid_w'], $cfg['picsize_product_mid_h'],$cfg['pic_zoom_bgcolor']);
					$sec = $picSet->save();

					//生成大图
					$picSet->suo($p . $row["original"],$cfg['picsize_product_big_w'], $cfg['picsize_product_big_h'],$cfg['pic_zoom_bgcolor']);
					$sec = $picSet->save();
					$picSet = null;
				}

			}
			$galleryStr = serialize($galleryArr);
		}
		
		$newlanid = 0;
		doMydb(0);
		for ($i = 0; $i < lannums; $i++) {
			$lanstr = $cfg["language"][$i];
			$lanpath = $cfg["languagepath"][$i];
			$title = post("title" . $lanstr);
			$minititle = post("minititle" . $lanstr);
			$lanzi = post("lanzi" . $lanstr);
			$huizi = post("huizi" . $lanstr);
			$brief = post("brief" . $lanstr);
			//$click = formatnum($_POST["click" . $lanstr], 0);
			
			
			
			//Meta信息
			$titles = post("titles" . $lanstr);
			$titles = $titles!="" ? $titles : $title;
			$tag = post("tag" . $lanstr);
			$about = post("about" . $lanstr);
			//$about = $about!="" ? $about : $brief;
				
			$content = post("content" . $lanstr);


			//预留字段。
			$standard = post("standard" . $lanstr);
			$other = post("other" . $lanstr);
			$parameter = post("parameter" . $lanstr);
			$guide = post("guide" . $lanstr);

			/*
			 * 如有其它自定义字段，在此添加, 同时SQL语句内也需要添加自定义字段与值
			 */
			$rsu = $db->GetOne("select classidstr,paths,model,classname from #@__class where paths<>'' and lanid=" . $classid." and lanstr='".$lanstr."'");
			if($rsu["classname"]!=""){
			if ($lanid) {
				
				if($title!=""){
					$thesql12 = "select * from #@__" . model . " where lanid=" . $lanid." and lanstr='".$lanstr."'";
					$db->dosql($thesql12);
					$rs12 = $db->GetArray();
					if(empty($rs12)){
						$thesqls12 = "insert into #@__" . model . " (lanid,lanstr,title) values ($lanid,'" . $lanstr . "','" . $title . "')";
					}
					$db->execute($thesqls12);
				}
				
				$addtime = $_POST["addtime"];
				if(!empty($_POST["titleen"])){
					$filename = strtolower(str_replace(" ", "-", $_POST["titleen"])) . ".html";
					$filename = str_replace("+", "", $filename);
					$filename = str_replace(":", "", $filename);
					$filename = str_replace("$", "", $filename);
					$filename = str_replace("?", "", $filename);
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
				$thesql = "update #@__" . model . " set
				classid=$classid,
				classidstr='" . $classidstr . "',
				title='" . $title . "',		
				minititle='" . $minititle . "',		
				lanzi='" . $lanzi . "',		
				huizi='" . $huizi . "',		
				product_sn='" . $product_sn . "',				
				price='" . $price. "',
                standard='" . $standard . "',
				parameter='" . $parameter . "',
				other='" . $other . "',			
				recommands='".$recommands."',
				downloads='".$downloads."',				
				brief='" . $brief . "',  
				paths='" . $paths . "',
				filename='" . $filename . "',
				bigpic='" . $bigpic . "',
				smallpic='" . $smallpic . "',
				midpic='" . $midpic . "',
				file='" . $file . "',
				titles='" . $titles . "',				
				tag='" . $tag . "',
				about='" . $about . "',
				gallery='" . $galleryStr . "',
				click=$click,
				edittime=$now			
				where lanstr='" . $lanstr . "' and lanid=" . $lanid;
				$db->execute($thesql);
				$thesql_s= $thesql;
				//echo $thesql;
				$thesql = "select * from #@__" . models . " where lanid=" . $lanid." and lanstr='".$lanstr."'";
				$db->dosql($thesql);
				$rs = $db->GetArray();
				if(!empty($rs)){
					$thesqls = "update #@__" . models . " set content='" . $content . "' where lanstr='" . $lanstr . "' and lanid=" . $lanid;
				}else{
					$thesqls = "insert into #@__" . models . " (lanid,lanstr,content) values ($lanid,'" . $lanstr . "','" . $content . "')";
				}
				$db->execute($thesqls);
				$oldpaths = wwwroot . $lanpath . $_POST["filenamestr"];
				$nowfilestr = wwwroot . $lanpath . $paths . $filename;
				if ($oldpaths != $nowfilestr && file_exists($oldpaths)) {
					delfile($oldpaths);
				}
				

			} else {
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
				$thesql = "insert into #@__" . model . " (lanid,lanstr,sortid,classid,classidstr,title,minititle,lanzi,huizi,price,product_sn,standard,other,parameter,recommands, downloads,brief,gallery,paths,filename,titles,tag,about,bigpic,smallpic,midpic,locked,addtime,edittime,click,file
				) values ($newlanid,'" . $lanstr . "',$newlanid,$classid,'" . $classidstr . "','" . $title . "','" . $minititle . "','" . $lanzi . "','" . $huizi . "','" . $price . "','" . $product_sn . "','" . $standard . "','" . $other . "','" . $parameter . "','".$recommands."','".$downloads."','" . $brief . "','" . $galleryStr . "','" . $paths . "','" . $filename . "',
				'" . $titles . "','" . $tag . "','" . $about . "','" . $bigpic . "','" . $smallpic ."','".$midpic. "'," . $cfg["adminadd_locked"] . ",$now,$now,$click,'" . $file . "')";
				$thesql_s= $thesql;
				$db->execute($thesql);
				if ($i == 0) {
					$newlanid = $db->GetLastID();
					if(!empty($_POST["titleen"])){
						$filename = strtolower(str_replace(" ", "-", $_POST["titleen"])) . ".html";
						$filename = str_replace("+", "", $filename);
						$filename = str_replace(":", "", $filename);
						$filename = str_replace("?", "", $filename);
						$filename = str_replace(",", "", $filename);
						$filename = str_replace("$", "", $filename);
						$filename = str_replace("-[$add]", "", $filename);
					}else{
						$filename = $this->getFilename1($_POST["titlezh_cn"]) . ".html";
						$filename = str_replace("+", "", $filename);
						$filename = str_replace(":", "", $filename);
						$filename = str_replace("?", "", $filename);
						$filename = str_replace("$", "", $filename);
						$filename = str_replace(",", "", $filename);
						$filename = str_replace("-[$add]", "", $filename);
					}
					$thesql2 = "update #@__" . model . " set lanid=$newlanid,sortid=$newlanid,filename='" . $filename . "' where id=" . $newlanid;
					$db->execute($thesql2);
				}
				$thesql2s = "insert into #@__" . models . " (lanid,lanstr,content) values ($newlanid,'" . $lanstr . "','" . $content . "')";
				$db->execute($thesql2s);
			}
		}
		}
		$addstr = $_POST["i"] != "" ? "【{$_POST["i"]}】" : "";
		doMydb(1);
		if ($lanid) {
			die("{ok}恭喜，" . $cfg["model_name"] . "编辑成功！");
		} else {
			die("{ok}恭喜，" . $cfg["model_name"] . $addstr . "添加成功！");
		}
	}


	/**
	 * 批量添加产品
	 */
	function adds() {
		global $cfg, $admin;
		
		$cfg["title"] = guid_str . "批量添加" . $cfg["model_name"];
		echo $this->reLabel(modelpath . "form_batch.html");
	}

	/**
	 *
	 * 更新单个产品
	 */
	function batch_add() {
		global $cfg, $db, $admin;
		$classid = formatnum($_POST["classid"], 0);
		if ($classid == 0)
		die("{err}请确定栏目是否存在！");
		$rs = $db->GetOne("select classidstr,paths,model from #@__class where paths<>'' and lanid=" . $classid);
		if (!instr($rs["model"], "product"))
		die("{err}错误：当前栏目所在模型【{$rs["model"]}】内不可以添加资料！");
		$classidstr = $rs["classidstr"];
		$paths = $rs["paths"];
		$now = time();
		$title_batch = post("title_batch" . deflan2);
		$title_batch = str_replace("\r\n", "\n",$title_batch);	
		$titleArr = explode("\n",$title_batch);

		$product_sn_batch = $_POST['product_sn_batch'];
		$product_sn_batch = str_replace("\r\n", "\n",$product_sn_batch);
		$product_sn_arr = explode("\n",$product_sn_batch);
			
		foreach ($titleArr as $key=>$title){
			
			$product_sn = $product_sn_arr[$key];
			$newlanid = 0;
			doMydb(0);
			for ($i = 0; $i < lannums; $i++) {
				$lanstr = $cfg["language"][$i];
				$lanpath = $cfg["languagepath"][$i];

				$title_batch = post("title_batch" . $lanstr);
				$title_batch = str_replace("\r\n", "\n",$title_batch);	
				$title_arr = explode("\n",$title_batch);			
				$title = $title_arr[$key];
				
				$filename = $this->getFilename($newlanid, $title, $now);
				
				$thesql = "insert into #@__product(lanid,lanstr,sortid,classid,classidstr,title,product_sn,filename,addtime,edittime
				) values ($newlanid,'" . $lanstr . "',$newlanid,$classid,'" . $classidstr . "','" . $title . "','" . $product_sn . "','" . $filename . "',$now,$now)";
				echo $thesql;
				$db->execute($thesql);
				if ($i == 0) {
					$newlanid = $db->GetLastID();
					$thesql2 = "update #@__product set lanid=$newlanid,sortid=$newlanid,filename='" . $filename . "' where id=" . $newlanid;
					$db->execute($thesql2);
				}
			}
		}
		$addstr = $_POST["i"] != "" ? "【{$_POST["i"]}】" : "";
		doMydb(1);
		if ($lanid) {
			die("{ok}恭喜，" . $cfg["model_name"] . "编辑成功！");
		} else {
			die("{ok}恭喜，" . $cfg["model_name"] . $addstr . "添加成功！");
		}
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

}

$myclass = new myclass();
$lanid = $_GET["lanid"];
switch ($_GET["action"]) {
	case "add":
	$admin->adminck("product_add");
		$myclass->add();
		break;
	case "adds":
		$admin->adminck("product_adds");
		$myclass->adds();
		break;
	case "batch_add":
		refPage(2);
		$admin->adminck("product_adds");
		$myclass->batch_add();
		break;
	case "getmodel":
		$myclass->getmodel();
		break;
	case "edit":
	$admin->adminck("product_edit");
		$myclass->edit($lanid);
		break;
	case "update":
		refPage(2);
		$admin->adminck_ajax("product_edit");
		$myclass->update();
		break;
	case "sort":
		refPage(1);
		$admin->adminck_ajax("product_edit");
		$myclass->sort(model);
		break;
	case "dbref":
		$myclass->dbref(model, models);
		break;
	case "toggle":
		refPage(1);
		$admin->adminck_ajax("product_edit");
		$myclass->toggle("product");
		break;
	case "commenddo":
		refPage(1);
		$myclass->commenddo(model, $lanid);
		break;
	case "guid":
		$myclass->guid(model);
		break;
	case "admindo":
		refPage(1);
		$admin->adminck_ajax("product_del");
		$myclass->admindo($lanid);
		break;
	default:
		$myclass->def();
}
?>