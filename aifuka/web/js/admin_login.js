/** The login ajax action javascript function */
function adminlogin_ck(form)
{
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	ajax_post("admin_login.php?action=ck",valuestr);
	return false;
}

function ajax_posted(sec)
{
	if(sec.indexOf("{ok}")<0)
		sucgoto("<u>"+sec+"</u>","",errgoto_times);
	else
		parent.location.href="admin_index.php";
}

function gotologin(){
	parent.location.href=adminpath+"admin_login.php";
}