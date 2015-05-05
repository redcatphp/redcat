<?php
namespace FluxServer\Http;
use ArrayAccess;
class Cookie implements ArrayAccess{
	protected $data;
	function __construct($data=null){
		if(!$data)
			$data = $_COOKIE;
		$this->data = $data;
	}
	function offsetExists($k){
		return isset($this->data[$k]);
	}
	function offsetUnset($k){
		if(isset($this->data[$k]))
			unset($this->data[$k]);
	}
	function offsetGet($k){
		return isset($this->data[$k])?$this->data[$k]:null;
	}
	function offsetSet($k,$v){
		$this->data[$k] = $v;
	}
	function __isset($k){
		return isset($this->data[$k]);
	}
	function __unset($k){
		if(isset($this->data[$k]))
			unset($this->data[$k]);
	}
	function __get($k){
		return isset($this->data[$k])?$this->data[$k]:null;
	}
	function __set($k,$v){
		$this->data[$k] = $v;
	}
	function overrideGlobal(){
		foreach($this->data as $k=>$v){
			$_COOKIE[$k] = $v;
		}
	}
}