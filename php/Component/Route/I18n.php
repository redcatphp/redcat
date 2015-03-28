<?php namespace Surikat\Component\Route;
use Surikat\Component\Config\Config;
use Surikat\Component\I18n\Lang;
use Surikat\Component\DependencyInjection\MutatorMagic;
class I18n extends Faceted {
	use MutatorMagic;
	
	protected $DispatcherUri;
	function __construct($DispatcherUri){
		$this->DispatcherUri = $DispatcherUri;
	}
	function __invoke(&$uri){
		parent::__invoke($uri);
		$this->uriParams[0] = $this->i18nBySubdomain($this->uriParams[0]);
		$uri = $this->buildPath();
	}
	protected function i18nBySubdomain($path){
		$path = urldecode($path);
		$templatePath = $path;
		$langMap = false;
		if($lang=$this->Http_Url->getSubdomainLang()){
			if(file_exists($langFile='langs/'.$lang.'.ini')){
				$langMap = parse_ini_file($langFile);
				if(isset($langMap[$path]))
					$templatePath = $langMap[$path];
				elseif(($k=array_search($path,$langMap))!==false){
					header('Location: /'.$k,false,301);
					exit;
				}
			}
		}
		else
			$lang = Config::langs('default');
		Lang::set($lang);
		$ctrl = $this->DispatcherUri->Mvc_Controller;
		$ctrl->addPrefixTmlCompile('.'.$lang.'/');
		$ctrl->Mvc_View->onCompile(function($TML)use($lang,$path,$langMap){
			$this->Templator_Toolbox->i18nGettext($TML);
			$this->Templator_Toolbox->i18nRel($TML,$lang,$path,$langMap);
			if($langMap){
				foreach($TML('a[href]') as $a){
					if(strpos($a->href,'://')===false&&strpos($a->href,'javascript:')!==0&&strpos($a->href,'#')!==0){
						if(($k=array_search($a->href,$langMap))!==false)
							$a->href = $k;
					}
				}
			}
		});
		return $templatePath;
	}
}