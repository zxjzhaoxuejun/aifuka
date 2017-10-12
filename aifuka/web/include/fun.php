<?php



/**

 * 系统公共函数库

 *

 * @version        $Id: web.config.php 1 10:33 2010年7月6日Z $

 * @package        10000CMS.Libraries

 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.

 * @license        http://www.www.tiandixin.net

 * @link           http://www.www.tiandixin.net

 */



/**

 *

 * 获取 URL 参数变量

 * @param unknown_type $url

 * @param unknown_type $name

 */

function getcan($url, $name) {

    preg_match("/[\?&]$name=([^&]+)/", $url, $match);

    if ($match[1]) {

        return urldecode($match[1]);

    }

    return "";

}





/**

 *

 * 将字符串格式日期解析为 时间 戳 

 * 格式要求为 "Y-m-d H:i:s";

 * @param $date

 */

function redate($date) {

    $arr = explode(" ", $date);

    foreach ($arr as $key => $value) {

        if ($value != "")

            $arr2[] = $value;

    }

    $arr = explode("-", $arr2[0]);

    $Y = formatnum($arr[0], 0);

    $m = formatnum($arr[1], 0);

    $d = formatnum($arr[2], 0);

    $arr = explode(":", $arr2[1]);

    $H = formatnum($arr[0], 0);

    $i = formatnum($arr[1], 0);

    $s = formatnum($arr[2], 0);

    return mktime($H, $i, $s, $m, $d, $Y);

}

function mydir($dir){

	$dh  = opendir($dir);

	while (false !== ($filename = readdir($dh))) {

		$files[] = $filename;

	}

	return  $files;

}





/**

 *

 * 检测从客户端转来的数据 并进行转义。

 */

function checkValue() {

    foreach ($_GET as $get_key => $get_var) {

        if (is_numeric($get_var)) {

            $_GET[$get_key] = get_int($get_var);

        } else {

            $_GET[$get_key] = get_str($get_var);

        }

    }

    foreach ($_POST as $post_key => $post_var) {

        if (is_numeric($post_var)) {

            $_POST[$post_key] = get_int($post_var);

        } else {

            $_POST[$post_key] = get_str($post_var);

        }

    }

}



/**

 *

 * 将数字格式化成整数

 * @param $number

 */

function get_int($number) {

    return intval($number);

}



/**

 *

 * 转义 字符串

 * @param $string

 */

function get_str($string) {

    if (PHP_VERSION >= 6 ||!get_magic_quotes_gpc()) {

        return addslashes($string);

    }

    return $string;

}



/**

 *

 * 当前位置导航

 * @param $arr

 * @param $color

 */

function p($arr, $color="blue") {

    if (isset($arr))

        foreach ($arr as $key => $value) {

            echo $key . "=><font color=$color>$value</font><br />";

        }

}



/**

 *

 * 对象自动导入函数

 * @param $_classname

 */

function __autoload($_classname) {

    $file = incpath . $_classname . ".class.php";

    if (file_exists($file)) {

        require_once($file);

    } else {

        die("Class 『" . $_classname . "』 creat failed！");

    }

}







/**

 *

 * 程序执行时间

 */

function execTime() {

    $time = explode(" ", microtime());

    $usec = (double) $time[0];

    $sec = (double) $time[1];

    return $sec + $usec;

}



/**

 *

 * 获取文章的名称，不包括扩展名。

 * @param $file

 */

function getFileName($file) {

    $file = basename($file);

    return preg_replace('/\.[a-zA-Z]{3,4}$/m', "", $file);

    

}



/**

 *

 * 网站路径变量查看

 */

function lookcs() {

    global $cfg;

    $temp = "<style>.lookcs{ color:red;}.lookcs b{ color:#000;display:inline-block;width:300px;text-align:right;margin-right:5px;}</style>";

    $temp.="<div class='lookcs'>";



    $temp.="<hr /><b>wwwroot=></b>" . wwwroot;

    $temp.="<br /><b>\$cfg[\"wwwroot\"]=></b>" . $cfg["wwwroot"];



    $temp.="<hr /><b>siteroot=></b>" . siteroot;

    $temp.="<br /><b>\$cfg[\"siteroot\"]=></b>" . $cfg["siteroot"];



    $temp.="<hr /><b>webroot=></b>" . webroot;

    $temp.="<br /><b>\$cfg[\"webroot\"]=></b>" . $cfg["webroot"];



    $temp.="<hr /><b>sitepath=></b>" . sitepath;

    $temp.="<br /><b>\$cfg[\"sitepath\"]=></b>" . $cfg["sitepath"];



    $temp.="<hr /><b>webpath=></b>" . webpath;

    $temp.="<br /><b>\$cfg[\"webpath\"]=></b>" . $cfg["webpath"];



    $temp.="<hr /><b>htmlPath()=></b>" . htmlPath();

    $temp.="<hr /><b>htmlPath('en')=></b>" . htmlPath("en");



    $temp.="<hr /></div>";



    $temp.="变量说明：<br />root 标示为物理地址，有主机物理地址wwwroot；站点物理地址siteroot；网站程序物理地址webroot。<br />path 标示为相对路径，有站点路径sitepath和核心程序路径webpath。";

    $temp.="<br />htmlPath()函数是取生存文件到根目录的语言文件路径。这样在加文件链接地址的时候，只需要使用这一个值加上栏目路径即可。<br />在站点目录的CSS、JS、FLASH或图片等地址，在做模版的时候需要在地址路径前加{\$sitepath /}";

    return $temp;

}



/**

 *

 * Enter description here ...

 * @param $model

 * @param $value

 */

function getLockedstr($model, $value=" and locked=0 ") {

    global $cfg;

    $iflocked = instr($cfg["model_havelocked"], $model);

    return $iflocked ? $value : "";

}



/**

 *

 * HTML 字符串转义

 * @param $str

 */

function tohtmlstr($str) {

    return htmlspecialchars($str, ENT_QUOTES);

}



/**

 *

 * Enter description here ...

 * @param $id

 * @param $dbname

 * @param $str

 * @param $def

 */

function gets($id, $dbname, $str, $def="") {

    global $db;

    $thesql = "select $id from #@__$dbname where $str";

    $sec = @$db->getValue($thesql, $id);

    if ($sec == "")

        $sec = $def;

    return $sec;

}



/**

 *

 * 分页解析 

 * @param $page

 */

function formatpage($page) {

    if (!is_numeric($page))

        $page = 1;

    if ($page < 1)

        $page = 1;

    return $page;

}



/**

 *

 * Enter description here ...

 * @param $url1

 * @param $url2

 */

function geturl($url1, $url2) {

    global $cfg;

    if ($cfg["web_tohtml"] || $cfg["web_model"] == "html") {

        return $url2;

    } else {

        return $url1;

    }

}



/**

 *

 * Enter description here ...

 * @param $name

 * @param $value

 * @param $def

 */

function G($name, $value="", $def="") {

    if ($value != "") {

        if (is_numeric($def)) {

            $value = formatnum($value, $def);

        }

        setcookies($name, $value);

    } else {

        $value = getcookies($name);

        if (is_numeric($def)) {

            $value = formatnum($value, $def);

        }

    }

    if ($value == "" && $def != "")

        $value = $def;

    return $value;

}



/**

 *

 * Enter description here ...

 * @param $arr

 */

function phpTostr($arr) {

    return serialize($arr);

}



/**

 *

 * Enter description here ...

 * @param $content

 */

function strTophp($content) {

    return unserialize($content);

}



/**

 *

 * Enter description here ...

 * @param $length

 * @param $str

 */

function randstr($length, $str="") {

    $pattern = $str === "" ? "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ" : $str;

    $len = $str === "" ? 61 : (strlen($str) - 1);

    for ($i = 0; $i < $length; $i++) {

        $key .= $pattern[mt_rand(0, $len)];

    }

    return $key;

}



/**

 *

 * Enter description here ...

 * @param $path

 */

function notSameName($path) {

    $path_parts = pathinfo($path);

    $ext = $path_parts['extension'];

    return str_replace("." . $ext, "_" . randstr(5) . "." . $ext, $path);

}



/**

 *

 * Enter description here ...

 * @param $file

 * @param $str

 */

function ifthefile($file, $str) {

    $file = strtoupper($file);

    $str = strtoupper($str);

    $arr = split(",", $str);

    $num = count($arr);

    for ($i = 0; $i < $num; $i++) {

        if (instr($file, "." . $arr[$i])) {

            return true;

        }

    }

    return false;

}



/**

 *

 * Enter description here ...

 * @param $file

 */

function delfile($file) {

    if ($file == wwwroot || $file == siteroot || $file == siteroot) {

        return;

    }

    if (is_dir($file)) {

        return deldir($file);

    } else if (file_exists($file)) {

        return unlink($file);

    }

    return false;

}



/**

 *

 * Enter description here ...

 * @param $dir

 */

function deldir($dir) {

    if ($handle = opendir($dir)) {

        while (false !== ($file = readdir($handle))) {

            if ($file != '.' && $file !== '..') {

                $fullpath = $dir . "/" . $file;

                if (!is_dir($fullpath)) {

                    unlink($fullpath);

                } else {

                    deldir($fullpath);

                }

            }

        }

        closedir($handle);

    }

    if (rmdir($dir)) {

        return true;

    } else {

        return false;

    }

}



/**

 *

 * Enter description here ...

 * @param $path

 */

function getThumbs($path) {

    $name = basename($path);

    $name2 = "Thumbs." . $name;

    return str_replace($name, $name2, $path);

}



/**

 *

 * Enter description here ...

 * @param $name

 */

function formatname($name) {

    $name = str_replace("%", "", $name);

    $name = str_replace(" ", "", $name);

    $name = urlencode($name);

    $name = str_replace("%", "-", $name);

    return $name;

}



/**

 *

 * Enter description here ...

 * @param $name

 */

function unformatname($name) {

    $name = str_replace("-", "%", $name);

    $name = urldecode($name);

    return $name;

}



/**

 *

 * Enter description here ...

 * @param $arr

 */

function rw($arr) {

    echo "<pre>";

    print_r($arr);

    echo "</pre>";

}



/**

 *

 * Enter description here ...

 * @param $ip

 */

function ip2num($ip) {

    return bindec(decbin(ip2long($ip)));

}



/**

 *

 * Enter description here ...

 * @param $num

 */

function num2ip($num) {

    return long2ip($num);

}



/**

 *

 * Enter description here ...

 * @param $content

 */

function delcrlf($content) {

    $content = str_replace("\r\n", "", $content);

    $content = str_replace("\n", "", $content);

    return $content;

}



/**

 *

 * Enter description here ...

 * @param $str

 */

function losehtml($str) {

    $pattern = array(

        "'<script[^>]*?>.*?</script>'si",

        "'<style[^>]*?>.*?</style>'si",

        "'<[/!]*?[^<>]*?>'si",

        "'<!--[/!]*?[^<>]*?>'si",

        "'([rn])[s]+'",

        "'&(quot|#34);'i",

        "'&(amp|#38);'i",

        "'&(lt|#60);'i",

        "'&(gt|#62);'i",

        "'&(nbsp|#160);'i",

        "'&(iexcl|#161);'i",

        "'&(cent|#162);'i",

        "'&(pound|#163);'i",

        "'&(copy|#169);'i",

        "'&#(d+);'e",

        "'\\r\\n'",

        "'\\n'"

    );

    $replace = array("", "", "", "", "", "\"", "&", "<", ">", "", chr(161), chr(162), chr(163), chr(169), "chr(1)", "", "");

    return preg_replace($pattern, $replace, $str);

}



/**

 *

 * Enter description here ...

 * @param $ip

 * @param $ips

 */

function checkIp($ip, $ips='') {

    global $cfg;

    if ($ips == "")

        return true;

    $ip = ip2num($ip);

    $iparr = explode(",", $ips);

    $num = count($iparr);

    for ($i = 0; $i < $num; $i++) {

        $iparr2 = explode("-", $iparr[$i]);

        $iparr2[1] = isset($iparr2[1]) ? $iparr2[1] : $iparr2[0];

        if ($ip <= $iparr2[1] && $ip >= $iparr2[0])

            return true;

    }

    return false;

}



/**

 *

 * 网络测试

 * @param $ip

 * @param $str

 */

function lookip($ip, $str="") {

    if ($str == "")

        $str = $ip;

    return "<a href=\"http://www.linkwan.com/gb/broadmeter/VisitorInfo/QureyIP.asp?QureyIP=$ip\" target=\"_blank\" title=\"$ip\">$str</a>";

}



/**

 *

 * 用户名过滤

 * @param $name

 * @param $ck

 */

function safename($name, $ck=false) {

    $username = ereg_replace("[^0-9a-zA-Z_@!\.-]{2,30}", '', $name);

    if (!$ck) {

        return $username;

    } else {

        return $username == $name;

    }

}



/**

 *

 * 过滤用户中的非法字符。

 * @param $name

 * @param $ck

 */

function safenames($name, $ck=false) {

    $delstr = "~!#$%^&*()+|\\=-{}[];:\"'<>?/,";

    $username = $name;

    $num = strlen($delstr);

    for ($i = 0; $i < $num; $i++) {

        $username = str_replace(substr($delstr, $i, 1), "", $username);

    }

    if (!$ck) {

        return $username;

    } else {

        return $username == $name;

    }

}



/**

 *

 * Enter description here ...

 * @param $userid

 * @param $self

 */

function alladminuserids($userid, $self=true) {

    $funstr = getallids($userid, "admin");

    if ($self) {

        $funstr = $funstr . $userid;

    } else {

        $funstr = $funstr . "-1";

    }

    return $funstr;

}



/**

 *

 * Enter description here ...

 * @param $classid

 * @param $self

 */

function allclassids($classid, $self=true) {

    $funstr = getallids($classid, "class");

    if ($self && $classid != 0) {

        $funstr.=$classid;

    }

    $funstr = trim($funstr, ",");

    if ($funstr == "")

        $funstr = 0;



    return $funstr;

}



/**

 *

 * Enter description here ...

 * @param $id

 * @param $data

 */

function getallids($id=0, $data="admin") {

    global $db, $cfg;

    $conn = $db->linkID;

    if ($data != "admin") {

        $cfg["thelanstr"] = " and lanstr='" . lanstr . "'";

        $theid = "lanid";

    } else {

        $cfg["thelanstr"] = "";

        $theid = "id";

    }

    if ($id == 0) {

        $thesql = $db->SetQuery("select $theid,fid from #@__" . $data . " where 1=1 " . $cfg["thelanstr"]);

        $result = mydb_query($thesql, $conn);

        while ($rs = mydb_fetch_array($result)) {

            $funstr.=$rs[$theid] . ",";

        }

    } else {

        $thesql = $db->SetQuery("select id,fid from #@__$data where fid=" . $id . $cfg["thelanstr"]);

        $result = mydb_query($thesql, $conn);

        while ($rs = mydb_fetch_array($result)) {

            $funstr.=$rs["id"] . ",";

            $cfg["datesheet"] = $data;

            $funstr.=getallids2($rs["id"], ",");

        }

    }

    return $funstr;

}



/**

 *

 * Enter description here ...

 * @param $fid

 * @param $str

 */

function getallids2($fid, $str) {

    global $db, $cfg;

    $conn = $db->linkID;

    $thesql2 = $db->SetQuery("select id,fid from #@__" . $cfg["datesheet"] . " where id<>$fid and fid=" . $fid . $cfg["thelanstr"]);

    $result2 = mydb_query($thesql2, $conn);

    while ($rs2 = mydb_fetch_array($result2)) {

        $funstr.=$rs2["id"] . $str;

        $funstr.=getallids2($rs2["id"], $str);

    }

    return $funstr;

}



/**

 *

 * Enter description here ...

 * @param $classidstr

 */

function getTopclassid($classidstr) {

    $arr = explode(",", $classidstr);

    return $arr[0];

}



/**

 *

 * Enter description here ...

 * @param $dbname

 * @param $fid

 * @param $str

 */

function getFidstr($dbname, $fid, $str=",") {

    return trim(getFidstr2($dbname, $fid, $str), $str);

}



/**

 *

 * Enter description here ...

 * @param $dbname

 * @param $fid

 * @param $str

 */

function getFidstr2($dbname, $fid, $str=",") {

    global $db, $cfg;

    $conn = $db->linkID;

    $thesql = $db->SetQuery("select id,fid from #@__$dbname where id=" . $fid);

    $result = mydb_query($thesql, $conn);

    while ($rs = mydb_fetch_array($result)) {

        $funstr = $str . $rs["id"] . $funstr;

        $funstr = getFidstr2($dbname, $rs["fid"], $str) . $funstr;

    }

    return $funstr;

}



/**

 *

 * 管理员权限检测

 * @param unknown_type $adminstr

 * @param unknown_type $str

 */

function inadminstr($adminstr, $str) {

    if ($adminstr == "all")

        return true;

    return instr("," . $adminstr . ",", "," . $str . ",");

}



/**

 *

 * 包含语言版本配置文件。

 * @param $str

 */

function lang($str) {

    global $cfg;

    require_once(incpath . "language_" . $cfg["lanstr"] . ".php");

    return $cfg["text_" . $str];

}



/**

 *

 * 将 XML文件解析 为 XML对象。

 * @param $model

 * @param $prefix

 * @param $id

 * @param $lanstr

 */

function loadlanXml($model, $prefix="", $id="", $lanstr="") {

    global $cfg;

    $cfg["thefunname" . $id] = $prefix;

    if ($lanstr === "")

        $lanstr = $cfg["lanstr"];

    $doc = "lanxmldoc" . $id . $lanstr;

    if (!is_object($cfg[$doc])) {

        $lanxmldoc = simplexml_load_file(lanxmlpath . $model . "/" . $lanstr . ".xml");



        $cfg[$doc] = $lanxmldoc;

    }

}



/**

 *

 * The language xml file

 * @param $tag

 * @param $id

 * @param $lanstr

 */

function t($tag, $id="", $lanstr="") {

    global $cfg;

    if ($lanstr == "")

        $lanstr = $cfg["lanstr"];

    $doc = "lanxmldoc" . $id . $lanstr;

    $lanxmldoc = $cfg[$doc];

    return $lanxmldoc->{$cfg["thefunname" . $id] . $tag};

}



/**

 *

 * 从XML文件解析语言配置信息

 * @param $model

 * @param $id

 * @param $lanstr

 */

function loadformXml($model, $id="", $lanstr="") {



    global $cfg;

    if ($lanstr == "")

        $lanstr = $cfg["lanstr"];

    $doc = "formxmldoc" . $id . $lanstr;

    if (!is_object($cfg[$doc])) {

        $formxmldoc = simplexml_load_file(formxmlpath . $model . "/" . $lanstr . ".xml");

        $cfg[$doc] = $formxmldoc;

    }

}



/**

 *

 * Enter description here ...

 * @param $path

 * @param $id

 * @param $lanstr

 */

function form($path, $id="", $lanstr="") {

    global $cfg;

    if ($lanstr == "")

        $lanstr = $cfg["lanstr"];

    $doc = "formxmldoc" . $id . $lanstr;

    $formxmldoc = $cfg[$doc];

    if (instr($path, "/")) {

        $arr = explode("/", $path);

        $temp = $formxmldoc->$arr[0]->$arr[1];

    } else {

        $temp = $formxmldoc->$path;

    }

    return $temp;

}



/**

 *

 * Enter description here ...

 */

function getmintime() {

    list($usec, $sec) = explode(" ", microtime());

    return ((float) $usec + (float) $sec);

}



/**

 *

 * 将文件内容写入地址。

 * @param $path

 * @param $content

 */

function writetofile($path, $content) {

    mkdirs(dirname($path));

    file_put_contents($path, $content);

}



/**

 *

 * 多级目录创建

 * @param $dir

 * @param $mode

 */

function mkdirs($dir, $mode=0777) {

    if (is_dir($dir) || @mkdir($dir, $mode))

        return true;

    if (!mkdirs(dirname($dir), $mode))

        return false;

    return @mkdir($dir, $mode);

}



/**

 *

 * 判断是否是数字，否则输出第后个默认值

 * @param $num

 * @param $err

 */

function formatnum($num, $err) {

    if (is_numeric($num)) {

        return $num;

    } else {

        return $err;

    }

}



/**

 *

 * Enter description here ...

 */

function GetIP() {

    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {

        $cip = $_SERVER["HTTP_CLIENT_IP"];

    } else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {

        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];

    } else if (!empty($_SERVER["REMOTE_ADDR"])) {

        $cip = $_SERVER["REMOTE_ADDR"];

    } else {

        $cip = '';

    }

    preg_match("/[\d\.]{7,15}/", $cip, $cips);

    $cip = isset($cips[0]) ? $cips[0] : '0.0.0.0';

    unset($cips);

    return $cip;

}



/**

 *

 * Enter description here ...

 * @param $name

 * @param $value

 */

function setsession($name, $value) {

    global $cfg;

    $names = $cfg["prefix"] . $name;

    $_SESSION[$names] = $value;

}



/**

 *

 * Enter description here ...

 * @param $name

 */

function getsession($name) {

    global $cfg;



    return $_SESSION[$cfg["prefix"] . $name] . "";

}



/**

 *

 * Enter description here ...

 * @param $name

 */

function issession($name) {

    global $cfg;

    return (isset($_SESSION[$cfg["prefix"] . $name]) && $_SESSION[$cfg["prefix"] . $name] != "");

}



/**

 *

 * Enter description here ...

 * @param $name

 */

function delsession($name) {

    global $cfg;

    unset($_SESSION[$cfg["prefix"] . $name]);

}



/**

 *

 * Enter description here ...

 * @param $name

 * @param $value

 * @param $time

 */

function setcookies($name, $value, $time="") {

    global $cfg;

    $time = formatnum($time, $_SERVER["REQUEST_TIME"] + 2592000);

    setcookie($cfg["prefix"] . $name, $value, $time, "/");

}



/**

 *

 * Enter description here ...

 * @param $name

 */

function getcookies($name) {

    global $cfg;

    return str_replace("\\'", "'", $_COOKIE[$cfg["prefix"] . $name]);

}



/**

 *

 * Enter description here ...

 * @param $name

 */

function iscookies($name) {

    global $cfg;

    return (isset($_COOKIE[$cfg["prefix"] . $name]) && $_COOKIE[$cfg["prefix"] . $name] != "");

}



/**

 *

 * Enter description here ...

 * @param $name

 */

function delcookies($name) {

    global $cfg;

    setcookie($cfg["prefix"] . $name, "", time() - 1, "/");

}



/**

 *

 * Enter description here ...

 * @param unknown_type $mes

 * @param unknown_type $url

 * @param unknown_type $s

 */

function sucgotos($mes="", $url="", $s=0) {

    global $cfg;

    $cfg["mes"] = $mes;

    $cfg["url"] = $url;

    $cfg["s"] = $s == 0 ? $cfg["errgoto_times"] : $s;

    $alzCms = new alzCms();

    require_once(incpath . "language_" . $cfg["lanstr"] . ".php");

    die($alzCms->reLabel("admin/admin_gotos.html"));

}



/**

 *

 * Enter description here ...

 * @param $mes

 * @param $url

 * @param $s

 */

function sucgoto($mes='', $url=0, $s='') {

    echo "<script type='text/javascript'>sucgoto('$mes','$url',$s);</script>";

}



/**

 *

 * Enter description here ...

 * @param $str

 * @param $len

 * @param $str2

 */

function left($str, $len, $str2='...') {

    $oldstr = $str;

    for ($i = 0; $i < $len; $i++) {

        $temp_str = substr($str, 0, 1);

        if (ord($temp_str) > 127) {

            $i++;

            if ($i < $len) {

                $new_str[] = substr($str, 0, 3);

                $str = substr($str, 3);

            }

        } else {

            $new_str[] = substr($str, 0, 1);

            $str = substr($str, 1);

        }

    }

    $str = @join($new_str);

    if ($oldstr != $str)

        $str.=$str2;

    return $str;

}



/**

 *

 * 判断是否存在于某个字符串内

 * @param $str1

 * @param $str2

 */

function instr($str1, $str2) {

    $sec = @strpos($str1, $str2);

    return ($sec || $sec === 0);

}



/**

 *

 * Enter description here ...

 * @param $urlstr

 */

function page_on($urlstr) {

    $lochref = "http://" . $_SERVER[HTTP_HOST] . $_SERVER[PHP_SELF];

    echo $lochref;

    return instr($lochref, $urlstr);

}



/**

 *

 * 文件大小 格式化

 * @param $decimals

 * @param $force_unit

 * @param $dec_char

 * @param $thousands_char

 */

function formatfilesize($number, $decimals=2, $force_unit=false, $dec_char='.', $thousands_char=',') {

    $units = array(' B', ' KB', ' MB', ' GB', ' TB');

    if ($force_unit === false)

        $unit = floor(log($number, 2) / 10);

    else

        $unit = $force_unit;

    if ($unit == 0)

        $decimals = 0;

    return @number_format($number / pow(1024, $unit), $decimals, $dec_char, $thousands_char) . $units[$unit];

}



/**

 *

 * Enter description here ...

 * @param $lanstr

 */

function getLanPath($lanstr) {

    global $cfg;

    for ($i = 0; $i < lannums; $i++) {

        if ($cfg["language"][$i] == $lanstr)

            return $cfg["languagepath"][$i];

    }

    return "html/";

}



/**

 *

 * Enter description here ...

 * @param $lanstr

 */

function htmlPath($lanstr="") {

    global $cfg;

    $lanstr = $lanstr == "" ? $cfg["lanstr"] : $lanstr;



    if ($lanstr == deflan) {

        $path = sitepath;

    } else {

        $path = sitepath . $lanstr . "/";

    }

    return $path;

}



/**

 *

 * Enter description here ...

 * @param $bigpic

 */

function getSmallpic($bigpic) {

    $name = basename($bigpic);

    if (instr($bigpic, "/")) {

        $temp = str_replace("/" . $name, "/small/" . $name, $bigpic);

    } else {

        $temp = "small/" . $name;

    }

    return $temp;

}



/**

 *

 * 获取中等图片的名称

 * @param $bigpic

 */

function getPicName($bigpic,$type='small') {

    $name = basename($bigpic);

    if (instr($bigpic, "/")) {

        $temp = str_replace("/" . $name, "/$type/" . $name, $bigpic);

    } else {

        $temp = "$type/" . $name;

    }

    return $temp;

}



/**

 *

 * 获取  POST 变量值

 * @param $id

 */

function post($id) {

    $temp = $_POST[$id];

    $temp = str_replace("[\$add]", "+", $temp);

	$temp = preg_replace('/<span style=\\\"([^"]*?)\\\" \/>/','',$temp);

	$temp = preg_replace('/<span style=\"([^"]*?)\" \/>/','',$temp);
	
	$temp = str_replace("'", "‘", $temp);

    return $temp;

}



/**

 *

 * 获取 GET 变量值。

 * @param $id

 */

function get($id) {

    return $_GET[$id];

}



/**

 *

 * Enter description here ...

 * @param $case

 */

function doMydb($case=0) {

    global $db, $cfg;

    if ($cfg["mydb_type"] != "Sqlite")

        return;

    switch ($case) {

        case 0:

            sqlite_query($db->linkID, 'BEGIN TRANSACTION');

            break;

        case 1:

            sqlite_query($db->linkID, 'COMMIT TRANSACTION');

            break;

        case -1:

            sqlite_query($db->linkID, 'ROLLBACK TRANSACTION');

            break;

    }

}



/**

 *

 * 从 属性 序列化文件中获取 分类的属性

 * @param $name

 */

function getcache($name) {

    $temp = strTophp(@file_get_contents(temppath . $name . ".inc"));

    if (!is_array($temp)) {

        $temp = new ArrayObject();

    }

    return $temp;

}



/**

 *

 * Enter description here ...

 * @param $name

 * @param $arr

 */

function setcache($name, $arr) {

    file_put_contents(temppath . $name . ".inc", phpTostr($arr));

}



/**

 *

 * 执行 DB 查询

 * @param $thesql

 * @param $conn

 */

function mydb_query($thesql, $conn) {

    global $cfg;

    if ($cfg["mydb_type"] == "Mysql") {

        return mysql_query($thesql, $conn);

    } else {

        return sqlite_query($conn, $thesql);

    }

}



/**

 *

 * Enter description here ...

 * @param $result

 */

function mydb_fetch_array($result) {

    global $cfg;

    if ($cfg["mydb_type"] == "Mysql") {

        return mysql_fetch_array($result);

    } else {

        return sqlite_fetch_array($result);

    }

}



/**

 *

 * 目录列表

 * @param $defdir

 * @return $temp Array 

 */

function listdir($defdir='.') {

    $temp = array();

    if (is_dir($defdir)) {

        $fh = opendir($defdir);

        while (($file = readdir($fh)) !== false) {

            if (strcmp($file, '.') == 0 || strcmp($file, '..') == 0 || $file == "Desktop.ini" || $file == "Thumbs.db")

                continue;

            $filepath = $defdir . '/' . $file;

            if (is_dir($filepath))

                $temp = array_merge($temp, listdir($filepath));

            else

                array_push($temp, $filepath);

        }

        closedir($fh);

    }else {

        $temp = false;

    }

    return $temp;

}



/**

 *

 * Enter description here ...

 * @param $ip

 */

function getAddress($ip) {

    global $cfg;

    $c = @file_get_contents(trim(str_replace("[ip]", $ip, $cfg["count_url"])));

    if (!$c)

        return "网络错误";

    $c = iconv($cfg["count_urlcharset"], "utf-8", $c);

    preg_match("/" . $cfg["count_urlre"] . "/", $c, $match);

    if ($match[1]) {

        return trim($match[1]);

    } else {

        return "未知";

    }

}



/**

 *

 * Enter description here ...

 * @param $val

 */

function return_bytes($val) {

    $val = trim($val);

    $last = strtolower($val{strlen($val) - 1});

    switch ($last) {

        case 'g':

            $val *= 1024;

        case 'm':

            $val *= 1024;

        case 'k':

            $val *= 1024;

    }



    return $val;

}



/**

 *

 * Enter description here ...

 * @param $file_path

 */

function file_mode_info($file_path) {

    /* 如果不存在，则不可读、不可写、不可改 */

    //    echo $file_path;

    if (!file_exists($file_path)) {

        return false;

    }



    $mark = 0;



    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {

        /* 测试文件 */

        $test_file = $file_path . '/cf_test.txt';



        /* 如果是目录 */

        if (is_dir($file_path)) {

            /* 检查目录是否可读 */

            $dir = @opendir($file_path);

            if ($dir === false) {

                return $mark; //如果目录打开失败，直接返回目录不可修改、不可写、不可读

            }

            if (@readdir($dir) !== false) {

                $mark ^= 1; //目录可读 001，目录不可读 000

            }

            @closedir($dir);



            /* 检查目录是否可写 */

            $fp = @fopen($test_file, 'wb');

            if ($fp === false) {

                return $mark; //如果目录中的文件创建失败，返回不可写。

            }

            if (@fwrite($fp, 'directory access testing.') !== false) {

                $mark ^= 2; //目录可写可读011，目录可写不可读 010

            }

            @fclose($fp);



            @unlink($test_file);



            /* 检查目录是否可修改 */

            $fp = @fopen($test_file, 'ab+');

            if ($fp === false) {

                return $mark;

            }

            if (@fwrite($fp, "modify test.\r\n") !== false) {

                $mark ^= 4;

            }

            @fclose($fp);



            /* 检查目录下是否有执行rename()函数的权限 */

            if (@rename($test_file, $test_file) !== false) {

                $mark ^= 8;

            }

            @unlink($test_file);

        }

        /* 如果是文件 */ elseif (is_file($file_path)) {

            /* 以读方式打开 */

            $fp = @fopen($file_path, 'rb');

            if ($fp) {

                $mark ^= 1; //可读 001

            }

            @fclose($fp);



            /* 试着修改文件 */

            $fp = @fopen($file_path, 'ab+');

            if ($fp && @fwrite($fp, '') !== false) {

                $mark ^= 6; //可修改可写可读 111，不可修改可写可读011...

            }

            @fclose($fp);



            /* 检查目录下是否有执行rename()函数的权限 */

            if (@rename($test_file, $test_file) !== false) {

                $mark ^= 8;

            }

        }

    } else {

        if (@is_readable($file_path)) {

            $mark ^= 1;

        }



        if (@is_writable($file_path)) {

            $mark ^= 14;

        }

    }



    return $mark;

}



?>