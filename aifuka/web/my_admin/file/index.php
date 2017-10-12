<?php
/********************************************************
时间：2009-9-10 ~ 2009-9-25

程序：胡思文
********************************************************/
include("../../include/inc.php");
include(incpath."funlist.php");

class admin_file extends alzCms
{
	function def(){
		global $cfg;
		$cfg["filepath"]=getcookies("file_path");		
		$cfg["files_sel"] = $_GET['files'];		
		echo $this->reLabel(TPL_ADMIN_DIR."file/index.html");
	}

	function flist(){		
		global $cfg;
		$file_orderby=formatnum(getcookies("file_orderby"),$cfg["file_orderby"]); //0路径 1名称 2后缀 3大小 4时间
		$file_orderasc=formatnum(getcookies("file_orderasc"),$cfg["file_orderasc"]);//0顺序 1倒序
		$cfg["filepath"]=getcookies("file_path");
		$cfg["setpath"]=getcookies("file_setpath");
		
		$cfg['curpath'] = getcookies('file_curpath');
		
		$replace_path = webpath .str_replace($cfg['curpath'], '', $cfg["setpath"]) ;
		
		$files = $_GET['files'];

		$fileArr = explode("|", $files);
		
		$all=$this->filelistarr($cfg["filepath"]);
		$dirs=$all["dirs"];
		$dirs=$this->array_sort($dirs,$file_orderby,$file_orderasc);
		$cfg["i"]=0;
		if($cfg["filepath"]!=$cfg["setpath"])$funstr="<div class=\"upPath\"><a href=\"javascript:F.upPath();\"><img src=\"filepic/upPath.gif\" />返回上一级</a></div>";
		for($i=0;$i<count($dirs);$i++){
			$cfg["i"]++;
			$cfg["path"]=$dirs[$i][0];
			$cfg["name"]=unformatname($dirs[$i][1]);
			$cfg["ext"]="Folder";
			
			
			$cfg["type"]="文件夹";
			$cfg["size"]="-";
			$cfg["time"]=$dirs[$i][4];
			$cfg["pic"]="filepic/Folder.gif";
			$cfg["dirsto"]='onDblClick="F.fileListLoad(\''.$cfg["filepath"].$cfg["name"].'/\');"';
			$funstr.=$this->reLabel(TPL_ADMIN_DIR."file/list.html");
		}
		$files=$all["files"];
		$files=$this->array_sort($files,$file_orderby,$file_orderasc);
		for($i=0;$i<count($files);$i++){
			$cfg["i"]++;
			$cfg["path"]=$files[$i][0];
			
			
			$cfg['checked'] = in_array(str_replace($replace_path, '', $cfg['path']), $fileArr) ? 'checked' : '';
			
			$cur_path=iconv("gb2312","utf-8",$cur_path);
			$cfg["name"]=unformatname($files[$i][1]);
			$cfg["ext"]=$files[$i][2];
			$cfg["type"]=strtoupper($files[$i][2]);
			$cfg["size"]=formatfilesize($files[$i][3]);
			$cfg["time"]=$files[$i][4];
			$cfg["pic"]=$this->getpic($files[$i][0]);
			$cfg["dirsto"]='onDblClick="F.fileOk();"';
			
			
			$funstr.=$this->reLabel(TPL_ADMIN_DIR."file/list.html");
		}
		echo $funstr;
	}

	function getpic($path){
		global $cfg;
		$path2=getThumbs(wwwroot.$path);
		if(file_exists($path2)){
			$path2=str_replace(wwwroot,"",$path2);
			return $path2;
		}else{
			$patharr = pathinfo($path);
			$ext=$patharr['extension'];
			if($ext=="gif"||$ext=="jpg"||$ext=="png"||$ext=="bmp")
				return $path;
			else
				return "filepic/".$cfg["ext"].".gif";
		}
	}
	
	function filelistarr($path){
		$arr = array('dirs'=>array(),'files'=>array());
		$dir=webroot.$path;
		$filepath=webpath.$path;
		if($handle=opendir($dir)){
			while(false!==($file=readdir($handle))){
				if($file!='.'&&$file!=='..'){
					$cur_path=$dir.$file;//DIRECTORY_SEPARATOR
					$filesize=filesize($cur_path);
					$time=date("Y-m-d H:i",filemtime($cur_path));
					$cur_path=iconv("gb2312","utf-8",$cur_path);
					$name=basename($cur_path);
					if(is_dir($cur_path)){
						$cur_path=$filepath.$file;
						$cur_path=iconv("gb2312","utf-8",$cur_path);
						$arr['dirs'][] = array($cur_path,$name,"","",$time);
					}else if(!instr($cur_path,"Thumbs.")&&!instr($cur_path,".ini")){
						$cur_path=$filepath.$file;
						$cur_path=iconv("gb2312","utf-8",$cur_path);
						$path_parts = pathinfo($cur_path);
						$ext=$path_parts['extension'];
						if($filesize!=0)$filesize=$filesize;
						$arr['files'][] = array($cur_path,$name,$ext,$filesize,$time);
					}
				}
			}
			closedir($handle);
		}
		return $arr;
	}
	
	function array_sort($records,$field,$reverse,$defaultSortField=0){      
		$uniqueSortId=0;
		$hash=array();
		$sortedRecords=array();
		$tempArr=array();
		$indexedArray=array();
		$recordArray=array();	
		foreach($records as $record){
			$uniqueSortId++;
			$recordStr=implode("|",$record)."|".$uniqueSortId;
			$recordArray[]=explode("|",$recordStr);
		}
		$primarySortIndex=count($record);
		$records=$recordArray;
		foreach($records as $record){$hash[$record[$primarySortIndex]] = $record[$field];}
		uasort($hash,"strnatcasecmp");
		if($reverse)
		$hash = array_reverse($hash,true);
		$valueCount = array_count_values($hash);
		foreach($hash as $primaryKey=>$value){$indexedArray[] = $primaryKey;}
		$i = 0;
		foreach($hash as $primaryKey=>$value){
			$i++;
			if($valueCount[$value]>1){
				foreach($records as $record){if($primaryKey==$record[$primarySortIndex]){$tempArr[$record[$defaultSortField]."__".$i]=$record;break;}}
				$index=array_search($primaryKey,$indexedArray);
				if(($i==count($records))||($value!=$hash[$indexedArray[$index+1]])){
					uksort($tempArr,"strnatcasecmp");
					if($reverse){$tempArr=array_reverse($tempArr);}
					foreach($tempArr as $newRecs){$sortedRecords[]=$newRecs;}
					$tempArr=array();
				}
			}else{
				foreach($records as $record){if($primaryKey==$record[$primarySortIndex]){$sortedRecords[]=$record;break;}}
			}
		}
		return $sortedRecords;
	}
	
	function upload(){
		global $cfg;
		$cfg["filepath"]=getcookies("file_path");
		$cfg["filetype"]=getcookies("file_type");
		$cfg["types"]=$this->formattype(getcookies("file_type"));
		$size=formatnum(getcookies("file_size"),1024)*1024; //B
		$cfg["size"]=formatfilesize($size);
		$cfg["sizes"]=formatfilesize($size*10);
		$path=getcookies("file_path");
		$cfg["uploads"]="&completeFunction=F.uploadComplete()&fileTypes=".$cfg["types"]."&fileTypeDescription=".$cfg["filetype"]."&totalUploadSize=$sizes&fileSizeLimit=$size&uploadPage=upfile.php?path=$path";
		echo $this->reLabel(TPL_ADMIN_DIR."file/upload.html");
	}
	
	function rename(){
		global $cfg;
		$cfg["fileurl"]=$_GET["fileurl"];
		$cfg["showurl"]=unformatname($_GET["fileurl"]);
		echo $this->reLabel(TPL_ADMIN_DIR."file/rename.html");
	}
	
	function renamedo(){
		$oldname=$_GET["oldname"];
		$newname=$_GET["newname"];
		$file_path=getcookies("file_path");
		if(file_exists(webroot.$file_path.$newname)){
			die("{err}此文件名已经存在！");
		}else{
			$suofile=getThumbs(webroot.$file_path.$oldname);
			if(file_exists($suofile)){rename($suofile,getThumbs(webroot.$file_path.$newname));}
			$sec=rename(webroot.$file_path.$oldname,webroot.$file_path.$newname);
			if($sec)die("{ok}");
		}
	}
	
	function addfolder(){
		global $cfg;
		$cfg["fileurl"]=$_GET["fileurl"];
		echo $this->reLabel(TPL_ADMIN_DIR."file/addfolder.html");
	}
	
	function addfolderdo(){
		$thename=$_GET["thename"];
		$file_path=getcookies("file_path");
		if(is_dir(webroot.$file_path.$thename))die("此文件夹已经存在!");
		if(mkdirs(webroot.$file_path.$thename))die("文件夹新建成功!");
	}
	
	function movef(){
		global $cfg;
		$file_path=getcookies("file_path");
		$cfg["paths"]=$_GET["paths"];
		$all=$this->filelistarr($file_path);
		$dirs=$all["dirs"];
		for($i=0;$i<count($dirs);$i++)
		{
			$cfg["forder"].=$dirs[$i][1]."|";
		}
		echo $this->reLabel(TPL_ADMIN_DIR."file/movef.html");
	}
	
	function movefdo(){
		$moveto=$_GET["moveto"];
		$moveto=wwwroot.$moveto;
		$paths=$_GET["paths"];
		$file_setpath=getcookies("file_setpath");
		$file_path=getcookies("file_path");
		if(!is_dir($moveto))die("目标文件夹不存在!");
		$patharr=split(",",$paths);
		$allnum=count($patharr);
		$secnum=0;
		$errnum=0;
		for($i=0;$i<$allnum;$i++){
			$newname=$moveto."/".basename(webroot.$patharr[$i]);
			$newname2=getThumbs($newname);
			$oldname=wwwroot.$patharr[$i];
			$oldname2=getThumbs($oldname);
			if(file_exists($oldname)&&!file_exists($newname)){
				if(file_exists($oldname2))rename($oldname2,$newname2);
				rename($oldname,$newname);
				$secnum++;
			}else{
				$errnum++;
			}
		}
		die($secnum."个文件移动成功，".$errnum."个文件移动失败！");
	}
	
	function dels(){
		$files=$_GET["files"];
		$filearr=split(",",$files);
		$secnum=0;
		$errnum=0;
		for($i=0;$i<count($filearr);$i++){
			$file=wwwroot.$filearr[$i];
			$file2=getThumbs($file);
			if(delfile($file)){$secnum++;}else{$errnum++;}
			if(file_exists($file2))delfile($file2);
		}
		die($secnum."个文件删除成功，".$errnum."个文件删除失败！");
	}
	
	function editFile(){
		global $cfg;
		$cfg["fileurl"]=wwwroot.$_GET["fileurl"];
		$cfg["oldcontent"]=file_get_contents($cfg["fileurl"]);
		echo $this->reLabel(TPL_ADMIN_DIR."file/editFile.html");
	}
	
	function editFiledo(){
		$fileurl=$_POST["fileurl"];
		$content=$_POST["content"];
		$content=stripslashes(str_replace("\x0d\x0a", "\x0a", $content));
		if(file_put_contents($fileurl,$content)){die("{ok}");}else{die("{err}");}
	}
	
	function repairfile(){
		$file_path=webroot.getcookies("file_path");
		$file_path=rtrim($file_path,"/");
		$this->repairfile2($file_path);
		die("{ok}");
	}
	
	function repairfile2($dir){
		global $picSet;
		if($handle=opendir($dir)){
			while(false!==($file=readdir($handle))){				
				if($file!='.'&&$file!=='..'){
					$fullpath=$dir."/".$file;
					//$newpath=$dir."/".formatname($file);
					$fullpath=iconv("gb2312","utf-8",$fullpath);
					//$newpath=iconv("gb2312","utf-8",$newpath);
					//if($newpath!=$fullpath&&!file_exists($newpath)){rename($fullpath,$newpath);}
					if(!is_dir($fullpath)){
						if(ifthefile($fullpath,"jpg,gif,png"))
						{
							$picSet=new picSet($fullpath);
							$picSet->suo(getThumbs($fullpath),100,100);
							$picSet->save();
						}
					}else{
						$this->repairfile2($fullpath);
					}
				}
			}
			closedir($handle);
		}
	}
	
	function formattype($type){
		switch ($type){
			case "ALL":
				return "*.*";
				break;
			case "NOT":
				return "NOT";
				break;
			default:
				return "*.".str_replace(",","%3b*.",$type);
		}
	}
}

$admin_file=new admin_file();

switch($_GET["action"]){
	case "flist":
		$admin_file->flist();
		break;
	case "upload":
		$admin_file->upload();
		break;
	case "rename":
		$admin_file->rename();
		break;
	case "renamedo":
		$admin_file->renamedo();
		break;
	case "addfolder":
		$admin_file->addfolder();
		break;
	case "addfolderdo":
		$admin_file->addfolderdo();
		break;
	case "movef":
		$admin_file->movef();
		break;
	case "movefdo":
		$admin_file->movefdo();
		break;
	case "dels":
		$admin_file->dels();
		break;
	case "editFile":
		$admin_file->editFile();
		break;
	case "editFiledo":
		$admin_file->editFiledo();
		break;
	case "repairfile":
		$admin_file->repairfile();
		break;
	default:
		$admin_file->def();
}
?>