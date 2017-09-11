<?php
class bank{
static $private='2';
static $a='bank';
static $db='bank';
static $cb='bnk';
static $cols=['label','value','type','at','cl'];//credit-rights
static $typs=['var','int','int','int','int'];
static $conn=0;
static $db2='bank_credits';
static $open=1;
static $coins=['red','blue','green'];
static $coinb=['red'=>0,'blue'=>1,'green'=>2];
static $money=['mass','time','space'];
static $usage=['product','work','resource'];
static $clr=['#CC3366','#3366CC','#66CC33'];
static $pic=['coffee','plane','suitcase'];
static $css=['red'=>'mass','blue'=>'time','green'=>'space'];

function __construct(){
	$r=['a','db','cb','cols','db2','conn'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
	appx::install(array_combine(self::$cols,self::$typs));
	Sql::create(self::$db2,['uid'=>'int','red'=>'int','blue'=>'int','green'=>'int'],1);}

static function admin($rid=''){
	$p['rid']=$rid; $p['o']='0';
	$r=appx::admin($p);
	$r[]=['','j',self::$cb.'|'.self::$a.',stream2|rid='.$rid,'circle','stock'];
	return $r;}

static function titles($p){return appx::titles($p);}
static function injectJs(){return '';}
static function headers(){
	list($red,$green,$blue)=self::$clr;
	Head::add('csscode','
	.coin{background:#ffffff; padding:0 4px;}
	.coin:hover{box-shadow:0,0,6px,rgba(0,0,0,0.4);}
	.btsav:hover .coin{color:black;}
	.mass,.time,.space{margin:2px; padding:2px 6px 1px; color:white;}
	.mass{background:'.$red.';}
	.space{background:'.$blue.';}
	.time{background:'.$green.';}
	.coin_in{background:#f4f4f4; margin:4px 0; color:black;}');
	Head::add('jscode',self::injectJs());}

#edit
static function collect($p){
	return appx::collect($p);}

static function del($p){
	//$p['db2']=self::$db2;
	return appx::del($p);}

static function save($p){
	return appx::save($p);}

static function modif($p){
	return appx::modif($p);}

static function fc_type($k,$v){
	if(!auth(6))return hidden($k,$v);
	return radio($k,self::$usage,$v,'2');}

static function fc_at($k,$v){return hidden($k,$v);}
static function fc_cl($k,$v){return hidden($k,$v);}

static function form($p){
	//$p['html']='txt';
	$p['fctype']=1;
	$p['fcat']=1;
	$p['fccl']=1;
	return appx::form($p);}

static function edit($p){
	//$p['collect']=self::$db2;
	//$p['help']='bank_edit';
	//$p['sub']=1;
	return appx::edit($p);}

static function create($p){
	//$p['pub']=0;//default privacy
	return appx::create($p);}

//coins
static function coin($n,$ty=0){
	//$ret=ascii(8475).' '.$n;//$unit=unicode('%u211D');
	if(is_numeric($ty))$ty=self::$clr[$ty];
	$ret=ico('circle','color:'.$ty);//money
	$ret=span($ret.$n,'coin');
	return $ret;}

static function stock($n,$ty){
	$ret=div(lang(self::$coins[$ty].'_money'));
	$ret.=div(self::coin($n,$ty),'coin_in');
	return div($ret,'coin '.self::$money[$ty]);}

static function investigate(){$uid=ses('uid');
	$r=self::read(); //p($r);
	if(!$r)return self::init();
	if($r)foreach($r as $k=>$v){
		if($v['at']==$uid)$rb[$v['type']][]=$v['value'];
		if($v['uid']==$uid)$rb[$v['type']][]=(0-$v['value']);}
	if($rb)foreach($rb as $k=>$v){$coin=self::$coins[$k]; $va=array_sum($v); $rc[$coin]=$va;}
	return $rc;}

static function account(){$ret='';
	$r=self::investigate();
	if($r)self::update($r); $i=0;
	if($r)foreach($r as $k=>$v)$ret.=div(self::stock($v,$i++),'cell');
	return div($ret,'','row account');}

static function watch($p){$ret=''; $d=val($p,'amount'); $ty=val($p,'type'); $i=0;
	$r=Sql::read(self::$coins,self::$db2,'rw',['uid'=>ses('uid')]);
	if($r)foreach($r as $k=>$v)$ret.=self::stock($v,$i++);
	$ret=div($ret,'','account');
	if($d)$ret.=div(lang('you spend').' '.self::coin($d,$ty),'btn');
	return $ret;}

#transaction
static function finalize_transaction($p){
	$r=vals($p,['uid','label','value','type','at','cl']);
	$ex=Sql::read('id',self::$db,'v',$r);
	if($ex==$p['tid'])Sql::update(self::$db,'cl',1,$ex);
	return $ex;}

static function create_transaction($p){
	$r=vals($p,['uid','label','value','type','at','cl']);
	$r['type']=self::$coinb[$r['type']];//red=>0
	if($ex=Sql::read('id',self::$db,'v',$r))return $ex;
	return Sql::insert(self::$db,$r);}

static function possibility($p){
	list($amount,$type,$at)=valk($p,['value','type','at']);
	if(is_numeric($type))$type=self::$coins[$type];
	$stock=Sql::read($type,self::$db2,'v',['uid'=>ses('uid')]);
	if($stock<$amount)return span(langp('insufficient credit'),'alert');}

static function payment($p){
	list($amount,$type,$at,$lbl)=valk($p,['value','type','at','label']);
	$p['uid']=ses('uid');
	$ok=self::possibility($p); if($ok)return $ok;
	$p['tid']=self::create_transaction($p);
	if(!$p['tid'])return help('transaction fail','alert');
	if($at==$p['tid'])return 1;
	$ok=self::finalize_transaction($p);
	if($ok)return $ok;}

static function transfert($p){
	$r=self::build($p); //p($r);
	$ok=self::possibility($r); if($ok)return $ok;
	$rb=['uid'=>ses('uid'),'at'=>$r['uid'],'cl'=>1];
	Sql::updates(self::$db,$rb,$p['id']);
	return span(lang('transaction completed'),'valid');}

//refresh new sums
static function update($rc){
	$id=Sql::read('id',self::$db2,'v',['uid'=>ses('uid')]);
	if($id)Sql::updates(self::$db2,$rc,ses('uid'),'uid');
	else{array_unshift($rc,ses('uid')); Sql::insert(self::$db2,$rc);}}

static function init(){
	$r=['uid'=>'0','label'=>'system donation','value'=>0,'type'=>0,'at'=>ses('uid'),'cl'=>1];
	$r['value']=100; $r['type']=0; $ok=Sql::insert(self::$db,$r);
	$r['value']=10; $r['type']=1; $ok=Sql::insert(self::$db,$r);
	$r['value']=1; $r['type']=2; $ok=Sql::insert(self::$db,$r);
	return ['red'=>100,'blue'=>10,'green'=>1];}

#play
static function read(){$uid=ses('uid');
	$w='where (uid='.$uid.' or at='.$uid.') and cl=1';
	return Sql::read('all',self::$db,'rr',$w);}

static function build($p){$id=val($p,'id');
	$r=Sql::read('all',self::$db,'ra',$id);
	return $r;}

static function play($p){
	$r=self::build($p); //p($r);
	$rid=randid('pay');
	$ret=div($r['label'],'tit');
	$bt=langp('pay').' '.self::coin($r['value'],$r['type']);
	if($r['at']==0)$ret.=div(aj($rid.'|bank,transfert|id='.$p['id'],$bt,'btsav'),'',$rid);
	else $ret.=span(lang('transaction closed'),'alert');
	return $ret;}

//stream
static function stream2($p){$rid=val($p,'rid');
	$a=self::$a; $db=self::$db; $cb=self::$cb; $cols=self::$cols; 
	$t=$cols[0]; $uid=ses('uid'); $me=lang('me');
	$dsp=ses($a.'dsp',val($p,'display'));
	$ret=self::account().br();
	$r=Sql::read('id,uid,label,value,type,at,dateup',$db,'rr','where (uid="'.$uid.'" or at='.$uid.') and cl=1 order by up desc');
	$rt[]=['id',lang('label'),lang('credit'),lang('debit'),lang('type'),lang('from'),lang('to'),lang('date')];
	if($r)foreach($r as $k=>$v){$ok=1; $credit=''; $debit=''; $from=''; $to='';
		$money=self::$money[$v['type']]; $usage=self::$usage[$v['type']];
		if($v['at']==$uid)$credit=div($v['value'],$money); 
		if($v['uid']==$uid)$debit=div(0-$v['value'],$money);
		$tit=aj($cb.'|'.$a.',edit|id='.$v['id'].',rid='.$rid,$v[$t],''); //$tit=$v[$t];
		$from=$v['uid']==$uid?$me:$v['uid']; $to=$v['at']==$uid?$me:$v['at'];
		$rt[]=['#'.$v['id'],$tit,$credit,$debit,lang($usage),$from,$to,span($v['date'],'date')];}
	$ret.=Build::table($rt,'','',1);
	return $ret;}

static function stream($p){$rid=val($p,'rid'); $ret='';
	$a=self::$a; $db=self::$db; $cb=self::$cb; $cols=self::$cols; 
	$t=val($p,'t',$cols[0]); $uid=ses('uid'); $usr=ses('user');
	$dsp=ses($a.'dsp',val($p,'display'));
	$r=Sql::read('id,uid,'.$t.',value,type,at,dateup',$db,'rr','where uid="'.$uid.'" and cl=0 order by up desc');
	if($r)foreach($r as $k=>$v){$ok=1;
		$tit=$v[$t]?$v[$t]:'#'.$v['id'];
		$com='edit'; $ic=self::coin($v['value'],$v['type']);
		$btn=$ic.$tit.' '.span('#'.$v['id'].' '.$v['date'],'date');
		$c=$dsp==1?'bicon':'licon';
		if($ok)$ret.=aj($cb.'|'.$a.','.$com.'|id='.$v['id'].',rid='.$rid,$btn,$c);}
	if(!$ret)$ret=help('no element','txt');
	return div($ret,'');}

#call (read)
static function tit($p){
	$p['t']=self::$cols[0];
	return appx::tit($p);}

static function call($p){
	return appx::call($p);}

#com (edit)
static function com($p){
	return appx::com($p);}

#interface
static function content($p){
	self::install();
	return appx::content($p);}
}
?>