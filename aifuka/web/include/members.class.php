<?php
/**
 * 公共文件引用
 *
 * @version        $Id: web.config.php 1 10:33 2010年7月6日Z $
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */
class members extends alzCms
{

	/**
	 *
	 * 我的评论页面
	 */
	function comment(){
		global $cfg,$db;
		$cfg["page_title"] = $this->_lang("my_comment","member")."-".$cfg["webname".$cfg["lanstr"]];
		$cfg["page_keywords"] = $cfg["webkeywords".$cfg["lanstr"]];
		$cfg["page_description"] =$cfg["webdescription".$cfg["lanstr"]];
		$tpl=$this->loadtpl("member/comment");
		$cfg["modelcontent"]=$this->reLabel($tpl);
		$tpl=$this->loadtpl("main_member");
		$content=$this->reLabel($tpl);
		echo $content;
	}

	/**
	 *
	 * 忘记密码页面
	 */
	function forget()
	{
		global $cfg;
		$cfg["page_title"] = $this->_lang("getpass","member")."-".$cfg["webname".$cfg["lanstr"]];
		$cfg["page_keywords"] = $cfg["webkeywords".$cfg["lanstr"]];
		$cfg["page_description"] =$cfg["webdescription".$cfg["lanstr"]];
			
		$tpl=$this->loadtpl("member/forget");
		$cfg["modelcontent"]=$this->reLabel($tpl);
		$tpl=$this->loadtpl("main_member");
		$content=$this->reLabel($tpl);
		echo $content;
	}

	/**
	 *
	 * 会员中心默认页面
	 */
	function center(){
		global $cfg;
		$cfg["page_title"] = $this->_lang("center","member")."-".$cfg["webname".$cfg["lanstr"]];
		$cfg["page_keywords"] = $cfg["webkeywords".$cfg["lanstr"]];
		$cfg["page_description"] =$cfg["webdescription".$cfg["lanstr"]];
		$tpl=$this->loadtpl("member/member");
		$cfg["modelcontent"]=$this->reLabel($tpl);

		$tpl=$this->loadtpl("main_member");
		$content=$this->reLabel($tpl);
		echo $content;
	}

	/**
	 *
	 * 更改密码页面
	 */
	function change_pass(){
		global $cfg;
		$cfg["page_title"] = $this->_lang("change","member")."-".$cfg["webname".$cfg["lanstr"]];
		$cfg["page_keywords"] = $cfg["webkeywords".$cfg["lanstr"]];
		$cfg["page_description"] =$cfg["webdescription".$cfg["lanstr"]];

		$cfg["htmlPath"]=htmlPath();
		$cfg['front_username'] = $_SESSION['front_user_name'];
			
		$cfg['level_name'] = $_SESSION['front_level'];
		$tpl=$this->loadtpl("member/password");
		$cfg["modelcontent"]=$this->reLabel($tpl);
		$tpl=$this->loadtpl("main_member");
		$content=$this->reLabel($tpl);
		echo $content;
	}
	/**
	 *  会员注册页面
	 *
	 */
	function register(){
		global $cfg,$db;

		//$this->checkIP();
		$cfg["page_title"] = $this->_lang("register","member")."-".$cfg["webname".$cfg["lanstr"]];
		$cfg["page_keywords"] = $cfg["webkeywords".$cfg["lanstr"]];
		$cfg["page_description"] =$cfg["webdescription".$cfg["lanstr"]];

		$cfg["htmlPath"]=htmlPath();
		//	loadlanxml("members","reg_");
		if($topclassid!=""||$classid!="")header("location:index.php?topclassid=$topclassid&classid=$classid&lanstr=$lanstr");
		$tpl=$this->loadtpl("member/register");
		$cfg["modelcontent"]=$this->reLabel($tpl);
		$tpl=$this->loadtpl("main_member");
		$content=$this->reLabel($tpl);
		echo $content;
	}


	function getarticle(){
		global $cfg,$db;
		$thesql = 'select * from #@__article where title="'.$_POST['title'].'"';
		$rs = $db->GetOne($thesql);
		if($rs){
			echo "success";
		}else{
			echo "密码错误";
		}
	}
	/**
	 *
	 * 资料修改页面
	 */
	function modify(){
		global $cfg,$db;
		$cfg["target"]=" target=\"_blank\"";
		$topclassid=$_GET["topclassid"];
		$classid=$_GET["classid"];
		$cfg["id"]=$_GET["id"];
		$lanstr=$_GET["lanstr"];
		$cfg["lanstr"]=$lanstr;

		$cfg["page_title"] = $this->_lang("modify","member")."-".$cfg["webname".$cfg["lanstr"]];
		$cfg["page_keywords"] = $cfg["webkeywords".$cfg["lanstr"]];
		$cfg["page_description"] =$cfg["webdescription".$cfg["lanstr"]];

		$cfg["htmlPath"]=htmlPath();
		//	loadlanxml("members","reg_");
		if($topclassid!=""||$classid!="")header("location:index.php?topclassid=$topclassid&classid=$classid&lanstr=$lanstr");
		$tpl=$this->loadtpl("member/modify");

		$thesql = 'select * from #@__member where id="'.$_SESSION['front_user_id'].'"';
		$rs = $db->GetOne($thesql);
		$cfg['realname'] = $rs['realname'];
		$cfg['company'] = $rs['company'];
		$cfg['tel'] = $rs['tel'];
		$cfg['email'] = $rs['email'];
		$cfg['address'] = $rs['address'];

		$cfg["modelcontent"]=$this->reLabel($tpl);
		$tpl=$this->loadtpl("main_member");
		$content=$this->reLabel($tpl);
		echo $content;
	}

	/**
	 *
	 * 提交评论操作
	 */
	function subcomment()
	{
		global $cfg,$db;

		if($_SESSION["front_user_id"]<1){

			$user_name = $_POST["username"];
			$password = md5(trim($_POST["password"]));

			if($user_name=='' || $_POST["password"]==''){
				$this->halt($this->_lang("username_wrong"));
				exit;
			}
			$thesql = 'select id,user_name,password,level from #@__member where user_name="'.$user_name.'"';
			$rs = $db->GetOne($thesql);
			if($user_name == $rs["user_name"] && $password==$rs["password"])
			{
				$_SESSION["front_user_id"] = $rs["id"];
				$_SESSION["front_user_name"] = $rs["user_name"];
				$_SESSION["front_level"] = $rs["level"];
			}else{
				$this->halt($this->_lang("user_or_pass","member"));
			}
		}

		$now = time();
		$objid   = formatnum($_POST["objid"],0);
		$classidstr   = isset($_POST["classidstr"]) ? $_POST["classidstr"] : '';
		$objtitle = isset($_POST["objtitle"]) ? $_POST["objtitle"] : '';
		$content     = $_POST["msgcontent"];
		$lanstr      = $cfg["lanstr"];
		$user_id     = $_SESSION['front_user_id'];
		$user_name   = $_SESSION['front_user_name'];
		$thesql="insert into #@__comment(lanstr,objid, objtitle,classidstr,username, user_id,content,recontent,addtime) 
		values ('$lanstr',$objid,'$objtitle','$classidstr','$user_name',$user_id,'$content','',$now)";
		$db->execute($thesql);

		include(incpath.'class.phpmailer.php');
		$mail             = new PHPMailer();
		$body             = "有人在您的网站上留言了，请马上登陆后台审核。本邮件系统自动发送，请勿回复。";
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPDebug  = false;             // enables SMTP debug information (for testing)
		// 1 = errors and messages
		$mail->CharSet = 'UTF-8';                               // 2 = messages only
		$mail->SMTPAuth   = true;
		$mail->Host       = $cfg["mail_smtp"]; // SMTP server
		$mail->Port       = 25;                    // set the SMTP port for the GMAIL server
		$mail->Username   = $cfg["mail_account"];// SMTP account username
		$mail->Password   = $cfg["mail_passwd"];           // SMTP account password
		$mail->SetFrom($cfg["mail_account"], 'Sales');
		$mail->AddReplyTo($cfg["mail_account"],"Sales");
		$mail->Subject   = "有人在您的网站上留言了";
		$mail->AltBody    = "请查阅"; // optional, comment out and test
		$mail->MsgHTML($body);
		$address = $cfg["mail_reciever"];
		$mail->AddAddress($address, "收件人");
		$mail->Send();
		if($objid>0)
		$this->halt($this->_lang("comment_ok","feedback"));
		else
		$this->halt($this->_lang("msg_ok","feedback"));



	}


	/**
	 *
	 * Enter description here ...
	 */
	function register_act()
	{
		global $db,$cfg;

		$code =$_POST["code"];

		if($_SESSION['front_verifycode'] != $code){
			$this->halt($this->_lang("code_wrong","member"));
			exit;
		}
		$username = $_POST["uname"];
		if($username=='' || $this->check_username($username)){
			$this->halt($this->_lang("username_exist"));
			exit;
		}
		$password = md5($_POST["pwd"]);
		$realname = $_POST["realname"];
		$company = $_POST["company"];
		$tel = $_POST["tel"];
		$email = $_POST["email"];
		$address = $_POST["address"];

		$now=time();
		$ip=GetIp();
		$thesql = "insert into #@__member(user_name,password,realname,company,tel,email,address,reg_time,last_time,last_ip) values('".$username."','".$password."','".$realname."','".$company."','".$tel."','".$email."','".$address."','".$now."','".$now."','".$ip."')";

		$db->execute($thesql);

		$_SESSION["front_user_id"] = $db->GetLastID();
		$_SESSION["front_user_name"] = $username;
		$_SESSION["front_level"] = 1;

		//date_default_timezone_set('PRC');
		include(incpath.'class.phpmailer.php');
		$mail             = new PHPMailer();
		$body             = "有新会员在网站注册了，请马上登陆后台审核。本邮件系统自动发送，请勿回复。";
		$body             = eregi_replace("[\]",'',$body);
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPDebug  = false;
		//	$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
		// 1 = errors and messages
		// 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Host       = $cfg["mail_smtp"]; // SMTP server
		$mail->Port       = 25;                    // set the SMTP port for the GMAIL server
		$mail->Username   = $cfg["mail_account"];// SMTP account username
		$mail->Password   = $cfg["mail_passwd"];           // SMTP account password
		$mail->SetFrom($cfg["mail_account"], 'Sales');
		$mail->AddReplyTo($cfg["mail_account"],"Sales");

		$mail->Subject    = "有新会员在网站注册了，请马上登陆后台审核。本邮件系统自动发送，请勿回复。";
		$mail->AltBody    = ""; // optional, comment out and test
		$mail->MsgHTML($body);
		$address = $cfg["mail_reciever"];
		$mail->AddAddress($address, "收件人");
		$mail->AddAddress($address, "John Doe");
		$mail->Send();
		$this->halt($this->_lang("reg_ok","member"),webpath.'member.php?action=msglist&lanstr='.$cfg['lanstr']);




	}
	
	
	
	/**
	 *
	 * Enter description here ...
	 */
	function register_act1()
	{
		global $db,$cfg;

		$code =$_POST["code"];

		if($_SESSION['front_verifycode'] != $code){
			//$this->halt($this->_lang("code_wrong","member"));
			echo "验证码不正确";
			exit;
		}
		$username = $_POST["username"];
		if($username=='' || $this->check_username($username)){
			echo "用户名不能为空或已存在";
			exit;
		}
		//$password = md5($_POST["pwd"]);
		$realname = $_POST["username"];
		//$company = $_POST["company"];
		$tel = trim($_POST["tel"]);
		$email = trim($_POST["email"]);
		$buydate = trim($_POST["buydate"]);
		$buymodel = trim($_POST["buymodel"]);
		$buyshop = trim($_POST["buyshop"]);
		$buyprice = trim($_POST["buyprice"]);
		$qq = trim($_POST["qq"]);
		$haoma = trim($_POST["haoma"]);
		if($buymodel==""){
			echo "购机型号不能为空";
			exit;
		}
		if($email==""){
			echo "用户邮箱不能为空";
			exit;
		}
		if($haoma==""){
			echo "机身码不能为空";
			exit;
		}
		if($tel==""){
			echo "用户手机不能为空";
			exit;
		}
		
		$now=time();
		$ip=GetIp();
		$thesql = "insert into #@__member(user_name,realname,tel,email,reg_time,last_time,last_ip,buydate,buymodel,buyshop,buyprice,qq,haoma) values('".$username."','".$realname."','".$tel."','".$email."','".$now."','".$now."','".$ip."','".$buydate."','".$buymodel."','".$buyshop."','".$buyprice."','".$qq."','".$haoma."')";

		$db->execute($thesql);

		//date_default_timezone_set('PRC');
		include(incpath.'class.phpmailer.php');
		$mail             = new PHPMailer();
		$body             = "有新会员在网站注册了，请马上登陆后台审核。本邮件系统自动发送，请勿回复。";
		$body             = eregi_replace("[\]",'',$body);
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPDebug  = false;
		//	$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
		// 1 = errors and messages
		// 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Host       = $cfg["mail_smtp"]; // SMTP server
		$mail->Port       = 25;                    // set the SMTP port for the GMAIL server
		$mail->Username   = $cfg["mail_account"];// SMTP account username
		$mail->Password   = $cfg["mail_passwd"];           // SMTP account password
		$mail->SetFrom($cfg["mail_account"], 'Sales');
		$mail->AddReplyTo($cfg["mail_account"],"Sales");

		$mail->Subject    = "有新会员在网站注册了，请马上登陆后台审核。本邮件系统自动发送，请勿回复。";
		$mail->AltBody    = ""; // optional, comment out and test
		$mail->MsgHTML($body);
		$address = $cfg["mail_reciever"];
		$mail->AddAddress($address, "收件人");
		//$mail->AddAddress($address, "John Doe");
		$mail->Send();
		//$this->halt($this->_lang("reg_ok","member"),webpath.'member.php?action=msglist&lanstr='.$cfg['lanstr']);
		echo "success";
		exit();
	}
	

	/**
	 *
	 * 更改会员资料
	 */
	function register_edit()
	{
		global $db,$cfg;
		$realname = $_POST["realname"];
		$company = $_POST["company"];
		$tel = $_POST["tel"];
		$email = $_POST["email"];
		$address = $_POST["address"];
		$thesql="UPDATE #@__member SET 	realname = '".$realname."',company = '".$company."',tel = '".$tel."',email = '".$email."',
		address = '".$address."'  WHERE id =".$_SESSION['front_user_id'];
			
		$db->execute($thesql);
		$this->halt($this->_lang("modify_ok","member"));
	}

	/**
	 *
	 * 登陆验证
	 */
	function getpassword()
	{
		global $db,$cfg;
		$username = $_POST["username"];
		$address    = $_POST["email"];

		if($username==''){
			$this->halt($this->_lang("user_input","member"));
			exit;
		}
		if(!$this->check_username($username)){
			$this->halt($this->_lang("user_unexist","member"));
			exit;
		}
		if($address==''){
			$this->halt($this->_lang("email_wrong","member"));
			exit;
		}
		$thesql = "select email from #@__member where user_name='".$username ."'";
		$rs = $db->GetOne($thesql);
		if($address != $rs['email']){
			$this->halt($this->_lang("email_nomath","member"));
			exit;
		}
		$newpassword  = 'ec'.rand(1000,9999);
		$password = md5($newpassword);

		$thesql="UPDATE #@__member SET 	password = '".$password."' where user_name='".$username ."'";
		$db->execute($thesql);
			
			
		include(incpath.'class.phpmailer.php');
		$mail             = new PHPMailer();

		if($cfg['lanstr'] == 'zh_cn'){
			$title  = "您好，感谢您的支持，";
			$body   = "您的会员登陆密码是: ".$newpassword;
			$body  .= "<br> 请您登陆修改，并妥善保管。<br>";
			$body  .= " 登陆地址：<a href='http://".$_SERVER["HTTP_HOST"]."'>Ecolinkin</a>";
		}
		else {
			$title  = "Thanks for your support,   ";
			$body   = "you login pass word is: ".$newpassword;
			$body  .= "<br>please login to modify and save<br>";
			$body  .="Login address: <a href='http://".$_SERVER["HTTP_HOST"]."'>Ecolinkin</a>";
		}
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPDebug  = false;             // enables SMTP debug information (for testing)
		// 1 = errors and messages
		$mail->CharSet = 'UTF-8';                               // 2 = messages only
		$mail->SMTPAuth   = true;
		$mail->Host       = $cfg["mail_smtp"]; // SMTP server
		$mail->Port       = 25;                    // set the SMTP port for the GMAIL server
		$mail->Username   = $cfg["mail_account"];// SMTP account username
		$mail->Password   = $cfg["mail_passwd"];           // SMTP account password
		$mail->SetFrom($cfg["mail_account"], 'Sales');
		$mail->AddReplyTo($cfg["mail_account"],"Sales");
		$mail->Subject   = $title;
		$mail->AltBody    = ""; // optional, comment out and test
		$mail->MsgHTML($body);
		$mail->AddAddress($address, "");
		$mail->Send();
		$this->halt($this->_lang("email_send","member"),webpath.'index.php?'.$cfg['lanstr']);

	}


	/**
	 *
	 * 登陆验证
	 */
	function changepassword()
	{
		global $db,$cfg;

		$oldpassword = md5(trim($_POST["oldpassword"]));
		$password = md5($_POST["pwd"]);


		$thesql = 'select password from #@__member where user_name="'.$_SESSION['front_user_name'].'"';
		$rs = $db->GetOne($thesql);
		if($oldpassword ==$rs["password"])
		{
			$thesql="UPDATE #@__member SET 	password = '".$password."' WHERE id =".$_SESSION['front_user_id'];
			$db->execute($thesql);
			$this->halt($this->_lang("change_ok","member"));


		}else{
			$this->halt($this->_lang("pass_wrong","member"));
		}
	}

	/**
	 *
	 * 登陆验证
	 */
	function logincheck()
	{
		global $db,$cfg;
		$user_name = $_POST["username"];
		$password = md5(trim($_POST["password"]));
		$auto_login=$_POST["auto_login"];

		$code =$_POST["code"];
		if($code && $_SESSION['front_verifycode'] != $code){
			$this->halt($this->_lang("code_wrong","member"));
			exit;
		}

		$thesql = 'select id,user_name,password,level from #@__member where user_name="'.$user_name.'"';
		$rs = $db->GetOne($thesql);
		if($user_name == $rs["user_name"] && $password==$rs["password"])
		{
			$_SESSION["front_user_id"] = $rs["id"];
			$_SESSION["front_user_name"] = $rs["user_name"];
			$_SESSION["front_level"] = $rs["level"];
			$this->_halt($this->_lang("login_ok","member"),webpath."member.php?lanstr=".$cfg['lanstr']);
		}else{
			$this->halt($this->_lang("user_or_pass","member"));
		}
	}

	/**
	 *
	 * 评论
	 */
	function msglist($loopstr)
	{
		global $cfg,$db;
		$pagesize=20;
		$cfg["pagesize"]=$pagesize;
		$beginid=($cfg["page"]-1)*$pagesize;
		$beginid = $beginid < 0 ? 0 : $beginid;
		$thesql="select id from #@__comment where user_id=".$_SESSION["front_user_id"]." and objid=0 and lanstr='".$cfg["lanstr"]."' AND locked=0";

		$db->dosql($thesql);
		$cfg["allnums"]=$db->nums();

		$limitstr="limit $beginid,$pagesize";
		$thesql="select * from #@__comment where user_id=".$_SESSION["front_user_id"]." and objid=0 and lanstr='".$cfg["lanstr"]."' AND locked=0 order by id desc $limitstr";

		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{

			
			$cfg["time"] = date("Y-m-d H:i:s", $rs['addtime']);			
			$cfg["content"]= $rs["content"];
			$cfg["recontent"]= $rs['retime'] > 0 ? $rs['recontent'] : $this->_lang('noreply','feedback');
			$cfg["retime"] = $rs['retime'] > 0 ?  '[  '.date("Y-m-d H:i:s", $rs['retime']).'  ]' : '';
			$temp.=$this->reLabel2($loopstr);

		}
		return $temp;

	}

	/**
	 *
	 * 评论
	 */
	function commentlist($loopstr)
	{
		global $cfg,$db;
		$pagesize=20;
		$cfg["pagesize"]=$pagesize;
		$beginid=($cfg["page"]-1)*$pagesize;
		$beginid = $beginid < 0 ? 0 : $beginid;
		$thesql="select id from #@__comment where user_id=".$_SESSION["front_user_id"]." and objid>0 and lanstr='".$cfg["lanstr"]."' AND locked=0";

		$db->dosql($thesql);
		$cfg["allnums"]=$db->nums();

		$limitstr="limit $beginid,$pagesize";
		$thesql="select * from #@__comment where user_id=".$_SESSION["front_user_id"]." and objid>0 and lanstr='".$cfg["lanstr"]."' AND locked=0 order by id desc $limitstr";

		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{

			$cfg["username"]= $rs["username"];
			$cfg["content"]= $rs['content'];
			
			$temp.=$this->reLabel2($loopstr);

		}
		return $temp;

	}


	/**
	 *
	 * Enter description here ...
	 */
	function username_quit()
	{
		$_SESSION["front_user_id"] = NULL;
		$_SESSION["front_user_name"] =  NULL;
		$_SESSION["front_user_type"] = NULL;
	}

	/**
	 *
	 * Enter description here ...
	 * @param $check_username
	 */
	function check_username($check_username)
	{
		global $db,$cfg;
		$username=$check_username;
		$thesql = 'select user_name from #@__member where user_name="'.$username.'"';
		$rs = $db->GetOne($thesql);
		if($rs["user_name"])
		{
			return 1;
		}else{
			return 0;
		}
	}

	function checkIP(){
		global $cfg;
		if($cfg['mainland_reg']){

			$meip= ip2long(GetIp());

			$filename="ip.txt";      //定义操作文件
			$ip_lib=file($filename);   //读取文件数据到数组中
			$count = count($ip_lib);

			for($i=0; $i < $count; $i++)
			{
				list($sip,$eip) = explode('-',$ip_lib[$i]);
				$sip = ip2long(trim($sip));
					
				$eip = ip2long(trim($eip));
				if($meip >= $sip && $meip <= $eip)
				{
					$this->halt($this->_lang("no_priv"));
					exit();

				}
			}
		}
	}

}


?>