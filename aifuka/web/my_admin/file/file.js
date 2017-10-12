var onefile_flag=false;

var F={
	title:"文件管理",
	setpath:"",
	path:"",
	secid:"",
	type:'NOT',
	size:0,
	addmore:'',
	open:function(){
		var a=arguments;
		setCookie("file_path",a[1]);
		var oldfile_setpath=getCookie("file_setpath");
		if(oldfile_setpath!=a[1]||Null(oldfile_setpath)){setCookie("file_patharr","");}
		setCookie("file_setpath",a[1]);
		
		setCookie("file_secid",a[2]);
		var other=a[3]||{type:'NOT',size:0,addmore:''};
		setCookie("file_type",other["type"]);
		setCookie("file_size",other["size"]);
		setCookie("file_addmore",other["addmore"]);
		setCookie("file_curpath",other["curpath"]);
		
		
		
		var e=a[5];
		if(e){
			var x=e.clientX;
			var y=e.clientY;
			this.onefile(x,y,a[0],a[1],a[2],other["size"],other["type"]);
		}else{
			Msg.open(a[0],adminpath+"file/index.php?files="+a[4]);
		}
	},
	onefile:function(x,y,title,path,secid,size,type)
	{
		var d=getId("uploaddiv");
		var sTop=scrollTop();
		if(d){document.body.removeChild(d);}
		d=document.createElement("div");
		d.id="uploaddiv";
		var c='<b>'+title+'</b><img src="'+adminpath+'skins/close2.gif" onclick="F.Closeonefile();" />';
		c+='<iframe src="'+adminpath+'file/uponefile.php?size='+size+'&type='+type+'&secid='+secid+'&path='+path+'" style="width:250px;height:105px;" allowTransparency="true" scrolling="no" frameborder="0"></iframe>';
		d.innerHTML=c;
		document.body.appendChild(d);
		noright2(d);
		var w=d.clientWidth;
		d.style.left=(x-w+80)+"px";
		d.style.top=y-80+sTop+"px";
		d.onmouseover=function(){onefile_flag=false;}
		d.onmouseout=function(){onefile_flag=true;}
		document.onclick=function(){if(onefile_flag){F.Closeonefile();}}
	},
	Closeonefile:function()
	{
		onefile_flag=false;
		var d=getId("uploaddiv");
		if(d){document.body.removeChild(d);}
	},
	fileListLoad:function(path,files){
		if(!Null(path)){
			path=formatname(path);
			setCookie("file_path",path);
			var file_patharrstr=getCookie("file_patharr");
			if(file_patharrstr.indexOf(","+path)<0){setCookie("file_patharr",file_patharrstr+","+path);}
		}
		this.fileListArr();
		ajaX("?action=flist&files="+files,"lefts");
		this.fileSetUp();
	},
	fileListLoad2:function(){
		ajaX("?action=flist",getFid("lefts"));
	},
	fileListArr:function()
	{
		var file_patharrstr=getCookie("file_patharr");
		var file_path=getCookie("file_path");
		file_patharr=file_patharrstr.split(",");
		var file_setpath=getCookie("file_setpath");
		var temp,selected;
		temp="<select onchange='F.patharrto(this.value);'>";
		//temp+="<option value='"+webpath+file_setpath+"'>允许文件根目录："+webpath+file_setpath+"</option>";
		temp+=this.getoption(file_patharrstr,",",webpath,file_path,"");
		temp+="</select>";
		getId("fileGuidList").innerHTML=temp;
	},
	fileSetUp:function(){
		return;
		var err=0;
		function fileLoopSet(){
			var File_sTop=formatnum(getCookie("File_sTop"),0);
			var File_i=getCookie("File_i");
			var fileI=getId("file"+File_i);
			if(fileI){
				fileI.className+="on";
				getId("ckbox"+File_i).checked=true;
				getId("lefts").scrollTop=File_sTop;
				getId("oldId").value=File_i;
				clearInterval(LoopSet);
			}else if(err>100){clearInterval(LoopSet);}else{err++;}
		}
		var LoopSet=setInterval(fileLoopSet,100);
	},
	getoption:function(arr,str,addstr,def,del)
	{
		var file_setpath=getCookie("file_setpath");
		var arrs=arr.split(str),temp;
		for(var i=0;i<arrs.length;i++){
			if(!Null(arrs[i])&&(addstr+arrs[i])!=del)
			{
				selected=(arrs[i]==def)?" selected":"";
				temp+="<option value='"+addstr+arrs[i]+"'"+selected+">"+addstr+arrs[i]+"</option>"
			};
		}
		return temp;
	},
	patharrto:function(path){
		var file_setpath=getCookie("file_setpath");
		path=path.replace(webpath,"");
		this.fileListLoad(path);
	},
	upPath:function(){
		this.path=getCookie("file_path");
		this.setpath=getCookie("file_setpath");
		if(this.setpath.length<this.path.length)
		{
			var arr=this.path.split("/");
			var dellen=arr[arr.length-2].length+1;
			var len=this.path.length-dellen;
			this.path=this.path.substring(0,len);
			setCookie("file_path",this.path);
			this.fileListLoad(this.path);
		}		
	},
	upLoad:function()
	{
		DvWnd.open('文件上传','?action=upload&_i='+Math.random(),535,360);
	},
	changList:function(){
		var obj=getId("left");
		if(obj.className=="filelist2"){
			obj.className="filelist1"
			setCookie("upfile_filelist","filelist1");
		}else{
			obj.className="filelist2"
			setCookie("upfile_filelist","filelist2");
		}
	},
	showRight:function(){
		var obj2=getId("right");
		var obj=getId("lr-img");
		if(obj2.style.display=="none"){
			obj2.style.display="";
			obj.src="filepic/R.gif";
			setCookie("upfile_right","open");
		}else{
			obj2.style.display="none";
			obj.src="filepic/L.gif";
			setCookie("upfile_right","")
		}
	},
	fileListSet:function(){
		var obj=getId("left");
		var a=getCookie("upfile_filelist");
		if(a&&obj)obj.className=a;
		var upfile_right=getCookie("upfile_right");
		if(upfile_right=="open"){
			var obj2=getId("right");
			var obj=getId("lr-img");
			obj2.style.display="";
			obj.src="filepic/R.gif";
		}
	},
	showOff:function(){getId("rightmenu").style.display="none";},
	browserCk:function(){
		var s=window.navigator.userAgent;
		if(s.indexOf("Tencent")>0)
			return "TT";
		else if(s.indexOf("Firefox")>0)
			return "FF";
		else if(s.indexOf("Chrome")>0)
			return "GG";
		else if(s.indexOf("MSIE 6.0")>0)
			return "IE6";
		else if(s.indexOf("MSIE 7.0")>0)
			return "IE7";	
		else
			return "IE";
	},
	fileSet:function(obj,fileUrl,fileType,fileSize,i,e){
		//alert(fileUrl);
		e=window.event||e;
		var ctrlKey=e.ctrlKey;
		var shiftKey=e.shiftKey;
		setCookie("File_i",i);
		setCookie("File_sTop",getId("lefts").scrollTop);
		var filePath=getCookie("file_path");
		var isfile=true;
		var istxt=false;
		var ispic=false;
		var pictypestr=",JPG,GIF,BMP,PNG,";
		var txttypestr=",PHP,HTML,HTM,TXT,JS,CSS,XML,";
		if(pictypestr.indexOf(","+fileType+",")>=0){ispic=true;}
		if(txttypestr.indexOf(","+fileType+",")>=0){istxt=true;}
		var pic=getId("filepic");		
		if(ispic){
			pic.src="filepic/loading.gif";
			var img=new Image();
			img.onload=function(){img.onload=null;pic.src=fileUrl;getId("filesx").style.display="";getId("filesx").innerHTML="图片尺寸："+pic.width+"×"+pic.height;}
			img.src=fileUrl;
		}else{
			pic.src="filepic/big_"+fileType+".gif";getId("filesx").style.display="none";
		}		
		if(fileType!="文件夹"){pic.alt="点击浏览";pic.onclick=function(){window.open(fileUrl);}}
		pic.onerror=function(){pic.src="filepic/big_def.gif";}	
		getId("nowid").value=i;
		if(fileType=="文件夹"){isfile=false;}
		if(isfile){
			getId("geturl").value=fileUrl;
			getId("fileType").value=fileType;
			getId("fileSize").value=fileSize;		
		}		
		var cbutton=parseInt(e.button);
		var browstr=this.browserCk();	
		if(browstr=="TT"||browstr=="IE6"){rbutton=0;}else{rbutton=2;}
		var isMouseR=(cbutton==rbutton||cbutton>2);
		if(isMouseR){
			var rhtml='<table>';
			rhtml+='<tr><td><a href="javascript:F.fileListLoad(\''+filePath+'\')">刷新</a></td></tr>';
			if(isfile){	rhtml+='<tr><td><a href="'+fileUrl+'" target="_blank">打开</a></td></tr>';}	
			if(istxt){rhtml+='<tr><td><a href="javascript:F.editFile(\''+fileUrl+'\')">编辑</a></td></tr>';}
			//if(ispic){rhtml+='<tr><td><a href="javascript:cutPic(\''+fileUrl+'\',1)">图片裁切[等比]</a></td></tr>';}
			//if(ispic){rhtml+='<tr><td><a href="javascript:cutPic(\''+fileUrl+'\',2)">图片裁切[自由]</a></td></tr>';}
			rhtml+='<tr><td><a href="javascript:F.reName(\''+fileUrl+'\','+isfile+')">重命名</a></td></tr>';
			rhtml+='<tr><td><a href="javascript:F.addFolder();">新建文件夹</a></td></tr>';
			rhtml+='<tr><td><a href="javascript:F.moveF();">移动</a></td></tr>';
			rhtml+='<tr><td><a href="javascript:F.dels()">删除</a></td></tr>';
			rhtml+='</table>';
			var theY=e.clientY;
			getId("rightmenu").style.display="";
			if(theY>300){getId("rightmenu").style.top=(theY-110)+"px";}else{getId("rightmenu").style.top=theY+"px";}
			getId("rightmenu").style.left=e.clientX+"px";
			getId("rightmenu").innerHTML=rhtml;
		}		
		var oldId=formatnum(getId("oldId").value,0);
		if(shiftKey){
			var minI=oldId>i?i:oldId;
			var maxI=oldId<i?i:oldId;
			for(var n=minI;n<=maxI;n++){getId("ckbox"+n).checked=true;getId("file"+oldId).className="filelist";}
		}else{
			if(getId("file"+oldId)){getId("file"+oldId).className="filelist";if(!ctrlKey){getId("ckbox"+oldId).checked=false;}}		
			getId("ckbox"+i).checked=getId("ckbox"+i).checked?false:true;
		}
		getId("file"+i).className+="on";
		getId("oldId").value=i;
	},
	uploadComplete:function(){
		this.fileListLoad2();
		alert('上传任务完成');
		location.href=location.href;
	},
	addFolder:function()
	{
		DvWnd.open('新建文件夹','?action=addfolder&_i='+Math.random(),500,90);
	},
	reName:function(fileurl,isfile)
	{
		var arr=fileurl.split("/");
		fileurl=arr[arr.length-1];
		DvWnd.open('文件重命名','?action=rename&fileurl='+fileurl+'&_i='+Math.random(),500,90);
	},
	renameform:function(form)
	{
		var oldname=form.oldname;
		var newname=form.newname;
		if(newname.value==""){alert("请输入新的文件名！");newname.focus();return false;}
		if(newname.value==oldname.value){alert("名称未作任何改动！");newname.focus();return false;}
		if(!safenames(newname.value)){alert("非法的文件名！");newname.focus();return false;}
		var sec=ajax("?action=renamedo&oldname="+oldname.value+"&newname="+formatname(newname.value));
		if(sec.indexOf("{ok}")>=0)
		{
			this.fileListLoad2();
			alert("操作成功！");
			DvWnd.close();
		}else if(sec.indexOf("{err}")>=0){
			alert(sec.split("{err}")[1]);
			newname.focus();
			return false;
		}else{
			alert(sec);
			newname.focus();
			return false;
		}
		return false;
	},
	addfolderform:function(form)
	{
		var thename=form.thename;
		if(!safenames(thename.value,false)){alert("非法的文件名！");thename.focus();return false;}
		var sec=ajax("?action=addfolderdo&thename="+formatname(thename.value));
		this.fileListLoad2();
		alert(sec);
		thename.focus();
		thename.value="";
		return false;
	},
	moveF:function()
	{
		var paths=getCheckbox("files");
		if(paths==""){alert("未选中任何文件!");return;}
		DvWnd.open('文件移动','?action=movef&paths='+paths+'&_i='+Math.random(),500,90);
	},
	movefilelist:function(dirs,id)
	{
		var obj=getId(id),temp;
		if(obj)
		{
			var file_patharrstr=getCookie("file_patharr");
			var file_setpath=getCookie("file_setpath");
			var file_path=getCookie("file_path");
			temp="<select name='moveto'>";
			temp+="<option>请选择目标文件夹</option>";
			if(file_path!=file_setpath)temp+="<option value='"+webpath+file_setpath+"'>根目录"+webpath+file_setpath+"</option>";
			temp+="<option>============子目录部分=========</option>";
			temp+=this.getoption(dirs,"|",webpath+file_setpath,"",webpath+file_path);
			temp+="<option>============缓存记录部分=======</option>";
			temp+=this.getoption(file_patharrstr,",",webpath,"",webpath+file_path);
			temp+="</select>";
			obj.innerHTML=temp;
		}
	},
	movefform:function(form)
	{
		var paths=form.paths.value;
		var moveto=form.moveto.value;
		if(moveto==""){alert("请选择目标文件夹！");return false;}
		var sec=ajax("?action=movefdo&moveto="+moveto+"&paths="+paths);
		this.fileListLoad2();
		alert(sec);
		DvWnd.close();
		return false;
	},
	dels:function()
	{
		files=getCheckbox("files");
		if(Null(files)){alert("未选中任何文件！");return;}
		if(confirm("确定删除选中文件？"))
		{
			var sec=ajax("?action=dels&files="+files);
			if(sec!=""){
				this.fileListLoad();
				sucgoto("<b>"+sec+"</b>");
			}
		}
	},
	editFile:function(fileurl)
	{
		DvWnd.open('文件编辑','?action=editFile&fileurl='+fileurl,900,460);
	},
	editsize:function(size)
	{
		var obj=getId("content");
		obj.style.font=size;
	},
	editreset:function(){return (confirm("确定重置？"));	},
	editcancel:function(){if(confirm("确定取消？")){DvWnd.close();}},
	editFileform:function(form){
		ajax_post("?action=editFiledo","fileurl="+form.fileurl.value+"&content="+urlencode(form.content.value),"editFile");
		return false;
	},
	repairfile:function()
	{
		if(confirm("如果文件数目多，此操作将花一定的时间，确定执行？")){
			ajax_post("?action=repairfile","","repairfile");
		}
	},
	fileOk:function(){
		var secid=getCookie("file_secid");
		var addmore=getCookie("file_addmore");
		var secvalue="",andstr="";
		if(!Null(addmore)&&addmore!="undefined"){
			andstr="\r\n";
			secvalue=this.getCheckbox("files",andstr);
		}
		if(secvalue=="")secvalue=getId("geturl").value;
		if(secid&&secvalue==""){DvWnd.open('返回错误','请选择文件！',200,150,0);return false;}
		secvalue=this.getTrueValue(secvalue);
		if(parent.window.right){
			var obj=parent.window.right.getId(secid);
			var obj2=parent.window.right.getId(secid+"type");
			var obj3=parent.window.right.getId(secid+"size");
			if(obj){obj.value=secvalue;}
			if(obj2){obj2.value=getId("fileType").value;}
			if(obj3){obj3.value=getId("fileSize").value;}
			Msg.close();
		}else{
			var obj=getFid(secid);
			var obj2=getFid(secid+"type");
			var obj3=getFid(secid+"size");
			if(obj){obj.value=secvalue;}
			if(obj2){obj2.value=getId("FileType").value;}
			if(obj3){obj3.value=getId("FileSize").value;}
			DvWnd.close();
		}
	},
	fileClear:function(){
		if(parent.window.right){
			var secid=getCookie("file_secid");
			var obj=parent.window.right.getId(secid);
			if(obj){obj.value=""};
			Msg.close();
			return;
		}else{
			var obj=getFid(secid);
			if(obj){obj.value=""};
			DvWnd.close();
		}
	},
	getCheckbox:function(thename,andstr){
		var checkboxArr=getTag("input");
		var funstr="",funstr2="";
		if(Null(andstr))andstr="\r\n";
		for(i=0;i<checkboxArr.length;i++)
		{
			if(checkboxArr[i].type=="checkbox")
			{
				if(checkboxArr[i].name==thename)
				{
					if(funstr!="")funstr2=andstr;
					if(checkboxArr[i].checked&&checkboxArr[i].className!="文件夹"){
						funstr+=funstr2+this.getTrueValue(checkboxArr[i].value);
					}
				}
			}
		}
		return funstr;
	},
	getTrueValue:function(value)
	{
		var file_setpath=getCookie("file_setpath");
		var file_curpath=getCookie("file_curpath");
		
		file_setpath = file_setpath.replace(file_curpath, "");
		
		
		var path=webpath+file_setpath;
		
		
		if(path=="/"){
			return trim(value,"/");
		}else{
			return value.replace(path,"");
		}
	},
	reSort:function(obj,i)
	{
		var file_orderasc=formatnum(getCookie("file_orderasc"),1);
		var newsortid=(file_orderasc==1)?0:1;
		setCookie("file_orderasc",newsortid);
		setCookie("file_orderby",i);
		F.fileListLoad();
	}
}

function ajax_postrepairfile(sec)
{
	if(sec.indexOf("{ok}")>=0)
	{
		F.fileListLoad();
		sucgoto("<b>恭喜，操作成功！</b>");
	}
}

function ajax_posteditFile(sec)
{
	if(sec.indexOf("{ok}")>=0)
	{
		alert("文件编成功！");
		DvWnd.close();
	}else{
		alet("未知错误，文件编辑失败！");
		DvWnd.close();
	}
}