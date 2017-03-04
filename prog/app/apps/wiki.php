<?php

class wiki{
static $private='6';
static $db='wiki';

//https://en.wikipedia.org/w/api.php?action=query&titles=Main%20Page&prop=revisions&rvprop=content&format=json

static function injectJs(){return '';}
		
static function headers(){
	Head::add('csscode','');
	Head::add('jscode',self::injectJs());}

static function install(){
	Sql::create(self::$db,['uid'=>'int','txt'=>'var'],1);}
	
#operations
static function del($p){
	$id=val($p,'id'); $rid=val($p,'rid');
	if(val($p,'ok'))Sql::delete(self::$db,$id);
	else return aj($rid.'|wiki,del|ok=1,rid='.$rid.',id='.$id,lang('confirm deleting'),'btdel');
	return self::com($p);}
	
static function modif($p){$id=val($p,'id');
	Sql::update(self::$db,'txt',val($p,'txt'),$id);
	return self::edit($p);}
	
static function save($p){
	$p['id']=Sql::insert(self::$db,[ses('uid'),val($p,'txt')]);
	return self::edit($p);}
	
static function add($p){
	$id=val($p,'id'); $rid=val($p,'rid'); $xid=val($p,'xid');
	$ret=aj($rid.'|wiki,menu|rid='.$rid.',xid='.$xid,langp('back'),'btn');
	$ret.=aj($rid.'|wiki,save|rid='.$rid.'|txt',lang('save'),'btsav').br();
	$ret.=textarea('txt','',40,4,'','',100);
	return $ret;}
	
#editor	
static function edit($p){
	$id=val($p,'id'); $rid=val($p,'rid'); $xid=val($p,'xid');
	$r=Sql::read('txt',self::$db,'ra',['id'=>$id]);
	$ret=aj($rid.'|wiki,menu|rid='.$rid.',xid='.$xid,langp('back'),'btn');
	$ret.=aj($rid.',,z|wiki,modif|rid='.$rid.',xid='.$xid.',id='.$id.'|txt',langp('modif'),'btsav');
	$ret.=aj($rid.'|wiki,del|rid='.$rid.',xid='.$xid.',id='.$id,langp('delete'),'btdel');
	$ret.=aj('popup|wiki,call|rid='.$rid.',xid='.$xid.',id='.$id,langp('view'),'btn');
	if($xid)$ret.=insertbt(lang('use'),$id.':wiki',$xid);
	$ret.=div(textarea('txt',$r['txt'],40,4,'','',100),'txt');
	return $ret;}
	
#reader
static function build($p){$id=val($p,'id');
	$r=Sql::read('txt',self::$db,'ra',['id'=>$id]);
	if(val($p,'brut'))$ret=$r['txt'];
	else $ret=Conn::load(['msg'=>$r['txt'],'app'=>'','mth'=>'','ptag'=>1]);
	return $ret;}
	
static function menu($p){$rid=val($p,'rid'); $xid=val($p,'xid');
	$r=Sql::read('id,txt,dateup',self::$db,'rr','order by id desc limit 20');
	$ret['head']=hlpbt('wiki');
	$ret['head'].=aj($p['rid'].'|wiki,add|rid='.$p['rid'],langp('add'),'btn');
	if($r)foreach($r as $k=>$v){$btn=$v['txt']?$v['txt']:$v['id'];
		$ret['obj'][]=aj($rid.'|wiki,edit|rid='.$rid.',xid='.$xid.',id='.$v['id'],$btn);}
	$structure=['head','list'=>'obj'];
	return Phylo::read($ret,$structure);}
	
#interfaces
static function tit($p){$id=val($p,'id');
	if($id)return Sql::read('dateup',self::$db,'v','where id='.$id);}
	
//call (used by connectors)
static function call($p){
	$ret=self::build($p);
	return div($ret,'wiki');}
	
//com (apps)
static function com($p){$id=val($p,'id');
	$bt=hlpbt('wiki');
	$p['xid']=val($p,'rid');
	$p['edit']=1;//objects used for edition don't appear to public
	return self::content($p);}
	
//interface
static function content($p){
	//self::install();
	$p['rid']=randid('md');
	$bt=hlpbt('wiki_app');
	$id=val($p,'id',val($p,'param'));
	if($id && val($p,'edit'))$ret=self::edit($p);
	elseif($id)$ret=self::call(['id'=>$id]);
	else $ret=self::menu($p);
	return div($ret,'board',$p['rid']);}
}
?>