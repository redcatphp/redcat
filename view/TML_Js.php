<?php namespace surikat\view; 
use surikat\view\CALL_APL;
class TML_Js extends CALL_APL{
	protected $selfClosed = true;
	protected $hiddenWrap = true;
	protected $callback = 'addJsScript';
	var $selector = false;
	function load(){
		if($this->closest('extend')){
			$o = $this;
			$this->closest()->onLoaded(function()use($o){
				$o->addJsScript();
			});
		}
	}
	function loaded(){
		$this->addJsScript();
	}
}
