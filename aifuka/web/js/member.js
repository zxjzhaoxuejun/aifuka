// JavaScript Document
function registers(form){
	var valuestr=getFormValue(form);

	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	ajax_post(webpath+"members.php?action=register_update",valuestr,"_registers");
}