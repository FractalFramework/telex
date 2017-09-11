<?php
class profile{
static $private='1';
static $db='profile';
static $default_clr='#1da1f2';//e6ecf0
static $roles=['human','group','industry','institution','collectivity','state','world'];

//install
static function install(){
Sql::create(self::$db,array('puid'=>'int','pusr'=>'var','pname'=>'var','status'=>'var','clr'=>'var','avatar'=>'var','banner'=>'var','web'=>'var','gps'=>'var','location'=>'var','privacy'=>'int','oAuth'=>'var','ntf'=>'int','role'=>'int'),1);}

static function injectJs(){return '';}
static function headers(){}

#tools
static function init_clr($p){
	if($clr=ses('clr'.$p['usr']))return $clr;
	$clr=Sql::read('clr',self::$db,'v','where pusr="'.$p['usr'].'"');
	return ses('clr',$clr?$clr:self::$default_clr);}

//banner		
static function banner_save($p){$f=val($p,'bkgim');
	if(substr($f,0,4)=='http')$f=File::saveimg($f,'prf','300','100');
	Sql::update(self::$db,'banner',$f,ses('uid'),'puid');
	return self::standard(['usr'=>ses('user'),'uid'=>ses('uid'),'big'=>val($p,'big')]);}

static function banner_edit($p,$big){$ret='';
	$im=val($p,'banner'); $usr=val($p,'pusr');
	$f='/img/medium/'.$im;
	//if(is_file($f))$ret.=img($f).br();
	$ret.=inp('bkgim',$im,30,lang('url',1)).' ';
	$ret.=aj('prfl|profile,banner_save|usr='.$usr.',big='.$big.'|bkgim',langp('save'),'btsav').' ';
	$ret.=Upload::call('bkgim');
	return $ret;}

static function banner($r,$big){
	$ban='img/full/'.$r['banner'];
	$clr=$r['clr'];
	if(is_file($ban)){
		$sty='background-color:#'.$clr.'; background-image:url(/'.$ban.'); background-size:cover; background-position:center center;';}
	else $sty='background-image:linear-gradient(#97c2ff,#'.$clr.');';
	if($big)$sty.=' height:280px;';
	$ret=div(val($r,'cntban'),'banner','',$sty);
	if($r['banner'])return imgup($ban,$ret);
	else return $ret;}

//avatar
static function avatar_im($im,$sz){//mini,full
	if($im)return 'img/'.$sz.'/'.$im;}
	
static function avatar_save($p){$f=val($p,'urlim'); $usr=val($p,'pusr');
	if(substr($f,0,4)=='http')$f=File::saveimg($f,'prf','140','140');
	Sql::update(self::$db,'avatar',$f,ses('uid'),'puid'); $p['avatar']=$f;
	return self::avatar($p,val($p,'big'));}

static function avatar_edit($p,$big){
	$im=val($p,'avatar'); $usr=val($p,'pusr'); $ret=''; $f='/img/mini/'.$im;
	//if(is_file($f))$ret=img($f).br();
	$ret.=inp('urlim',$im,30,lang('url',1)).' ';
	$ret.=aj('avt|profile,avatar_save|pusr='.$usr.',big='.$big.',clr='.$p['clr'].'|urlim',langp('save'),'btsav').' ';
	$ret.=Upload::call('urlim');
	return $ret;}

static function avatar_big($p){$im=val($p,'im');
	$f=self::avatar_im($im,'full');
	return img($f);}

static function avatar($p,$big){
	$usr=$p['pusr']; $im=$p['avatar']; $clr=$p['clr'];
	$f=self::avatar_im($im,$big?'full':'mini');
	$bt=self::divim($f,$big?'avatarbig':'avatar',$clr);
	$ret=imgup(self::avatar_im($im,'full'),$bt);
	return $ret;}

static function divim($f,$c,$clr){
	if($clr)$clr='background-color:#'.$clr.'; ';
	return div('',$c,'',$clr.'background-image:url(\'/'.$f.'\');');}

//status
static function status_save($p){$id=val($p,'id');
	$rk=['pname','status','web','gps','clr','role'];
	ses('clr'.$p['usr'],$p['clr']);
	$r=vals($p,$rk); Sql::updates(self::$db,$r,$id);
	return self::standard($p);}

//authorize levels//by
static function roles($d){
	$w='inner join login on '.self::$db.'.puid=login.id 
	inner join tlex_ab on login.name=tlex_ab.usr 
	where ab="'.ses('uid').'"';
	//$r=Sql::read('puid,login.name,role',self::$db,'rr',$w); p($r);
	if(auth(6))$n=7; else $n=4;
	foreach(self::$roles as $k=>$v)if($k<$n)$r[]=$v;
	return $r;}

static function status_edit($p){
	if($v='id')$ret=hidden($v,$p[$v]);
	if($v='pname')$ret.=inp($v,$p[$v],'',lang('name',1)).br();
	if($v='status')$ret.=tag('textarea',['id'=>$v,'placeholder'=>lang('presentation',1),'maxlength'=>255],$p[$v]).br();
	if($v='role')$ret.=select($v,self::roles($v),$p[$v],2).br();
	if($v='web')$ret.=input($v,$p[$v],'',lang('web',1)).br();
	if($v='clr'){$clr=$p[$v]?$p[$v]:val($p,'clr'); $clrb=invert_color($clr,1);
		$ret.=tag('input',['type'=>'text','id'=>$v,'value'=>$clr,'size'=>30,'placeholder'=>lang('color',1),'style'=>'background:#'.$clr.'; color:#'.$clrb,'onkeyup'=>'affectclr(this)'],'',1).br();}
	$ret.=aj('prfl|profile,status_save|usr='.$p['pusr'].'|id,pname,status,web,gps,clr,role',langp('save'),'btsav');
	return $ret;}

static function username($p){
	$usr=val($p,'pusr',ses('user')); $name=val($p,'pname');
	if(val($p,'privacy'))$name.=ico('lock',14,'grey');
	$ret=div(href('/'.$usr,$name),'usrnam');
	$ret.=div(href('/'.$usr,'@'.$usr),'grey');
	return $ret;}

static function status($r){
	$ret=div(nl2br(val($r,'status')),'statusdiv');
	$rol=val($r,'role',0); $rol=self::$roles[$rol];
	$ret.=div(langp($rol),'statusdiv');
	if($web=val($r,'web'))
		$ret.=div(href(http($web),ico('link',12).$web,'grey',1),'statusdiv');
	$ret.=div(self::gps($r),'statusdiv');
	return div($ret,'');}//statustxt

//gps
static function gpsav($p){$gps=val($p,'gps');
	$id=Sql::read('id',self::$db,'v','where puid='.ses('uid'));
	Sql::update(self::$db,'gps',$gps,$id);
	if($gps)$loc=Gps::com(['coords'=>$gps]); else $loc='';
	Sql::update(self::$db,'location',$loc,$id);
	return self::gps(['pusr'=>ses('user'),'gps'=>$gps,'location'=>$loc]);}

static function gps($r){$ret='';
	if($r['gps'] && $r['location'])
		$ret=popup('map,call|coords='.$r['gps'],pic('location').$r['location'],'grey');
	elseif($r['pusr']==ses('user'))
		$ret=btj(span(pic('location'),'','gpsloc'),'geo()','grey');
	return $ret;}

//mail_edit
static function mail_edit($p){
	if(val($p,'sav')){Sql::update('login','mail',$p['mail'],ses('uid')); sez('mail',$p['mail']);}
	$mail=Sql::read('mail','login','v',['id'=>ses('uid')]);
	$ret=inp('mail',$mail,30,lang('name',1)).' ';
	$ret.=aj('prml,,z|profile,mail_edit|sav=1|mail',langp('save'),'btsav');
	return $ret;}

//notifs
static function ntfbt($p){$state=val($p,'ntf'); $sav=val($p,'sav');
	//$r=vals($r['ntf'],[0,1,2,3]);
	if($sav){$state=$state==1?'0':'1';
		Sql::update(self::$db,'ntf',$state,ses('uid'),'puid');}
	if($state==0){$ic='toggle-on'; $bt='on'; $hlp=help('notifs_on','valid');}
	else{$ic='toggle-off'; $bt='off'; $hlp=help('notifs_off','alert');}
	return aj('prnt|profile,ntfbt|sav=1,ntf='.$state,ico($ic,22).lang($bt)).div($hlp);}

//privacy
static function privbt($p){$state=val($p,'privacy'); $sav=val($p,'sav');
	if($sav){$state=$state==1?'0':'1';
		Sql::update(self::$db,'privacy',$state,ses('uid'),'puid');}
	if($state==1){$ic='toggle-on'; $bt='private'; $hlp=help('privacy_on','alert');}
	else{$ic='toggle-off'; $bt='public'; $hlp=help('privacy_off','valid');}
	return aj('prvc|profile,privbt|sav=1,privacy='.$state,ico($ic,22).lang($bt)).div($hlp);}

//oAuth
static function oAuthsav($p){$ret=keygen::build([]);
	if($id=val($p,'id'))Sql::update(self::$db,'oAuth',$ret,$id);
	return $ret;}

static function oAuth($p){
	$ret=span($p['oAuth'],'grey','oath').' ';
	$ret.=aj('oath|profile,oAuthsav|id='.$p['id'],langp('gen oAuth'),'btn').' ';
	$ret.=tag('h4','',lang('call timeline'));
	$ret.=div('http://tlex.fr/api/call/tm:'.ses('user'),'console');
	$ret.=tag('h4','',lang('call id'));
	$ret.=div('http://tlex.fr/api/call/id:312','console');
	$ret.=tag('h4','',lang('post telex'));
	$ret.=div('http://tlex.fr/api.php?oAuth='.$p['oAuth'].'&msg=hello','console');
	return $ret;}

static function modifpass($p){
	$op=val($p,'oldpsw'); $np=val($p,'newpsw');
	if($op && $np){
		$ok=Sql::read('id','login','v','where id='.ses('uid').' and password=password("'.$op.'")');
		if($ok){
		Sql::query('update login set password=password("'.$np.'") where id="'.ses('uid').'"');
		//update('login','password','password("'.$np.'")',ses('uid'));
			return help('new password saved');}}
	$ret=input_label('oldpsw','',lang('old password'));
	$ret.=input_label('newpsw','',lang('new password'));
	$ret.=aj('mdfp|profile,modifpass||oldpsw,newpsw',lang('save'),'btsav');
	return $ret;}

static function deleteaccount($p){$ret='';
	$prm='rmprf|profile,deleteaccount|id='.$p['id'];
	$open=Sql::read('auth','login','v','where name="'.ses('user').'"');
	if(val($p,'confirm')){
		//Sql::update('profile','privacy',2,ses('uid'),'puid');
		Sql::update('login','auth',1,ses('uid'));
		return help('account disactivated');}
	elseif(val($p,'del')){$prm.=',confirm=1';
		$ret.=help('tlex_remove_account','alert').br();
		$ret.=aj($prm,langp('confirm deleting'),'btdel');}
	elseif(val($p,'restore')){
		Sql::update('login','auth',2,ses('uid'));
		$ret.=aj($prm.',del=1',langp('remove account'),'btdel');}
	elseif($open==1)$ret.=aj($prm.',restore=1',langp('restore account'),'btdel');
	else $ret.=aj($prm.',del=1',langp('remove account'),'btdel');
	return div($ret,'','rmprf');}

//edit
static function edit($p){
	$cols='id,puid,pusr,pname,status,clr,avatar,banner,web,gps,location,privacy,oAuth,ntf,role';
	$r=Sql::read($cols,self::$db,'ra','where puid='.ses('uid'),0);
	$ret=tag('h2','',lang('status'));
	$ret.=div(self::status_edit($r),'board');
	$ret.=tag('h2','',lang('banner'));
	$ret.=div(self::banner_edit($r,val($p,'big')),'board');
	$ret.=tag('h2','',lang('avatar'));
	$ret.=div(self::avatar_edit($r,val($p,'big')),'board');
	$ret.=tag('h2','',lang('location'));
	if($r['gps'])$del=aj('prfloc|profile,gpsav',pic('delete')); else $del='';
	$ret.=div(self::gps($r).$del,'board','prfloc');
	$ret.=tag('h2','',lang('mail'));
	$ret.=div(self::mail_edit($r),'board','prml');
	$ret.=tag('h2','',lang('notifications'));
	$ret.=div(self::ntfbt($r),'board','prnt');
	$ret.=tag('h2','',lang('privacy'));
	$ret.=div(self::privbt($r),'board','prvc');
	$ret.=tag('h2','','Api');
	$ret.=div(self::oAuth($r),'board');
	$ret.=tag('h2','',lang('Twitter Api')).hlpbt('twitterApi');
	$ret.=div(App::open('admin_twitter'),'board');
	$ret.=tag('h2','',lang('modif password'));
	$ret.=div(self::modifpass($r),'board','mdfp');
	$ret.=tag('h2','',lang('remove account'));
	$ret.=div(self::deleteaccount($r),'board');
	return div($ret,'','');}

//build
static function datas($usr){
	$cols='puid,pusr,pname,status,clr,avatar,banner,web,gps,location,privacy,oAuth,ntf,role';
	$r=Sql::read($cols,self::$db,'ra','where pusr="'.$usr.'"');
	if(!$r && $usr && $usr==ses('user'))$r=self::create($usr);
	//if(!$r)$r=['puid'=>'','pusr'=>$usr,'pname'=>$usr,'status'=>'','clr'=>'','avatar'=>'','banner'=>'','web'=>'','gps'=>'','location'=>'','privacy'=>'','oAuth'=>'','ntf'=>'','role'=>''];
	if(!$r['clr'])$r['clr']=sesif('clr'.$usr,self::$default_clr);//Clr::random()
	else sez('clr'.$usr,$r['clr']);//clr
	return $r;}

static function follow($p){
	$usr=val($p,'usr'); $sm=val($p,'small'); $wait=val($p,'wait');
	if(val($p,'approve')){
		$bt=aj('tlxbck|tlxcall,follow|approve='.$usr,langp('approve'),'btsav');
		$bt.=aj('tlxbck|tlxcall,follow|refuse='.$usr,langp('refuse'),'btdel');
		$ret=div($bt,'followbt');}
	else $ret=tlex::followbt(['usr'=>$usr,'small'=>$sm,'wait'=>$wait]);
	return $ret;}

static function build($p){
	$usr=val($p,'usr'); $uid=val($p,'uid'); $wait=val($p,'wait');
	$big=val($p,'big'); $sm=val($p,'small'); $fc=val($p,'face');//modes
	$r=self::datas($usr); //pr($r);
	//$wait=Sql::read('wait','tlex_ab','v','where ab="'.$usr.'"');//pending
	$ret['banner']=div(self::banner($r,$big),'banr');
	$ret['avatar']=span(self::avatar($r,$big),'','avt');
	if(ses('user') && ses('user')!=$usr)
		$ret['follow']=self::follow($p); else $ret['follow']='';
	$ret['subscribe']=tlex::subscribt($usr,$uid);
	$ret['username']=div(self::username($r),'username');
	if(!$fc)$ret['status']=div(self::status($r),$big?'':'status'); else $ret['status']='';
	return $ret;}

static function small($p){$usr=val($p,'usr');
	$r=self::datas($usr);
	//$r['cntban']=tlex::avatar($r);
	$r['cntban']=div(self::username($r),'bansmall');
	$ret=self::banner($r,'');
	return $ret;}

static function standard($p){
	$r=self::build($p);
	$ret=div($r['username'].$r['status']);
	$ret=div($r['banner'].$r['avatar'].$r['follow'].$ret);//,'','prfl'
	return div($ret,'profile');}

static function big($p){$usr=val($p,'usr');
	$r=self::build(['usr'=>$usr,'big'=>'1']);
	if(ses('user')!=$usr)$subsc=div($r['follow'],'right'); else $subsc='';
	$ret[0]=$r['banner'].div($subsc.$r['subscribe'],'subscrban');
	$ret[1]=$r['avatar'].div($r['username'].$r['status'],'board-sans','prfl');
	return $ret;}

//create	
static function create($usr){$uid=ses('uid');
	$id=Sql::read('id',self::$db,'v','where puid='.$uid);
	if(!$id && $uid){
		$kg=keygen::build(); $clr=sesif('clr'.$usr,self::$default_clr);//Clr::random()
		$r=['puid'=>ses('uid'),'pusr'=>$usr,'pname'=>$usr,'status'=>'','clr'=>$clr,'avatar'=>'','banner'=>'','web'=>'','gps'=>'','location'=>'','privacy'=>0,'oAuth'=>$kg,'ntf'=>0,'role'=>0];
		$r['id']=Sql::insert(self::$db,$r);
		return $r;}}

//com
static function com($usr,$o=''){
	$r=Sql::read('pname,avatar,status',self::$db,'rw','where pusr="'.$usr.'"');
	$f=self::avatar_im($r[1],'mini');
	$ret=self::divim($f,'avatarsmall','');
	if($o==2)$ret.=span($r[0],'btxt');
	if(!$o)$ret.=href('/'.$usr,$r[0],'btxt');
	return $ret;}

static function name($uid,$o=''){
	$ret=Sql::read('pname',self::$db,'v','where puid="'.$uid.'"');
	if($o)$usr=Sql::read('name','login','v','where id="'.$uid.'"');
	if($o)$ret=href('/'.$usr,$ret,'btxt');
	return $ret;}

//interface
static function content($p){
	//self::install();
	$usr=val($p,'user',ses('user')); $id=val($p,'id');
	if(ses('uid'))self::create($usr);
	$ret=self::standard(['id'=>$id,'usr'=>$usr]);
	return $ret;}
}
?>
