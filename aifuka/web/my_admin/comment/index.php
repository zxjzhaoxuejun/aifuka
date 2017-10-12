<?php

/* * ******************************************************
 时间：2009-10-21

 程序：胡思文
 * ****************************************************** */
include("../../include/inc.php");
include(incpath . "funlist.php");

class myclass extends alzCms {

	function __destruct() {
		$this->admincache2();
	}

	function __construct() {
		global $cfg, $admin;
		
		$this->admincache();
		loadlanXml("feedback", "feedback_");
		if ($_GET["c"] != "") {
			setcookies("classid", 0);
			setcookies("page", 1);
			setcookies("commend", "");
			setcookies("del", 0);
			setcookies("searchtype", "");
			setcookies("keyword", "");
			setcookies("locked", 0);
			$cfg["classid"] = formatnum($_GET["classid"], 0);
			$cfg["page"] = formatnum($_GET["page"], 1);
			$cfg["commendstr"] = $_GET["commend"];
			$cfg["del"] = formatnum($_GET["del"], 0);
			$cfg["searchtype"] = $_GET["searchtype"];
			$cfg["keyword"] = $_GET["keyword"];
			$cfg["locked"] = formatnum($_GET["locked"], 0);
		} else {
			$cfg["classid"] = G("classid", $_GET["classid"]);
			$cfg["page"] = G("page", $_GET["page"], 1);
			$cfg["commendstr"] = G("commend", $_GET["commend"]);
			$cfg["del"] = G("del", $_GET["del"], 0);
			$cfg["searchtype"] = G("searchtype", $_GET["searchtype"]);
			$cfg["keyword"] = G("keyword", $_GET["keyword"]);
			$cfg["locked"] = G("locked", $_GET["locked"], 0);
		}
		define("model", "comment");
		define("modelpath", TPL_ADMIN_DIR."comment/");
		define("model_imagepath", feedback_imagepath);
		$cfg["model_name"] = "会员留言与评论";
	}

	function def() {
		global $cfg, $db;
		$cfg["otherguidstr"] = $cfg["commendstr"] . "|" . $cfg["locked"] . "|" . $cfg["del"];
		

		if ($cfg["locked"]) {
			//批量操作按钮
			$cfg['adminBatchAction'] = ' <a class="class_addchildclass" href="javascript:void(0)" onclick="adminDoType(\'dels\')" >删除</a>
                <a class="class_addchildclass" href="javascript:void(0)" onclick="adminDoType(\'dellocked\')" >审核所选</a>';


			$addsql.=" and locked=1 and del=0";
		} else {
			$classidsql = $cfg["classid"] != 0 ? " and classid in(" . allclassids($cfg["classid"]) . ") " : "";
			$addsql.=$classidsql . " and del=0 and locked=0 ";
			//批量操作按钮
			$cfg['adminBatchAction'] = ' <a class="class_addchildclass" href="javascript:void(0)" onclick="adminDoType(\'dels\')" >删除</a>
                <a class="class_addchildclass" href="javascript:void(0)" onclick="adminDoType(\'locked\')" >取消审核</a>	';		

		}

		echo $cfg['type']=='feedback' ? $this->reLabel(modelpath . "feedback.html") : $this->reLabel(modelpath . "index.html");;
	}


	function mylist($loopstr) {
		global $cfg, $db, $admin;
		$pagesize = $cfg["adminpagesize"];
		$cfg["pagesize"] = $pagesize;
		$beginid = ($cfg["page"] - 1) * $pagesize;
		$addsql = " where 1=1 ";
		if ($cfg["locked"])
		$addsql.=" and locked=1 ";

		$addsql.= $cfg['type']=='feedback' ? " and objid=0 " : ' and objid>0 ';
		$addsql.=$this->getsearsql();
		$numsql = "select * from #@__" . model . $addsql;
		$cfg["allnums"] = $db->num($numsql, "allnum");
		$thesql = "select * from #@__" . model . $addsql . "order by id desc limit $beginid,$pagesize";
		$db->dosql($thesql);

		while ($rs = $db->GetArray()) {
			$cfg["lanid"] = $rs["id"];
			$cfg["sortid"] = $rs["sortid"];

			if ($rs["locked"] <= 0) {
				$cfg["title"] = "<b class=red>" . $rs["title"] . "</b>";
				$cfg["url"] = str_replace($cfg["keyword"], "<b class=red>" . $cfg["keyword"] . "</b>", $rs["url"]);
			} else {
				$cfg["title"] = left($rs["title"], $cfg["titlecutnum"]);
				$cfg["url"] = $rs["url"];
			}
			/**
			 * 显示的字段
			 */
			$cfg["username"] = $rs["username"];

			if($rs["classidstr"]){
				$arr = explode(',', $rs["classidstr"]);
				$cfg['url']="?topclassid={$arr[0]}&classid=".$arr[0]."&id=".$rs["objid"];
			}
			$cfg["title"] = $rs["objtitle"];
				
			$cfg["content"] = $rs["content"];
			$cfg["addtime"] = date("Y-m-d H:i:s", $rs["addtime"]);
			$cfg["ip"] = lookip($rs["ip"]);
			$cfg["restr"] = $rs["recontent"] != "" ? "<b class='cn'>已回复</b>" : "";
			$funstr.=$this->reLabel2($loopstr);
		}
		return $funstr;
	}

	/**
	 *
	 * @global type $cfg
	 * @global type $db
	 * @global type $admin
	 * @param type $lanid
	 */
	function edit($lanid) {
		global $cfg, $db, $admin;
		$cfg["id"] = $lanid;
		$thesql = "update #@__" . model . " set locked = 1 where id=" . $lanid;
		$db->dosql($thesql);
		$thesql = "select * from #@__" . model . " where id=" . $lanid;
		$db->dosql($thesql);
		while ($rs = $db->GetArray()) {//通过lanid访问对应数据
			$cfg["title"] = $rs["title"];
			$cfg["username"] = $rs["username"];
			$cfg["tel"] = $rs["tel"];
			$cfg["phone"] = $rs["phone"];
			$cfg["email"] = $rs["email"];
			$cfg["ip"] = $rs["ip"];
			$cfg["addtime"] = date("Y-m-d H:i:s", $rs["addtime"]);
			$cfg["contents"] = $rs["content"];
			$cfg["recontent"] = $rs["recontent"];
		}
		$cfg["pagetitle"] = guid_str . $cfg["model_name"] . "回复";
		echo $this->reLabel(modelpath . "form.html");
	}

	function update() {
		global $cfg, $db, $admin;
		$id = formatnum($_POST["id"], 0);
		$time = time();
		doMydb(0);
		if ($id) {
			$content = $_POST["contents"];
			$recontent = $_POST["recontent"];
			$thesql = "update #@__" . model . " set	content='" . $content . "',
			recontent='" . $recontent . "',
			retime=$time where id=" . $id;
			$db->execute($thesql);
		}
		doMydb(1);

		die("{ok}恭喜，回复成功！");
	}

	function userinfo() {
		global $cfg, $db, $admin;
		$username = $_GET['username'];
		$thesql = "select * from #@__member where user_name='$username'";
		$rs = $db->GetOne($thesql);
		$cfg["lanid"]=$rs["id"];
		$cfg["user_name"]=$rs["user_name"];
		$cfg["email"]=$rs["email"];
		$cfg["company"]=$rs["company"];
		$cfg["tel"]=$rs["tel"];
		$cfg["realname"]=$rs["realname"];
		$cfg["level_name"] = $rs['level']==2 ? "认证会员" : '普通会员';
		$cfg["reg_time"]=date('Y-m-d H:i:s',$rs["reg_time"]);
		echo $this->reLabel(modelpath . "userinfo.html");
	}

	function getsearsql() {
		global $cfg, $db;
		if ($cfg["keyword"] == "")
		return;
		if ($cfg["searchtype"] == "")
		return;
		return " and " . $cfg["searchtype"] . " like '%" . $cfg["keyword"] . "%' ";
	}

	function admindo($lanidstr) {
		global $db;
		$str = $_GET["str"];
		$fun = $_GET["fun"];
		
		switch ($fun) {
			case "dels":
				$thesql = "delete from #@__comment where id in (" . $lanidstr . ")";
				$db->execute($thesql);
				break;
			case "locked":
				$this->mydid($lanidstr, model, "locked=1", "id");
				break;
			case "dellocked":
				$this->mydid($lanidstr, model, "locked=0", "id");
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
$cfg['type'] = $_GET["type"];
switch ($_GET["action"]) {
	
	case "edit":
		$admin->adminck_ajax("comment_reply");
		$myclass->edit($lanid);
		break;
	case "update":
		refPage(2);
		$admin->adminck_ajax("comment_reply");
		$myclass->update();
		break;
	case "userinfo":

		$myclass->userinfo();
		break;
	case "dbref":
		$myclass->dbref(model, models);
		break;
	case "commenddo":
		refPage(1);
		$myclass->commenddo(model, $lanid);
		break;
	case "guid":
		$myclass->guid(model);
		break;
	case "read":
		$myclass->read($lanid);
		break;
	case "admindo":
		refPage(1);
		$admin->adminck_ajax("comment_del");
		$myclass->admindo($lanid);
		break;
	default:
		$myclass->def();
}
?>