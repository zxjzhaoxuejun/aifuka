var HHH=new Object();
(function (s) {	
	//s.width=300;
	//s.height=100;
	s.content="";
	s.flag=false;
	s.title="系统提示";
	var dialog=null;
	s.alert=function(str,w,h,title,bgiframe){
		if(str){s.content=str;}
		if(w){this.width=w;}
		if(h){this.height=h;}
		s.title=title||s.title;
		dialog = new ZxjayDialog()
		dialog.ImgZIndex = 107;
		dialog.DialogZIndex = 108;
		dialog.Text = this.title;
		dialog.Content = s.content;
		dialog.Width = this.width;
		if(bgiframe){
			dialog.MaskImage.style.backgroundColor="#999999";
			dialog.MaskImage.style.filter = "alpha(opacity=10)";
			dialog.MaskImage.style.opacity="0.1";
		}
		dialog.bg_t=1;
		dialog.Show(1);
	};
	s.confirm=function(content,fun){
		if(content){s.content=content;}
		dialog = new ZxjayDialog()
		dialog.ImgZIndex = 107;
		dialog.DialogZIndex = 108;
		dialog.Text = this.title;
		dialog.Content = s.content;
		dialog.Width = this.width;
		dialog.icon=dialog.Icon.confirm_icon;
		dialog.bg_t=1;
		dialog.OK=function(){
			dialog.Close();
			if(fun){eval(fun+"();")}
		}
		return dialog.Show(2);
	};
	s.text=function(str,w,h,title,bgiframe){
		s.title=title||s.title;
		if(str){s.content=str;}
		if(w){this.width=w;}
		if(h){this.height=h;}
		dialog = new ZxjayDialog()
		dialog.ImgZIndex = 107;
		dialog.DialogZIndex = 108;
		dialog.Text = this.title;
		dialog.Content = s.content;
		dialog.Width = this.width;
		dialog.Height = this.height;
		dialog.bg_t=0;
		if(bgiframe){
			dialog.MaskImage.style.backgroundColor="#999999";
			dialog.MaskImage.style.filter = "alpha(opacity=10)";
			dialog.MaskImage.style.opacity="0.1";
		}
		dialog.Show(0);
	};
	s.url=function(url,w,h,title,bgiframe){
		dialog = new ZxjayDialog()
		dialog.ImgZIndex = 107;
		dialog.DialogZIndex = 108;
		dialog.Text = title||this.title;
		if(w){this.width=w;}
		if(h){this.height=h;}
		if(bgiframe){
			dialog.MaskImage.style.backgroundColor="#999999";
			dialog.MaskImage.style.filter = "alpha(opacity=10)";
			dialog.MaskImage.style.opacity="0.1";
		}
		dialog.Content = "<iframe id='ifmdg' width='100%' height='"+this.height+"' src='"+url+"' scrolling='auto' frameborder='0'></iframe> ";
		dialog.Width = this.width;
		dialog.bg_t=0;
		dialog.Show(0);	
	};
	s.close=function(){
		if(!dialog)dialog=new ZxjayDialog()
		dialog.Close();	
	};
})(Object.prototype)

var ZxjayDialog = function()
{
    this.flag=false;
    var me = this;
    this.MaskImage = null;
    this.Content = null;
    this.Text = null;
    this.Container = null;
    this.ImagePath = webpath+"dialog/";
    this.posX = 0;
    this.posY = 0;
    this.IsDown = false;
    this.Width = 300;
    this.Height = 0;
    this.DocVisibleWidth = 0;
    this.DocVisibleHeight = 0;
    this.DocMaxWidth = 0;
    this.DocMaxHeight = 0;
    this.ImgZIndex = 101;
    this.DialogZIndex = 102;
    this.ButtonOK = null;
    this.ButtonCancel = null;
    this.ButtonRetry = null;
	this.bg='<table width="100%"><tr><td width="60" align="center"><img src="{img}"/></td><td>{content}</td></tr></table>';
	this.bg_t=0;
    this.Icon =
    {
        Close_Normal: this.ImagePath + "close_normal.gif",
        Close_Higthlight: this.ImagePath + "close_highlight.gif",
        Dialog_Icon: this.ImagePath + "icon.gif",
		alert_icon: this.ImagePath + "icon_alert.gif",
		confirm_icon: this.ImagePath + "icon_query.gif"
    };
	this.icon=this.Icon.alert_icon;
    this.Remove = function()
    {
        document.body.removeChild(this.Container);
        document.body.removeChild(this.MaskImage);
    }

    this.OK = function()
    {
        me.Close();
        flag=true;
        return true;
    }

    this.Close = function()
    {
        this.Hide();
        flag=false;
        return false;
    }

    this.Hide = function()
    {
        this.Container.style.display = "none";
        this.MaskImage.style.display = "none";
    }
	this.scolltop=function()
	{
		var yScroll;
		if (self.pageYOffset) {
			yScroll = self.pageYOffset;
			//xScroll = self.pageXOffset;
		} else if (document.documentElement && document.documentElement.scrollTop){
			yScroll = document.documentElement.scrollTop;
		} else if (document.body) {
			yScroll = document.body.scrollTop;
		}
		//arrayPageScroll = new Array('',yScroll)
		return yScroll; 
	}
    this.MaskImage = document.createElement("div");
    this.MaskImage.style.position = "absolute";
    this.MaskImage.style.left = 0;
    this.MaskImage.style.top = 0;
	this.MaskImage.style.backgroundColor="#000000";
	this.MaskImage.style.filter = "alpha(opacity=50)";
	this.MaskImage.style.opacity="0.5";
    document.body.appendChild(this.MaskImage);


    this.Container = document.createElement("div");
    this.Container.style.position = "absolute";
    document.body.appendChild(this.Container);

    this.borderLine = document.createElement("div");
    this.borderLine.className = "zxjayDialog_border";
    this.Container.appendChild(this.borderLine);

    this.titleBar = document.createElement("div");
    this.titleBar.className = "zxjayDialog_titleBar";
    this.borderLine.appendChild(this.titleBar);

    var dialogIco = document.createElement("img");
    dialogIco.className = "zxjayDialog_ico";
	dialogIco.src =this.Icon.Dialog_Icon ;
    this.titleBar.appendChild(dialogIco);

    this.titleText = document.createElement("div");
    this.titleText.className = "zxjayDialog_titleText";
    this.titleBar.appendChild(this.titleText);
    
    this.titleCloseButton = document.createElement("img");
    this.titleCloseButton.className = "zxjayDialog_titleCloseButton";
    this.titleCloseButton.title = "关闭";
    this.titleCloseButton.src = this.Icon.Close_Normal;
    this.titleBar.appendChild(this.titleCloseButton);

    this.dialogContent = document.createElement("div");
    this.dialogContent.className = "zxjayDialog_content";
    this.borderLine.appendChild(this.dialogContent);

    this.buttonPanel = document.createElement("div");
    this.buttonPanel.className = "zxjayDialog_buttonPanel";
    this.borderLine.appendChild(this.buttonPanel);

    this.ButtonOK = document.createElement("input");
	this.ButtonOK.type = "button";
    this.ButtonOK.value = "确 定";
    this.ButtonOK.className = "zxjayDialog_commandButton";
    this.buttonPanel.appendChild(this.ButtonOK);

    this.ButtonRetry = document.createElement("input");
	this.ButtonRetry.type = "button";
    this.ButtonRetry.value = "重 试";
    this.ButtonRetry.className = "zxjayDialog_commandButton";
    this.ButtonRetry.style.diaplay = "none";
    this.buttonPanel.appendChild(this.ButtonRetry);

    this.ButtonCancel = document.createElement("input");
	this.ButtonCancel.type = "button";
    this.ButtonCancel.value = "取 消";
    this.ButtonCancel.className = "zxjayDialog_commandButton";
    this.buttonPanel.appendChild(this.ButtonCancel);

    this.Hide();

    this.GetSize = function()
    {
        var cmpMd = document.compatMode == 'CSS1Compat';

        this.MaskImage.style.zIndex = this.ImgZIndex;
		if(this.bg_t==0)
		{
			this.dialogContent.innerHTML = this.Content;
		}else{
        	this.dialogContent.innerHTML = this.bg.replace("{img}",this.icon).replace("{content}",this.Content);
		}
        this.Container.style.zIndex = this.DialogZIndex;
        this.Container.style.width = this.Width + "px";
        this.titleText.innerHTML = this.Text;

        this.Height = Math.max(this.Container.offsetHeight, this.Container.clientHeight);

        if (cmpMd)
        {
            this.DocVisibleWidth = document.documentElement.clientWidth;
            this.DocVisibleHeight = document.documentElement.clientHeight;
        }else{
            this.DocVisibleWidth = document.body.clientWidth;
            this.DocVisibleHeight = document.body.clientHeight;
        }

        if (this.DocVisibleWidth < 10 || this.DocVisibleHeight < 10)
        {
            this.DocVisibleWidth = document.body.clientWidth;
            this.DocVisibleHeight = document.body.clientHeight;
        }

        if (cmpMd)
        {
            this.DocMaxWidth = "100%"; //Math.max(document.documentElement.clientWidth, document.documentElement.scrollWidth);
            this.DocMaxHeight =window.screen.height ;//document.documentElement.scrollHeight;
        }else{
            this.DocMaxWidth = "100%";
            this.DocMaxHeight = window.screen.height;
        }
    }

    this.SetProperty = function()
    {
        this.GetSize();
        this.MaskImage.style.width = this.DocMaxWidth + "";
        this.MaskImage.style.height = this.DocMaxHeight + "px";
        this.Container.style.left = (this.DocVisibleWidth - this.Width) / 2 + "px";
        if (this.DocVisibleWidth < this.Width){this.Container.style.left = "0px";}
        this.Container.style.top = ((this.DocVisibleHeight - this.Height) / 2+this.scolltop()) + "px";
        if (this.DocVisibleHeight < this.Height){this.Container.style.top = "0px";}
    }

    this.RegisteEvent = function()
    {
        this.titleCloseButton.onmouseover = function(){this.src = me.Icon.Close_Higthlight;}
        this.titleCloseButton.onmouseout = function(){this.src = me.Icon.Close_Normal;}
        this.titleCloseButton.onclick = function(){return me.Close();}
        this.ButtonOK.onclick = function(){return me.OK();}
        this.ButtonCancel.onclick = function(){return me.Close();}
        this.ButtonRetry.onclick = function(){me.Retry();}
        this.titleBar.onmousedown = function(e)
        {
            if (e == null) e = window.event;
            me.posX = e.clientX - parseInt(me.Container.style.left);
            me.posY = e.clientY - parseInt(me.Container.style.top);
            me.IsDown = true;
            return false;
        }

        this.titleBar.onselectstart = this.titleBar.ondrag = function(){return false;}

        this.ReleaseCapture = function(){me.IsDown = false;}

        this.MoveDialog = function(e)
        {
            if (me.IsDown)
            {
                if (!e) e = window.event;
                me.Container.style.left = (e.clientX - me.posX) + "px";
                me.Container.style.top = (e.clientY - me.posY) + "px";
                if (parseInt(me.Container.style.top) < 2){me.Container.style.top = "2px";}
                if (parseInt(me.Container.style.left) < 2){ me.Container.style.left = "2px";}
                if (e.clientY < 2 || e.clientX < 2){me.IsDown = false;}
                if (parseInt(me.Container.style.left) > me.DocMaxWidth - me.Width - 2){me.Container.style.left = me.DocMaxWidth - me.Width - 2 + "px";}
                if (parseInt(me.Container.style.top) > me.DocMaxHeight - me.Height - 2){me.Container.style.top = me.DocMaxHeight - me.Height - 2 + "px";}
            }
        }

        if (document.attachEvent)
        {
            document.attachEvent("onmousemove", this.MoveDialog);
            document.attachEvent("onmouseup", this.ReleaseCapture);
        }else if (document.addEventListener){
            document.addEventListener("mousemove", this.MoveDialog, false);
            document.addEventListener("mouseup", this.ReleaseCapture, false);
        }
    }

    this.Show = function(btnCount)
    {
        switch (btnCount)
        {
            case 0:                
                this.ButtonRetry.style.display = "none";
                this.ButtonCancel.style.display = "none";
                this.ButtonOK.style.display = "none";
                break;                
            case 1:
                this.ButtonRetry.style.display = "none";
                this.ButtonCancel.style.display = "none";
                break;                
            case 2:
                this.ButtonRetry.style.display = "none";
                this.ButtonCancel.style.display = "";
                break;
            case 3:
                this.ButtonRetry.style.display = "";
                this.ButtonCancel.style.display = "";
                break;
            default:
                this.ButtonRetry.style.display = "none";
                this.ButtonCancel.style.display = "";
                break;
        }
        this.MaskImage.style.display = "";
        this.Container.style.display = "";
        this.SetProperty();
        this.RegisteEvent();
        return this.flag;
	}
}