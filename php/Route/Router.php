<?php namespace Surikat\Route;
class Router implements Route {
	protected $match;
	protected $Controller;
	function __construct($match=null,$Controller=null){
		$this->match = $match;
		$this->setController($Controller);
	}
	function setController($Controller){
		$this->Controller = $Controller;
	}
	function match($url){
		$match = $this->match;
		$url = ltrim($url,'/');
		if(is_string($match)){
			if(strpos($match,'/^')===0&&strrpos($match,'$/')-strlen($match)===-2){
				$match = function($url)use($match){
					if(preg_match($match, $url, $params)){
						array_shift($params);
						return array_values($params);
					}
				};
			}
			else{
				$match = function($url)use($match){
					$match = ltrim($match,'/');
					if(strpos($url,ltrim($match,'/'))===0){
						return substr($url,strlen($match));
					}
				};
			}
		}
		return call_user_func($match,$url);
	}
}