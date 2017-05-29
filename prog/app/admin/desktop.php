<?php

class desktop{
static $private='1';
static $db='desktop';

static function headers(){
	//Head::add('jscode','');
	Head::add('csscode','fieldset, legend{border:0; background:#ddd; width:44%; display:table-cell;}');}

static function admin(){
	if(ses('uid')){
		$r[]=array('','j','popup|desktop,manage','','manage');
		return $r;}}

static function install(){
	Sql::create(self::$db,['uid'=>'int','dir'=>'var','type'=>'var','com'=>'var','picto'=>'var','bt'=>'var','auth'=>'int']);}

//fill sql from existing apps
static function readapps(){
	$dirs=Dir::read('app');
	if(is_array($dirs))foreach($dirs as $dir=>$files){
		if(is_array($files) && $dir)foreach($files as $k=>$file){
			if(is_string($file)){$app=before($file,'.');
				if($app)$private=isset($app::$private)?$app::$private:0;
				$dr='/phi/'.$dir;
				if(!$private or ses('auth')>=$private)
					$r=['uid'=>'0','dir'=>$dr,'type'=>'','com'=>$app,'picto'=>ics($app),'bt'=>$app];
					$nid=Sql::insert(self::$db,$r);}}}}

static function reload(){
	return aj('page|desktop',lang('reload'),'btn');}

#admin
//displace
static function savemdfdr($p){
	$where=auth(6)?' or uid="0"':'';
	$r=Sql::read('id,dir',self::$db,'rr','where uid="'.ses('uid').'"'.$where);
	if($p['mdfdr'])
	foreach($r as $k=>$v){$vb=str_replace($p['dir'],$p['mdfdr'],$v['dir']);
		if($vb!=$v['dir'])
			Sql::query('update desktop set dir="'.$vb.'" where id="'.$v['id'].'"');}
	return Desk::load('desktop','com',before($p['mdfdr'],'/'));}
	
static function modifdir($p){$sz=val($p,'sz',8);
	$j=ajs('div,page,2|desktop,savemdfdr','dir='.$p['dir'],'mdfdr');
	$prm=['type'=>'text','id'=>'mdfdr','value'=>$p['dir'],'size'=>$sz,'onblur'=>$j];
	$ret=tag('input',$prm,'',1);
	return $ret;}
//rename
static function savemdfbt($p){
	if(auth(6) && $p['id'])Sql::update(self::$db,'bt',$p['mdfbt'],$p['id']);
	return Desk::load('desktop','com',$p['dir']);}

static function modifbt($p){
	$r=Sql::read('bt,dir',self::$db,'ra','where id="'.$p['id'].'"');
	$j=ajs('div,page,2|desktop,savemdfbt','id='.$p['id'],'mdfdr');
	$prm=array('type'=>'text','id'=>'mdfbt','value'=>$r['bt'],'size'=>8,'onblur'=>$j);
	$ret=tag('input',$prm,'',1);
	return $ret;}
//del
static function del($p){
	$nid=Sql::delete(self::$db,$p['id']);
	return self::manage($p);}
//update
static function update($p){
	$keys='dir,type,com,picto,bt'; $r=explode(',',$keys);
	foreach($r as $k=>$v)Sql::update(self::$db,$v,$p[$v],$p['id']);
	//return lang('updated').' '.self::reload();
	return self::manage($p);}

static function edit($p){$ret='';
	$keys='dir,type,com,picto,bt';
	$r=Sql::read($keys,self::$db,'ra','where id="'.$p['id'].'"');
	foreach($r as $k=>$v)$ret.=goodinput($k,$v).' '.label($k,$k).br();
	$ret.=aj('dskmg|desktop,update|id='.$p['id'].'|'.$keys,lang('save'),'btsav');
	$ret.=aj('dskmg|desktop,del|id='.$p['id'],langp('del'),'btdel');
	return div($ret,'','dskdt');}

static function save($p){
	$r=Sql::columns(self::$db,2);
	foreach($r as $k=>$v)$rb[$k]=val($p,$k);
	$nid=Sql::insert(self::$db,$rb);
	if($nid)self::manage($p);}

static function add($p){
	$r=Sql::columns(self::$db,2);
	$keys=implode(',',array_keys($r)); unset($r['uid']);
	$ret=hidden('uid',ses('uid'));
	foreach($r as $k=>$v)$ret.=input($k,$k,16,1).br();
	$ret.=aj('dskpop|desktop,save||'.$keys,lang('add'),'btn');
	return div($ret,'','dskpop');}

static function tlex_app($p){$app=val($p,'app'); $ret='';
	$ex=Sql::read('id',self::$db,'v',['dir'=>'/apps/tlex','com'=>$app]);
	$rb=['uid'=>ses('uid'),'dir'=>'/apps/tlex','type'=>'','com'=>$app,'picto'=>ics($app),'bt'=>$app,'auth'=>2];
	if(!$ex)$nid=Sql::insert(self::$db,$rb);
	else $ret=aj('popup|desktop,del|id='.$ex,langp('delete'),'btdel');
	return $ret.aj('popup|desktop|dir=/apps/tlex',lang('desktop'),'btn');}

//edit on place
static function mdfbtn($p){
	if($p['col']=='picto')$btn=ico($p['val']).' '; else $btn=$p['val'];
	return aj($p['cbk'].'|desktop,modif|id='.$p['id'].',col='.$p['col'].',val='.jurl($p['val']).',cbk='.$p['cbk'],$btn,'btn');}

static function savemdf($p){$p['val']=$p[$p['idv']];
	Sql::update(self::$db,$p['col'],$p['val'],$p['id']);
	return self::mdfbtn($p);}

static function modif($p){
	$idv='mdf'.$p['id'].$p['col'];
	$js=ajs('div,'.$p['cbk'].',2|desktop,savemdf','cbk='.$p['cbk'].',id='.$p['id'].',col='.$p['col'].',idv='.$idv,$idv);
	$r=array('type'=>'text','id'=>$idv,'value'=>$p['val'],'size'=>16,'onblur'=>$js);
	$ret=tag('input',$r,'',1);
	return $ret;}

//manage
static function manage($p){$ret=''; $ra=''; $dir=val($p,'dir');
	if(isset($p['addrow'])){$r=Sql::columns(self::$db,2);
		foreach($r as $k=>$v)$rb[$k]='';
		$rb['uid']=ses('uid'); $rb['dir']=$dir;
		$nid=Sql::insert(self::$db,$rb);}
	if(auth(4))$ret=aj('dskmg|desktop,manage|dir='.$dir.',addrow=1',langp('add'),'btn');
	//$ret.=aj('dskmg|desktop,manage|dir='.$dir,langp('refresh'),'btn');
	//$ret.=aj('popup|desktop,readapps|'.lang('reflush apps'),'btn');
	//table
	if(auth(4))$keys='id,dir,type,com,picto,bt,auth'; else $keys='id,dir,picto,bt,auth';
	$kr=explode(',',$keys); $n=count($kr);
	if($dir)$wh=' and dir like "'.$dir.'%"'; else $wh='';
	$r=Sql::read($keys,self::$db,'','where (uid="'.ses('uid').'" or auth<"'.ses('auth').'") '.$wh.' order by id');
	foreach($r as $k=>$v){
		//$ra[$k][0]=aj('popup|desktop,edit|id='.$v[0],$v[0],'btn');
		for($i=1;$i<$n;$i++){$cbk='inp'.$k.$i;//public can edit $v[6]
			if($kr[$i]=='picto')$ti=ico($v[$i]);
			else $ti=strlen($v[$i])>20?substr($v[$i],0,16).'...':$v[$i];
			if($kr[$i]=='com')$v[$i]=jurl($v[$i]);
			$bt=aj($cbk.'|desktop,modif|dir='.$dir.',id='.$v[0].',col='.$kr[$i].',val='.$v[$i].',cbk='.$cbk,$ti,'btn');
			$ra[$k][]=span($bt,'',$cbk);}
		$ra[$k][]=aj('dskmg|desktop,del|dir='.$dir.',id='.$v[0],pic('delete'),'btdel');}
	$modes=hlpbt('desktop_modes','mode','btn');
	$icons=aj('popup|fontawesome','icon');
	$auth=hlpbt('desktop_auth','auth','btn');
	if(auth(4))$rk=array('root',$modes,'app',$icons,'button',$auth);
	else $rk=array('root',$icons,'button',$auth);
	if($ra)array_unshift($ra,$rk); else $ra[]=$rk;
	$ret.=Build::table($ra);
	return div($ret,'','dskmg');}

//$r[]=array('dir','//j/in/lk','app','method','icon');
static function com(){
	//$r=Sql::columns(self::$db,3); unset($r['uid']); $keys=implode(',',array_keys($r));
	$keys='id,dir,type,com,picto,bt,auth'; $w='where uid="'.ses('uid').'"';
	if(auth(4))$w.=' or (dir="/apps/tlex" and auth<'.ses('auth').') ';
	$w.=' or dir like "/system%" or dir like "/public%"';
	return Sql::read($keys,self::$db,'id',$w.'order by dir');}

//content
static function content($p){$ret='';
	//self::install();
	$ret=Desk::load('desktop','com',val($p,'dir'));
	if(val($p,'dir') && !$ret)$ret=Desk::load('desktop','com','');
	return $ret;}
}
?>