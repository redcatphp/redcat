<?php namespace Templix\MarkupX;
use Templix\MarkupHtml5\Img as MarkupHtml5_Img;
use Templix\Templix;
class Img extends MarkupHtml5_Img{
	function loaded(){
		if($this->src&&strpos($this->src,'://')===false){
			if(!($this->height&&$this->width)){
				$size = @getimagesize($this->src);
				if(isset($size[0])&&isset($size[1])){
					$this->width =  $size[0];
					$this->height = $size[1];
				}
			}
			if($this->devLevel()&Templix::DEV_IMG&&$this->src&&strpos($this->src,'://')===false&&strpos($this->src,'_t=')===false){
				if(strpos($this->src,'?')===false)
					$this->src .= '?';
				else
					$this->src .= '&';
				$this->src .= '_t='.time();
			}
		}
	}
}