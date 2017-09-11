<?php
class Admin{

//profile
static function badger($p){
$r=Sql::read('name','login','rv','where mail="'.ses('mail').'" and auth>1 order by name');//
foreach($r as $v){//$rb[]=aj('bdg|Admin,badger_switch|usr='.$v,$v,'');
	$rb[]=aj('reload,bdg,loged_ok|login,badger|user='.$v,$v,'');}
$ret=div(implode('',$rb),'list');
if($usr=val($p,'usr'))$ret.=password('psw','').aj('|login',lang('login'),'btsav');
$ret.=div('','','bdg');
return $ret;}

static function login(){
//$r[]=['login','in','login,com','user','login'];
$r[]=['login','in','login,com|auth=2','user','login'];
$r[]=['lang','j','returnVar,lng,reload|Lang,set|lang=fr','flag','fr'];
$r[]=['lang','j','returnVar,lng,reload|Lang,set|lang=en','flag','en'];
$r[]=['lang','j','returnVar,lng,reload|Lang,set|lang=es','flag','es'];
return $r;}

static function profile(){
$usr=ses('user')?ses('user'):'profile'; $dev=ses('dev');
$r[]=[$usr,'pop','profile,edit','user','edit profile'];
$r[]=[$usr.'/lang','j','returnVar,lng,reload|Lang,set|lang=fr','flag','fr'];
$r[]=[$usr.'/lang','j','returnVar,lng,reload|Lang,set|lang=en','flag','en'];
$r[]=[$usr.'/lang','j','returnVar,lng,reload|Lang,set|lang=es','flag','es'];
if(auth(6) or $dev=='prog'){
	$r[]=[$dev.'/mode','j','ses,,reload||k=dev,v=prog','','prog'];
	$r[]=[$dev.'/mode','j','ses,,reload||k=dev,v=prod','','prod'];}
$n=Sql::read('count(id)','login','v','where mail="'.ses('mail').'" and auth>1');
if($n>1)$r[]=[$usr.'/badger','in','Admin,badger','','badger'];
$r[]=[$usr,'pop','desktop|dir=/documents','','desktop'];
$r[]=[$usr.'/utils','','pad','file-text-o','notes'];
$r[]=[$usr.'/utils','','tickets','','tickets'];
$r[]=[$usr.'/about','pop','art|id=6','tlex','welcome'];
$r[]=[$usr.'/about','pop','art|id=54','art','features'];
$r[]=[$usr.'/about','pop','applist,tlex|','art','list of Apps'];
$r[]=[$usr.'/about','pop','art|id=4','info','confidentiality'];
$r[]=[$usr.'/about','pop','art|id=5','art','developpers'];
$r[]=[$usr.'/about','pop','devnote','','devnote'];
$r[]=[$usr.'/about','pop','contact','','contact'];
$r[]=[$usr.'/about','pop','art|id=2','info','credits'];
//$r[]=[$usr.'/'.'utils','','convert','file-text-o','convert'];
if(auth(6)){
	$r[]=[$dev.'/admin','pop','admin_lang','','lang'];
	$r[]=[$dev.'/admin','pop','admin_help','help','helps'];
	$r[]=[$dev.'/admin','pop','admin_icons','','pictos'];
	$r[]=[$dev.'/admin','pop','admin_labels','','labels'];
	$r[]=[$dev.'/admin','pop','update,loaddl','','update'];
	$r[]=[$dev.'/doc','pop','admin_sys','','sys'];
	$r[]=[$dev.'/doc','pop','admin_lib','','lib'];
	$r[]=[$dev.'/doc','pop','admin_conn','','conn'];
	$r[]=[$dev.'/doc','pop','devnote','','devnote'];
	$r[]=[$dev.'','j','popup,,xx|dev2prod','','push'];}
$r[]=[$usr,'j',',,reload|login,disconnect','','logout'];
return $r;}

//com
static function com(){
	$keys='id,dir,type,com,picto,bt';
	$r=Sql::read($keys,'desktop','id','where uid="'.ses('uid').'" or dir="/apps/tlex" order by dir');// or auth=0 
	if(is_array($r))foreach($r as $k=>$v)$r[$k][0]='root'.$r[$k][0];//add root
	return $r;}

#menus
static function menu(){
	$app=ses('app'); $dev=ses('dev');
	$r=self::com();
	if(!$r)$r=applist::comdir();
	$r[]=['','lk','/app/'.$app,'',$app];
	//if(auth(4) && $app)$r[]=['','j','pagup|dev,seeCode|appSee='.$app,'code','Code'];
	//if($app && method_exists($app,'admin')){$rb=$app::admin(); if($rb)$r=array_merge($r,$rb);}
	if($app && method_exists($app,'admin')){$q=new $app; $rb=$q->admin();//['app'=>$app]
		if($rb)$r=array_merge($r,$rb);}
	return $r;}

#content
static function content($p){
	$app=val($p,'app'); ses('app',$app); $own=ses('user');
	$usr=val($p,'usr'); $id=val($p,'id',val($p,'th'));
	if(is_numeric($usr)){$id=$usr; $usr='';}
	$prf=$own?'profile':'login';
	$login=Menu::call(['app'=>'Admin','method'=>$prf,'drop'=>1]);
	//nav
	$ret=span($login,'right');
	$ret.=href(host(),ico('star'),'btn abbt');
	//if($app!='tlex')$ret.=href('/app/'.$app,pic($app).hlpxt($app),'btn abbt');
	if($app && method_exists($app,'admin_bt'))$ret.=$app::admin_bt($usr);
	else $ret.=Menu::call(['app'=>'Admin','method'=>'menu','css'=>'fix','drop'=>1]);
	return div(div($ret,'navigation'),'topbar');}
}
?>