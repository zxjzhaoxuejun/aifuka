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
        define("model", "feedback");
        define("modelpath", TPL_ADMIN_DIR."feedback/");
        define("model_imagepath", feedback_imagepath);
        $cfg["model_name"] = t("title");
    }

    function def() {
        global $cfg, $db;
        $cfg["otherguidstr"] = $cfg["commendstr"] . "|" . $cfg["locked"] . "|" . $cfg["del"];
        

        if ($cfg["locked"]) {
            //批量操作按钮
            $cfg['adminBatchAction'] = ' <a class="class_addchildclass  twinkle" href="javascript:void(0)" onclick="adminDoType(\'dels\')" >删除</a>	
                <a class="class_addchildclass  twinkle" href="javascript:void(0)" onclick="adminDoType(\'dellocked\')" >审核所选</a>';


            $addsql.=" and locked=1 and del=0";
        } else {
            $classidsql = $cfg["classid"] != 0 ? " and classid in(" . allclassids($cfg["classid"]) . ") " : "";
            $addsql.=$classidsql . " and del=0 and locked=0 ";
            //批量操作按钮
            $cfg['adminBatchAction'] = ' <a class="class_addchildclass  twinkle" href="javascript:void(0)" onclick="adminDoType(\'dels\')" >删除</a>	
                <a class="class_addchildclass  twinkle" href="javascript:void(0)" onclick="adminDoType(\'locked\')" >取消审核</a>	';		
               
        }
		if($cfg["classid"]==454){
			echo $this->reLabel(modelpath . "index_order.html");
		}else{
        	echo $this->reLabel(modelpath . "index.html");
		}
    }

    function mylist($loopstr) {
        global $cfg, $db, $admin;
        $pagesize = $cfg["adminpagesize"];
        $cfg["pagesize"] = $pagesize;
        $beginid = ($cfg["page"] - 1) * $pagesize;
        $addsql = " where 1=1 ";
        if ($cfg["locked"])
            $addsql.=" and locked=1 ";
        $classidsql = $cfg["classid"] != 0 ? " and classid in(" . allclassids($cfg["classid"]) . ") " : "";
        $addsql.=$classidsql;
        $addsql.=$this->getsearsql();
        $numsql = "select * from #@__" . model . $addsql;
        $cfg["allnums"] = $db->num($numsql, "allnum");
        $thesql = "select * from #@__" . model . $addsql . "order by id desc limit $beginid,$pagesize";
        $db->dosql($thesql);
        if ($cfg["commend_" . model . "_key"] != "") {
            $name = explode("|", $cfg["commend_" . model . "_name" . $cfg["lanstr"]]);
            for ($i = 0; $i < count($name); $i++) {
                $str.=" " . $name[$i] . " ";
            }
            $cfg["commendtitle"] = "<td class='cellc'>$str</td>";
        }
        while ($rs = $db->GetArray()) {
            $cfg["classname"] = gets("classname", "class", "lanstr='" . lanstr . "' and lanid=" . $rs["classid"]);
            $cfg["lanid"] = $rs["id"];
            $cfg["sortid"] = $rs["sortid"];
            $cfg["commend"] = $rs["commend"];
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
            $cfg["tel"] = $rs["tel"];
            $cfg["phone"] = $rs["phone"];
            $cfg["email"] = $rs["email"];
            $cfg["contents"] = $rs["content"];
            $cfg["addtime"] = date("Y-m-d H:i:s", $rs["addtime"]);
            $cfg["ip"] = lookip($rs["ip"]);
			$cfg["fax"] = $rs["fax"];
			$cfg["company"] = $rs["company"];
			$cfg["address"] = $rs["address"];
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
			$cfg["fax"] = $rs["fax"];
            $cfg["phone"] = $rs["phone"];
            $cfg["email"] = $rs["email"];
			$cfg["company"] = $rs["company"];
			$cfg["zip"] = $rs["zip"];
			$cfg["address"] = $rs["address"];
            $cfg["ip"] = $rs["ip"];
            $cfg["addtime"] = date("Y-m-d H:i:s", $rs["addtime"]);
            $cfg["contents"] = $rs["content"];
            $cfg["recontent"] = $rs["recontent"];
			$cfg["classid"] = $rs["classid"];
        }
        $cfg["pagetitle"] = guid_str . $cfg["model_name"] . "回复";
		if($cfg["classid"]==454){
			echo $this->reLabel(modelpath . "form_order.html");
		}else{
        	echo $this->reLabel(modelpath . "form.html");
		}
    }

    function update() {
        global $cfg, $db, $admin;
        $id = formatnum($_POST["id"], 0);
        doMydb(0);
        if ($id) {
            $title = $_POST["title"];
            $content = $_POST["contents"];
            $recontent = $_POST["recontent"];
            $thesql = "update #@__" . model . " set
			title='$title',
			content='" . $content . "',
			recontent='" . $recontent . "'
			where id=" . $id;
            $db->execute($thesql);
        }
        doMydb(1);

        die("{ok}恭喜，回复成功！");
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
                $thesql = "delete from #@__feedback where id in (" . $lanidstr . ")";
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
switch ($_GET["action"]) {
  
    case "edit":
		$admin->adminck_ajax("feedback_reply");
        $myclass->edit($lanid);
        break;
    case "update":
        refPage(2);
		$admin->adminck_ajax("feedback_reply");
        $myclass->update();
        break;
    case "sort":
        refPage(1);
        $myclass->sort(model);
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
		$admin->adminck_ajax("feedback_del");
        $myclass->admindo($lanid);
        break;
    default:
        $myclass->def();
}
?>