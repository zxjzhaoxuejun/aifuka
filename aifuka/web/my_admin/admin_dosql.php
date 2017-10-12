<?php
/**
 *  SQL语句执行程序文件
 *  
 */
include("../include/inc.php");
include(incpath."funlist.php");

class dosql extends alzCms
{
	
	function __construct(){
		global $admin;
		$admin->adminck("sys_dosql");
		$this->admincache();
	}
	
	function def(){
		echo $this->reLabel("admin/admin_dosql.html");
	}
	
	function dosqls()
	{
		global $db,$admin;
		$thesql=str_replace("~","'",$_GET["sql"]);
		doMydb(0);
		if(instr($thesql,"select"))
		{
			if(!instr($thesql,"limit")){
				$thesql.=" limit 0,100";
			}
			$db->dosql($thesql);
			while($rs=$db->GetArray())
			{
				foreach ($rs as $key=>$value)
				{
					$temp.="<b>".$key."=></b>".$value."<br> ";
				}
				$temp.='<hr />';
			}
			if($temp=="")$temp="<div class='err'>查无数据</div>";
			die($temp);
		}else{
			$sec=$db->getNums($thesql);
			if($sec>=0){
				die("<div class='ok'>“<strong>".$thesql."</strong>”执行成功，影响记录数：".$sec." ！</div>");
			}else{
				die("<div class='err'>“<strong>".$thesql."</strong>”执行错误，请认真检查！<hr />错误描述：".$db->err()."</div>");
			}
		}
		doMydb(1);
	}
    function __destruct(){$this->admincache2();}
}

$dosql=new dosql();
if($_GET["action"]=="do")$dosql->dosqls();
$dosql->def();
?>