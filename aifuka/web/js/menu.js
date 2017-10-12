document.all&&document.execCommand("BackgroundImageCache",false,true); 
var Sys = {
	IE : navigator.userAgent.toLowerCase().match(/msie ([\d.]+)/),
	Firefox : navigator.userAgent.toLowerCase().match(/firefox\/([\d.]+)/),
	Chrome : navigator.userAgent.toLowerCase().match(/chrome\/([\d.]+)/),
	IE6 : navigator.userAgent.toLowerCase().match(/msie ([\d.]+)/) && ([/MSIE (\d)\.0/i.exec(navigator.userAgent)][0][1] == 6)
} 
function $(Id){return document.getElementById(Id)};
function $$(p,e){return p.getElementsByTagName(e)};
function addListener(element,e,fn){ element.addEventListener?element.addEventListener(e,fn,false):element.attachEvent("on" + e,fn)};
function removeListener(element,e,fn){ element.removeEventListener?element.removeEventListener(e,fn,false):element.detachEvent("on" + e,fn)};
function create(elm,parent,fn){var element = document.createElement(elm);parent.appendChild(element);if(fn)fn(element);return element};
function getTarget(e){return e.srcElement||e.target;};
function getNext(e){ return e.relatedTarget||e.toElement;};
function getobjpos(el,left){
	var val = 0;
	while (el !=null) {
		val += el["offset" + (left? "Left": "Top")];
		el = el.offsetParent;
	}
	return val;
};
function fixEvent(event) {
	if (event) return event;
	//event = ((this.ownerDocument || this.document || this).parentWindow || window).event;
	//var scrolldoc = isChrome || isSafari ? document.body : document.documentElement;
	event = window.event;
	event.pageX = event.clientX + document.documentElement.scrollLeft;
	event.pageY = event.clientY + document.documentElement.scrollTop;
	event.target = event.srcElement;  
	event.stopPropagation = fixEvent.stopPropagation;  
	event.preventDefault = fixEvent.preventDefault;
	if(event.type == "mouseout") {
		event.relatedTarget = event.toElement;
	}else if(event.type == "mouseover") {
		event.relatedTarget = event.fromElement;
	}                                                 
	return event;
};
fixEvent.preventDefault = function() { this.returnValue = false;};
fixEvent.stopPropagation = function() { this.cancelBubble = true;};
var CurrentStyle = function(element){ return element.currentStyle || document.defaultView.getComputedStyle(element, null);};
var Bind = function(object, fun,args) {return function() { return fun.apply(object,args||[]);}}
var BindAsEventListener = function(object, fun,arg) {
	return function(event) {return fun.apply(object, [(event || window.event)].concat(arg||[]));}
};
var Extend = function(){
	var args = arguments;
	if(!args[1])args = [this,args[0]];                                       
	for(var property in args[1])args[0][property] = args[1][property];         
	return args[0];
};
 
var Class = function(properties){
	var _class = function(){return (arguments[0] !== null && this.initialize && typeof(this.initialize) == 'function') ? this.initialize.apply(this, arguments) : this;};
	_class.prototype = properties;
	return _class;
};

var Menu = new Class({
	options:{ 
		h_style     : "menuTitle",
		c_style     : "menuBody",				//列表容器样式
		l_style     : "menuList menuList",			//列表样式
		l_style1    : "menuList menuListover",		//鼠标放上去后的列表样式
		l_bg_style  : "menuList menuBg",			//有子菜单时候的背景
		l_bg_style1 : "menuList menuBgover",      	//有子菜单时鼠标放上去的样式
		l_disabled  : "menuList menuDisabled",	//设置不能选中时的样式
		l_hr        : "hr",           			//分行符的样式
		direction : "X"
	},
	initialize : function(container,data,elm,options){
		this.container = container;  //设置容器
		this.data      = data;       //数据
		this.elm       = elm;       //设置list是什么元素 可以是div 也可以是a 也可以是其他
		this.stack     = [];        //元素堆栈 看哪些元素已经存在了
		this.obj       = null;      //记录哪一项被选种过
		this.lists     = [];        //为查找元素而记录元素 
		Extend(this.options,options||{});
		Extend(this,this.options);
		var elm = this.container.getElementsByTagName(this.elm);
		for(var i=0,l=elm.length;i<l;i++){
			if(this.data[i].txt == i){
				addListener(elm[i],'mouseover',BindAsEventListener(this,this.Title,[this.data[i],elm[i]]));
				addListener(elm[i],'mouseout',BindAsEventListener(this,this.Hide));
			}
		}
	},
	Title : function(e,d,obj){
		this.obj = obj;
		obj.className = this.h_style;
		if(!d.menu)return;
		var container = this.Makebody(d,obj);
		if(this.direction=="X"){
			container.style.left = getobjpos(obj,1) + "px";
			container.style.top  = obj.offsetHeight + getobjpos(obj,0) + "px";
		}else{
			container.style.left = getobjpos(obj,1)+obj.offsetWidth + "px"
			container.style.top  = getobjpos(obj,0) + "px";			
		}
		
	},
	Makemenu : function(e,d,obj){
		this.mouseover(obj);
		if(!d.menu)return;
		var container = this.Makebody(d,obj);
		container.style.left = this.pos(obj,container,"X")?(getobjpos(obj,1)+ obj.offsetWidth+ "px"):(getobjpos(obj,1) - container.offsetWidth + 2 + "px");
		container.style.top = this.pos(obj,container,"Y")?(getobjpos(obj,0) + "px"):(getobjpos(obj,0) - container.offsetHeight+2+20+"px");
	},
	Makebody : function(d,obj){
		if(!obj.getAttribute('container')){
			var _self = this;		
			var container = create('div',document.body,function(o){o.className = _self.c_style;});
			container.onmouseup = function(e){fixEvent(e).stopPropagation();fixEvent(e).preventDefault();}	
		}else{
			var container = this.lists[parseInt(obj.getAttribute('container'))-1];
			container.style.display = "block";
			this.resetstyle(container,d);
		}	
		this.stack.push(container);
		if(!obj.getAttribute('container')){
			addListener(container,"mouseout",BindAsEventListener(this,this.Hide));
			this.lists.push(container)
			obj.setAttribute("container",this.lists.length);
			var Item  = null, _self = this;
			for(var i = 0,l = d.menu.length;i<l;i++)
			{
				(function(i){Item = create("div",container,function(o){
					if(d.menu[i].ico){o.innerHTML = "<img src = '"+d.menu[i].ico+"'>";}
					with(o){innerHTML = innerHTML+d.menu[i].txt; 
						setAttribute("menu",d.menu[i].menu?"true":"false");
						className = d.menu[i].menu?_self.l_bg_style:_self.l_style;
					}	
				})})(i);
				if(d.menu[i].group)create("span",container,function(o){o.className = _self.l_hr});
				if(!d.menu[i].exist){Item.className = _self.l_disabled;continue;};
				addListener(Item,"mouseover",BindAsEventListener(this,this.Makemenu,[d.menu[i],Item]));
				addListener(Item,"mouseout",BindAsEventListener(this,this.Hide,[Item]));
				Item.oncontextmenu = function(e){fixEvent(e).stopPropagation();fixEvent(e).preventDefault();}
				Item.onmouseup = function(e){fixEvent(e).stopPropagation();fixEvent(e).preventDefault();}
			}
		}else{
			this.lists[parseInt(obj.getAttribute('container'))-1].style.display = "block";
		}
		return container;	
	},
	Hide : function(e,o){
		o&&this.contains(o.parentNode,getNext(e))&&this.mouseout(e,o);
		var exist = false; 
		for(var index = 0,l = this.stack.length;index<l;index++){
			if(this.contains(this.stack[index],getNext(e))){exist = true;break;}	
		};
		if(exist){
			for(var i = index + 1;i<this.stack.length;i++)
			{this.stack[i].style.display = "none";}	
			this.stack.length = index + 1;
		}else{
			for(var i = 0;i<this.stack.length;i++)
			{this.stack[i].style.display = "none";}
			this.stack.length = 0;
		}
		if(this.stack.length == 0)this.obj.className = "";
	},
	mouseover : function(obj){
		for(var i=0,l=$$(obj.parentNode,this.elm).length;i<l;i++)
		{
			if($$(obj.parentNode,this.elm)[i].className ==this.l_disabled)continue;
			$$(obj.parentNode,this.elm)[i].className = $$(obj.parentNode,this.elm)[i].getAttribute("menu")=="true"?this.l_bg_style:this.l_style;
		}
		if(obj.className == this.l_disabled)return;
		obj.className = obj.getAttribute("menu")=="true"?this.l_bg_style1:this.l_style1
	},
	mouseout : function(e,obj){
		if(obj.className == this.l_disabled)return;
		obj.className = obj.getAttribute("menu")=="true"?this.l_bg_style:this.l_style;
	},	
    contains: function(parent,child){

		if(!parent||!child)return false;
        if(parent == child) return true;
		return Sys.IE?parent.contains(child):(parent.compareDocumentPosition(child)==20)?true:false;
		return false;		
    },
	Bubble : function(e){ fixEvent(e).stopPropagation();},
	Stopdefault : function(e){ fixEvent(e).preventDefault();},
	pos : function(o,w,arg){
		if(arg=="X"){
			var xx = Sys.Chrome?document.body.clientWidth:document.documentElement.clientWidth;
			return (xx -getobjpos(o,1)-o.offsetWidth-5)>w.offsetWidth;
		}else{
			var xx = Sys.Chrome?document.body:document.documentElement;
			return (xx.clientHeight -getobjpos(o,0)+xx.scrollTop+o.offsetHeight-5)>w.offsetHeight;
		}
	},
	resetstyle : function(c,d){
		for(var i=0,l=$$(c,this.elm).length;i<l;i++)
		{
			$$(c,this.elm)[i].className = this.l_style; 
			if(d.menu[i].menu)$$(c,this.elm)[i].className  = this.l_bg_style;
			if(!d.menu[i].exist)$$(c,this.elm)[i].className = this.l_disabled;
		}
	}
});