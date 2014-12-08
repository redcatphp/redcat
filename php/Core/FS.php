<?php namespace Surikat\Core; 
abstract class FS {
	static function recurse($file,$arg,$pattern=null,$asc=null,&$ret=[],$skiplink=null){
		foreach(glob($file.'/'.($pattern?$pattern:'*')) as $f){
			if($skiplink&&is_link($f)){
				continue;
			}
			elseif($asc){
				$ret[] = call_user_func($arg,$f);
				if(is_dir($f))
					$ret[] = self::recurse($f,$arg);
			}
			else{
				if(is_dir($f))
					$ret[] = self::recurse($f,$arg);
				$ret[] = call_user_func($arg,$f);
			}
		}
		return $ret;
	}
	static function mkdir($file,$isFile=null){
		$dir = $file;
		if($isFile)
			$dir = dirname($file);
		return @mkdir($dir,0777,true);					
	}
	static function humanSize($bytes,$decimals=2){
		$sz = 'BKMGTP';  
		$factor = floor((strlen($bytes) - 1) / 3);  
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];  
    }
    static function get_absolute_path($path) {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }
    static function rmdir($dir){
		$dh = opendir($dir);
		while($file=readdir($dh)){
			if($file!='.'&&$file!='..'){
				$fullpath = $dir.'/'.$file;
				if(is_file($fullpath))
					unlink($fullpath);
				else
					self::rmdir($fullpath);
			}
		}
		closedir($dh);
		return is_dir($dir);
	}
}