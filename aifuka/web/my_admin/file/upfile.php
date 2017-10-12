<?php
include("../../include/inc.php");
include(incpath."admin_iplist.php");
include(incpath."web_config.php");
include(incpath."web_config_do.php");
include(incpath."application_class.php");
include(incpath."fun.php");
include(incpath."admin.class.php");

include(incpath."getLabel.class.php");
include(incpath."alzCms.class.php");
include(incpath."picset.class.php");


set_time_limit(0);
$path=$_GET["path"];
$path2=webroot.$path.formatname($_FILES["Filedata"]["name"]);
if($cfg["samename_type"]==0&&file_exists($path2)){$path2=notSameName($path2);}
move_uploaded_file($_FILES["Filedata"]["tmp_name"],$path2);

if(ifthefile($path2,$cfg["pic_ext_c"])){
	$picSet=new picSet($path2);
	//图片控制
	if($cfg["suobig_c"]){$picSet->bigc($cfg["suobig_w"],$cfg["suobig_h"]);}
	//缓存缩略图
	$picSet->suo(getThumbs($path2),100,100);
	//水印
	$fileup_shui_type=$cfg["shui_type"];
	$fileup_shui_pos=$cfg["shui_pos"];
	if($fileup_shui_type==1){
		$picSet->waterText($cfg["shui_text"],$fileup_shui_pos,$cfg["shui_posX"],$cfg["shui_posY"],$cfg["shui_color"],$cfg["shui_fontsize"]);
	}else if($fileup_shui_type==2){
		$picSet->waterImage($cfg["shui_pic"],$fileup_shui_pos,$cfg["shui_posX"],$cfg["shui_posY"]);
	}
	$sec=$picSet->save();
	$picSet=null;
}
?>