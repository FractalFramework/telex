<?php
class App{
static $alx=[];
static function open($app,$p='',$mth=''){$ret='';
	if(isset($p['prm']))$p=_jrb($p['prm']);//when calling not by ajax
	if(!is_array($p) && strpos($p,'{')!==false)$p=json_decode($p,true);
	if(isset($p['appMethod']) && $p['appMethod'])$mth=$p['appMethod']; else $mth='content';
	if(method_exists($app,$mth)){
		$private=isset($app::$private)?$app::$private:0;
		//if(ses('auth')>=$private)$ret=$app::$mth($p);
		if(ses('auth')>=$private){$a=new $app; $ret=$a->$mth($p);
			//when ['headers'=>'1']
			if(method_exists($app,'headers'))
				if(!get('appName') or isset($p['headers']))
					if(!array_key_exists($app,self::$alx)){
						self::$alx[$app]=1; $a->headers();}}
		else $ret=help('need auth '.$private,'paneb');}
	else return div(hlpxt('no app loaded').' : '.$app.'::'.$mth,'paneb');
	return $ret;}
}
?>