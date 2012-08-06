<?php
/*
#
# QRcode image class library for PHP4  version 0.50beta9 (C)2002-2004,Y.Swetake
#
# This version supports QRcode model2 version 1-40.
#
*/

require "qrcode.php";

class Qrcode_image extends Qrcode{

    var $module_size;
    var $quiet_zone;

    function Qrcode_image(){
       $this->Qrcode();
       $this->module_size=4;
       $this->quiet_zone=4;
    }

    function set_module_size($z){
        if ($z>0 && $z<9){
            $this->module_size=$z;
        }    
    }

    function set_quietzone($z){
        if ($z>0 && $z<9){
            $this->quiet_zone=$z;
        }
    }

    function qrcode_image_out($org_data,$image_type='png',$filename=''){
      $this->image_out($this->cal_qrcode($org_data),$image_type,$filename);
    }

    function image_out($data,$image_type='png',$filename=''){
         $im=$this->mkimage($data);
         if ($image_type=="jpeg"){
	   if (strlen($filename)>0){
               ImageJPEG($im,$filename);
           } else {
               ImageJPEG($im);
           }
         } else {
	   if (strlen($filename)>0){
	       ImagePNG($im,$filename);
           } else {
               ImagePNG($im);
           }
         }
    }

    function mkimage($data){
        $data_array=explode("\n",$data);
        $c=count($data_array)-1;
        $image_size=$c;
        $output_size=($c+($this->quiet_zone)*2)*$this->module_size;

        $img=ImageCreate($image_size,$image_size);
        $white = ImageColorAllocate ($img, 255, 255, 255);
        $black = ImageColorAllocate ($img, 0, 0, 0);

        $im=ImageCreate($output_size,$output_size);

        $white2 = ImageColorAllocate ($im ,255,255,255);
        ImageFill($im,0,0,$white2);

        $y=0;
        foreach($data_array as $row){
           $x=0;
           while ($x<$image_size){
           if (substr($row,$x,1)=="1"){
               ImageSetPixel($img,$x,$y,$black);
           }
           $x++;
           }
        $y++;
        }
        $quiet_zone_offset=($this->quiet_zone)*($this->module_size);
        $image_width=$image_size*($this->module_size);

        ImageCopyResized($im,$img,$quiet_zone_offset ,$quiet_zone_offset,0,0,$image_width ,$image_width ,$image_size,$image_size);

        return($im);
    }
}

?>