<?php

class picSet {

    var $ground_info;
    var $ground_im;
    var $ground_w;
    var $ground_h;
    var $suoimg;
    var $suo_c;
    var $suo_io;
    var $tosrc;
    var $suo_w;
    var $suo_h;
    var $water;
    var $water_w;
    var $water_h;
    var $posX;
    var $posY;
    var $xOffset;
    var $yOffset;
    var $save;
    var $return;
    var $close;
    var $_type;

    function __construct($groundImage="") {
        $this->close = false;
        $this->water = false;
        $this->save = false;
        $this->big_c = false;
        $this->suo_c = false;
        $this->suo_io = false;
        $this->groundImage = $groundImage;
		$this->_type = false;
        if (!empty($this->groundImage) && file_exists($this->groundImage)) {
            $this->ground_info = getimagesize($this->groundImage);
            $this->ground_w = $this->ground_info[0];
            $this->ground_h = $this->ground_info[1];
            @ini_set("memory_limit", "100M");
            switch ($this->ground_info[2]) {
                case 1:$this->ground_im = imagecreatefromgif($this->groundImage);
                    break;
                case 2:$this->ground_im = imagecreatefromjpeg($this->groundImage);
                    break;
                case 3:$this->ground_im = imagecreatefrompng($this->groundImage);
						imagesavealpha($this->ground_im,true);//
						$this->_type = true;
                    break;
                default:$this->return = "不支持的格式";
                    $this->close = true;
                    return;
            }
        } else {
            $this->return = "源件不存在";
            $this->close = true;
            return;
        }
    }

    function __destruct() {
        if (isset($this->water_info))
            unset($this->water_info);
        if (isset($this->ground_info))
            unset($this->ground_info);
        if (isset($this->water_im))
            imagedestroy($this->water_im);
        if (isset($this->ground_im))
            imagedestroy($this->ground_im);
    }

    /**
     *
     * 生成缩略图，当图片小时，亦按图片原大小 把图片放于指定大小的图片中间。
     * @param $suo_w
     * @param $suo_h
     */
    function format_suo($suo_w, $suo_h, $bgColor="#FFFFFF") {
        $suo = Array();
        if ($suo_w !== 0 && $suo_h !== 0) {
            if ($this->ground_info[2] != 1 && function_exists('imagecreatetruecolor'))
                $thumbimg = imagecreatetruecolor($suo_w, $suo_h);
            else
                $thumbimg = imagecreate($suo_w, $suo_h);
				
			
            $new_width = $suo_w;
            $new_heigth = $suo_h;

            $start_x = 0;
            $saart_y = 0;
			
			$R = $G = $B = 255;
			if (!empty($bgColor) && (strlen($bgColor) == 7)) {
                $R = hexdec(substr($bgColor, 1, 2));
                $G = hexdec(substr($bgColor, 3, 2));
                $B = hexdec(substr($bgColor, 5));
            }
			
			if($this->_type){
				imagealphablending($thumbimg,false);//
				imagesavealpha($thumbimg,true);//
			}else{
				$white = imagecolorallocate($thumbimg, $R, $G, $B);
				imagefill($thumbimg, 0, 0, $white);
			}
            $x_ratio = round($this->ground_w / $suo_w, 2);
            $y_ratio = round($this->ground_h / $suo_h, 2);
            //如果等比例大小
            if ($x_ratio == $y_ratio) {
                if ($this->ground_w < $suo_w) {
                    $new_width = $this->ground_w;
                    $new_heigth = $this->ground_h;
                    $start_x = ($suo_w - $this->ground_w) / 2;
                    $start_y = ($suo_h - $this->ground_h) / 2;
                }
                //如果图片很宽, 以宽度为基准
            } else if ($x_ratio > $y_ratio) {
                //图片小
                if ($this->ground_w < $suo_w) {
                    $new_width = $this->ground_w;
                    $new_heigth = $this->ground_h;

                    $start_x = ($suo_w - $this->ground_w) / 2;
                    $start_y = ($suo_h - $this->ground_h) / 2;

                    //   echo "图片宽而小";
                }
                //图片宽
                else {
                    $new_heigth = $this->ground_h * ($suo_w / $this->ground_w);

                    $start_y = abs($suo_h - $new_heigth) / 2;
                    //   echo "图片宽而大";
                }
            }
            //如果图片很长
            else {
                //图片短
                if ($this->ground_h < $suo_h) {
                    $new_width = $this->ground_w;
                    $new_heigth = $this->ground_h;

                    $start_x = ($suo_w - $this->ground_w) / 2;
                    $start_y = ($suo_h - $this->ground_h) / 2;
                }
                //图片高
                else {
                    $new_width = $this->ground_w * ($suo_h / $this->ground_h);
                    echo $this->ground_w;
                    $start_x = abs($suo_w - $new_width) / 2;


                    //  echo "图片长而高";
                }
            }

            if (function_exists("imagecopyresampled")) {

                imagecopyresampled($thumbimg, $this->ground_im, $start_x, $start_y, 0, 0, $new_width, $new_heigth, $this->ground_w, $this->ground_h);
            } else {
                imagecopyresized($thumbimg, $this->ground_im, $start_x, $start_y, 0, 0, $new_width, $new_heigth, $this->ground_w, $this->ground_h);
            }
            $suo["img"] = $thumbimg;
        }
        return $suo;
    }

    /**
     *
     * 生成缩略图。
     * @param unknown_type $tosrc
     * @param unknown_type $suo_w
     * @param unknown_type $suo_h
     */
    function suo($tosrc, $suo_w, $suo_h,$bgColor="#FFFFFF") {
        if ($this->close)
            return $this->return;

       
        $this->tosrc = $tosrc;
        if (!file_exists(dirname($tosrc)))
            mkdir(dirname($tosrc));
        $suo = $this->format_suo($suo_w, $suo_h);
        $this->save = true;
        $this->suoimg = $suo["img"];
        $this->save = true;
        $this->suo_io = true;
        $this->suo_c = true;
    }

    /**
     *
     * Enter description here ...
     * @param $big_w
     * @param $big_h
     */
    function bigc($big_w, $big_h) {
        if ($this->close)
            return $this->return;
        
        $suo = $this->format_suo($big_w, $big_h);
        if ($big_w !== 0 && $big_h !== 0 && $big_w < $this->ground_w && $big_h < $this->ground_h) {
            $this->ground_im = $suo["img"];
            $this->ground_w = $suo["w"];
            $this->ground_h = $suo["h"];
            $this->save = true;
            $this->suo_c = true;
        }
    }

    /**
     *
     * Enter description here ...
     * @param $oldw
     * @param $oldh
     * @param $w
     * @param $h
     */
    function getpicw_h($oldw, $oldh, $w, $h) {
        $w_h = Array();
        if ($oldw < $w && $oldh < $h) {
            $w_h["w"] = $oldw;
            $w_h["h"] = $oldh;
            return $w_h;
        }
        $wBh = $oldw / $oldh;
        if ($wBh > $w / $h) {
            $w_h["w"] = $w;
            $w_h["h"] = $w / $wBh;
            return $w_h;
        } else {
            $w_h["h"] = $h;
            $w_h["w"] = $h * $wBh;
            return $w_h;
        }
    }

    /**
     *
     * Enter description here ...
     * @param $waterImage
     * @param $waterPos
     * @param $xOffset
     * @param $yOffset
     */
    function waterImage($waterImage, $waterPos=0, $xOffset=0, $yOffset=0) {
        if ($this->close)
            return $this->return;
        $waterImage = webroot . "images/water/" . $waterImage;
        if (!empty($waterImage) && file_exists($waterImage)) {
            $water_info = getimagesize($waterImage);
            $this->water_w = $water_info[0];
            $this->water_h = $water_info[1];
            if ($this->water_w < $this->ground_w && $this->water_h < $this->ground_h) {
                $this->water = true;
                $this->save = true;
                $this->xOffset = $xOffset;
                $this->yOffset = $yOffset;
                switch ($water_info[2]) {
                    case 1:$water_im = imagecreatefromgif($waterImage);
                        break;
                    case 2:$water_im = imagecreatefromjpeg($waterImage);
                        break;
                    case 3:$water_im = imagecreatefrompng($waterImage);
                        break;
                    default:$this->return = "不支持的水印图片格式";
                        return;
                }
                $this->pos($waterPos);
                imagealphablending($this->ground_im, true);
                imagecopy($this->ground_im, $water_im, $this->posX + $this->xOffset, $this->posY + $this->yOffset, 0, 0, $this->water_w, $this->water_h);
            }
        }
    }

    /**
     *
     * Enter description here ...
     * @param $waterText
     * @param $waterPos
     * @param $xOffset
     * @param $yOffset
     * @param $textColor
     * @param $fontSize
     * @param $fontfile
     */
    function waterText($waterText, $waterPos=0, $xOffset=0, $yOffset=0, $textColor='#cccccc', $fontSize=28, $fontfile='simsun.ttf') {
        if ($this->close)
            return $this->return;
        $fontfile = webroot . "include/" . $fontfile;
        if (!file_exists($fontfile)) {
            $this->return = "字体不存在";
            return;
        }
        $temp = imagettfbbox($fontSize, 0, $fontfile, $waterText);
        $this->water_w = $temp[2] - $temp[6];
        $this->water_h = $temp[3] - $temp[7];
        if ($this->water_w < $this->ground_w && $this->water_h < $this->ground_h) {
            $this->xOffset = $xOffset;
            $this->yOffset = $yOffset;
            if (!empty($textColor) && (strlen($textColor) == 7)) {
                $R = hexdec(substr($textColor, 1, 2));
                $G = hexdec(substr($textColor, 3, 2));
                $B = hexdec(substr($textColor, 5));
            } else {
                $this->return = "错误的色码";
                return;
            }
            unset($temp);
            $this->pos($waterPos);
            imagealphablending($this->ground_im, true);
            imagettftext($this->ground_im, $fontSize, 0, $this->posX + $this->xOffset, $this->posY + $this->water_h + $this->yOffset, imagecolorallocate($this->ground_im, $R, $G, $B), $fontfile, $waterText);
            $this->save = true;
            $this->water = true;
        }
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $waterPos
     */
    function pos($waterPos) {
        switch ($waterPos) {
            case 0:
                $this->posX = rand(0, ($this->ground_w - $this->water_w));
                $this->posY = rand(0, ($this->ground_h - $this->water_h));
                break;
            case 1:
                $this->posX = 0;
                $this->posY = 0;
                break;
            case 2:
                $this->posX = ($this->ground_w - $this->water_w) / 2;
                $this->posY = 0;
                break;
            case 3:
                $this->posX = $this->ground_w - $this->water_w;
                $this->posY = 0;
                break;
            case 4:
                $this->posX = 0;
                $this->posY = ($this->ground_h - $this->water_h) / 2;
                break;
            case 5:
                $this->posX = ($this->ground_w - $this->water_w) / 2;
                $this->posY = ($this->ground_h - $this->water_h) / 2;
                break;
            case 6:
                $this->posX = $this->ground_w - $this->water_w;
                $this->posY = ($this->ground_h - $this->water_h) / 2;
                break;
            case 7:
                $this->posX = 0;
                $this->posY = $this->ground_h - $this->water_h;
                break;
            case 8:
                $this->posX = ($this->ground_w - $this->water_w) / 2;
                $this->posY = $this->ground_h - $this->water_h;
                break;
            case 9:
                $this->posX = $this->ground_w - $this->water_w;
                $this->posY = $this->ground_h - $this->water_h;
                break;
            default:
                $this->posX = rand(0, ($this->ground_w - $this->water_w));
                $this->posY = rand(0, ($this->ground_h - $this->water_h));
                break;
        }
    }

    /**
     *
     * Enter description here ...
     */
    function save() {
        if ($this->close) {
            if ($this->suo_io) {
                copy($this->groundImage, $this->tosrc);
                return $this->return . "，无缩略生成，COPY成功";
            }
            return $this->return;
        }
        if ($this->save) {
            switch ($this->ground_info[2]) {
                case 1:
                    if ($this->suo_c)
                        imagegif($this->suoimg, $this->tosrc);
                    if ($this->water)
                        imagegif($this->ground_im, $this->groundImage);
                    break;
                case 2:
                    if ($this->suo_c)
                        imagejpeg($this->suoimg, $this->tosrc,100);
                    if ($this->water)
                        imagejpeg($this->ground_im, $this->groundImage,100);
                    break;
                case 3:
                    if ($this->suo_c)
                        imagepng($this->suoimg, $this->tosrc,8);
                    if ($this->water)
                        imagepng($this->ground_im, $this->groundImage,8);
                    break;
                default:return 6;
            }
            return "{ok}";
        }
    }

}

?>