<?php
/**
 *
 * Enter description here ...
 * @var unknown_type
 */
define("lannums",$cfg["lannums"]);
define("deflan",$cfg["deflan"]);//系统默认语言，通过配置改变
define("deflan2",$cfg["deflan2"]);//系统默认语言，通过配置改变
$cfg["lanstr"]=deflan;//当前使用语言，通过切换按纽改变,以后扩展
$cfg["lanstr2"]="";//如编辑删除等图标，默认显示后缀不加lanstr
define("lanstr",$cfg["lanstr"]);
define("lanstr2",$cfg["lanstr2"]);
date_default_timezone_set($cfg["web_timezone"]);
define("readfailed",$cfg["readfailed"]);
define("guid_str",$cfg["guid_str"]);
define("htmlIndex",$cfg["htmlIndex"]);
?>