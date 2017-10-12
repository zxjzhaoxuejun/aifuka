<?php
include("../../include/inc.php");
include(incpath."funlist.php");
$alz=new alzCms();
$cfg["size"]=$_GET["size"];
$cfg["type"]=$_GET["type"];
$cfg["secid"]=$_GET["secid"];
$cfg["path"]=$_GET["path"];
$cfg["typesr"]="*.".str_replace(",",";*.",$cfg["type"]);

echo $alz->reLabel(TPL_ADMIN_DIR."file/uponefileform.html");
?>