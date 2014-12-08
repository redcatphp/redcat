<?php namespace Surikat\Tool\Crypto;
class Toolbox{
	static function hash($info,$hash=false){
		if($hash)
			return self::hash($info)==$hash;
		else
			return hash('sha256',SurikatConfig('salts.0').$info.SurikatConfig('salts.1'));
	}
	static function doubleSalt($toHash,$username){
		$password = str_split($toHash,(strlen($toHash)/2)+1);
		$hash = hash('md5', $username.$password[0].'centerSalt'.$password[1]);
		return $hash;
	}
	static function steganoUserMaker($login,$pass,$path=null){
		if($path==null)
			$path = DATA_PATH.'users';
		Stegano::EncryptBox(self::hasher($pass), $path.'/'.md5($login).'.png', hash('sha512',$login));
	}
	public static function steganoUserChecker($login,$pass,$path=null){
		if($path==null) $path = DATA_PATH.'users';
		if(is_file($path.'/'.md5($login).'.png')){
			$hash = Stegano::Decrypt($path.'/'.md5($login).'.png', hash('sha512',$login));
			if($hash&&self::hasher($pass, $hash))
				return true;
		}
		return false;
	}
	public static function hasher($info, $encdata = false){
		$strength = "08";
		if($encdata){
			if(substr($encdata, 0, 60) == crypt($info, "$2a$".$strength."$".substr($encdata, 60)))
				return true;
			else
				return false;
		}
		else{
			$salt = "";
			for ($i = 0; $i < 22; $i++)
				$salt .= substr("./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789", mt_rand(0, 63), 1);
			return crypt($info, "$2a$".$strength."$".$salt).$salt;
		}
	}
	public static function crypt_apr1_md5($plainpasswd) {
		$salt = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
		$len = strlen($plainpasswd);
		$text = $plainpasswd.'$apr1$'.$salt;
		$bin = pack("H32", md5($plainpasswd.$salt.$plainpasswd));
		for($i = $len; $i > 0; $i -= 16)
			$text .= substr($bin, 0, min(16, $i));
		for($i = $len; $i > 0; $i >>= 1)
			$text .= ($i & 1) ? chr(0) : $plainpasswd{0};
		$bin = pack("H32", md5($text));
		for($i = 0; $i < 1000; $i++) {
			$new = ($i & 1) ? $plainpasswd : $bin;
			if ($i % 3) $new .= $salt;
			if ($i % 7) $new .= $plainpasswd;
			$new .= ($i & 1) ? $bin : $plainpasswd;
			$bin = pack("H32", md5($new));
		}
		$tmp = '';
		for ($i = 0; $i < 5; $i++) {
			$k = $i + 6;
			$j = $i + 12;
			if ($j == 16) $j = 5;
			$tmp = $bin[$i].$bin[$k].$bin[$j].$tmp;
		}
		$tmp = chr(0).chr(0).$bin[11].$tmp;
		$tmp = strtr(strrev(substr(base64_encode($tmp), 2)),
		"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
		"./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");
		return "$"."apr1"."$".$salt."$".$tmp;
	}
}