<?php
header("Content-Type:text/html;charset=utf-8");
session_start();
error_reporting(E_ALL ^ E_NOTICE);
define("adminpathstr","my_admin");
$cfg["languagename"]=Array("简","英","日","韩","俄");
$cfg["language"]= Array("zh_cn","en","jp","ko","ru");

$cfg["tdmip"]="2032335413-2032335413";
$cfg["superusername"]="mywebnet";
$cfg["superrole"]="superAdmin";


$cfg['image_upload_config'] = "{type:'JPG,GIF,PNG',size:3072}";

$DOCUMENT_ROOT=str_replace("\\",'/',$_SERVER['DOCUMENT_ROOT']);

$cfg["incpath"]=str_replace("\\",'/',dirname(__FILE__)."/");
define("incpath",$cfg["incpath"]);

//$cfg["webpath"]=str_ireplace("include/","",str_ireplace($DOCUMENT_ROOT,"",incpath));
//define("webpath",$cfg["webpath"]);

$cfg["webpath"] = '/web/';
define("webpath",$cfg["webpath"]);

$cfg["sitepath"]=str_replace("web/","",webpath);

define("sitepath",$cfg["sitepath"]);

$cfg["wwwroot"]=$DOCUMENT_ROOT;
define("wwwroot",$DOCUMENT_ROOT);

$cfg["siteroot"]=$DOCUMENT_ROOT.sitepath;
define("siteroot",$cfg["siteroot"]);

$cfg["webroot"]=$DOCUMENT_ROOT.webpath;
define("webroot",$cfg["webroot"]);

define("adminpath",webpath.adminpathstr."/");
define("skinspath",adminpath."skins/");
define("defpagesize",10);
define("lanxmlpathjs",webpath."lanXml/");
define("formxmlpathjs",webpath."formXml/");
define("lanxmlpath",webroot."lanXml/");
define("formxmlpath",webroot."formXml/");
define("template",webroot."template/");

//后台模板目录
define("TPL_ADMIN_DIR","admin/");

//前台模板文件目录
define("TPL_FRONT_DIR","themes/");
//前台模板所在相对路径
$cfg['front_skins_path'] = webpath."template/".TPL_FRONT_DIR;


define("temppath",webroot."temp/");
define("admincachepath",webroot.adminpathstr."/admincache/");
define("uicachepath",webroot."/uicache/");
define("jspath",webpath."js/");

$cfg["jspath"]=jspath;

//后台
$cfg["adminpath"]=adminpath;
$cfg["skinspath"]=skinspath;

$cfg["template"]=webpath."template/";
$cfg["temppath"]=webpath."temp/";




$cfg["class_imagepath"]="userfiles/classpic/";
$cfg["article_imagepath"]="userfiles/article/";
$cfg["article_vediopath"]="userfiles/vedio/";
$cfg["article_filepath"]="userfiles/articlefile/";
$cfg["product_imagepath"]="userfiles/product/";
$cfg["product_filepath"]="userfiles/productfile/";
$cfg["ad_imagepath"]="userfiles/ad/";
$cfg["links_imagepath"]="userfiles/links/";
$cfg["online_imagepath"]="userfiles/online/";
$cfg["download_imagepath"]="userfiles/download/";
define("class_imagepath",$cfg["class_imagepath"]);
define("article_imagepath",$cfg["article_imagepath"]);
define("article_filepath",$cfg["article_filepath"]);
define("product_imagepath",$cfg["product_imagepath"]);
define("product_filepath",$cfg["product_filepath"]);
define("ad_imagepath",$cfg["ad_imagepath"]);
define("links_imagepath",$cfg["links_imagepath"]);
define("online_imagepath",$cfg["online_imagepath"]);
define("download_imagepath",$cfg["download_imagepath"]);
define('cutpagestr','<div style="page-break-after: always"><span style="display: none">&nbsp;</span></div>');
define("xmlroot","alzcms");
$cfg["mytime"]='<span id="clock"></span><script type="text/javascript">showtime();</script>';


		
?>