<?php
//error_reporting(-1);
//ini_set('display_startup_errors',true);
//ini_set('display_errors','stdout');

//define('SURIKAT_FREEZE_DI',true);
if(!@include(__DIR__.'/surikat/surikat.php'))
	symlink('../surikat','surikat')&&include('surikat/surikat.php');

$di->create('KungFu\Cms\FrontController\Index')->runFromGlobals();