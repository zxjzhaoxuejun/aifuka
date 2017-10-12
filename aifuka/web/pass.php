<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<script src="/web/template/themes/rsn/skins/js/jquery-1.4.2.min.js" type="text/javascript"></script>
</head>

<body>
<form id="form2" method="post">
<table width="100%" align="left">
	<tr>
		<td height="50" align="center">
        
      <div style="margin-left:5px; margin-right:8px; margin-top:28px; line-height:21px;">			
			<font style="font-size:14px;"><strong>说明：</strong>请输入下载验证码，如有疑问请登陆<a href="/web/register.php" target="_blank" style="font-size:14px; color:#FF0000;">注册会员</a>,谢谢！</font><br /><br />
		  <input class="inputs post_" name="password" type="text" id="password" size="30" style="width:200px;">
          <input type="hidden" class="post_inputs" name="downurl" value="<?php echo $_GET["url_d"]; ?>" id="downurl" />
			</div>        </td>
	</tr>
	<tr>
		<td height="35" align="center">
		<input type="button" value=" 确 定 " class="mybutton" onclick="check_passs1();" />
		<input type="reset" value=" 重 置 " class="mybutton" />		</td>
	</tr>
</table>
</form>
</body>
</html>
<script>
function check_passs1(){
	var url=$("#downurl").val();
	$.post("/web/member.php?action=article",{title:$("#password").val()},function(data){
		if(data=="success"){
			location.href=url;
		}else{
			alert("密码错误");
		}
	});
}
</script>
