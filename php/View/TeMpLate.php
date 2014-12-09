<?php namespace Surikat\View;
use Surikat\Core\Dev;
use Surikat\View;
use Surikat\Tool;
use Surikat\Core\Sync;
use Surikat\Core\FS;
use Surikat\Tool\PHP;
use Surikat\Tool\Min\HTML as minHTML;
use Surikat\Tool\Min\PHP as minPHP;
class TeMpLate {
	var $forceCompile;
	var $path;
	var $dirCwd = [];
	var $dirCompile;
	var $dirCache;
	var $compile = [];
	var $toCachePHP = [];
	var $toCacheV = [];
	var $childNodes = [];
	var $isXhtml;
	var $present;
	protected $devCompileFile;
	function __construct($file=null,$vars=null,$options=null){
		$this->setDirCompile(SURIKAT_TMP.'tml/compile/');
		$this->setDirCache(SURIKAT_TMP.'tml/cache/');
		$this->setDirCwd([
			SURIKAT_PATH.'tml/',
			SURIKAT_SPATH.'tml/',
		]);
		if(isset($file))
			$this->setPath($file);
		if(isset($vars))
			$this->set($vars);
		if(isset($options))
			$this->setOptions($options);
	}
	protected $Controller;
	function setController($Controller){
		$this->Controller = $Controller;
	}
	function getController(){
		return $this->Controller;
	}
	function setOptions($options=[]){
		foreach($options as $k=>$v){
			if(is_integer($k))
				continue;
			if(is_array($v)&&is_array($this->$k))
				$this->$k = array_merge($this->$k,$v);
			else
				$this->$k = $v;
		}
	}
	function setPath($path){
		$this->path = $path;
	}
	function setDirCompile($d){
		$this->dirCompile = rtrim($d,'/').'/';
		$this->devCompileFile = $this->dirCompile.'.dev';
	}
	function setDirCache($d){
		$this->dirCache = rtrim($d,'/').'/';
	}
	function setDirCwd($d){
		$this->dirCwd = [];
		$this->addDirCwd($d);
	}
	function getDirCwd(){
		return $this->dirCwd;
	}
	function addDirCwd($d){
		foreach((array)$d as $dir)
			if(!in_array($dir,$this->dirCwd))
				$this->dirCwd[] = rtrim($dir,'/').'/';
	}
	function onCompile($call){
		$this->compile[] = $call;
	}
	function getPath(){
		return $this->path;
	}
	function prepare(){
		if(!($file=$this->find()))
			throw new Exception('404');
		$node = new TML(file_get_contents($file),$this);
		foreach($this->compile as $callback)
			call_user_func($callback,$node);
		$this->childNodes[] = $node;
		return $node;
	}
	function evalue(){
		$compileFile = $this->dirCompile.$this->path.'.svar';
		if((!isset($this->forceCompile)&&Dev::has(Dev::VIEW))||$this->forceCompile||!is_file($compileFile))
			$this->compileStore($compileFile,serialize($ev=$this->prepare()));
		else
			$ev = unserialize(file_get_contents($compileFile,LOCK_EX));
		eval('?>'.$ev);
		return $this;
	}
	function devRegeneration(){
		if(Dev::has(Dev::VIEW)){
			if(!is_file($this->devCompileFile)){
				FS::mkdir($this->devCompileFile,true);
				file_put_contents($this->devCompileFile,'');
			}
		}
		elseif(is_file($this->devCompileFile)){
			unlink($this->devCompileFile);
			FS::rmdir($this->dirCompile);
			FS::rmdir($this->dirCache);
		}
	}
	function display($file=null,$vars=[]){
		if(isset($file))
			$this->setPath($file);
		$this->devRegeneration();
		if((!isset($this->forceCompile)&&Dev::has(Dev::VIEW))||!is_file($this->dirCompile.$this->path))
			$this->compilePHP($this->dirCompile.$this->path,(string)$this->prepare());
		if(!empty($this->vars))
		$vars = array_merge($this->vars,$vars);
		if(!empty($vars))
			extract($vars);
		include($this->dirCompile.$this->path);
		return $this;
	}
	function find($path=null){
		if(!isset($path))
			$path = $this->path;
		foreach($this->dirCwd as $d){			
			if(strpos($path,'/')===0&&is_file($file=$d.$path))
				return $file;
			$local = $d.dirname($this->path).'/'.$path;
			if(strpos($path,'./')===0)
				return $local;
			if($file=realpath($local))
				return $file;
		}
	}
	function mtime($file,$sync,$forceCache=true){
		return Sync::mtime($this->dirCache.$file,$sync,$forceCache);
	}
	function cacheInc($h,$sync=null){
		if(func_num_args()<2)
			return $this->dirCache.$this->path.'/'.$h;
		if($this->mtime($h,$sync))
			return $this->dirCache.$this->path.'/'.$h.'.php';
		else
			readfile($this->dirCache.$this->path.'/'.$h);
	}
	function cacheRegen($file,$str){
		$file = substr($file,0,-4);
		file_put_contents($file,$str,LOCK_EX);
		clearstatcache(true,$file);
		echo $str;
	}
	function cacheV($file,$str){
		$this->toCacheV[] = [$file,$str];
	}
	function cachePHP($file,$str){
		$this->toCachePHP[] = [$file,$str];
	}
	protected function _cacheV($file,$str){
		$file = $this->dirCache.$this->path.'/'.$file;
		if(!Dev::has(Dev::VIEW))
			$str = minHTML::minify($str);
		return $this->compileStore($file,$str);
	}
	protected function _cachePHP($file,$str){
		$file = $this->dirCache.$this->path.'/'.$file.'.php';
		if(!Dev::has(Dev::VIEW))
			$str = minPHP::minify($str);
		return $this->compileStore($file,$str);
	}
	function dirCompileToDirCwd($v){
		$dir = rtrim($this->dirCompile,'/');
		if(strpos($v,$dir)===0)
			$v = $this->dirCwd[0].substr($v,strlen($dir));
		return realpath($v);
	}
	function T_FILE($v){
		return $this->dirCompileToDirCwd($v);
	}
	function T_DIR($v){
		return $this->dirCompileToDirCwd($v);
	}
	protected static function phpEmulations($str){
		$tokens = token_get_all($str);
		$str = '';
		foreach($tokens as $token)
			switch(is_array($token)?$token[0]:null){
				case T_DIR:
					$str .= '$this->T_DIR(__DIR__)';
				break;
				case T_FILE:
					$str .= '$this->T_FILE(__FILE__)';
				break;
				default:
					$str .= is_array($token)?$token[1]:$token;
				break;
			}
		return $str;
	}
	protected function compileStore($file,$str){
		FS::mkdir($file,true);
		$str = self::phpEmulations($str);
		if(!Dev::has(Dev::VIEW))
			$str = minPHP::minify($str);
		return file_put_contents($file,$str,LOCK_EX);
	}
	protected function compilePHP($file,$str){
		foreach($this->toCachePHP as $cache)
			$this->_cachePHP($cache[0],$cache[1]);
		foreach($this->toCacheV as $cache)
			$this->_cacheV($cache[0],$cache[1]);
		return $this->compileStore($file,$str);
	}
	
	protected $vars = [];
    function get($key=null){
		if(!func_num_args())
			return $this->vars;
        return isset($this->vars[$key])?$this->vars[$key]:null;
    }
    function set($key, $value = null) {
        if(is_array($key)||is_object($key)){
            foreach ($key as $k => $v)
                $this->vars[$k] = $v;
        }
        else
            $this->vars[$key] = $value;
    }
    function has($key) {
        return isset($this->vars[$key]);
    }
    function clear($key = null){
        if (is_null($key))
            $this->vars = [];
        else
            unset($this->vars[$key]);
    }
    
    function __set($k,$v){
		$this->vars[$k] = $v;
	}
	function __get($k){
		return isset($this->vars[$k])?$this->vars[$k]:null;
	}
	function __isset($k){
		return isset($this->vars[$k]);
	}
	function __unset($k){
		if(isset($this->vars[$k]))
			unset($this->vars[$k]);
	}
	function offsetSet($k,$v){
		return $this->__set($k,$v);
	}
	function offsetGet($k){
		return $this->__get($k);
	}
	function offsetExists($k){
		return $this->__isset($k);
	}
	function offsetUnset($k){
		return $this->__unset($k);
	}
}