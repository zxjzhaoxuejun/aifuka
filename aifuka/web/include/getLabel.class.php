<?php
/**
 * 模板标签正则匹配类。
 * 用户匹配模板文件，并返回 匹配结果 ，交由 替换 函数进行处理。
 * 
 * @version        $Id:getLable.class.php 1 10:33 2010年7月6日Z $
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */

/**
 *
 *  模板文件标签 匹配类。 
 * @author guoho
 *
 */
class getLabel
{
	function __construct($templateStr)
	{
		$this->templateStr=$templateStr;
	}

	/**
	 *
	 * 匹配循环标签内容 ，即 {$xxx } {/$}
	 */
	function getLoop()
	{
		preg_match_all("/\{\\$([a-zA-Z0-9_.]+) (.+?)\}([^\{]*)\{\/\\$\}/",$this->templateStr,$match); //1函数名，2参数，3循环内容

		return $match;
	}

	/**
	 *
	 *  匹配 程序中 类库中的方法 ，如没有指定类，则方法为 本类的方法，如指定类名，则为其它指定类中的方法。
	 */

	function getNomal()
	{
		preg_match_all("/\{\\$([a-zA-Z0-9_.]+) ([a-zA-Z].+?)\/\}/",$this->templateStr,$match); //1函数名，2参数
		return $match;
	}

	/**
	 *
	 * 匹配模板非数组,非函数变量标签。{$xxx /}
	 */
	function getConst()
	{
		preg_match_all("/\{\\$([a-zA-Z0-9_]+)\s*\/\}/",$this->templateStr,$match); //1常量名
		return $match;
	}

	/**
	 *
	 * 循环标签内的 子标签
	 */
	function getNomal2() 
	{
		preg_match_all("/\(\\$([a-zA-Z0-9_]+) ([a-zA-Z].+?)\/\)/",$this->templateStr,$match); //1函数名，2参数
		return $match;
	}

	/**
	 *
	 * 循环标签内的
	 */
	function getConst2()
	{
		preg_match_all("/\(\\$([a-zA-Z0-9_]+)\s*\/\)/",$this->templateStr,$match); //1常量名
		return $match;
	}

	/**
	 *
	 * 获取 [$9] 数字变量标签 
	 */
	function getHtml()
	{
		preg_match_all("/\[\\$([0-9]+)\]/",$this->templateStr,$match);
		return $match;
	}

	/**
	 *
	 * 获取 [$xx 9] 字符串 加数字变量标签 
	 */
	function getHtmls()
	{
		preg_match_all("/\[([a-z]+)([0-9]+)\]/",$this->templateStr,$match);
		return $match;
	}
}
?>