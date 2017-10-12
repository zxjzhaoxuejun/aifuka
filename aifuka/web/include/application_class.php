<?php
/**
 * 序列化的程序 或 对象 管理类
 *
 * 作用是将一些 数组 ，如 管理员权限信息，类目属性，序列化，以文本数据库，或缓存 文件的形式保存至临时文件。
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */
class application
{
	// 序列文件
	var $save_file;
	var $application = null;
	var $app_data='';
	var $writed=false;

	/**
	 *
	 * Enter description here ...
	 * @param $file
	 */
	function __construct($file="adminuser.inc")
	{
		$this->application = array();
		$this->save_file= temppath.$file;
	}

	/**
	 *
	 * 添加新的对象属性值，并重新写入文件。
	 * @param $var_name
	 * @param $var_value
	 */
	function setValue($var_name,$var_value)
	{
		if(!is_string($var_name)||empty($var_name)) return false;
		if($this->writed)
		{
			$this->application[$var_name] = $var_value;
			return;
		}
		$this->application = $this->getValue();
		if(!is_array($this->application)) 
		    settype($this->application,"array");
		$this->application[$var_name]=$var_value;
		$this->writed = true;
		$this->app_data = serialize($this->application);
		file_put_contents($this->save_file,$this->app_data);
	}

	/**
	 *
	 * 读取 序列化 的 程序文本文件，并反进行反序列化。 
	 */
	function getValue()
	{
		if(!is_file($this->save_file)) 
		    file_put_contents($this->save_file,$this->app_data);
		return unserialize(file_get_contents($this->save_file));
	}

	/**
	 *
	 * 返回 已 反序列化的 程序 对象。
	 * @param unknown_type $name
	 * @param unknown_type $value
	 */
	function app($name,$value="alzcms_app")
	{
		if($value!="alzcms_app")//插入
		{
			$this->writed=false;
			$this->setValue($name,$value);
		}else{//取值
			$valuearr = unserialize(file_get_contents($this->save_file));
			$value = $valuearr[$name];
		}
		return $value;
	}

	/**
	 *
	 * 删除一个属性，并重新序列化。
	 * @param unknown_type $name
	 */
	function del($name)
	{
		$this->application = $this->getValue();
		if(!is_array($this->application))
		    settype($this->application,"array");
		unset($this->application[$name]);
		$this->app_data = serialize($this->application);
		file_put_contents($this->save_file,$this->app_data);
	}
}
?>