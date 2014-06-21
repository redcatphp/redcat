<?php namespace surikat;
use surikat\control\ArrayObject;
use surikat\control\PHP;
use surikat\control\HTTP;
use surikat\view\FILE;
use surikat\view\TML;
class present extends ArrayObject{
	static function document(FILE $file){}
	static function load(TML $tml){
		if($tml->vFile->present&&strpos(implode(':',$tml->vFile->present->presentNamespaces),implode(':',$tml->_namespaces))===0)
			return;
		$c = get_called_class();
		$o = new $c();
		$o->merge(array(
			'templatePath'		=> $tml->vFile->path,
			'presentAttributes'	=> $tml->getAttributes(),
			'presentNamespaces'	=> $tml->_namespaces,
		));
		$o->assign();
		//$fl = ",EXTR_OVERWRITE|EXTR_PREFIX_INVALID|EXTR_REFS,'i'";
		$fl = ",EXTR_OVERWRITE|EXTR_PREFIX_INVALID,'i'";
		//$fl = "";
		$head = '<?php if(isset($THIS))$_THIS=$THIS;$THIS=new '.$c.'('.var_export($o->getArray(),true).');';
		$head .= '$THIS->execute();';
		$head .= 'extract((array)$THIS'.$fl.');?>';
		//print('<pre>'.htmlentities($head.$foot).'</pre>');exit;
		$tml->head($head);
		if(!empty($tml->childNodes))
			$tml->foot('<?php if(isset($_THIS));extract((array)($THIS=$_THIS)'.$fl.'); ?>');
		$tml->vFile->present = $o;
	}
	function assign(){}
	function execute(){
		 if(isset($this->presentAttributes->uri)&&$this->presentAttributes->uri=='static'&&(count(view::param())>1||!empty($_GET)))
			view::error(404);
		$this->dynamic();
	}
	function dynamic(){}
}
