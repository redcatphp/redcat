<?php namespace Surikat\Model;
use Surikat\Core\Dev;
use Surikat\Core\Config;
use Surikat\Core\STR;
class R extends RedBeanPHP\Facade{
	static function loadDB($key){
		$getter = 'db';
		if(!$key)
			$key = 'default';
		if($key!='default')
			$getter = $getter.'_'.implode('_',explode('/',$key));

		$type = Config::$getter('type');
		if(!$type)
			return;
		$port = Config::$getter('port');
		$host = Config::$getter('host');
		$file = Config::$getter('file');
		$name = Config::$getter('name');
		$prefix = Config::$getter('prefix');
		$case = Config::$getter('case');
		$frozen = Config::$getter('frozen');
		$user = Config::$getter('user');
		$password = Config::$getter('password');
		
		if($port)
			$port = ';port='.$port;
		if($host)
			$host = 'host='.$host;
		elseif($file)
			$host = $file;
		if($name)
			$name = ';dbname='.$name;
		if(!isset($frozen))
			$frozen = !Dev::has(Dev::DB);		
		if(!isset($case))
			$case = true;
		$dsn = $type.':'.$host.$port.$name;
		
		
		self::addDatabase($key,$dsn,$user,$password,$frozen,$prefix,$case);
		
		return true;
	}
	static function getDatabase($key=null){
		if(!$key)
			$key = 'default';
		if(!isset(self::$databases[$key]))
			self::loadDB($key);
		return parent::getInstance($key);
	}
	static function selectDatabase($key){
		if(!$key)
			$key = 'default';
		if(!isset(self::$databases[$key]))
			self::loadDB($key);
		return parent::selectDatabase($key);
	}
	static function nestBinding($sql,$binds){
		do{
			list($sql,$binds) = self::pointBindingLoop($sql,(array)$binds);
			list($sql,$binds) = self::nestBindingLoop($sql,(array)$binds);
			$containA = false;
			foreach($binds as $v)
				if($containA=is_array($v))
					break;
		}
		while($containA);
		return [$sql,$binds];
	}
	private static function pointBindingLoop($sql,$binds){
		$nBinds = [];
		foreach($binds as $k=>$v){
			if(is_integer($k))
				$nBinds[] = $v;
		}
		$i = 0;
		foreach($binds as $k=>$v){
			if(!is_integer($k)){
				$find = ':'.ltrim($k,':');
				while(false!==$p=strpos($sql,$find)){
					$preSql = substr($sql,0,$p);
					$sql = $preSql.'?'.substr($sql,$p+strlen($find));
					$c = count(explode('?',$preSql))-1;
					array_splice($nBinds,$c,0,[$v]);
				}
			}
			$i++;
		}
		return [$sql,$nBinds];
	}
	private static function nestBindingLoop($sql,$binds){
		$nBinds = [];
		foreach($binds as $k=>$v){
			if(is_array($v)){
				$find = '?';
				$c = count($v);
				$av = array_values($v);
				$i = 0;
				$ln = 0;
				do{
					if($ln)
						$p = strpos($sql,$find,$ln);
					else
						$p = STR::posnth($sql,$find,is_integer($k)?$k:0,$ln);
					if($p!==false){
						$nSql = substr($sql,0,$p);
						$nSql .= '('.implode(',',array_fill(0,$c,'?')).')';
						$ln = strlen($nSql);
						$nSql .= substr($sql,$p+strlen($find));
						$sql = $nSql;
						for($y=0;$y<$c;$y++)
							$nBinds[] = $av[$y];
					}
					$i++;
				}
				while(!is_integer($k)&&strpos($sql,$find)!==false);
			}
			else{
				$nBinds[] = $v;
			}
		}
		return [$sql,$nBinds];
	}
}
if(R::loadDB('default'))
	R::selectDatabase('default');