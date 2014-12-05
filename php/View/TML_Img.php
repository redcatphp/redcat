<?php namespace Surikat\View; 
use Surikat\Config\Dev;
class TML_Img extends TML{
	protected $selfClosed = true;#http://www.w3.org/TR/html5/syntax.html#void-elements
	function loaded(){
		if($this->src&&strpos($this->src,'://')===false){
			if(!($this->height&&$this->width)){
				$size = @getimagesize($this->src);
				if(isset($size[0])&&isset($size[1])){
					$this->width =  $size[0];
					$this->height = $size[1];
				}
			}
			if(Dev::has(Dev::IMG)&&$this->src&&strpos($this->src,'://')===false&&strpos($this->src,'_t=')===false){
				if(strpos($this->src,'?')===false)
					$this->src .= '?';
				else
					$this->src .= '&';
				$this->src .= '_t='.time();
			}
		}
	}
}