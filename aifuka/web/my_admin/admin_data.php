<?php
/**
 * 数据库操作 程序 
 * 包括数据库备份，还原，SQL语句执行等。
 */
include("../include/inc.php");
include(incpath."funlist.php");
include(incpath."sql_dump.php");

class data extends alzCms
{
	function __destruct(){$this->admincache2();}
	function __construct(){
		global $admin;
		$admin->adminck("sys_data");
		$this->admincache();
	}
	/**
	 * 
	 * Enter description here ...
	 */
	function def()
	{
		global $cfg;
		$allow_max_size = return_bytes(@ini_get('upload_max_filesize')); // 单位为字节
		$allow_max_size = $allow_max_size / 1024; // 转换单位为 KB	
		$cfg["allow_max_size"] = $allow_max_size;
		$cfg["sql_name"] = cls_sql_dump::get_random_name() . '.sql';
		echo $this->reLabel("admin/admin_data.html");
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $loopstr
	 */
	function datalist($loopstr)
	{
		global $db,$cfg;
		$otherTables = Array();
		$dedeSysTables = Array();
		$channelTables = Array();
		$db->dosql("Show Tables","t");
		$mysql_version = $db->GetVersion();
		while($row = $db->GetArray('t',MYSQL_BOTH))
		{
			if(ereg("^{$cfg_dbprefix}",$row[0])||in_array($row[0],$channelTables))
			{
				$dedeSysTables[] = $row[0];
			}else{
				$otherTables[] = $row[0];
			}
		}

		foreach ($dedeSysTables as $key=>$value)
		{
			$cfg["tablename"]=$value;
			$cfg["nums"] = $db->GetValue("Select count(*) as n From $value","n");
			$cfg["id"] = $key;
			$temp.=$this->reLabel2($loopstr);
		}
		return $temp;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $tablename
	 */
	function opimize($tablename)
	{

		global $db,$cfg;
		if(empty($tablename))
		{
			die("{err}没有指定表名！");	
		}
		else
		{
			$db->execute("OPTIMIZE TABLE `$tablename` ");
			die("{ok}恭喜，执行优化表'$tablename'成功！");		
		}
	}
	function repair($tablename)
	{
		global $db,$cfg;
		if(empty($tablename))
		{
			die("{err}没有指定表名！");
		}
		else
		{
			$db->execute("REPAIR TABLE `$tablename` ");
			die("{ok}恭喜，执行修复表'$tablename'成功");
		}	
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $tablename
	 */
	function date_formation($tablename)
	{//数据表结构
		global $db,$cfg;
		if(empty($tablename))
		{
			echo "{err}没有指定表名！";
		}
		else
		{
			$thesql = "SHOW CREATE TABLE ".$tablename;
			$db->dosql($thesql);
			$row = $db->GetArray();
			$ctinfo = $row["Create Table"];
			$cfg["date_information"] = str_replace(",","<br>",trim($ctinfo));
				
		}		
		echo $this->reLabel("admin/admin_date_info.html");
	}	
	
	/**
	 * 
	 * Enter description here ...
	 */
	function dumpsql()
	{
		global $db;
		/* 检查目录权限 */
		$path = siteroot.'web/data/sqldata';
		
		$mask = file_mode_info($path);
		if ($mask === false)
		{
			die("{err}目录'$path'不存在,请手动创建");
		}
		elseif ($mask != 15)
		{
			$warning = sprintf("目录 %s 权限有以下问题：", $path);
			if (($mask&1) < 1)
			{
				$warning .= "不可读";
			}
			if (($mask & 2) < 1)
			{
				$warning .= "不可写";
			}
			if (($mask & 4) < 1)
			{
				$warning .= "追加数据";
			}
			if (($mask & 8) < 1)
			{
				$warning .= "不能修改文件";
			}
			die("{err}".$warning);
		}
	
		/* 设置最长执行时间为5分钟 */
		@set_time_limit(300);
	
		/* 初始化 */
		$dump = new cls_sql_dump($db);
		$run_log =siteroot.'web/data/sqldata/run.log';
	
		/* 初始化输入变量 */
		if (empty($_REQUEST['sql_file_name']))
		{
			$sql_file_name = $dump->get_random_name();
		}
		else
		{
			$sql_file_name = str_replace("0xa", '', trim($_REQUEST['sql_file_name'])); // 过滤 0xa 非法字符
			$pos = strpos($sql_file_name, '.sql');
			if ($pos !== false)
			{
				$sql_file_name = substr($sql_file_name, 0, $pos);
			}
		}
	
		$max_size = empty($_REQUEST['vol_size']) ? 0 : intval($_REQUEST['vol_size']);
		$vol = empty($_REQUEST['vol']) ? 1 : intval($_REQUEST['vol']);
		$is_short = empty($_REQUEST['ext_insert']) ? false : true;
	
		$dump->is_short = $is_short;
	
		/* 变量验证 */
		$allow_max_size = intval(@ini_get('upload_max_filesize')); //单位M
		if ($allow_max_size > 0 && $max_size > ($allow_max_size * 1024))
		{
			$max_size = $allow_max_size * 1024; //单位K
		}
	
		if ($max_size > 0)
		{
			$dump->max_size = $max_size * 1024;
		}
	
		/* 获取要备份数据列表 */
		$type = empty($_POST['type']) ? '' : "full";
		$tables = array();
	
		switch ($type)
		{
			case 'full':
				$temp = $db->GetCol("SHOW TABLES");
	
				foreach ($temp AS $table)
				{
					if (in_array($table, $except))
					{
						$tables[$table] = -1;
					}
				
				}
	
				$dump->put_tables_list($run_log, $tables);
				break;
		}
	
		/* 开始备份 */
	
		$tables = $dump->dump_table($run_log, $vol);
	
		if ($tables === false)
		{
			die($dump->errorMsg());
		}
	
		if (empty($tables))
		{
			/* 备份结束 */
			if ($vol > 1)
			{
				/* 有多个文件 */
				if (!@file_put_contents(siteroot.'web/data/sqldata/' . $sql_file_name . '_' . $vol . '.sql', $dump->dump_sql))
				{
					die(sprintf("备份文件 %s 无法写入", $sql_file_name . '_' . $vol . '.sql'));
				}
				$list = array();
				for ($i = 1; $i <= $vol; $i++)
				{
					$list[] = array('name'=>$sql_file_name . '_' . $i . '.sql', 'href'=>'../' . DATA_DIR . '/sqldata/' . $sql_file_name . '_' . $i . '.sql');
				}
				$list_msg=implode(',',$list);
				die($list_msg);			
	
			}
			else
			{
				/* 只有一个文件 */
				if (!@file_put_contents(siteroot.'web/data/sqldata/' . $sql_file_name . '.sql', $dump->dump_sql))
				{
					die(sprintf("备份文件 %s 无法写入",$sql_file_name . '_' . $vol . '.sql'));
				};
	

			}
		}
		else
		{
			/* 下一个页面处理 */
			if (!@file_put_contents(siteroot.'web/data/sqldata/' . $sql_file_name . '_' . $vol . '.sql', $dump->dump_sql))
			{

				die(sprintf("备份文件 %s 无法写入", $sql_file_name . '_' . $vol . '.sql'));
			}
	

		}		
	}
}

$data=new data();

$action = $_GET["action"];
$tablename = $_GET["tablename"];

switch($action)
{
	case "opimize":
		$data->opimize($tablename);
		break;
	case "repair":
		$data->repair($tablename);
		break;
	case "date_formation":
		$data->date_formation($tablename);
		break;
	case "dumpsql":
		$data->dumpsql();
		break;
	default:
		$data->def();
		break;
		
}


?>