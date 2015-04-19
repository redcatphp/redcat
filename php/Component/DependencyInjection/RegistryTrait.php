<?php namespace Surikat\Component\DependencyInjection;
trait RegistryTrait{
	protected static $__instances = [];
	protected static $__instance;
	static function getStatic(){
		return isset(static::$__instance)?static::$__instance:call_user_func_array('static::setStatic',func_get_args());
	}
	static function setStatic(){
		return static::$__instance = static::getStaticRegistry(func_get_args());
	}
	static function getStaticRegistry($args=null,$class=null){
		$key = empty($args)?0:Container::hashArguments($args);
		if(!isset($class))
			$class = get_called_class();
		else
			$key = $class.'.'.$key;
		if(!isset(static::$__instances[$key])){
			if($class==__NAMESPACE__.'\Container'){
				if(is_array($args)&&!empty($args))
					static::$__instances[$key] = (new \ReflectionClass($class))->newInstanceArgs($args);
				else
					static::$__instances[$key] = new $class();
			}
			else{
				static::$__instances[$key] = Container::getStatic()->factoryDependency($class,null,true);
			}
		}
		return static::$__instances[$key];
	}
}