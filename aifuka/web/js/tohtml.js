var loadimg="<img src='/web/my_admin/skins/images/loading.gif' align='absmiddle' />";

function delay(numi,fun,div)
{
	if(numi%tohtml_delaynums==0){
		var times=tohtml_delaytimes;
		function loop(){
			if(times==0){
				clearInterval(delays);
				eval(fun+"();");
			}else{
				div.innerHTML=loadimg+" 程序暂停倒计时 <u>"+times+"</u> 秒……";
			}
			times--;
		}
		var delays=setInterval(loop,1000);
	}else{
		eval(fun+"()");
	}
}

function ajax_posting(){
	if(p!=0||f!=0)return;
	if(Null(loadingstr))loadingstr=" 网站更新中，请不要刷新此页面，耐心等待……";
	str=loadimg+loadingstr;
	var obj1=getId("submit1");
	var obj3=getId("submiting");
	if(obj1){obj1.style.display="none";}
	if(obj3){obj3.innerHTML=str;}
}

function tohtml_other()
{//这里可以作一些其它操作，比如说成生首页.

	ajax_post(adminpath+"tohtml/?action=alz-toindex","","_other");
}
function ajax_post_other(sec){
	tohtml_dos();
}