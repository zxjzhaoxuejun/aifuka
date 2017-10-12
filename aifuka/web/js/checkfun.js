//* formcheck calss author:lyl */
// * 表单验证类
// **/
/* validation calss author:lyl */
/*
字符串验证函数
validation(pattern) //根据模式验证pattern为正则表达式
isChinese //验证中文简体
isFloat //验证浮点数
isNumber //验证纯数字
isInt	//判断为整形字符串
isLetters //纯字母
isGroup //验证字符数字下划线组合
isEmail //验证邮件地址
isIPCode //验证身份证号码[18位]
isMobile //验证手机号码
isPhone //验证电话号码
isUrl //验证连接地址
isIP  //验证ip地址
isPostcode //验证邮政编码
isQQ  //验证QQ号码
*/
(function (s) {
	s.validation = function (pattern) {
		var strReg=pattern;
		return strReg.test(this);
	};	   
	s.isChinese = function () {
		var strReg=/^[\u4e00-\u9fa5]+$/;
		return strReg.test(this);
	};
	s.isFloat = function () {
		var strReg=/^[+-]?[0-9]+[.]?[0-9]+$/;
		return strReg.test(this);
	};
	s.isNumber = function () {
		var strReg=/^[1-9]*[0-9]*$/;
		return strReg.test(this);
	};
	s.isLetters = function () {
		var strReg=/^[A-Za-z]+$/;
		return strReg.test(this);
	};
	s.isGroup = function () {
		var strReg=/^[a-zA-Z0-9_]*$/;
		return strReg.test(this);
	};
	s.isEmail = function () {
		var strReg=/^\w+([-+.]\w+)*@(\w+([-.]\w+)*\.)+([a-zA-Z]+)+$/;
		return strReg.test(this);
	};
	s.isIPCode = function () {
		var strReg=/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9Xx])$/;
		return strReg.test(this);
	};
	s.isInt = function () {
		var strReg=/^(-){0,1}[1-9]{1}\d+$/;
		return strReg.test(this);
	};
	s.isMobile = function () {
		var strReg=/^(13|15|18)\d{9}$/;
		return strReg.test(this);
	};
	s.isPhone = function () {
		var strReg=/^(86)?(-)?(0\d{2,3})?(-)?(\d{7,8})(-)?(\d{3,5})?$/;
		return strReg.test(this);
	};
	s.isUrl = function () {
		var strReg=/^(http|https|ftp):(\/\/|\\\\)(([\w\/\\\+\-~`@:%])+\.)+([\w\/\\\.\=\?\+\-~`@:!%#]|(&)|&)+$/;
		return strReg.test(this);
	};
	s.isPostcode = function () {
		var strReg=/^[0-9]{6}$/;
		return strReg.test(this);
	};
	s.isQQ = function () {
		var strReg=/^[1-9]\d{4,8}$/;
		return strReg.test(this);
	};
	s.isIP = function () {
		var strReg=/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/;
		return strReg.test(this);
	};
	
	s.author=function()
	{
		return "lyl[long]";
	};
	s.trim = function()
	{
   		return this.replace(/(^\s*)|(\s*$)/g, "");
	}
	s.isTime=function()
	{
		var strReg = /^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}$/;
		return strReg.test(this);
	}
})(String.prototype);

var longerror=function(obj,msg)
{
	this.obj=obj;
	this.msg=msg;
};

var Formcheck = function(name)
{
  this.formName = name;
  this.errMsg = new Array();
  /* *
  * 检查用户是否输入了内容
  *
  * @param :  controlId   表单元素的ID
  * @param :  msg         错误提示信息
  */
  this.required = function(controlId, msg)
  {
    var obj = document.forms[this.formName].elements[controlId];
	var objp=obj.parentNode;
	if(objp.lastChild.nodeName=='SPAN')
	{
		objp.removeChild(objp.lastChild);
	}
    if (typeof(obj) == "undefined" || obj.value.trim() == "")
    {
      this.addErrorMsg(msg,obj);
    }
  }
  /* *
  * 检查用户输入的字符串长度是否符合
  *
  * @param :  controlId   表单元素的ID
  * @param :  fun     调用方法
  * @param :  required    是否必须
  * @param :  msg    错误消息
  */
  this.isAnything = function(controlId,fun,required,msg)
  {
    var obj = document.forms[this.formName].elements[controlId];
    obj.value = obj.value.trim();
	var objp=obj.parentNode;
	if(objp.lastChild.nodeName=='SPAN')
	{
		objp.removeChild(objp.lastChild);
	}
    if ( ! required && obj.value == '')
    {
      return;
    }
    if (!(obj.value != ''&&eval('obj.value.'+fun+'()')))
    {
      this.addErrorMsg(msg,obj);
    }
  }
  /* *
  * 检查用户输入的字符串长度是否符合
  *
  * @param :  controlId   表单元素的ID
  * @param :  fun     调用正则表达式
  * @param :  required    是否必须
  * @param :  msg    错误消息
  */
  this.isAnyPattern = function(controlId,fun,required,msg)
  {
    var obj =document.forms[this.formName].elements[controlId];
    obj.value = obj.value.trim();
	var objp=obj.parentNode;
	if(objp.lastChild.nodeName=='SPAN')
	{
		objp.removeChild(objp.lastChild);
	}
    if ( ! required && obj.value == '')
    {
      return;
    }
    if (!(obj.value != ''&&eval('obj.value.validation('+fun+')')))
    {
      this.addErrorMsg(msg,obj);
    }
  }
  /* *
  * 检查用户输入的字符串长度是否符合
  *
  * @param :  controlId   表单元素的ID
  * @param :  msg         错误提示信息
  * @param :  required    是否必须
  */
  this.lengthArea = function(controlId,Min,Max,required,msg)
  {
    var obj = document.forms[this.formName].elements[controlId];
    obj.value = obj.value.trim();
	
	var length=obj.value.length;
	var objp=obj.parentNode;
	if(objp.lastChild.nodeName=='SPAN')
	{
		objp.removeChild(objp.lastChild);
	}

    if ( ! required && obj.value == '')
    {
      return;
    }

    if (!( Min<=length && length<=Max))
    {
      this.addErrorMsg(msg,obj);
    }
  }
	
  /*
  * 检查两个表单元素的值是否相等
  *
  * @param : fstControl   表单元素的ID
  * @param : sndControl   表单元素的ID
  * @param : msg         错误提示信息
  */
  this.eqaul = function(fstControl, sndControl, msg)
  {
    var fstObj = document.forms[this.formName].elements[fstControl];
    var sndObj = document.forms[this.formName].elements[sndControl];
	var objp=sndObj.parentNode;
	if(objp.lastChild.nodeName=='SPAN')
	{
		objp.removeChild(objp.lastChild);
	}

    if (fstObj != null && sndObj != null)
    {
      if (fstObj.value == '' || fstObj.value != sndObj.value)
      {
        this.addErrorMsg(msg,sndObj);
      }
    }
  }
  
  this.eqauls = function(fstControl, sndControl, msg)
  {
    var fstObj = document.forms[this.formName].elements[fstControl];
    var sndObj = document.forms[this.formName].elements[sndControl];
	var objp=sndObj.parentNode;
	if(objp.lastChild.nodeName=='SPAN')
	{
		objp.removeChild(objp.lastChild);
	}
    if (fstObj.value != sndObj.value)
    {
        this.addErrorMsg(msg,sndObj);
    }

  }

  /* *
  * 检查前一个表单元素是否大于后一个表单元素
  *
  * @param : fstControl   表单元素的ID
  * @param : sndControl	  表单元素的ID
  * @param : msg			    错误提示信息
  */
  this.gt = function(fstControl, sndControl, msg)
  {
    var fstObj = document.forms[this.formName].elements[fstControl];
    var sndObj = document.forms[this.formName].elements[sndControl];

    if (fstObj != null && sndObj != null) {
      if (fstObj.value.isNumber()&& sndObj.value.isNumber()) {
        var v1 = parseFloat(fstObj.value) + 0;
        var v2 = parseFloat(sndObj.value) + 0;
      } else {
        var v1 = fstObj.value;
        var v2 = sndObj.value;
      }
	var objp=sndObj.parentNode;
	if(objp.lastChild.nodeName=='SPAN')
	{
		objp.removeChild(objp.lastChild);
	}
      if (v1 <= v2) this.addErrorMsg(msg,obj);
    }
  }
  /* *
  * 检查输入的内容是否是为空
  *
  * @param :  controlId   表单元素的ID
  * @param :  msg         错误提示信息
  * @param :  required    是否必须
  */
  this.isNullOption = function(controlId, msg)
  {
    var obj = document.forms[this.formName].elements[controlId];

    obj.value = obj.value.trim();
	var objp=obj.parentNode;
	if(objp.lastChild.nodeName=='SPAN')
	{
		objp.removeChild(objp.lastChild);
	}

    if (obj.value.length > 0 )
    {
      return;
    }
    else
    {
      this.addErrorMsg(msg,obj);
    }
  }
  /* *
  * 检查前一个表单元素是否小于后一个表单元素(日期判断)
  *
  * @param : controlIdStart   表单元素的ID
  * @param : controlIdEnd	  表单元素的ID
  * @param : msg              错误提示信息
  */
  this.islt = function(controlIdStart, controlIdEnd, msg)
  {
    var start = document.forms[this.formName].elements[controlIdStart];
    var end = document.forms[this.formName].elements[controlIdEnd];
    start.value =start.value.trim();
    end.value = end.value.trim();
	var objp=end.parentNode;
	if(objp.lastChild.nodeName=='SPAN')
	{
		objp.removeChild(objp.lastChild);
	}
    if(start.value <= end.value)
    {
      return;
    }
    else
    {
      this.addErrorMsg(msg,obj);
    }
  }

  /* *
  * 检查指定的checkbox是否选定
  *
  * @param :  controlId   表单元素的name
  * @param :  msg         错误提示信息
  */
  this.requiredCheckbox = function(chk, msg)
  {
    var obj = document.forms[this.formName].elements[controlId];
    var checked = false;

    for (var i = 0; i < objects.length; i ++ )
    {
      if (objects[i].type.toLowerCase() != "checkbox") continue;
      if (objects[i].checked)
      {
        checked = true;
        break;
      }
    }

    if ( ! checked) this.addErrorMsg(msg,obj);
  }

  this.passed = function(n)
  {
	if(typeof n == 'undefined')
		n=0;
    if (this.errMsg.length > 0)
    {
      var msg = "";
      for (i = 0; i < this.errMsg.length; i ++ )
      {
		  if(n==1)
		  {
			  if(parent.Object.alert)
			  {
				msg += "- " + this.errMsg[i].msg + "<br/>";
			  }else{
				msg += "- " + this.errMsg[i].msg + "\n";
			  }
		  }else
		  {
			  var obj=this.errMsg[i].obj.parentNode;
			  var div;
			  div = document.createElement("span");
			  div.innerHTML="　- "+this.errMsg[i].msg;
			  div.style.color="red";
			  obj.appendChild(div);
		  }
      }
	  if(n==1)
	  {
		  if(parent.Object.alert)
		  {
			 parent.Object.alert(msg);
		  }
		  else
		  {
			 alert(msg);
		  }
	  }
      return false;
    }
    else
    {
      return true;
    }
  }

  /* *
  * 增加一个错误信息
  *
  * @param :  str
  */
  this.addErrorMsg = function(str,obj)
  {
	  var err=new longerror(obj,str);
    this.errMsg.push(err);
  }
};