<?php namespace surikat\view; 
class PHP extends CORE{
	protected $hiddenWrap = true;
	var $nodeName = '#php';
	function __construct($parent,$nodeName,$text){
		$text = self::phpImplode($text,$parent->constructor);
		$this->innerHead($text);
	}
}
