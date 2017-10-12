<?php

/**
 * 程序配置参数文件
 *
 * @version        $Id: web.config.php 1 10:33 2010年7月6日Z $
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */

/**
 *
 * 程序总控制类,包含整站所用到的各种方法，包含一些子类所共用的方法。
 * @author guoho
 *
 */
class alzCms {
   
   function addKeylink($content){
   		global $db;
		
   		$db->dosql("SELECT * FROM #@__keylink ORDER BY lanid DESC");
		while($row=$db->GetArray())
		{		
	//	echo $content;
			$content = str_replace($row['word'], '<a href="'.$row['url'].'" class="keylink" target="_blank">'.$row['word'].'</a>', $content);
	
		}
		return $content;
   }
   
   /**
    * 
    * 获取单个栏目分类信息
    * @param $lanid
    */
   function getClassOne($lanid){
   		global $db, $cfg;
   		$thesql="select * from #@__class where lanid=".$lanid." and lanstr='".$cfg["lanstr"]."'";
		return $db->GetOne($thesql);
   }

   /**
    * 
    * 获取顶级分类 Banner 图片。
    * @param $lanid 当前分类 ID 。
    * @param $topid 顶级分类ID。
    */
   function getTopBanner($topid){
   		global $db,$cfg;   		
		$thesql = "select banner from #@__class where lanid= $topid and lanstr='{$cfg["lanstr"]}'";
		return $db->getValue($thesql, 'banner');		
   }
	
    /**
     *
     *   写入CACHE 文件
     */
    function admincache() {
        global $cacheIo, $cfg, $beginTime, $cacheFileName, $pageEndWrite;
        $beginTime = ExecTime();
        $pageEndWrite = true;
        if (!$cfg["admin_cache"] || $_GET["alzCmsDefault"] != "" || $_POST["alzCmsDefault"] != "" || $_GET["search"] != "" || $_GET["action"] == "edit") {
            $cacheIo = false;
            return;
        }
        ob_start();
        $cacheurl = md5($_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]);
        $cacheFileName = admincachepath . $cacheurl . ".inc";
        if (is_file($cacheFileName)) {
            $t = formatnum($cfg["admin_cachetime"], 3);
            if ($t > 0) {
                $tc = filemtime($cacheFileName) + $t * 60 - $_SERVER["REQUEST_TIME"];
                if ($tc >= 0) {
                    $cacheIo = false;
                    die(file_get_contents($cacheFileName));
                }
            } else {
                $cacheIo = false;
                die(file_get_contents($cacheFileName));
            }
        }
        $cacheIo = true;
    }

    /**
     *
     * Enter description here ...
     */
    function admincache2() {
        global $cacheIo, $beginTime, $cacheFileName, $pageEndWrite;
        if ($cacheIo) {
            file_put_contents($cacheFileName, ob_get_contents(), LOCK_EX);
        }
        $t = (ExecTime() - $beginTime) * 1000;
        $t = number_format($t, 3);
        if ($pageEndWrite && $_GET["alzCmsDefault"] == "" && $_POST["alzCmsDefault"] == "") {
            die("\r\n<div class='pageLoadTime'>页面执行时间：<b>$t</b>毫秒</div>\r\n</body>\r\n</html>");
        }
    }

    /**
     *
     * 显示缓存内容 。
     */
    function uicache() {
        global $cacheIo, $cfg, $cacheFileName;
        if (!$cfg["ui_cache"] || $_GET["alzCmsDefault"] != "" || $_POST["alzCmsDefault"] != "") {
            $cacheIo = false;
            return;
        }
        ob_start();
        $cacheurl = md5($_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]);
        $cacheFileName = uicachepath . $cacheurl . ".inc";

        if (is_file($cacheFileName)) {
            $t = formatnum($cfg["ui_cachetime"], 15);
            if ($t > 0) {
                $tc = filemtime($cacheFileName) + $t * 60 - $_SERVER["REQUEST_TIME"];
                if ($tc >= 0) {
                    $cacheIo = false;
                    die(file_get_contents($cacheFileName));
                }
            } else {
                $cacheIo = false;
                die(file_get_contents($cacheFileName));
            }
        }
        $cacheIo = true;
    }

    /**
     *
     * 写入 前台  CACHE 文件。
     */
    function uicache2() {
        global $cacheIo, $cacheFileName;
        if ($cacheIo) {
            file_put_contents($cacheFileName, ob_get_contents(), LOCK_EX);
        }
    }

    /**
     *
     * E后台页高度
     */
    function adminHeight() {
        return formatnum(getcookies("adminHeight"), 500);
    }

    /**
     *
     * 获取某个分类的子类，并返回 option 选项。
     */
    function childclass() {
        global $cfg, $db;
        $classid = $_GET["classid"];
        $thesql = "select lanid,classname from #@__class where fid='$classid' and lanstr='{$cfg["lanstr"]}' order by sortid,lanid";
        $db->dosql($thesql);
        $temp = "<select onchange='tochildclass(this);'><option>==子类列表==</option>";
        while ($rs = $db->GetArray()) {
            $temp.="<option value='?classid={$rs["lanid"]}'>" . $rs["classname"] . "</option>";
        }
        $temp.="</select>";
        if ($db->nums() == 0)
            $temp = "";
        return $temp;
    }

   

    /**
     *
     * Enter description here ...
     * @param $loopstr
     * @param $valuestr
     */
    function loop($loopstr, $valuestr) {
        global $loop, $db, $cfg;
        $model = $this->getCan($valuestr, "model");
        $classid = formatnum($this->getCan($valuestr, "classid"), $cfg["classid"]);
        $thesql = $this->getCan($valuestr, "sql");
        if (instr($thesql, "[\$lanstr]")) {
            $thesql = str_replace("[\$lanstr]", "lanstr='{$cfg["lanstr"]}' and", $thesql);
        }
        if (instr($thesql, "[\$classid]")) {
            $thesql = str_replace("[\$classid]", allclassids($classid), $thesql);
        }

        $db->dosql($thesql, "loop" . $model . $classid);
        $i = 0;
        while ($rs = $db->GetArray("loop" . $model . $classid)) {
            $i++;
            $loop = $rs;
            $loop["loop_webpath"] = $cfg["webpath"];
            $loop["loop_i"] = $i;
            $loop["addtime"] = date($cfg["web_formattime" . $cfg["lanstr"]], $rs["edittime"]);
            switch ($model) {
                case "article":
                    if ($cfg["web_tohtml"]) {
                        $loop["loop_url"] = $cfg["htmlPath"] . $rs["paths"] . $rs["filename"];
                    } else {
                        $loop["loop_url"] = "/web/index.php?topclassid=" . gettopclassid($rs["classidstr"]) . "&classid={$rs["classid"]}&id=" . $rs["lanid"];
                    }
                    $loop["loop_time"] = date($cfg["article_formattime{$cfg["lanstr"]}"], $rs["edittime"]);
                    break;
                case "product":
                    if ($cfg["web_tohtml"]) {
                        $loop["loop_url"] = $cfg["htmlPath"] . $rs["paths"] . $rs["filename"];
                    } else {
                        $loop["loop_url"] = "/web/index.php?topclassid=" . gettopclassid($rs["classidstr"]) . "&classid={$rs["classid"]}&id=" . $rs["lanid"];
                    }
                    break;
                case "class":
                    if ($cfg["web_tohtml"]) {
                        $loop["loop_url"] = $cfg["htmlPath"] . $rs["paths"];
                    } else {
                        $loop["loop_url"] = "/web/index.php?topclassid={$rs["topclassid"]}&classid=" . $rs["lanid"];
                    }
                    $loop["loop_line"] = $i != 1 ? "|" : "";
                    break;
            }
            $temp.=$this->reLabel2($loopstr, $loop);
        }
        return $temp;
    }

    /**
     *
     * Enter description here ...
     * @param $valuestr
     */
    function t($valuestr) {
        $tag = $this->getCan($valuestr, "tag");
        $id = $this->getCan($valuestr, "id");

        return t($tag, $id);
    }

	 /**
     *
     * 获取 各个语言版本的变量值。
     * @param $valuestr
     */
    function halt($msg,$url="") {
        global $cfg, $db;  
		
		$tpl=$this->loadtpl("member/msg");
		$cfg['msg'] = $msg;
		$cfg['url'] = trim($url) == "" ? 'javascript:history.go(-1);' :  trim($url);
		$cfg["modelcontent"]=$this->reLabel($tpl);
		$tpl=$this->loadtpl("main_member");
		$content=$this->reLabel($tpl);
		echo $content;
		   
      
    }
    
    /**
     *
     * 获取 各个语言版本的变量值。
     * @param $valuestr
     */
    function _halt($msg,$url="") {
        global $cfg, $db;  
		
		$tpl=$this->loadtpl("member/msg_login");
		$cfg['msg'] = $msg;
		$cfg['url'] = trim($url) == "" ? 'javascript:history.go(-1);' :  trim($url);
		$cfg["modelcontent"]=$this->reLabel($tpl);
		$tpl=$this->loadtpl("main_member");
		$content=$this->reLabel($tpl);
		echo $content;
    }
    
    
  
    /**
     *
     * 获取 各个语言版本的变量值。
	 *  使用方法 tag 语言变量 model 模型 start  截取字符开始位位置 len 截取长度
	 *  实例： {$lang model="article" tag="new" start="0" len="2" /} 	  
     * @param $valuestr
     */
    function lang($valuestr) {
        global $cfg, $db;
        $tag = $this->getCan($valuestr, "tag");
        $model = $this->getCan($valuestr, "model");
		$yuyan = $this->getCan($valuestr, "yuyan");
		
		$start = intval($this->getCan($valuestr, "start"));		
		$len = intval($this->getCan($valuestr, "len"));
		
        $model = $model=="" ? "text" : $model;
      	$tag = $model.'_'.$tag;
		if($yuyan=='1'){
			if($cfg["lanstr"]=='zh_cn'){
				$content = $db->getvalue("SELECT content FROM #@__language WHERE tag='$tag' AND model='$model' AND lanstr='en'", "content");
			}else{
				$content = $db->getvalue("SELECT content FROM #@__language WHERE tag='$tag' AND model='$model' AND lanstr='zh_cn'", "content");
			}
		}else{
			$content = $db->getvalue("SELECT content FROM #@__language WHERE tag='$tag' AND model='$model' AND lanstr='{$cfg["lanstr"]}'", "content");
		}
		
		if($start > 0 ){
			$content = mb_substr($content, $start, $len);
		}		
		return $content;   
    
    }
	
	 /**
     *
     * 获取 各个语言版本的变量值。
     * @param $valuestr
     */
    function _lang($tag,$model="") {
        global $cfg, $db;       
        $model = $model=="" ? "text" : $model;
      	$tag = $model.'_'.$tag;
	  	return $db->getvalue("SELECT content FROM #@__language WHERE tag='$tag' AND model='$model' AND lanstr='{$cfg["lanstr"]}'", "content");
     
    
    }
    
    /**
     *
     * 按语言版本来获取 cfg 数组变量
     * @param $valuestr
     */
    function langCfg($valuestr) {
        global $cfg;        
        $tag = $this->getCan($valuestr, "tag");
        return $cfg[$tag.$cfg['lanstr']];
		//return $cfg[$tag];
    }

    /**
     *
     * 按语言版本来获取 cfg 数组变量
     * @param $valuestr
     */
    function langCfg_1($valuestr) {
        global $cfg;        
        $tag = $this->getCan($valuestr, "tag");
        //return $cfg[$tag.$cfg['lanstr']];
		return $cfg[$tag];
    }
 

    /**
     * 模板标签函数。
     * 包含其它模板文件！其实质作用就是 通过 传递其它模板文件名称 解析 其它模板文件，并返回已解析的内容。
     * 模板文件 函数标签所对应的方法。
     * @param unknown_type $str
     */
    function inc($str) {
        global $cfg;
        $url = $this->getCan($str, "url");
        $url2 = $this->getCan($str, "url2");
        //获取前台模板目录
        $dir = defined("FRONT_THEME_DIR") ? FRONT_THEME_DIR : '';
        
        if (file_exists(template . $dir. $url)) {
            $content = $this->reLabel($dir. $url);
        } else {
            $content = $cfg["readfailed"];
        }
        if ($url2 != "") {
            file_put_contents(template .$dir.  $url2, $content);
        } else {
            return $content;
        }
    }

	/**
	 * 栏目地址 
	 */
	function classUrl($valuestr){
		global $db, $cfg;
		$id = $this->getCan($valuestr, "id");
		if ($id == "index") {
			if ($cfg["web_tohtml"]) {
				if(lannums>1){				
					$url = sitepath . $cfg["lanstr"] . htmlIndex;
				}
				else {
					$url = sitepath . htmlIndex;
				}

			} else {
				$url = "/web/index.php?topclassid=0&classid=0";
			}
		} elseif ($id != "" && !is_numeric($id)) {//语言跳转
			if ($cfg["web_tohtml"]) {
				if ($id == deflan2) {
					$url = sitepath;
				} else {
					$url = sitepath . $id . htmlIndex;
				}
			} else {
				$topclassid = formatnum($cfg["topclassid"], 0);
				$classid = formatnum($cfg["classid"], 0);

				$url = "/web/index.php?topclassid=$topclassid&classid=$classid&id=" . $cfg["id"] . "&lanstr=" . $id;
			}
		} else {//普通栏目ID跳转
			if ($id < 1)
			$id = $cfg["topclassid"];
			$thesql = "select * from #@__class where lanid=$id and lanstr='{$cfg["lanstr"]}'";
			$rs = $db->GetOne($thesql);
			if ($cfg["web_tohtml"]) {
				$url = $cfg["htmlPath"] . $rs["paths"];
			} else {
				$url = "/web/index.php?topclassid={$rs["topclassid"]}&classid=" . $rs["lanid"];
			}
			$url = $this->getClassUrl($rs["urlgoto"], $url);
		}
		return $url;
	}
    
     /**
     * 模板 条件 标签函数。
     * 
     * 模板文件 函数标签所对应的方法。
     * @param unknown_type $str
     */
    function condition($loopstr, $valuestr) {
        global $cfg;
        $operArr = array("==", ">=", ">", "!=", "<=", "<");
        $var = $this->getCan($valuestr, "var");  //变量
		$oper = $this->getCan($valuestr, "oper");  //操作符 
		$oper = in_array($oper, $operArr) ? $oper : "==";
        $val = $this->getCan($valuestr, "val");  // 值
		$result = false;
		
		switch($oper){
			case "==":
				$result = $cfg[$var] == trim($val);
				break;
			case "!=":
				$result = $cfg[$var] != trim($val);
				break;
			case ">=":
				$result = $cfg[$var] >= trim($val);
				break;
			case ">":
				$result = $cfg[$var] > trim($val);
				break;
			case "<=":
				$result = $cfg[$var] <= trim($val);
				break;
			case "<":
				$result = $cfg[$var] < trim($val);
				break;				
		}
        if ($result)
            return $this->reLabel2($loopstr);
        else 
            return "";
    
    }
    /**
     *
     * @tutorial 匹配 格式 URL 字符串中的 文件名称及地址
     * @param unknown_type $varstr   所传递的格式字符串
     * @param unknown_type $str   匹配格式对应的方法
     */
    function getCan($varstr, $str) {
        $match = "";
        preg_match("/" . $str . "=\"(.*?)\"/", $varstr, $match);
        return @$match[1];
    }

    /**
     *
     * 用于替换  [$xxx] 模板标签变量
     * @param $content
     */
    function reHtml($content) {
        global $cfg, $db;
        $rl = new getLabel($content);
        $H = $rl->getHtml();

        $lbcount = count($H[0]);
        $v3 = $content;
        for ($i = 0; $i < $lbcount; $i++) {
            $v1 = $H[0][$i];
            $id = $H[1][$i];
            $thesql = "select lanid,topclassid,paths from #@__class where lanid=$id and lanstr='{$cfg["lanstr"]}'";
            $rs = $db->GetOne($thesql);
            if ($cfg["web_model"] == "php") {
                $v2 = webpath . "?topclassid={$rs["topclassids"]}&classid={$rs["lanid"]}&lanstr={$cfg["lanstr"]}";
            } else {
                $v2 = htmlPath() . $rs["paths"];
            }
            $v3 = str_replace($v1, $v2, $v3);
        }
        $H = $rl->getHtmls();
        $lbcount = count($H[0]);
        for ($i = 0; $i < $lbcount; $i++) {
            $v1 = $H[0][$i];
            $model = $H[1][$i];
            $id = $H[2][$i];
            $thesql = "select lanid,classid,classidstr,paths,filename from #@__$model where lanid=$id and lanstr='{$cfg["lanstr"]}'";
            $rs = $db->GetOne($thesql);
            if ($cfg["web_model"] == "php") {
                $topclassid = gettopclassid($rs["classidstr"]);
                $v2 = webpath . "?topclassid={$topclassid}&classid={$rs["classid"]}&id={$rs["lanid"]}&lanstr={$cfg["lanstr"]}";
            } else {
                $v2 = htmlPath() . $rs["paths"] . $rs["filename"];
            }
            $v3 = str_replace($v1, $v2, $v3);
        }
        return $v3;
    }

    /**
     *
     * 解析模板文件，并返回内容。
     * @param unknown_type $template 模板文件
     * @param unknown_type $funArr   用于要替换标签的全局数组。
     */
    function reLabel($template, $funArr="") {
        global $cfg;

        if (!is_array($funArr))
            $funArr = $cfg;
        $class = $this;
        $content = file_get_contents(template . $template);

        $rl = new getLabel($content);

        //解析循环标签
        //循环标签 1函数名，2参数，3循环内容
        $match = $rl->getLoop();

        $lbcount = count($match[0]);
        for ($i = 0; $i < $lbcount; $i++) {
            $v1 = $match[0][$i]; //要替换的内容

            $fun = $match[1][$i];

            //取得类与函数
            if (instr($fun, ".")) {
                $arr = explode(".", $fun);
                $fun = $arr[1];
                $class = new $arr[0];
            }
            $v2 = $class->$fun($match[3][$i], $match[2][$i]);
            $content = str_replace($v1, $v2, $content);
        }


        /* 解析方法标签  */
        $N = $rl->getNomal();

        $lbcount = count($N[0]);
        for ($i = 0; $i < $lbcount; $i++) {
            $v1 = $N[0][$i];  //要替换的内容

            $fun = $N[1][$i];  //函数

            if (instr($fun, ".")) {
                $arr = explode(".", $fun);
                $fun = $arr[1];
                $class = new $arr[0];
            }
            $v2 = $class->$fun($N[2][$i]);   //$N[2][$i]:参数   //$v2 : 替换内容
            $content = str_replace($v1, $v2, $content);
        }


        /* 解析变量标签 ，变量为全局变量 cfg 或参数 $funArr 所传入的数组变量 */
        $C = $rl->getConst();
        $lbcount = count($C[0]);
        for ($i = 0; $i < $lbcount; $i++) {
            $v1 = $C[0][$i];  // 要替换的内容

            $v2 = $funArr[$C[1][$i]];
            $content = str_replace($v1, $v2, $content);
        }
        return $content;
    }

    /**
     * 解析 循环标签 及 循环标签的标签变量。
     * Parse the Loop template tag's child tag 
     * @param $templatestr
     * @param $funArr
     */
    function reLabel2($templatestr, $funArr="") { //子标签
        global $cfg;
        if (!is_array($funArr))
            $funArr = $cfg;

        $templatestr = trim($templatestr, "\r\n");
        $rl = new getLabel($templatestr);
        $N = $rl->getNomal2(); //常规标签

        $lbcount = count($N[0]);
        $v3 = $templatestr;
        for ($i = 0; $i < $lbcount; $i++) {
            $v1 = $N[0][$i];

            $v2 = $this->$N[1][$i]($N[2][$i]);
            $v3 = str_replace($v1, $v2, $v3);
        }

        $C = $rl->getConst2(); //常数

        $lbcount = count($C[0]);
        for ($i = 0; $i < $lbcount; $i++) {
            $v1 = $C[0][$i];
            $v2 = $funArr[$C[1][$i]];
            $v3 = str_replace($v1, $v2, $v3);
        }
        return $v3;
    }

    /**
     * 切换语言 HTML 代码
     * Enter description here ...
     * @param $loopstr
     * @param $varstr
     */
    function switchLang($loopstr, $varstr) {
        global $cfg;

        if (lannums < 2)
            return;
        $cfg["content"] = "<a id='all' class='mybutton' onclick='switchLang(\"all\",this);'>全部</a>";
        for ($i = 0; $i < lannums; $i++) {
            $cfg["content"].="<a id='" . $cfg["language"][$i] . "' class='mybutton' onclick='switchLang(\"" . $cfg["language"][$i] . "\",this);'>" . $cfg["languagename"][$i] . "</a>";
        }
        return $this->reLabel2($loopstr);
    }

    /**
     *
     * 多语言表单
     * @param $loopstr
     * @param $varstr
     */
    function loopLang($loopstr, $varstr) {
        global $cfg;

        $id = $this->getCan($varstr, "id");

        for ($i = 0; $i < lannums; $i++) {
            $cfg["thelanstr"] = $cfg["language"][$i];

            if ($cfg["thelanstr"] == deflan) {
               // $cfg["mustinput"] = "请输入名称！";
            } else {
                $cfg["mustinput"] = "";
            }
            $cfg["thelanname"] = lannums > 1 ? "[" . $cfg["languagename"][$i] . "]" : "";

            $cfg["thename"] = $id . $cfg["thelanstr"];

            $cfg["thevalue"] = $cfg[$cfg["thename"]];
            $funstr.=$this->reLabel2($loopstr);
        }
        return $funstr;
    }

    /**
     *
     * 
     * @param $varstr
     */
    function thevalue($varstr) {
        global $cfg;
        $id = $this->getCan($varstr, "id");
        return $cfg[$id . $cfg["thelanstr"]];
    }

    /**
     *
     * 更新表内数据。
     * @param $dbname
     * @param $dbname2
     */
    function dbref($dbname, $dbname2="") {
        global $db, $cfg;
        $sec = $this->formatdb($dbname);
        if ($dbname2 != "")
            $this->formatdb($dbname2);
        die("{ok}恭喜，[" . $dbname . "]表结构更新成功！" . $sec);
    }

    /**
     *
     * 刷新表结构
	 * @param $dbname   数据表名称 
     */
    function formatdb($dbname) {
        global $db, $cfg;
        doMydb(0);
		
        $idselect = $db->getDatabaseIdstr($dbname);
        $thesql = "select * from #@__$dbname group by lanid";
        $db->dosql($thesql, "dbref");
        while ($rs = $db->GetArray("dbref")) {
            $lanid = $rs["lanid"];
            $thelan = $rs["lanstr"];
            for ($i = 0; $i < lannums; $i++) {
                $lanstr = $cfg["language"][$i];
                $thesql2 = "select * from #@__$dbname where lanstr='" . $lanstr . "' and lanid=" . $lanid;
                $db->dosql($thesql2, "alz");
                if (!$db->nums("alz")) {
                    $insertsql = "insert into #@__$dbname ($idselect) select $idselect from #@__$dbname where lanstr='" . $thelan . "' and lanid=$lanid";
                    $db->execute($insertsql);
                    $id = $db->GetLastID();
                    $db->execute("update #@__$dbname set lanstr='" . $lanstr . "' where id=" . $id);
                }
            }
        }
        doMydb(1);
    }

    /**
     *
     * 返回语言选择 下拉选项 表单。
     * @param $valuestr
     */
    function lanSelect($valuestr) {
        global $cfg;
        $name = $this->getCan($valuestr, "name");
        $js = $this->getCan($valuestr, "js");
        $css = $this->getCan($valuestr, "css");
        $add = $this->getCan($valuestr, "add");
        return $this->languageSelect($name, $js, $css, $add);
    }

    /**
     *
     * 语言选择  下拉 选项 表单。
     * @param $name
     * @param $js
     * @param $css
     * @param $add
     */
    function languageSelect($name, $js="", $css="", $add="") {
        global $cfg;
        if ($js != "")
            $js = " onchange='$js'";
        if ($css != "")
            $css = " class='$css'";
        $temp = "<select name='" . $name . "' id='" . $name . "'" . $js . $css . ">\r\n";
        $temp.=$add;
        for ($i = 0; $i < lannums; $i++) {
            $lanname = $cfg["languagename"][$i];
            $lanstr = $cfg["language"][$i];
            $temp.="<option value='" . $lanstr . "'>" . $lanname . "</option>\r\n";
        }
        $temp.="</select>";
        return $temp;
    }

    /**
     *
     * Fck 编辑器。
     * @param $valuestr
     */
    function fckedit($valuestr) {

        global $cfg;
        $id1 = $this->getCan($valuestr, "id");
        $id = $id1 . $cfg["thelanstr"];

        $width = $this->getCan($valuestr, "width");
        $height = $this->getCan($valuestr, "height");
        $style = $this->getCan($valuestr, "style");
        $content = $cfg[$id1 . $cfg["thelanstr"]];

        if ($width == "")
            $width = "100%";
        if ($style == "" || $style == "Default") {
            $style = "Default";
            if ($height == "")
                $height = $cfg["fck_height"] . "px";
        }else {
            $style = "Basic";
            if ($height == "")
                $height = $cfg["fck_height2"] . "px";
        }
        if ($id == "about" . $cfg["thelanstr"]) {
            $themodel = $cfg["fck_model2"];
        } else {
            $themodel = $cfg["fck_model"];
        }

        switch ($themodel) {
            case 2:
                $thefun = "fckedit2";
                break;
            case 1:
                if ($cfg["thelanstr"] == deflan) {
                    $thefun = "fckedit2";
                } else {
                    $thefun = "notedit";
                }
                break;
            default:
                $thefun = "notedit";
        }

        return $this->$thefun($id, $content, $width, $height, $style);
    }

    /**
     *
     * Enter description here ...
     * @param $id
     * @param $content
     * @param $width
     * @param $height
     * @param $style
     */
    function fckedit2($id, $content, $width, $height, $style) {

        $content = htmlspecialchars($content);
        $funstr = "<input type=\"hidden\" id=\"$id\" name=\"$id\" value=\"$content\" style=\"display:none\" class=\"fckEdit_\" />\r\n";
        $funstr.="<input type=\"hidden\" id=\"" . $id . "___Config\" value=\"\" style=\"display:none\" />\r\n";
        $funstr.="<iframe id=\"" . $id . "___Frame\" src=\"" . webpath . "fckeditor/editor/fckeditor.html?InstanceName=$id&amp;Toolbar=$style\" width=\"$width\" height=\"" . $height . "px\" frameborder=\"0\" scrolling=\"no\"></iframe>\r\n";
        return $funstr;
    }

    /**
     *
     * Enter description here ...
     * @param $id
     * @param $content
     * @param $width
     * @param $height
     * @param $style
     */
    function notedit($id, $content, $width, $height, $style="") {
        $funstr = "<textarea style=\"width:$width; height:$height;\" id=\"$id\" name=\"$id\" class=\"$style post_\">$content</textarea>\r\n";
        return $funstr;
    }

    /**
     *
     * Enter description here ...
     * @param $id
     * @param $content
     * @param $width
     * @param $height
     * @param $allowhtml
     */
    function myedit($id, $content, $width="100%", $height="400", $allowhtml="0") {
        $content = htmlspecialchars($content);
        $temp = "<textarea name=\"$id\" id=\"uchome-ttHtmlEditor\" style=\"display:none;\">$content</textarea>\r\n";
        $temp.="<iframe src=\"" . webpath . "editor.php?allowhtml=$allowhtml\" name=\"uchome-ifrHtmlEditor\" id=\"uchome-ifrHtmlEditor\" scrolling=\"no\" border=\"0\" frameborder=\"0\" style=\"width:$width;border: 1px solid #C5C5C5;\" height=\"$height\"></iframe>\r\n";
        return $temp;
    }

    /**
     *
     * 更新排序
     * @param $dbname
     * @param $id
     */
    function sort($dbname="class", $id="lanid") {
        global $db;
        doMydb(0);
        $lanid = $_POST["lanid"];
        $sortid = $_POST["sortid"];
        if (!$lanid || !$sortid)
            die("{err}无可执行数据！");
        $lanidarr = explode(",", $lanid);
        $sortidarr = explode(",", $sortid);
        if (count($lanidarr) != count($sortidarr))
            die("{err}数据异常：SORTID!=LANID！");
        $num = count($lanidarr);
        for ($i = 0; $i < $num; $i++) {
            $thesql = "update #@__$dbname set sortid=$sortidarr[$i] where $id=$lanidarr[$i]";
            $db->execute($thesql);
        }
        doMydb(1);
        die("{ok}恭喜，排序成功！");
    }

    /**
     *
     * 图片预览
     * @param $path
     * @param $pic
     * @param $pic2
     */
    function viewpicurl($path, $pic, $pic2="") {
        if (instr($pic, "http://")) {
            $pic2 = $pic;
        } else {
            $pic2 = $pic2 != "" ? $pic2 : $pic;
            $pic2 = webpath . $path . $pic2;
            $pic = webpath . $path . $pic;
        }
        return "<img class='cur' align=\"absmiddle\" src='" . skinspath . "view.jpg' onmouseover='picView(this,\"$pic\",event);' onClick='window.open(\"$pic2\");' />";
    }

    /**
     *
     * 通过标题转换至 HTML 文件名
     * @param $id
     * @param $title
     * @param $time
     */
    function getFilename($id, $title, $time) {
        global $cfg;
        /*if ($title == "")
            $title = "0";
        $temp = $cfg["html_nametype"];
        if ($id != 0 && instr($temp, "{ID}")) {
            $temp = str_replace("{ID}", $id, $temp);
        }
        if (instr($temp, "{year}")) {
            $temp = str_replace("{year}", date("Y", $time), $temp);
        }
        if (instr($temp, "{month}")) {
            $temp = str_replace("{month}", date("m", $time), $temp);
        }
        if (instr($temp, "{day}")) {
            $temp = str_replace("{day}", date("d", $time), $temp);
        }
        if (instr($temp, "{pinyin}")) {
            $pinyin = $this->getPinYin($title);
            
            if ($pinyin == "")
                $pinyin = "file";
            $pinyin.= date("dHis",$time);
            $temp = str_replace("{pinyin}", $pinyin, $temp);
        }*/
		$temp=$id.".html";
        return $temp;
    }
	/**
     *
     * 通过标题转换至 HTML 文件名
     * @param $id
     * @param $title
     * @param $time
     */
    function getFilename1($title) {
        global $cfg;
        if ($title == "")
            $title = "0";
            $pinyin = $this->getPinYin($title);
        return $pinyin;
    }


    /**
     *
     * 获取字符串所对应的拼音。
     * @param unknown_type $str
     */
    function getPinYin($str) {
        global $cfg;
        $len = strlen($str);
        $i = 0;
        $temp = "";
        while ($i <= $len) {
            if (strlen($temp) >= $cfg["filenamemaxlength"])
                return $temp;
            $temp_str = substr($str, 0, 1);
            if (ord($temp_str) > 127) {
                $temp.=$this->getPinYin2(substr($str, 0, 3));
                $str = substr($str, 3);
                $i+=2;
            } else {
                $temp.=formatname(substr($str, 0, 1));
                $str = substr($str, 1);
                $i++;
            }
        }
        return $temp;
    }

    /**
     * 从数据库中提取某个汉字所对应的拼音。
     */
    function getPinYin2($str) {
        global $db;
        $thesql = "select pinyin from #@__pinyin where content like '%" . $str . "%'";
        return $db->getValue($thesql, "pinyin");
    }

    /**
     * 显示帮助提示图片
     * Enter description here ...
     * @param $valuestr
     */
    function help($valuestr) {
        global $cfg;
        $id = $this->getCan($valuestr, "id");
        $type = $this->getCan($valuestr, "type");

        $width = formatnum($this->getCan($valuestr, "width"), 0);
        $height = formatnum($this->getCan($valuestr, "height"), 0);
        $pic = $this->getCan($valuestr, "pic");
        if ($pic == "")
            $pic = "help.gif";

        return "<img style='cursor:pointer;' src='" . skinspath . $pic . "' align='absmiddle' onmouseover='help(event,this,$id,\"" . $type . "\",$width,$height);' />";
    }

   

    function classSelectList($valuestr) { //添加资料时候的所属栏目调用
        global $cfg, $db;
        $cfg["model"] = $this->getCan($valuestr, "model");
        $cfg["lanidstr"] = $cfg["lanidstr"] == "" ? "0," : $cfg["lanidstr"];
        $funstr = "<select name='classid' id='classid' class='post_'>\r\n";
        $funstr.=$this->classSelectLoopF(0, "├ ");
        $lanidstr = trim($cfg["lanidstr"], ",");
        $funstr.=$this->classSelectLoop2("├ ", $lanidstr);
        $funstr.="</select>\r\n";
        return $funstr;
    }

    function classSelectLoopF($fid, $str) {
        global $cfg, $db;
        $conn = $db->linkID;
        $thesql = "select * from #@__class where fid=$fid and lanstr='" . lanstr . "' and (model like '" . $cfg["model"] . "%') order by sortid,id";
        $cfg["first"] = false;
        $thesql = $db->SetQuery($thesql);
        $result = mydb_query($thesql, $conn);
        while ($rs = mydb_fetch_array($result)) {
            $lanid = $rs["lanid"];
            $cfg["lanidstr"].=$lanid . ",";
            $funstr.="<option value='$lanid'>" . $str . $rs["classname"] . "</option>\r\n";
            $funstr.=$this->classSelectLoop($lanid, "　" . $str);
        }
        return $funstr;
    }

    function classSelectLoop($fid, $str) {
        global $cfg, $db;
        $conn = $db->linkID;
        $thesql = "select * from #@__class where fid=$fid and lanstr='" . lanstr . "' and (model='nothing' or model like '" . $cfg["model"] . "%') order by sortid,id";
        $cfg["first"] = false;
        $thesql = $db->SetQuery($thesql);
        $result = mydb_query($thesql, $conn);
        while ($rs = mydb_fetch_array($result)) {
            $lanid = $rs["lanid"];
            $cfg["lanidstr"].=$lanid . ",";
            $funstr.="<option value='$lanid'>" . $str . $rs["classname"] . "</option>\r\n";
            $funstr.=$this->classSelectLoop($lanid, "　" . $str);
        }
        return $funstr;
    }

    function classSelectLoop2($str, $lanidstr) {
        global $cfg, $db;
        $thesql = "select * from #@__class where lanstr='" . lanstr . "' and model like '" . $cfg["model"] . "%' and lanid not in($lanidstr) order by sortid,id";
        $db->dosql($thesql);
        while ($rs = $db->GetArray()) {
            $lanid = $rs["lanid"];
            $funstr.="<option value='$lanid'>" . $str . $rs["classname"] . "</option>\r\n";
        }
        return $funstr;
    }
	
	//后台导航，当前位置，配合JS使用
    function guid($model) { 
        global $cfg, $db;
        $classid = formatnum($_GET["classid"], 0);
        if ($classid == 0) {
            $other = explode("|", $_GET["other"]);
            $commend = $other[0];
            $locked = $other[1];
            $del = $other[2];
            if ($locked) {
                $temp = guid_str . $cfg["model_name"] . "审核";
            }
            if ($del) {
                $temp = guid_str . $cfg["model_name"] . "回收站";
            }
            if ($cfg["commend_" . $model . "_key"] != "") {
                $key = explode("|", $cfg["commend_" . $model . "_key"]);
                $name = explode("|", $cfg["commend_" . $model . "_name" . lanstr]);
                $num = count($key);
                for ($i = 0; $i < $num; $i++) {
                    if ($key[$i] == $commend) {
                        $temp = guid_str . $name[$i];
                    }
                }
            }
        } else {
            $classidstr = getFidstr("class", $classid);
            $db->dosql("select lanid,classname,model from #@__class where lanstr='" . lanstr . "' and lanid in($classidstr) order by lid");
            while ($rs = $db->GetArray()) {
                if ($rs["model"] == $model) {
                    $temp.=guid_str . "<a href='?classid=" . $rs["lanid"] . "'>" . $rs["classname"] . "</a>";
                } else {
                    $temp.=guid_str . $rs["classname"];
                }
            }
        }
        die($temp);
    }

    function mydid($lanidstr, $dbname, $do, $id="lanid") {//删除、还原、审核、取消审核选中
        global $db;
        doMydb(0);
        $thesql = "update #@__$dbname set $do where $id in($lanidstr)";
        $db->execute($thesql);
        doMydb(1);
    }

    function mydidall($dbname, $do) {//删除、还原、审核、取消审核所有
        global $db;
        doMydb(0);
        $thesql = "update #@__$dbname set $do";
        $db->execute($thesql);
        doMydb(1);
    }

    /**
     * 
     * 彻底删除
     * @param $dbname
     * @param $lanidstr
     */
    function deltrue($dbname, $lanidstr="") {//彻底删除,文章和产品,内容表分开的情况
        global $db, $cfg;
        doMydb(0);
        if ($lanidstr != "") {
            $thesql = "select * from #@__$dbname where lanid in ($lanidstr) and del=1";
            $thesql2 = "delete from #@__$dbname where lanid in ($lanidstr) and del=1";
        } else {
            $thesql = "select * from #@__$dbname where del=1";
            $thesql2 = "delete from #@__$dbname where del=1";
        }
        $db->dosql($thesql);
        $lanidstr2 = "";
        while ($rs = $db->GetArray()) {
            $lanidstr2.=$rs["lanid"] . ",";
            if ($rs["paths"] != "" && $rs["filename"] != "") {
                for ($i = 0; $i < lannums; $i++) {
                    $htmlpath = htmlpath($cfg["language"][$i]);
                    $file = wwwroot . $htmlpath . $rs["paths"] . $rs["filename"];
                    delfile($file);
                }
            }
        }
        $db->execute($thesql2);
        $lanidstr2 = trim($lanidstr2, ",");
        if ($lanidstr2 != "") {
            $thesql = "delete from #@__" . $dbname . "s where lanid in ($lanidstr2)";
            $db->execute($thesql);
        }
        doMydb(1);
    }   

    /**
     * 
     * 模块名称
     * @param $valuestr
     */
    function modeltoptitle($valuestr) {
        global $cfg, $db;
        $classid = $cfg["classid"];
        $rs = $db->GetOne("select classname from #@__class where lanstr='{$cfg["lanstr"]}' and lanid=" . $classid);
       return left($rs["classname"], 25);
	   // return '<a title="' . $rs["classname"] . '" href="#">' . left($rs["classname"], 25) . '</a>';
       // return '<span class="green">'.mb_substr($rs["classname"],0, 1,"utf-8").'</span>'.mb_substr($rs["classname"],1, 25,"utf-8");
    }

    /*     * *
     * 模块英文名
     */

    function modeltoptitleen($valuestr) {
        global $cfg, $db;
        $classid = $cfg["classid"];
        $rs = $db->GetOne("select classname from #@__class where lanstr='en' and lanid=" . $classid);
        return $rs["classname"];
    }

    /**
     * 
     * 模块标题
     * @param $valuestr
     */
    function modeltitle($valuestr) {
        global $cfg, $db;
        $classid = $cfg["classid"];
        $rs = $db->GetOne("select classname from #@__class where lanstr='{$cfg["lanstr"]}' and lanid=" . $classid);
        return $rs["classname"];
    }
	
    /**
     * 
     * 面包导航
     * @param $valuestr
     */
    function modelguid($valuestr) {
        global $cfg, $db;

        $str = $this->getCan($valuestr, "str");
        if ($str == "")
            $str = " >> ";
        $lanstr = $cfg["lanstr"];
        $classidstr = $cfg["classidstr"];
 
        $url = geturl("?topclassid=0&classid=0", sitepath);
        $temp = "<a href='$url'>" . lang("index") . "</a>";
        if ($classidstr == "")
            return $temp;
        if ($classidstr == "search"||!empty($_POST["keyword"])) {
            if ($cfg["web_tohtml"]) {
                if ($cfg["lanstr"] == deflan) {
                    $url = sitepath;
                } else {
                    $url = sitepath . $cfg["lanstr"] . htmlIndex;
                }
            } else {
                $url = "?topclassid=0&classid=0";
            }
			if($cfg["lanstr"]=="zh_cn"){
				if($cfg["model"]=="article"){
					return "<a href='$url'>" . lang("index") . "</a> >> " . "产品问题搜索";
				}else{
            		return "<a href='$url'>" . lang("index") . "</a> >> " . "产品搜索";
				}
			}else{
				if($cfg["model"]=="article"){
					return "<a href='$url'>" . lang("index") . "</a> >> " . "Product problem search";
				}else{
					return "<a href='$url'>" . lang("index") . "</a> >> " . "Product search";
				}
			}
        }
        $thesql = "select topclassid,classname,lanid,paths,urlgoto from #@__class where lanid in($classidstr) and lanstr='$lanstr' order by lid";
        $db->dosql($thesql);
        while ($rs = $db->GetArray()) {
            $phpurl = "?topclassid={$rs["topclassid"]}&classid=" . $rs["lanid"];
            $htmlurl = $cfg["htmlPath"] . $rs["paths"];
            $url = geturl($phpurl, $htmlurl);
            $url = $this->getClassUrl($rs["urlgoto"], $url);
            $temp.=$str . "<a href='$url'>" . left($rs["classname"],15) . "</a>";
        }

       /* if ($cfg["id"]) {
            $titles = gets("title", $cfg["model"], "lanstr='{$cfg["lanstr"]}' and lanid=" . $cfg["id"]);
            $titles = $titles;
            $temp.=$str . $titles;
        }*/

        return $temp;
    }

    function geturl($valuestr) {//静态标签，url2要在前面加root，动态程序函数geturl不用加。
        global $cfg;
        $url1 = $this->getCan($valuestr, "url1");
        $url2 = $this->getCan($valuestr, "url2");
        if ($cfg["web_model"] == "php") {
            return $url1;
        } else {
            return sitepath . $url2;
        }
    }

    /**
     *
     * Enter description here ...
     * @param $urlgoto
     * @param $url
     */
    function getClassUrl($urlgoto, $url) {//栏目ID/完整地址/不跳转为空
        if ($urlgoto == "")
            return $url;
        if (is_numeric($urlgoto)) {
            global $cfg, $db;
            $thesql = "select paths,topclassid from #@__class where lanid=$urlgoto and lanstr='{$cfg["lanstr"]}'";
            $rs = $db->GetOne($thesql);
            if ($rs) {
                if ($cfg["web_tohtml"]) {
                    $temp = $cfg["htmlPath"] . $rs["paths"];
                } else {
                    $temp = "/web/index.php?topclassid={$rs["topclassid"]}&classid={$urlgoto}&lanstr=" . $cfg["lanstr"];
                }
            } else {
                $temp = webpath;
            }
        } else {

            $temp = "http://" . str_replace("http://", "", $urlgoto);
        }
        return $temp;
    }

    function getChildNum($fid) {
        global $db;
        $thesql = "select lanid from #@__class where fid=$fid";
        $db->dosql($thesql, "ChildNum");
        return $db->nums("ChildNum");
    }

    function mylist($valuestr) {
        global $cfg, $db;
        $getclass = $this->getCan($valuestr, "class");
        $class = $getclass != "" ? $getclass : "mylist";
        $id = formatnum($this->getCan($valuestr, "id"), 0);
        $topclassid = formatnum($cfg["topclassid"], 0);
        $classid = formatnum($cfg["classid"], 0);
        if ($classid != $topclassid) {
            $thesql = "select lanid from #@__class where fid=$classid and lanstr='" . $cfg["lanstr"] . "'";
            $db->dosql($thesql);
            if (!$db->nums()) {
                $classid = gets("fid", "class", "lanid=" . $classid); //无子类,等于其父ID
            }
        }
        if ($topclassid != 10) {
            $classid = $id != 0 ? $id : $topclassid;
        }
        $thesql = "select topclassid,lanid,classname,paths from #@__class where fid=$classid and lanstr='" . $cfg["lanstr"] . "' order by sortid,lanid";
        $db->dosql($thesql);
        if (!$db->nums())
            return;
        $temp = "<div class='$class'>\r\n";
        while ($rs = $db->GetArray()) {
            $topclassid = $rs["topclassid"];
            $classid = $rs["lanid"];
            $url = geturl("/web/index.php?topclassid=$topclassid&classid=$classid", $cfg["htmlPath"] . $rs["paths"]);
            $a = "<a href='$url'>{$rs["classname"]}</a>";
            $temp.="<div class='{$class}_1 class$classid'>$a</div>\r\n";
            //$temp.=$this->mylist2($class,$topclassid,$classid);
        }
        $temp.="</div>\r\n";
        return $temp;
    }

    function mylist2($class, $topclassid, $classid) {
        global $cfg, $db;
        $thesql = "select lanid,classname,paths from #@__class where fid=$classid and lanstr='" . $cfg["lanstr"] . "' order by sortid,lanid";
        $db->dosql($thesql, "list2");
        if (!$db->nums("list2"))
            return;
        $temp = "\t<div class='{$class}_1s class$classid'>\r\n";
        while ($rs = $db->GetArray("list2")) {
            $classid = $rs["lanid"];
            $url = geturl("?topclassid=$topclassid&classid={$rs["lanid"]}", $cfg["htmlPath"] . $rs["paths"]);
            $a = "<a href='$url'>{$rs["classname"]}</a>";
            $temp.="\t\t<div class='{$class}_2 class$classid'>$a</div>\r\n";
            //$temp.=$this->mylist3($class,$topclassid,$classid);
        }
        $temp.="\t</div>\r\n";
        return $temp;
    }

    function mylist3($class, $topclassid, $classid) {
        global $cfg, $db;
        $thesql = "select lanid,classname,paths from #@__class where fid=$classid and lanstr='" . $cfg["lanstr"] . "' order by sortid,lanid";
        $db->dosql($thesql, "list3");
        if (!$db->nums("list3"))
            return;
        $temp = "\t\t\t<div class='{$class}_2s class$classid'>\r\n";
        while ($rs = $db->GetArray("list3")) {
            $classid = $rs["lanid"];
            $url = geturl("/web/index.php?topclassid=$topclassid&classid={$rs["lanid"]}", $cfg["htmlPath"] . $rs["paths"]);
            $a = "<a href='$url'>{$rs["classname"]}</a>";
            $temp.="\t\t\t\t<div class='{$class}_3 class$classid'>$a</div>\r\n";
        }
        $temp.="\t\t\t</div>\r\n";
        return $temp;
    }

    /**
     * 从系统参数中获取网站 标题、简介及描述信息
     * Enter description here ...
     * @param $name
     * @param $tag
     * @param $about
     */
    function web_seo($name="", $tag="", $about="") {
        global $cfg;
      
        if ($name == "") {
            $name = $cfg["webname{$cfg["lanstr"]}"];
        } else {
            $name = $name.'-'.$cfg["webname{$cfg["lanstr"]}"];
        }
        if ($tag == "")
            $tag = $cfg["webkeywords{$cfg["lanstr"]}"];
        if ($about == "")
            $about = $cfg["webdescription{$cfg["lanstr"]}"];
        
        $cfg["page_title"] = losehtml($name);
        
        $cfg["page_keywords"] = losehtml($tag);
        $cfg["page_description"] = losehtml($about);
    }

    function loadingbar($valuestr) {
        global $cfg;
        $class = $this->getCan($valuestr, "class");
        $id = $this->getCan($valuestr, "id");
        $temp = "<div class=\"{$class}\">\r\n";
        $temp.="<div class=\"{$class}_num\" id=\"{$id}_num\">0%</div>\r\n";
        $temp.="<div class=\"{$class}_ing\" id=\"{$id}_ing\"></div>\r\n";
        $temp.="<div class=\"{$class}_bg\"></div>\r\n";
        $temp.="</div>";
        return $temp;
    }

    /**
     *
     * Enter description here ...
     * @param $valuestr
     */
    function page($valuestr) {
        global $cfg;
        if ($cfg["pageto"] == "")
            $cfg["pageto"] = "pageto";
        $io = formatnum($cfg["page_style"], 1);

        $temp = '<script type="text/javascript">page(' . $cfg["allnums"] . ',' . $cfg["pagesize"] . ',' . $io . ',"' . $cfg["pageto"] . '");</script>';
        if ($cfg["web_tohtml"]) {
            $temp.=$this->htmlpage($cfg["filename"], $cfg["pages"], $cfg["page"]);
        }

        return $temp;
    }

    function norecord($loopstr) {
        global $cfg;
        if ($cfg["have_record"])
            return;
        $funstr = $this->reLabel2($loopstr);
        return $funstr;
    }

    function format_inpage($content, $page=1) {
        global $cfg;
        $page--;
        if (instr($content, cutpagestr)) {
            if ($cfg["inpageto"] == "")
                $cfg["inpageto"] = "inpageto";
            $arr = explode(cutpagestr, $content);
            $pagestr = '<script type="text/javascript">page(' . count($arr) . ',1,0,"' . $cfg["inpageto"] . '");</script>';
            if (isset($arr[$page])) {
                $return = $arr[$page];
            } else {
                $return = $arr[0];
            }
            return $return . $pagestr;
        } else {
            return $content;
        }
    }

    function htmlpage($filename, $pages, $page, $inpage="") {
        global $cfg;
        loadlanXml("page", "page_", "inpage");
        if (!$inpage) {
            $page = $pages - $page + 1;
        }
        $back = $page - 1;
        $next = $page + 1;
        $temp = "<div style='display:none;'>";
        for ($i = 1; $i <= $pages; $i++) {
            $temp.="<a href='" . $this->htmlpage2($filename, $i, $inpage) . "'>$i</a>";
        }
        $temp.="</div>";
        return $temp;
    }

    function htmlpage2($filename, $page, $inpage) {
        global $cfg;
        if (!$inpage) {
            $page = $cfg["pages"] - $page + 1;
            $page1 = $cfg["pages"];
        } else {
            $page1 = 1;
        }
        if ($page == $page1) {
            return $filename;
        } else {
            return $filename . $cfg["page_prefix"] . $page . "/";
        }
    }

    function getsearsql($type, $keyword, $model, $keystr) {
        global $cfg, $db;
        if ($keyword == "")
            return;
        if ($type != "" && instr($keystr, $type)) {
            return $this->getsearsqlone($type, $keyword, $model, "and");
        }
        if ($type == "") {
            $arr = explode(",", $keystr);
            foreach ($arr as $id => $value) {
                $temp.=$this->getsearsqlone($value, $keyword, $model, "or");
            }
            if ($temp != "")
                $temp = " and (1=2 $temp) ";
            return $temp;
        }
    }

    function getsearsqlone($type, $keyword, $model, $andstr) {
        global $cfg, $db;
        if ($keyword == "")
            return;
        if ($type == "")
            return;
        if ($type == "content" && ($model == "article" || $model == "product")) {
            $thesql = "select lanid from #@__" . $model . "s where content like '%" . $keyword . "%' and lanstr='" . $cfg["lanstr"] . "'";
            $db->dosql($thesql);
            while ($rs = $db->GetArray()) {
                $lanidstr.=$rs["lanid"] . ",";
            }
            $lanidstr = trim($lanidstr, ",");
            if ($lanidstr != "") {
                return " $andstr lanid in ($lanidstr) ";
            } else {
                return " $andstr 1=2 ";
            }
        } else {
            return " $andstr $type like '%" . $keyword . "%' ";
        }
    }

    function getFileDownImg($path, $file, $t="") {
        if ($file != "") {
            $arr = explode(".", $file);
            $src = adminpath . "file/filepic/" . $arr[count($arr) - 1] . ".gif";
            $srcerr = adminpath . "file/filepic/unknown.gif";
            $filesrc = webpath . "images/" . $path . "/" . $file;
            if (isset($arr[1])) {
                if ($t == "") {
                    return "<a href='$filesrc' target='_blank' title='" . lang('down') . "'><img src='$src' align='absmiddle' onerror=this.src='$srcerr' /></a>";
                } else {
                    return lang("filedown") . "：<a href='$filesrc' target='_blank' title='$file '><img src='$src' align='absmiddle' onerror=this.src='$srcerr' />" . lang("down") . "</a>";
                }
            }
        }
        return "";
    }

    function getSwf($src, $width, $height) {
        $temp = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0\" width=\"$width\" height=\"$height\">\r\n";
        $temp.="<param name=\"movie\" value=\"$src\" />\r\n";
        $temp.="<param name=\"quality\" value=\"high\" />\r\n";
        $temp.="<param name=\"wmode\" value=\"transparent\" />\r\n";
        $temp.="<embed wmode=\"transparent\" src=\"$src\" width=\"$width\" height=\"$height\" quality=\"high\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\"></embed>\r\n";
        $temp.="</object>";
        return $temp;
    }

    function gets($valuestr) {
        global $cfg, $db;
        $id = $this->getCan($valuestr, "id");
        $dbname = $this->getCan($valuestr, "dbname");
        $type = $this->getCan($valuestr, "type");
        $thesql = "select $id from #@__$dbname where $type limit 0,1";
        $rs = $db->dosql($thesql, "gets" . $id . $dbname . $type);
        return $rs[$id];
    }

    function form($valuestr) {
        $model = $this->getCan($valuestr, "model");
        $tag = $this->getCan($valuestr, "tag");
        $id = $this->getCan($valuestr, "id");
        $lanstr = $this->getCan($valuestr, "lanstr");

        loadformXml($model, $id, $lanstr);
        if ($tag != "")
            return form($tag, $id, $lanstr);
    }

    /**
     * 
     * The record command button
     * @param unknown_type $valuestr
     */
    function commendlist($valuestr) {

        global $cfg;
        if ($cfg["commend_io"] == "false")
            return "";
        $model = $this->getCan($valuestr, "model");
        if ($cfg["commend_" . $model . "_key"] != "") {
            $temp = "";
            $key = explode("|", $cfg["commend_" . $model . "_key"]);
            $name = explode("|", $cfg["commend_" . $model . "_name" . $cfg["lanstr"]]);

            $num = count($key);
            for ($i = 0; $i < $num; $i++) {
                if (instr($cfg["commend"], $key[$i])) {
                    $v = "<span onclick=\"commenddo(this,'" . $name[$i] . "','" . $key[$i] . "'," . $cfg["lanid"] . ");\"><img src='" . skinspath . "on.gif' class='commendimg' title='点亮此灯，将产品推荐至首页位置，或其它推荐位置'/></span>";
                } else {
                    $v = "<span onclick=\"commenddo(this,'" . $name[$i] . "','" . $key[$i] . "'," . $cfg["lanid"] . ");\"><img src='" . skinspath . "/off.gif' class='commendimg' title='点亮此灯，将产品推荐至首页位置，或其它推荐位置' /></span>";
                }
                $temp.="$v";
            }
            return "<td>$temp</td>";
        }
    }
	
 	function toggle($table) {
        global $cfg, $db;
        doMydb(0);
        $field = $_GET["field"];
        $val = intval($_GET["val"]);
        
        $lanid = intval($_GET['lanid']);
      
        if($lanid>0){
        	$db->execute("update #@__$table set $field='" . $val . "' where lanid=" . $lanid);
        	//echo "update #@__$table set $field='" . $val . "' where lanid=" . $lanid;
        }      
        doMydb(1);
        die("<img src='".adminpath."skins/".($val ? '1' : '0').".gif' class='commendimg' />");
    }
    
    function commenddo($model, $lanid) {
        global $cfg, $db;
        doMydb(0);
        $name = $_GET["name"];
        $key = $_GET["key"];
        $thesql = "select commend from #@__$model where commend<>'' and lanid=" . $lanid;
        $commend = $db->getValue($thesql, "commend");
        if (instr($commend, $key)) {
            $commend = str_replace($key, "", $commend);
            $db->execute("update #@__$model set commend='" . $commend . "' where lanid=" . $lanid);
            $temp = "<img src='".adminpath."skins/off.gif' class='commendimg' />";
        } else {
            $commend = $commend . $key;
            $db->execute("update #@__$model set commend='" . $commend . "' where lanid=" . $lanid);
            $temp = "<img src='".adminpath."skins/on.gif' class='commendimg' />";
        }
        doMydb(1);
        die($temp);
    }

    function commendtitle($loopstr, $valuestr) {

        global $cfg;

        if ($cfg["commend_io"] == "false")
            return "";
        $model = $this->getCan($valuestr, "model");
        if ($cfg["commend_" . $model . "_key"] != "") {
            $name = explode("|", $cfg["commend_" . $model . "_name" . $cfg["lanstr"]]);
            $num = count($name);
            for ($i = 0; $i < $num; $i++) {
                $temp.=" " . $name[$i] . " ";
            }
        }
        $cfg["commendtitle"] = $temp;
        return $this->reLabel2($loopstr);
    }

    function seo_tag($tag, $content) {
        if ($tag == "") {
            return $this->seo_tag_get($content);
        } else {
            $tag = $this->seo_tag_save($tag);
            return $tag;
        }
    }

    function seo_tag_get($content) {
        global $db;
        $temp = "";
        $thesql = "select tag from #@__tag";
        $db->dosql($thesql, "gettag");
        while ($rs = $db->GetArray("gettag")) {
            if (instr($content, $rs["tag"])) {
                $temp.=$rs["tag"] . ",";
            }
        }
        return trim($temp, ",");
    }

    function seo_tag_save($tag) {
        global $cfg, $db;
        doMydb(0);
        if (!$cfg["tag_autosave"])
            return;
        $tag = str_replace("，", ",", $tag);
        $arr = explode(",", $tag);
        $num = count($arr);
        for ($i = 0; $i < $num; $i++) {
            $thesql = "select tag from #@__tag where tag='{$arr[$i]}'";
            if (!$db->num($thesql)) {
                $db->execute("insert into #@__tag (tag,click,nums) values ('$arr[$i]',0,0)");
            }
        }
        doMydb(1);
        return $tag;
    }
	
    function addTagLink($content, $tag, $model, $id) {
        global $cfg;
        if ($cfg["tag_link_io"] && $tag != "") {
            $arr = explode(",", $tag);
            $url = $cfg["tag_link_to" . $cfg["lanstr"]];
            $num = count($arr);
            for ($i = 0; $i < $num; $i++) {
                $tagi = urlencode($arr[$i]);
                $tags = $arr[$i];
                if ($url != "") {
                    $href = str_replace("{tag}", $tagi, $url);
                } else {
                    $href = $this->getTagHref($tags, $model, $id);
                }
                if ($href == "")
                    $href = $cfg["weburl" . $cfg["lanstr"]];
                $content = str_replace($tags, "<a href=\"$href\" target=\"_blank\" class=\"mytag\" onclick=\"tagClick('{$tagi}')\">$tags</a>", $content);
            }
            return $content;
        }else {
            return $content;
        }
    }

    function getTagHref($tag, $model, $id) {
        global $db, $cfg;
        switch ($model) {
            case "onepage":
                $thesql = "select topclassid,lanid,paths from #@__class where model='onepage' and lanstr='{$cfg["lanstr"]}' and tag like '%{$tag}%' and lanid<>$id";
                $rs = $db->GetOne($thesql);
                if (!$rs) {
                    $url = "";
                    break;
                }
                if ($cfg["web_tohtml"]) {
                    $url = sitepath . $rs["paths"];
                } else {
                    $url = "/web/index.php?topclassid={$rs["topclassid"]}&classid={$rs["lanid"]}&lanstr={$cfg["lanstr"]}";
                }
                break;
            default:
                $thesql = "select * from #@__$model where tag like '%{$tag}%' and lanstr='{$cfg["lanstr"]}' and lanid<>$id and del=0 and locked=0";
                $rs = $db->GetOne($thesql);
                if (!$rs) {
                    $url = "";
                    break;
                }
                if ($cfg["web_tohtml"]) {
                    $url = sitepath . $rs["paths"] . $rs["filename"];
                } else {
                    $topclassid = gettopclassid($rs["classidstr"]);
                    $url = "/web/index.php?topclassid={$topclassid}&classid={$rs["classid"]}&id={$rs["lanid"]}";
                }
        }
        if ($url != "") {
            return $url;
        } else {
            return $cfg["weburl" . $cfg["lanstr"]];
        }
    }

    function numTopic($num=0, $n="") {
        global $cfg;
        $n = $n == "" ? $cfg["count_style"] : $n;
        if ($n == 0)
            return $num;
        $len = strlen($num);
        $temp = "";
        for ($i = 0; $i < $len; $i++) {
            $temp.="<img src='" . webpath . "count/pic/$n/" . substr($num, $i, 1) . ".gif' align='absmiddle' />";
        }
        return $temp;
    }

    function temp($valuestr) {
        global $cfg;
        if (instr($valuestr, "\"")) {
            $name = $this->getCan($valuestr, "id");
            $lanstr = $this->getCan($valuestr, "lanstr");
        } else {
            $name = $valuestr;
        }
        if ($lanstr == "" && $cfg["lanstr"] != deflan)
            $lanstr = $cfg["lanstr"];
        $file = temppath . $name . $lanstr . ".inc";
        if (is_file($file))
            return file_get_contents($file);
        return "";
    }
	function allnums(){
		global $cfg;
		//exit($cfg["allnums"]);
		return $cfg["allnums"];
	}
	function pagesize(){
		global $cfg;
		return $cfg["pagesize"];
	}
	function page_current(){
		global $cfg;
		return $cfg["page"];
	}
}

?>