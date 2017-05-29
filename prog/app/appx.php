<?php
//abstract app
class appx{
static $private='1';
static $a='';//app name
static $db='';//data base
static $cb='';//callback name
static $cols=[];//cols of db
static $typs=[];//types of cols
static $conn='';//connectors
static $db2='';//db2
static $boot;

static function boot(){//$a=self::$a;
	if(self::boot==null)self::$boot=new $a;}

static function install($p){$r['uid']='int';
	Sql::create(self::$db,merge($r,$p),1);}

#admin menus
static function admin($p=''){
	$a=self::$a; $cb=self::$cb; $rid=val($p,'rid'); $o=val($p,'o');
	if($rid)$r[]=['','j','tlxapps|tlxcall,menuapps|rid='.$rid,'',$a];
	if($o){$r[]=['','j',$cb.'|'.$a.',stream|display=2,rid='.$rid,'list','-'];
		$r[]=['','j',$cb.'|'.$a.',stream|display=1,rid='.$rid,'th-large','-'];}
	else $r[]=['','j',$cb.'|'.$a.',stream|rid='.$rid,'','open'];
	if(in_array('pub',self::$cols)){
		$r[]=['','j',$cb.'|'.$a.',stream|spread=2,rid='.$rid,'user','-'];
		$r[]=['','j',$cb.'|'.$a.',stream|spread=1,rid='.$rid,'users','-'];}
	if(ses('uid'))$r[]=['','j',$cb.'|'.$a.',create|rid='.$rid,'plus','-'];
	$r[]=['','pop','Help,com|ref='.$a.'_app','help','-'];
	if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|appSee='.$a,'code','Code'];
	if(auth(6)){
		$r[]=['admin/identity','pop','admin_lang,open|ref='.$a.',app='.$a,'lang','name'];
		$r[]=['admin/identity','pop','admin_help,open|ref='.$a,'help','name'];
		$r[]=['admin/identity','pop','admin_help,open|ref='.$a.'_app','help','help'];
		$r[]=['admin/identity','pop','admin_icons,open|ref='.$a,'picto','pictos'];
		$r[]=['admin/identity','pop','admin_labels,open|ref='.$a,'tag','label'];
		$r[]=['admin','pop','desktop,tlex_app|app='.$a,'desktop','tlex apps'];}
	return $r;}

#titles to display in popup for each method
static function titles($p){
	$d=val($p,'appMethod');
	$r['content']='welcome';
	$r['collect']='collected datas';
	$r['play']=self::$a;
	if(isset($r[$d]))return lang($r[$d]);}

#edit
static function privilege($name){
	$ex=Sql::read('id','tlex_ab','v',['usr'=>ses('user'),'ab'=>$name]);
	if($ex)return 1;}

static function permission($db,$id,$pub){
	$uid=Sql::read('uid',$db,'v',$id);
	if($uid==ses('uid') or $pub==4)return 1;
	if($pub){$pub=Sql::read('pub',$db,'v',$id);
		if($pub==2){$name=Sql::read('name','login','v',$uid);
			return self::privilege($name);}}}

static function del($p){$id=$p['id'];
	$a=self::$a; $db=self::$db; $cb=self::$cb;
	$own=Sql::read('id',$db,'v',['uid'=>ses('uid'),'id'=>$id]);
	if($own!=$id)return help('operation not permited','alert');
	if(val($p,'ok')){Sql::delete($db,$id); 
		if($db2=val($p,'db2'))Sql::delete($db2,$id,'bid');
		return $a::stream($p);}
	$ret=aj($cb.'|'.$a.',del|ok=1,id='.$id,lang('confirm deleting'),'btdel');
	$ret.=aj($cb.'|'.$a.',edit|id='.$id,lang('cancel'),'btn');
	return $ret;}

static function batch_vals($p,$cols,$o=''){
	if($o)$r=[ses('uid')];
	foreach($cols as $k=>$v){
		if($v=='int')$default=0;
		elseif($v=='date')$default=date('Y-m-d',time());
		else $default='';
		$r[$k]=val($p,$k,$default);}
	return $r;}

static function save($p){
	$a=self::$a; $db=self::$db; $cb=self::$cb; $cols=Sql::columns($db,4);
	$r=self::batch_vals($p,$cols,1);
	$p['id']=Sql::insert($db,$r);
	return $a::edit($p);}

/*static function save2($p){
	$db=self::$db2;
	$cols=Sql::columns($db,2);
	$r=vals($p,array_keys($cols));
	$p['id']=Sql::insert($db,$r);
	return aj(self::$cb.'|'.self::$a.',play|id='.$p['bid'],langp('thank you'));}*/

static function modif($p){$id=val($p,'id');
	$a=self::$a; $db=self::$db; $cols=Sql::columns($db,4); $pub=in_array('pub',$cols)?1:0;
	$ok=self::permission($db,$id,$pub); if(!$ok)return lang('permission denied');
	$r=self::batch_vals($p,$cols,0);
	Sql::updates($db,$r,$id);
	return $a::edit($p);}

//privacy
static function privacy_prm(){
	return [0=>'private',1=>'clan-visible',2=>'clan-editable',3=>'all-visible',4=>'all-editable'];}
static function privacy($n){$r=self::privacy_prm(); return lang($r[$n]);}

static function pub($nm,$val,$uid){$ret=''; $r=self::privacy_prm();
	if(!$uid or $uid==ses('uid'))foreach($r as $k=>$v){$chk=$k==$val?'checked':'';
		$atb=['type'=>'radio','name'=>$nm,'id'=>$nm.$k,'value'=>$k,'checked'=>$chk];
		$ret.=div(tag('input',$atb,'',1).label($nm.$k,lang($v)));}
	else $ret=hidden($nm,$val).lang($r[$val],'nfo');
	return $ret;}

//form
static function sets($p){$ret=''; $com=val($p,'com');
	if($com){$rv=explode('|',$com); $nb=count($rv); unset($p['com']);}
	else $nb=val($p,'nb',2);
	for($i=1;$i<=$nb;$i++){$inp[]='com'.$i; $j=atjr('multhidden',[$nb,'com']);
		if(isset($rv[$i-1]))$v=$rv[$i-1]; else $v=val($p,'com'.$i);
		$prm=['id'=>'com'.$i,'value'=>$v,'size'=>40,'onkeyup'=>$j,'onclick'=>$j];
		$prm['placeholder']=lang('setting').' '.$i;
		$ret.=div(tag('input',$prm,'',1));}
	$inps=implode(',',$inp); $_POST['com']=$inps;
	if($nb<20)$ret.=aj('setting|appx,sets|nb='.($nb+1).'|'.$inps,langp('add setting'),'btn');
	if(!val($p,'nb'))$ret=div($ret,'','setting').hidden('com',$com);
	return $ret;}

static function answ($p){$ret=''; $answ=val($p,'answ');
	if($answ){$rv=explode('|',$answ); $nb=count($rv); unset($p['answ']);}
	else $nb=val($p,'nb',2);
	for($i=1;$i<=$nb;$i++){$inp[]='answ'.$i; $j=atjr('multhidden',[$nb,'answ']);
		if(isset($rv[$i-1]))$v=$rv[$i-1]; else $v=val($p,'answ'.$i);
		$prm=['id'=>'answ'.$i,'value'=>$v,'size'=>40,'onkeyup'=>$j,'onclick'=>$j];
		$prm['placeholder']=lang('choice').' '.$i;
		$ret.=div(tag('input',$prm,'',1));}
	$inps=implode(',',$inp); $_POST['answ']=$inps;
	if($nb<20)$ret.=aj('choices|appx,answ|nb='.($nb+1).'|'.$inps,langp('add choice'),'btn');
	if(!val($p,'nb'))$ret=div($ret,'','choices').hidden('answ',$answ);
	return $ret;}

static function form($p){$cb=self::$cb; $ret='';
	$cols=Sql::columns(self::$db,4); $uid=val($p,'uid'); $html=val($p,'html');
	foreach($cols as $k=>$v){$val=val($p,$k); $bt='';
		if($k==$html)$bt=divarea($k,$val);
		elseif($k=='txt'){if(self::$conn)$bt=art::wswg($k);
			$bt.=textarea($k,$val,60,4,'','',$v=='var'?255:0);}
		elseif($k=='pub')$bt=self::pub($k,$val,$uid);
		elseif($k=='answ')$bt=self::answ($p);
		elseif($k=='com')$bt=self::sets($p);
		elseif($k=='cl')$bt=Build::toggle(['id'=>$k,'v'=>$val]);
		elseif($k=='nb')$bt=bar($k,$val,1,1,10);
		elseif($v=='var')$bt=input($k,$val,63,'','',255);
		elseif($v=='text')$bt=textarea($k,$val,60,12,'');
		elseif($v=='date')$bt=inp($k,$val?$val:date('Y-m-d',time()),8,'');
		elseif($v=='int')$bt=inp($k,$val,8,'');
		$ret.=div(label($k,lang($k)),'applabel').$bt;}
	if(val($p,'sub')){$a=self::$a; $ret.=div($a::subform($p),'',$cb.'sub');}
	return $ret;}

/*static function form2($p){
	$cb=self::$cb; $ret=''; $db=self::$db2; $id=$p['id']; $r['bid']=$id;
	$cols=Sql::columns($db,2); $cls=Sql::columns($db,3); $uid=val($p,'uid');
	foreach($cols as $k=>$v){$val=val($p,$k); $label='';
		if($k=='bid')$bt=hidden($k,$id);
		elseif($k=='uid')$bt=hidden($k,ses('uid'));
		elseif($v=='var')$bt=input($k,$val,63,'','',255);
		elseif($v=='text')$bt=textarea($k,$val,60,12,'');
		elseif($v=='date')$bt=inp($k,$val?$val:date('Y-m-d',time()),8,'');
		elseif($v=='int')$bt=inp($k,$val,8,'');
		if($k!='bid' && $k!='uid')$label=label($k,lang($k),'');
		$ret.=div(div($label,'row').div($bt,'cell'),'row');}
	$bt=aj(self::$cb.'|'.self::$a.',save2|id='.$id.'|'.$cls,langp('save'),'btsav');
	$ret.=div(div('','row').div($bt,'cell'),'row');
	return $ret;}*/

//admin	
static function create($p){
	$id=val($p,'id'); $rid=val($p,'rid');
	$a=self::$a; $cb=self::$cb; $cls=implode(',',self::$cols);
	$ret=aj($cb.'|'.$a.',stream|rid='.$rid,pic('back'),'btn');
	if($hlp=val($p,'help'))$ret.=hlpbt($hlp);
	$ret.=aj($cb.'|'.$a.',save|rid='.$rid.'|'.$cls,lang('save'),'btsav').br();
	$ret.=$a::form($p).br();
	return $ret;}

static function edit($p){
	$id=val($p,'id'); $rid=val($p,'rid'); 
	$db2=val($p,'collect'); $uid=ses('uid'); $own=0;
	$a=self::$a; $cb=self::$cb; $db=self::$db; $cls=implode(',',self::$cols);
	$r=Sql::read('id,uid,'.$cls,$db,'ra',$id); $pub=val($r,'pub'); $r['sub']=val($p,'sub');
	$ok=self::permission($db,$id,$pub); if($r['uid']==$uid or auth(6))$own=1;
	$ret=aj($cb.'|'.$a.',stream|rid='.$rid,pic('back'),'btn');
	if($rid)$ret.=insertbt(lang('use'),$id.':'.$a.'',$rid);
	$ret.=aj($cb.'edit|'.$a.',call|headers=1,id='.$id.',rid='.$rid,langp('view'),'btn');
	if($own or $ok){$r['own']=1;
		$ret.=aj($cb.'|'.$a.',edit|id='.$id.',rid='.$rid,langp('edit'),'btn');
		if($hlp=val($p,'help'))$ret.=hlpbt($hlp);
		$ret.=aj($cb.'|'.$a.',modif|id='.$id.',rid='.$rid.'|'.$cls,langp('save'),'btsav');}
	if($own){
		$ret.=aj($cb.'edit|'.$a.',del|id='.$id.',rid='.$rid,langpi('delete'),'btdel');
		if($db2)$ret.=aj($cb.'edit|'.$a.',collect|id='.$id.',db='.$db2,langpi('datas'),'btn');}
	$ret.=href('/'.$a.'/'.$id,pic('url'),'btn');
	if($own or $ok)$ret.=div($a::form($r),'',$cb.'edit');
	else $ret.=self::call($p);//arrived here by krack
	return $ret;}

#collected datas
static function collect($p){
	$ra=Sql::columns($p['db'],2); unset($ra['bid']); $ra=array_keys($ra); array_unshift($ra,'name');
	$r=Sql::read_inner(implode(',',$ra),$p['db'],'login','uid','rr',['bid'=>$p['id']],0);
	$r=array_merge([$ra],$r);
	return Build::table($r,'',0,1);}

#build
static function build($p){$id=val($p,'id');
	$a=self::$a; $db=self::$db;
	$cols=implode(',',self::$cols);
	//$cols=Sql::columns($db,2);
	$r=Sql::read($cols,$db,'ra',$id);
	//tlex will use Conn; this var is sent by tlex::reader
	if(isset($r['txt']) && self::$conn)
		$r['txt']=Conn::load(['msg'=>$r['txt'],'app'=>'','mth'=>'','ptag'=>self::$conn]);
	return $r;}

#play
static function template(){
	return '[(tit)*class=tit:div][(txt)*class=txt:div]';}

static function play($p){$ret='';
	$r=self::build($p); $a=self::$a;
	if(val($p,'pub'))$ret.=aj(self::$cb.'|'.$a.',stream|rid='.val($p,'pub'),pic('back'),'btn');
	$template=$a::template();
	$ret.=Vue::read($r,$template);
	return $ret;}

#stream //0=private,1=clan-visible,2=clan-editable,3=all-visible,4=all-editable
static function stream($p){$rid=val($p,'rid'); $ret=''; $w=''; $pb='';
	$a=self::$a; $db=self::$db; $cb=self::$cb; $cols=self::$cols; 
	$t=val($p,'t',$cols[0]); $uid=ses('uid'); $usr=ses('user');
	$dsp=ses($a.'dsp',val($p,'display')); $spread=ses($a.'spd',val($p,'spread'));
	$pub=in_array('pub',$cols)?1:0; if($spread==2)$pub='';
	if($pub)$r=Sql::read_inner($db.'.id,uid,'.$t.',pub,name,dateup',$db,'login','uid','rr','order by uid asc, id desc');
	else $r=Sql::read('id,uid,'.$t.',dateup',$db,'rr','where uid="'.$uid.'" order by id desc');
	if($r)foreach($r as $k=>$v){$ok=1;
		$tit=$v[$t]?$v[$t]:'#'.$v['id'];
		if($pub){$pb=$v['pub'];
			if($pb==2 or $pb==4)$lock=ico('unlock'); else $lock=ico('lock');
			if($v['uid']==$uid){$com='edit'; $ic='file-o';}
			else{$ic='file';
				if($pb==1 or $pb==2){
					$ex=self::privilege($v['name']);
					if($ex)$com=$pb==1?'call':'edit'; else $ok=0;}
				elseif($pb==3)$com='call'; elseif($pb==4)$com='edit'; else $ok=0;}}
		else{$com='edit'; $ic='file-o';}
		$btn=ico($ic).$tit.' '.span('#'.$v['id'].' '.$v['date'],'date');
		if($dsp==1)$lock='';
		if($pub)$btn.=' '.span(lang('by').' '.$v['name'].' '.$lock,'small');
		$c=$dsp==1?'bicon':'licon';
		if($ok)$ret.=aj($cb.'|'.$a.','.$com.'|id='.$v['id'].',rid='.$rid,$btn,$c);}
	if(!$ret)$ret=help('no element','txt');
	return div($ret,'');}

#interfaces
//title (used by desktop and shares)
static function tit($p){$id=val($p,'id');
	$t=val($p,'t',self::$cols[0]);
	if($id)return Sql::read($t,self::$db,'v',$id);}

//call (read)
static function call($p){$ret=''; $a=self::$a;
	$ret=$a::play($p);
	if(!$ret)return help('id not exists','board');
	return div($ret,'',self::$cb.$p['id']);}

//com (write)
static function com($p){$rid=val($p,'rid'); $a=self::$a; $ret='';
	//rid (will focus on tlex editor)
	if(method_exists($a,'admin'))$ret=Menu::call(['app'=>$a,'method'=>'admin','rid'=>$rid]);
	$ret.=$a::content($p);
	return $ret;}

//interface
static function content($p){
	$a=self::$a; $cb=self::$cb;
	$p['id']=val($p,'id',val($p,'param'));
	if($p['id'])$ret=$a::call($p);
	else $ret=$a::stream($p);
	return div($ret,'paneb',$cb);}
}
?>