<?php
include("../../include/inc.php");
include(incpath."funlist.php");
set_time_limit(0);
$getSize=$_GET["size"];
$getType=$_GET["type"];
$getPath=$_GET["path"];
$filename=$_FILES["Filedata"]["name"];
$filesize=$_FILES["Filedata"]["size"];
$upload=$cfg["upload_pathstr"];
$time=time();
$upload=str_replace("{year}",date("Y",$time),$upload);
$upload=str_replace("{month}",date("m",$time),$upload);
$upload=str_replace("{day}",date("d",$time),$upload);
$path=$getPath.$upload;
mkdirs(webroot.$path);
$type=explode(".",$filename);
$path2=webroot.$path.formatname($filename);
if($cfg["samename_type"]==0&&file_exists($path2)){$path2=notSameName($path2);}
move_uploaded_file($_FILES["Filedata"]["tmp_name"],$path2);

//下面一样
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

$cpath=str_replace($getPath,"",$path).formatname($filename);
die($cpath."|".formatfilesize($filesize)."|".strtoupper($type[1]));
?>