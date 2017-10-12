noright();
var step=1.1;	//缩放比例
var stepW=30;	//移动步长
var speed=30;
var oneImgH=116;
var nn6=document.getElementById&&!document.all;
var viewPicCs=getCookie("viewPicCs").split("{$}");
var lan=viewPicCs[0];
var addUrl=viewPicCs[1];
var nowI=formatnum(viewPicCs[2],0);
var viewPicNowSrc=viewPicCs[3];
var viewpicList=viewPicCs[4];
var dragObj=getId("dragObj");
var imgs=getId("imgs");
var test=getId("test");
var images1=getId("images1");
var images2=getId("images2");
var toUp=getId("toUp");
var toDown=getId("toDown");
var isdrag=false,x,y,oDragObj,nowSrc,i,text1,text2,text3,text4,text5,text6,text7,text8,text9,text10,text11,text12,text13,text14,toGuidCenter2;
if(lan=="zh_cn"){document.title=company;text1="向上",text2="向右",text3="向下",text4="向左",text5="还原",text6="放大",text7="缩小",text8="关闭",text9="滚动鼠标可放大或缩小",text10="图片尺寸：",text11="图片大小：",text12="图片格式：",text13="缩放比例：",text14="当前尺寸：";}else{document.title="Pic View";text1="Up",text2="Right",text3="Down",text4="Left",text5="Reset",text6="Zoom in",text7="Zoom out",text8="Close",text9="Rolling the mouse to zoom in or out",text10="Picture Size：",text11="File size：",text12="Image format：",text13="Scaling：",text14="Current Size：";}
getId("up").alt=text1;
getId("right").alt=text2;
getId("down").alt=text3;
getId("left").alt=text4;
getId("re").alt=text5;
getId("big").alt=text6;
getId("smll").alt=text7;
getId("close").alt=text8;
getId("picSxt1").innerHTML=text10;
getId("picSxt2").innerHTML=text11;
getId("picSxt3").innerHTML=text12;
getId("picSxt4").innerHTML=text13;
getId("picSxt5").innerHTML=text14;
if(ie){getId("description").innerHTML=text9;}
var picArr=viewpicList.split(",");
//*************************************************************************************************************//
if(picArr.length>1){
	var imgStr="<table>";
	if(Null(viewPicNowSrc)){nowSrc=addUrl+picArr[0];}else{nowSrc=viewPicNowSrc;}
	for(var i=0;i<picArr.length;i++){if(picArr[i]!=""){imgStr+="<tr><td id='imgList"+i+"'><img src='loading.gif' onload='setImg(this,100,100,\""+addUrl+picArr[i]+"\")' onclick='changeImgs(this.src,"+i+");' /></td></tr>";}}
	imgStr+="</table>";
	imgs.innerHTML=imgStr;
}else{
	if(Null(viewPicNowSrc)){nowSrc=addUrl+viewpicList;}else{nowSrc=viewPicNowSrc;}
	toUp.style.display="none";
	toDown.style.display="none";
}
//*************************************************************************************************************//
var defL,defT,imgGuidLoop;
window.onload=function(){
	formatUpDown();
	formatHeight();
	changeImgs(nowSrc,nowI);
	setPicSx();
	var img=new Image();
	img.onload=function(){
		var pW=screen.width-140;
		var pH=screen.height-95;
		defL=(pW-images2.width)/2;
		defT=(pH-images2.height)/2;
		dragObj.style.left=defL+"px";
		dragObj.style.top=defT+"px";
	}
	img.src=images2.src;
}
//*************************************************************************************************************//
function formatHeight(){
	var pH=formatnum(clientHeight(),600);
	var cH=formatnum(getId("control").offsetHeight,160);
	if(picArr.length>1){imgs.style.height=parseInt(pH-cH-110)+"px";}else{imgs.style.height=parseInt(pH-cH)+"px";}
}
function formatUpDown(){
	formatHeight();
	var imgT=imgs.scrollTop;
	if(imgT==0)toUp.src="zoom_up_no.gif";
	if(imgT==imgs.scrollHeight-imgs.clientHeight||imgs.scrollHeight<=imgs.clientHeight)toDown.src="zoom_down_no.gif";
	if(imgT>0)toUp.src="zoom_up_def.gif";
	if(imgs.scrollHeight>imgs.clientHeight&&imgs.scrollHeight-imgs.clientHeight!=imgT)toDown.src="zoom_down_def.gif";
}

function imgGuid(obj,cases){
	obj.src=obj.src.replace("def.gif","on.gif");
	obj.onmouseout=function(){formatUpDown();}
	obj.onmousedown=function(){
		clearInterval(toGuidCenter2);
		var imgGuidLoop;
		if(cases==1){imgGuidLoop=setInterval(imgGuidUp,10);}else{imgGuidLoop=setInterval(imgGuidDown,10);}
		obj.onmouseup=function(){clearInterval(imgGuidLoop);}
	}	
}
function imgGuidUp(){
	switch(imgs.scrollTop){
		case 0:	toUp.src="zoom_up_no.gif";clearInterval(imgGuidLoop);break;
		default:toDown.src=toDown.src.replace("no.gif","def.gif");toUp.src=toUp.src.replace("no.gif","on.gif");imgs.scrollTop-=30;
	}
}
function imgGuidDown()
{
	if(imgs.scrollHeight-imgs.clientHeight==imgs.scrollTop){
		toDown.src="zoom_down_no.gif";
		clearInterval(imgGuidLoop);
	}else{
		if(imgs.scrollTop>0){toUp.src="zoom_up_def.gif";}
		imgs.scrollTop+=30
	}
}
function toGuidCenter(n){
	var cT=formatnum(oneImgH*n-(imgs.clientHeight-oneImgH)/2,0);
	var speedC=10;
	function toGuidCenterLoop(){
		if(cT<0)cT=0;
		if(cT>imgs.scrollHeight-imgs.clientHeight)cT=imgs.scrollHeight-imgs.clientHeight;
		if(cT-imgs.scrollTop>30){
			imgs.scrollTop+=30;
		}else if(cT-imgs.scrollTop<-30){
			imgs.scrollTop-=30;
		}else{
			clearInterval(toGuidCenter2);
			imgs.scrollTop=cT;
			formatUpDown();
			return;
		}
	}
	toGuidCenter2=setInterval(toGuidCenterLoop,speedC);
}
//*************************************************************************************************************//
function changeImgs(src,n){
	if(src.indexOf("loading.gif")>0)return;
	if(Null(src)){src=addUrl+picArr[n];}	
	if(src==images1.src)return;
	var img=new Image();
	images1.src="loading.gif";
	img.onload=function(){
		images1.src=img.src;
		images2.src=img.src;
		images1.width=img.width;
		images1.height=img.height;
		images2.width=img.width;
		images2.height=img.height;
		setPicSx();
		toGuidCenter(n);
		setImgListOn(n);
	}
	img.src=src;
}
function setPicSx(){
	var picSx1=getId("picSx1");
	var picSx2=getId("picSx2");
	var picSx3=getId("picSx3");
	var picSx4=getId("picSx4");
	var picSx5=getId("picSx5");
	picSx1.innerHTML=images2.offsetWidth+"X"+images2.offsetHeight;
	if(ie){picSxt2.style.display="";picSx2.style.display="";picSx2.innerHTML=Math.round(images1.fileSize/1024*100)/100+" KB";}
	var imgSuffix=images1.src.split(".");	
	picSx3.innerHTML=imgSuffix[imgSuffix.length-1];
	picSx4.innerHTML=Math.round(images1.width/images2.offsetWidth*100)+"%";
	picSx5.innerHTML=images1.offsetWidth+"X"+images1.offsetHeight;
}
function setImgListOn(n){
	if(picArr.length>1){
		for(var i=0;i<picArr.length;i++){if(getId("imgList"+i))getId("imgList"+i).className="";}
		getId("imgList"+n).className="imgListOn";
	}
}
//*************************************************************************************************************//
images1.onmousedown=initDrag;
images1.onmouseup=new Function("isdrag=false");
document.onkeydown=function(e){e=window.event||e;var key=e.keyCode;if(key==37||key==38){preViousPic();};if(key==39||key==40){nextPic();}}
//*************************************************************************************************************//
function preViousPic(){if(nowI>0&&picArr[nowI-1]!=""){changeImgs("",nowI-1);nowI--;}}
function nextPic(){if(nowI<picArr.length-1&&picArr[nowI+1]!=""){changeImgs("",nowI+1);nowI++;}}

function initDrag(e){
	var oDragHandle=nn6?e.target:event.srcElement;
	var topElement="HTML";
	while(oDragHandle.tagName!=topElement&&oDragHandle.className!="dragAble")
	{ 
		oDragHandle=nn6?oDragHandle.parentNode:oDragHandle.parentElement;
	} 
	if(oDragHandle.className=="dragAble"){ 
		isdrag=true;
		oDragObj=oDragHandle;
		nTY=parseInt(oDragObj.style.top+0);
		y=nn6?e.clientY:event.clientY;
		nTX=parseInt(oDragObj.style.left+0);
		x=nn6?e.clientX:event.clientX;
		document.onmousemove=moveMouse;
		return false;
	} 
}

function moveMouse(e){ 
	if(isdrag){ 
		oDragObj.style.top=(nn6?nTY+e.clientY-y:nTY+event.clientY-y)+"px";
		oDragObj.style.left=(nn6?nTX+e.clientX-x:nTX+event.clientX-x)+"px";
		return false;
	} 
} 

function imgMove(obj,cases)
{
	imgMoveLoop()
	function imgMoveLoop(){
		switch(cases){
			case "down":dragObj.style.top=parseInt(dragObj.style.top)+stepW+"px";break;
			case "up":dragObj.style.top=parseInt(dragObj.style.top)-stepW+"px";break;
			case "right":dragObj.style.left=parseInt(dragObj.style.left)+stepW+"px";break;
			default:dragObj.style.left=parseInt(dragObj.style.left)-stepW+"px";
		}
	}
	var imgMove2=setInterval(imgMoveLoop,speed);
	obj.onmouseup=function(){clearInterval(imgMove2);}
}

function wheelImg(){if(getPra("no")==""){var c=event.wheelDelta;if(c>0)bigit();else smallit();}setPicSx();}

function zoomImg(obj,cases)
{
	function changeImgLoop(){
		switch(cases){
			case 1:bigit();break;
			case 0:smallit();break;
			default:realsize();
		}
		setPicSx();
	}
	if(cases==2){
		changeImgLoop();return;
	}else{
		var changeImg2=setInterval(changeImgLoop,speed);
		obj.onmouseup=function(){clearInterval(changeImg2);}
	}
}
function smallit(){var height1=images1.height;var width1=images1.width;images1.height=height1/step;images1.width=width1/step;}
function bigit(){var height1=images1.height;var width1=images1.width;images1.height=height1*step;images1.width=width1*step;}
function realsize(){images1.height=images2.height;images1.width=images2.width;dragObj.style.left=defL+"px";dragObj.style.top=defT+"px";}