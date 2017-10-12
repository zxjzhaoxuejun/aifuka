<?php
/**
 *
 */
include("inc.php");
include("funlist2.php");
$action=$_GET["action"];

switch($action)
{
	case "diyformsave":
		diyformsave();
		break;
	case "count":
	
		expireMail();
		break;
	
	case "userLogin":
		userLogin();
		break;
	case "tagClick":
		tagClick();
		break;
	case "fw":
		fw();
		break;
	
	case "refright":
		refPage(0);
		break;
}

/**
 *
 * Enter description here ...
 */
function tagClick()
{
	global $cfg,$db;
	$tag=$_GET["tag"];
	$thesql="update #@__tag set click=click+1 where tag='$tag'";
	$db->execute($thesql);
}

function fw(){
	global $cfg,$db;
	$fw=$_GET["fw"];
	$thesql = "select * from #@__article where title='".$fw."' and classid=338 limit 0,1";
	$rs=$db->GetOne($thesql);
	echo $rs["id"];
}

function expireMail(){
	global $cfg;
	$expire_time = mktime(0,0,0,$cfg['expire_month'],$cfg['expire_day'],$cfg['expire_year']);
	$log = file_get_contents('log.log');
	
	
	if($cfg['is_expire_notice'] && ($expire_time - time()) < 30*24*3600 && $expire_time>time()  && date('Ymd')!=$log )  {
		
		include(incpath.'class.phpmailer.php');
		$mail             = new PHPMailer();
		$body             = file_get_contents('expire.php');
	
		
		$body = str_replace("{realname}", $cfg['expire_customer_name'], $body);
		$body = str_replace("{domain}", $cfg['expire_customer_domain'], $body);
		
		$body = str_replace("{expire_date}", $cfg['expire_year'].'-'.$cfg['expire_month'].'-'.$cfg['expire_day'], $body);
		
		$body = str_replace("{expire_service_qq}", $cfg['expire_service_qq'], $body);
		
		$body = str_replace("{expire_service_tel}", $cfg['expire_service_tel'], $body);
		
		$body = str_replace("{reply_time}", date("Y-m-d"), $body);
	
		
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPDebug  = false;             // enables SMTP debug information (for testing)
		// 1 = errors and messages
		$mail->CharSet = 'UTF-8';                               // 2 = messages only
		$mail->SMTPAuth   = true;
		$mail->Host       = $cfg['expire_mail_server']; // SMTP server
		$mail->Port       = 25;                    // set the SMTP port for the GMAIL server
		$mail->Username   = $cfg['expire_mail_account'];// SMTP account username
		$mail->Password   =  $cfg['expire_mail_pass'];           // SMTP account password
		$mail->SetFrom($cfg['expire_mail_reply'], '极地网络客服');
		$mail->AddReplyTo($cfg['expire_mail_reply'],  '极地网络客服');
		$mail->Subject   = "[网站到期提醒]您的网站即将到期，请您马上处理！";
		//$mail->AltBody   = "网站到期提醒"; // optional, comment out and test
		$mail->MsgHTML($body);
		$address = $cfg["expire_customer_mail"];
		$mail->AddAddress($address, "收件人");
		$mail->Send();
		file_put_contents('log.log', date("Ymd",time()));
	}

}


/**
 *
 * Enter description here ...
 */
function diyformsave()
{
	global $cfg,$db;
	$codestr=$_POST["codestr"];
	if($codestr!=""&&$codestr!=$_SESSION["codestr"]){die("errcode");}
	$sec=$_POST["sec"];
	$datatable=$cfg["mydb_dbprefix"].$_POST["datatable"];
	$filed=$_POST["filed"];
	$filedarr=explode("|",$filed);
	$num=count($filedarr);
	for($i=0;$i<$num;$i++)
	{
		$filedstr.=$filedarr[$i].",";
		$str=$_POST[$filedarr[$i]];
		$content.="'$str',";
	}
	$filedstr=trim($filedstr,",");
	$content=trim($content,",");
	$now=$_SERVER["REQUEST_TIME"];
	$classid=formatnum($_POST["classid"],0);
	$lanstr=$_POST["lanstr"];
	$locked=$cfg["feedback_deflocked"];
	$ip=GetIp();
	$thesql="insert into $datatable (classid,addtime,lanstr,locked,ip,$filedstr) values ($classid,$now,'$lanstr',$locked,'$ip',$content)";

	$db->execute($thesql);
	echo $sec;
	//echo $thesql;
	//echo $filedarr;
	/*include(incpath.'class.phpmailer.php');
	$mail             = new PHPMailer();
	$body             = "有人在您的网站上留言了，请马上登陆后台审核。本邮件系统自动发送，请勿回复。";
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->SMTPDebug  = false;             // enables SMTP debug information (for testing)
	// 1 = errors and messages
	$mail->CharSet = 'UTF-8';                               // 2 = messages only
	$mail->SMTPAuth   = true;
	$mail->Host       = $cfg["mail_smtp"]; // SMTP server
	$mail->SMTPSecure = "ssl";
	$mail->Port       = 465;                 // set the SMTP port for the GMAIL server
	$mail->Username   =  $cfg["mail_account"];// SMTP account username
	$mail->Password   = $cfg["mail_passwd"];           // SMTP account password
	$mail->SetFrom($cfg["mail_account"], 'Sales');
	$mail->AddReplyTo($cfg["mail_account"],"Sales");
	$mail->Subject   = "有人在您的网站上留言了";
	$mail->AltBody    = "请查阅"; // optional, comment out and test
	$mail->MsgHTML($body);
	$address = $cfg["mail_reciever"];
	$mail->AddAddress($address, "收件人");
	if (!$mail->Send())
  {
	 echo $mail->ErrorInfo;
  }else{
  	echo $sec;
  }*/

}

/**
 *
 * Enter description here ...
 */
function userLogin()
{
	global $db,$cfg;
	$username = $_POST["username"];
	$passwored = $_POST["pwd"];
	$sec = $_POST["sec"];
	$fail = $_POST["fail"];
	$thesql = "select * from #@__member where user_name='".$username."' and password='".$passwored."' limit 0,1";
	$rs=$db->GetOne($thesql);
	if(isset($rs["user_name"]))
	{
		setsession("userid",$rs["lanid"]);
		setsession("username",$username);
		setsession("password",md5($passwored));
		setcookies("userid",$rs["lanid"]);
		setcookies("username",$username);
		setcookies("password",md5($userpwd));
		die($sec);
	}else
	{
		die($fail);
	}
}


?>