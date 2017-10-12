<?php

/* * ******************************************************
  时间：2009-10-2

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
    
        define("model", "myqq");
        define("modelpath", TPL_ADMIN_DIR."myqq/");
        $cfg["model_name"] = "在线客服";
    }

    function def() {
        global $db, $cfg, $admin;
        $thesql = "select * from #@__" . model;
        $rs = $db->GetOne($thesql);      
        echo $this->reLabel(modelpath . "index.html");
    }

    function mylist($loopstr) {
        global $cfg, $db, $admin;       
        $thesql = "select * from #@__" . model . " where lanstr='" . lanstr . "' order by sortid desc";

        $db->dosql($thesql);
        while ($rs = $db->GetArray()) {
            $cfg["lanid"] = $rs["lanid"];
            $cfg["sortid"] = $rs["sortid"];           
            $cfg["title"] = $rs["title"];
            $cfg["account"] = $rs["account"];
			$cfg["type"] = $rs["type"];
			if($rs["type"]=="wangwang"){
				$cfg["type"]="国际旺旺";
			}elseif($rs["type"]=="ali"){
				$cfg["type"]="国内旺旺";
			}
            $cfg["del_qq"] = "<a class=\"class_del\" href=\"?action=del&lanid=" . $rs["lanid"] . "\">[删除]</a>";
            $funstr.=$this->reLabel2($loopstr);
        }
        return $funstr;
    }

    function add() {
        global $cfg, $db, $admin;
        
        $cfg["pagetitle"] = guid_str . "添加" . $cfg["model_name"];
        $cfg["type"] = "";
        echo $this->reLabel(modelpath . "form.html");
    }

    function edit($lanid) {
        global $cfg, $db, $admin;
        $cfg["lanid"] = $lanid;
        $thesql = "select * from #@__" . model . " where lanid=" . $lanid;
        $db->dosql($thesql);
        while ($rs = $db->GetArray()) {
            if ($rs["lanstr"] == deflan) {//共同数据部分
                $cfg["type"] = $rs["type"];               
                $cfg["account"] = $rs["account"];               
            }
            $cfg["title" . $rs["lanstr"]] = $rs["title"];
            
        }
     
        
         $cfg["pagetitle"] = guid_str . $cfg["model_name"] . "编辑";
        echo $this->reLabel(modelpath ."form.html");
    }

    function update() {
        global $cfg, $db, $admin;
        $lanid = formatnum($_POST["lanid"], 0);
        doMydb(0);
        $type = $_POST["type"];
        $account = $_POST["account"];
     
        $newlanid = 0;
        for ($i = 0; $i < lannums; $i++) {
            $lanstr = $cfg["language"][$i];
            $title = $_POST["title".$lanstr];
            if ($lanid) {//编辑
                $thesql = "update #@__" . model . " set
				type='" . $type . "',
                lanstr='".$lanstr. "',
				account='" . $account . "',
				title='" . $title . "'				
				where lanstr='" . $lanstr . "' and lanid=" . $lanid;

                $db->execute($thesql);
            } else {               
                $thesql = "insert into #@__" . model . " (lanid ,lanstr,type,account,title ,sortid) 
                        values ($newlanid,'" . $lanstr . "','" . $type . "','" . $account . "','" . $title . "',0)";
                $db->execute($thesql);
                 if ($i == 0) {
                    $newlanid = $db->GetLastID();
                    $thesql2 = "update #@__" . model . " set lanid=$newlanid,sortid=$newlanid where id=" . $newlanid;
                    $db->execute($thesql2);
                }
                
            
            }
        }
        doMydb(1);

        if ($lanid) {
            die("{ok}恭喜，" . $cfg["model_name"] . "编辑成功！");
        } else {
            die("{ok}恭喜，" . $cfg["model_name"] . "添加成功！");
        }
    }
 

    function admindo($lanidstr)
	{
		global $db;
		$str=$_GET["str"];
		$fun=$_GET["fun"];
		switch ($fun)
		{
			case "dels":
				$thesql = "delete from #@__myqq where lanid in(" .$lanidstr.")";   
				     
        		$db->execute($thesql);			
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
	
 function del($lanid) {
        global $cfg, $db, $admin;
      
        doMydb(0);
        $thesql = "delete from #@__myqq where lanid=" . formatnum($lanid, 0);
       
        $db->execute($thesql);
        doMydb(1);
        die("{ok}恭喜，删除成功！");

       
    }

}

$myclass = new myclass();
$lanid = $_GET["lanid"];
switch ($_GET["action"]) {
    case "add":
		$admin->adminck("service_add");
        $myclass->add();
        break;
    case "edit":
		$admin->adminck("service_edit");	
        $myclass->edit($lanid);
        break;
      case "sort":
         refPage(1);
		 $admin->adminck_ajax("service_edit");	
         $myclass->sort("myqq");
		 break;
    case "update":
        refPage(2);
		 $admin->adminck_ajax("service_edit");			
        $myclass->update();
        break;
   
    case "admindo":
		refPage(1);
		$admin->adminck_ajax("service_del");	
		$myclass->admindo($lanid);
    default:
        $myclass->def();
}
?>