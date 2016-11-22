<?php

class telex{
	static $private=0;
	static $width=590;
	static $objects=0;
	static $db='telex_xt';
	static $title='Telex';
	static $description='Telex is an objects social network';
	static $image='http://tlex.fr/usr/telex/telex.png';
	
	//install
	static function install(){
	Sql::create(self::$db,['uid'=>'int','txt'=>'var','lbl'=>'int','ib'=>'int'],'1');
	Sql::create('telex_ab',['usr'=>'var','ab'=>'var','list'=>'var','no'=>'int'],'');
	Sql::create('telex_web',['url'=>'var','tit'=>'var','txt'=>'var','img'=>'var']);
	Sql::create('telex_lik',['luid'=>'int','lik'=>'int']);
	//typntf:1=quote,2=reply,3=like,4=subsc,5=chat
	Sql::create('telex_ntf',['4usr'=>'var','byusr'=>'var','typntf'=>'int','txid'=>'var','state'=>'int']);}
	
	static function titles($p){
		$d=val($p,'appMethod'); if(!$d or $d==1)return;
		$r['content']='welcome';
		$r['read']='telex';
		if(val($p,'ntf'))$r['read']='notifications';
		$r['editor']='publish';
		if($type=val($p,'type')=='bers')$r['subscrptn']='subscribers';
		else $r['subscrptn']='subscriptions';
		$r['objplayer']='object player';
		if(isset($r[$d]))return lang($r[$d]);}
	
	#headers		
	static function injectJs(){
		if(ses('uid'))return '
	var activelive=1; var nbnew=0; var reloadtime=10000;
	setTimeout("telexlive(0)",1000);
	setTimeout("telexlive(1)",3600000);';}
	
	static function headers(){
		Head::add('meta',array('attr'=>'property','prop'=>'og:title','content'=>self::$title));
		Head::add('meta',array('attr'=>'property','prop'=>'og:description','content'=>self::$description));
		Head::add('meta',array('attr'=>'property','prop'=>'og:image','content'=>self::$image));
		Head::add('csslink','/css/telex.css');
		Head::add('jslink','/js/telex.js');
		Head::add('jscode',self::injectJs());}
	
	#ajax
	static function refresh($p){$own=ses('user');
		$p['count']=1; $recents=self::api($p);
		$notifs=Sql::read('count(id)','telex_ntf','v','where 4usr="'.$own.'" and (typntf=1 or typntf=2 or typntf=3) and state=1');//typntf:1=quote,2=reply,3=like,4=subsc,5=chat
		$subscr=Sql::read('count(id)','telex_ntf','v','where 4usr="'.$own.'" and typntf=4 and state=1');
		$chat=Sql::read('count(id)','telex_ntf','v','where 4usr="'.$own.'" and typntf=5 and state=1');
		return $recents.'-'.$notifs.'-'.$subscr.'-'.$chat;}
	
	#saves
	static function savemetas($d){$r=self::playmetas($d);
		if(!$r){$r=Html::metas($d);
			if($r[2])$f=File::saveimg($r[2],'web','590','400'); else $f='';
			if($r[0])Sql::insert('telex_web',[$d,$r[0],$r[1],$f]);}
		return $r;}
	
	static function del($p){$id=val($p,'did');
		if(!val($p,'confirm')){
			$ja='div,tlxbck,x|telex,del|did='.$id.',confirm=1';
			$no=btj(pic('close'),'closediv(\'panedit'.val($p,'idv').'\')');
			return aj($ja,langp('confirm deleting').' telex #'.$id,'btdel').' '.$no;}
		else{Sql::delete(self::$db,$id); Sql::delete('telex_ntf',$id,'txid');}
		return self::read($p);}
	
	//build connectors
	static function build_conn($d,$o=''){$ret='';
		$d=clean_n($d);
		$d=str_replace("\n",' (nl) ',$d);
		$r=explode(' ',$d);
		foreach($r as $v){
			if(substr($v,0,1)=='@'){$v=substr($v,1); $ret[]='['.$v.':@]'; $_POST['ntf'][$v]=1;}
			elseif(substr($v,0,1)=='#')$ret[]='['.substr($v,1).':#]';
			elseif(is_img($v)){
				$f=File::saveimg($v,'telex','590','400');
				$ret[]='['.($f?$f:$v).':img]';}
			elseif(substr($v,0,4)=='http'){
				$xt=extension($v);
				if($xt=='.mp3')return '['.$v.':audio]';
				elseif($xt=='.mp4')return '['.$v.':mp4]';
				//elseif($p=='http')return Conn::href($d,'btlk',1);
				$metas=self::savemetas($v);
				if(Video::provider($v))$ret[]='['.$v.':video]';
				elseif($metas)$ret[]='['.$v.':web]';
				else $ret[]='['.$v.':url]';}
			else $ret[]=$v;
			$conn=substr($v,0,1)=='['?1:0;
			if($conn && substr($v,-4)==':id]' && $id=substr($v,1,-4)){
				$usr=Sql::read_inner('name',self::$db,'login','uid','v','where '.self::$db.'.id='.$id);
				if($usr)$_POST['ntf-r'][$usr]=1;}}//notify
		if($ret)$d=implode(' ',$ret);
		$d=str_replace(' (nl) ',"\n",$d);
		$d=str_replace(':img]'."\n",':img]',$d);//
		return trim($d);}
	
	static function save($p){$txt=val($p,$p['ids']); $_POST['ntf']='';
		$lbl=val($p,'lbl'); if($lbl && !is_numeric($lbl))
			$lbl=Sql::read('id','labels','v','where ref="'.$lbl.'"');
		if($conn=val($p,'conn'))$txt='['.$txt.':'.$conn.']';
		if($txt){$txt=self::build_conn($txt,1);
			$id=Sql::insert(self::$db,array(ses('uid'),$txt,$lbl,val($p,'ibs')));
			if(isset($_POST['ntf']))self::saventf($id,1,'ntf');
			if(isset($_POST['ntf-r']))self::saventf($id,2,'ntf-r');}
		return self::read($p);}
	
	#editor
	//static function area($p){}
	
	/*
		//$r['contenteditable']='true'; $r['id']=$rid; $r['class']='area';
		//$ret.=tag('div',$r,$msg);
		//$ret=tag('form',['name'=>'pubbt','action'=>'javascript:ajaxCall(\'div,tlxbck,resetform|telex,save\',\'ids='.$rid.'|'.$rid.'\',\'\');'],$ret);
		//$sav=span(btj(langp('publish'),'document.forms[\'pubbt\'].submit();','btsav'),'right');
	*/
	
	static function realib($id){
		$d=Sql::read('txt','telex_xt','v','where id='.$id);
		if(strpos($d,':id]'))$id=segment($d,'[',':id]');
		return $id;}
	
	//publishbt()//insertbt()
	static function editor($p){
		$ib=val($p,'ib'); $idv=val($p,'idv'); $rid=randid('ids');
		$ret=''; $appsbt='';
		if($qo=val($p,'quote'))$msg='['.self::realib($qo).':id]';
		else if($to=val($p,'to'))$msg='@'.$to.' ';
		else if($id=val($p,'id'))$msg=Sql::read('txt',self::$db,'v','where id='.$id);
		else $msg=val($p,'msg');
		if($ib)$ret.=div(lang('in-reply to').' '.$to,'grey');
		if($qo)$ret.=div(lang('repost'),'grey');
		$js='strcount1(\''.$rid.'\',255); resizearea(\''.$rid.'\');';
		$r=array('class'=>'area','id'=>$rid,'onkeyup'=>$js,'onmousedown'=>$js,'placeholder'=>'message');
		//if(!$ib && !$qo && !$to)$r['onfocus']='togglediv(\'edtsv\',1);';
		$ret.=tag('textarea',$r,$msg);//form
		$count=span('255','btxt small','strcnt'.$rid).' ';
		$ja='div,tlxbck,resetform|telex,save|ibs='.$ib.',ids='.$rid.'|'.$rid.',lbl';
		$prm['onclick']='closediv(\'tlxapps\'); closediv(\'lbcbk\');'; $prm['id']='edtbt';
		$sav=span($count.aj($ja,langp('publish'),'btsav',$prm),'right').' ';//save
		if(!$ib && !$qo && !$to && !$id){
			$tg=dropdown('tlxcall,menuapps|rid='.$rid,langp('applications'),'btn');
			$tg.=dropdown('tlxcall,ascii|rid='.$rid,'&#128522;','btn');
			//if(auth(4))$tg.=toggle('tlxapps|fontawesome,com|id='.$rid,picto('smile'),'btn');
			$tg.=dropdown('tlxcall,labels',langp('labels'),'btn');
			$tg.=span(span('','','lblxt'),'','lbcbk').hidden('lbl',0);
			$sav.=div($tg,'edtbt');//embed used to close others
			$sav.=div('','','tlxapps','');//put editor here if use object mode
			$ret.=div($sav,'','edtsv');}//,'display:none;'
		else{$ret.=div($sav,'','edtsv');}
			//$ret.=btj(pic('close'),'closediv(\'panedit'.$idv.'\')');
		$ret.=div('','clear');
	return $ret;}
	
	#players
	static function playmetas($d){
		return Sql::read('tit,txt,img','telex_web','rw','where url="'.$d.'"');}
		
	static function playweb($d,$o=''){
		$d=http($d); $r=self::playmetas($d); //$r=self::savemetas($d);
		if($o){$p=Video::provider($d); if($p)$id=Video::extractid($d,$p);}
		$t=$r[0]?$r[0]:domain($d);
		$lk=href($d,$t,'btlk','',1);
		if(!$r)return $lk;
		if(substr($r[2],0,4)=='http'){$f=$r[2];
			if($r[2])$imx=@getimagesize($r[2]); else $imx[0]='x';
			if(is_numeric($imx[0]))$img=img($r[2],'590'); else $img='';}
		elseif($r[2])$f=self::thumb($r[2],'medium','web'); else $f='';
		if(is_file($f))$img=img('/'.$f,'590'); else $img='';
		if($img && isset($id))$ban=pagup('Video,call|p='.$p.',id='.$id,$img,'');
		elseif($img)$ban=pagup('telex,objplayer|popwidth=550px,obj=playweb,p1='.nohttp($d),$img,'');
		else $ban=$img;
		$ret=tag('strong','',$lk).div($r[1]).div(href($d,'','grey')).div('','clear');
		$ret=div($ret,'pncxt');
		return div($ban.$ret,'panec');}
		
	static function playquote($id){
		$r=self::api(['id'=>$id]);
		if(!$r)return div(lang('telex_deleted'),'paneb');
		$v=$r[0]; $v['idv']='qlx'.$id;
		$ret=self::panehead($v,'popup');
		$ret=tag('header',['class'=>''],$ret);
		$ret.=Conn::load(['msg'=>$v['txt'],'app'=>'telex','mth'=>'reader','opt'=>'it2']);
		return div($ret,'paneb',$v['idv']);}
	
	static function thumb($f,$dim,$dir='telex'){
		$dr='img/'.$dir.'/'; $fb='medium/'.$f; $big=is_file($dr.$fb);
		if($dim=='mini' or $dim=='micro')$im='mini/'.$f;
		elseif($dim=='medium')$im=$big?$fb:'full/'.$f; else $im='full/'.$f;
		if(is_file($dr.$im))if(filesize($dr.$im))return $dr.$im;}
	
	static function playthumb($f,$dim='',$o=''){$sz=590; if($dim=='micro')$sz=86;
		if(substr($f,0,4)=='http')$f=File::saveimg($f,'telex',$sz);
		$fb=self::thumb($f,$dim);
		if($o)return img('/'.$fb,$sz);
		if($f)return imgup('img/telex/full/'.$f,img('/'.$fb,$sz));}
	
	static function url($p,$o,$e=''){$t=$o?$o:domain($p);
		//$pop=popup('telex,objplayer|obj=playweb,p='.$p.',o='.$o,$t,'btlk');
		return href($p,$t.' '.pic('external-link'),'','',$e);}
	
	static function objplayer($p){$func=$p['obj'];
		return self::$func(val($p,'p1'),val($p,'p2'));}
	
	//connectors
	static function reader($d,$b=''){
		list($p,$o,$c)=readconn($d);
		if(is_img($d))return img($d,'','',$o);
		switch($c){
			case('@'):return dropdown('telex,profile|usr='.$p,'@'.$p,'btlk'); break;
			case('#'):return aj('popup|telex,search_txt|srch='.$p,'#'.$p,'btlk'); break;
			case('b'):return tag('strong',['class'=>$o],$p); break;
			case('i'):return tag('em',['class'=>$o],$p); break;
			case('q'):return tag('blockquote',['class'=>$o],$p); break;
			case('red'):return tag('red',['class'=>$o],$p); break;
			case('list'):return Conn::mklist($p); break;
			case('id'):if(is_numeric($p))return self::playquote($p); break;
			case('url'):return self::url($p,$o,''); break;
			case('img'):$ret=''; if(Conn::$one!=1)$ret=self::playthumb($p,'full');
				self::$objects[$c][]=[$p,$o]; Conn::$one=1; return $ret; break;
			case('web'):self::$objects[$c][]=[$p,$o]; return self::playweb($p); break;
			case('audio'):return audio($p); break;
			case('mp4'):return video($p); break;
			case('video'):self::$objects[$c][]=[$p,$o]; return self::playweb($p,1); break;
			case('art'):return href('/art/'.$p,article::tit(['id'=>$p]),'btlk'); break;
			case('article'):self::$objects[$c][]=[$p,$o]; return; break;
			case('chat'):self::$objects[$c][]=[$p,$o]; return; break;
			case('gps'):self::$objects[$c][]=[$p,$o]; return; break;
			case('app'):return aj('popup|'.$p.'|param='.$o,pico($p,32),'btn'); break;
			case('open'):if(method_exists($p,$o))return $p::$o; break;
			case('picto'):return picto($p,$o?$o:24); break;
			case('pic'):return pic($p,$o?$o:24); break;
			case('ascii'):return '&#'.$p.';'; break;
			default:if(method_exists($c,'call')){self::$objects[$c][]=[$p,$o];
				return App::open($c,['appMethod'=>'call','id'=>$p]);} break;
		}
		return '['.$d.']';}
	
	//objects
	static function objects(){$ret=''; $r=self::$objects; $sz='24'; $css='licon';//36 from css
		if($r)foreach($r as $kr=>$vr){$imok=0;
		foreach($vr as $k=>$v){$fc=''; $ic=''; list($p,$o)=$v;
		switch($kr){
		case('img'):if($imok)$ret.=self::playthumb($p,'micro'); $imok=1; break;
		case('article'): $t=Sql::read('tit','articles','v','where id='.$p);
			$ret.=pagup('article,read|popwidth:550px,tlx=1,id='.$p,pic('file-text-o',$sz).span($t),$css);
			break;
		case('chat'):$rb=Sql::read_inner('name','chatlist','login','ruid','rv','where roid='.$p);
			if($rb)$with=lang('with').' '.implode(', ',$rb); else $with='';
			$ret.=aj('pagup,,,1|chat|param='.$p.',headers=1',pic('comments',$sz).span($with),$css); break;
		case('gps'):$t=Gps::com(['coords'=>$p]);
			$ret.=pagup('map,com|coords='.$p,pic('map-marker',$sz).span($t),$css); break;
		//case('poll'):$t='oo'; $ret.=pagup('map,com|coords='.$p,pic('map-marker',$sz).span($t),$css); break;
		}
		if($ic)$t=pic($ic,$sz).span($t);//when called method need an interface
		if($fc)$ret.=pagup('telex,objplayer|obj='.$fc.',p1='.$p.',p2='.$o,$t,$css);}}
		if($ret)return div($ret,'');}//panec
	
	#search	
	static function search_txt($p){$srch=val($p,'srch');
		$ret=div($srch,'btit');
		$r=self::api(['sr'=>$srch]);
		if($r)foreach($r as $k=>$v)$ret.=self::pane($v,1);
		return $ret;}
	
	static function searchbt(){
		$prm=['id'=>'srchfrm','action'=>'javascript:search2(\'srch\');'];
		$r=array('type'=>'text','id'=>'srch','value'=>'','size'=>28,'placeholder'=>lang('search'));
		return tag('form',$prm,tag('input',$r,'',1));}
	
	#like
	static function savelike($p){$id=val($p,'id'); $lid=val($p,'lid'); $nlik=val($p,'nlik');
		if($lid){Sql::delete('telex_lik',$lid); $p['lid']='';}
		else{$p['lid']=Sql::insert('telex_lik',[ses('uid'),$id]);
			$_POST['ntf-lik'][$p['name']]=1; self::saventf($id,3,'ntf-lik');}
		return self::likebt($p);}
		
	static function likebt($p){$rid=randid('lik'); $mylik=''; $sty='';
		$id=val($p,'id'); $lid=val($p,'lid'); $n='';
		$nlik=Sql::read('count(id)','telex_lik','v','where lik='.$id);
		if($lid){
			$mylik=Sql::read('id','telex_lik','v','where lik='.$id.' and luid='.ses('uid'));
			if($mylik)$sty='color:#e81c4f;';}
		$bt=pic('heart',$sty,'like','','',span($nlik,'liknb'));
		$ret=aj($rid.'|telex,savelike|id='.$id.',lid='.$mylik.',name='.$p['name'],$bt);
		return span($ret,'',$rid);}
	
	#follow
	static function follow($p){
		$usr=val($p,'usr'); $list=val($p,'follow',val($p,'subschan'));
		$unf=val($p,'unfollow'); $rid=val($p,'rid');
		if($list){Sql::insert('telex_ab',[ses('user'),$usr,$list]);
			$_POST['ntf-ab'][$usr]=1; self::saventf(ses('user'),4,'ntf-ab');
			return self::followbt($p);}
		elseif($unf){Sql::delete('telex_ab',$unf);
			$ntf=Sql::read('id','telex_ntf','v','where 4usr="'.$usr.'" and typntf=4');
			Sql::delete('telex_ntf',$ntf); return self::followbt($p);}
		elseif(val($p,'chan')){$r=Sql::read('distinct(list)','telex_ab','k','where usr="'.$usr.'"');
			$r=merge($r,['mainstream'=>1,'local'=>1,'global'=>1,'passion'=>1,'extra'=>1]);
			$ret=div(lang('subscribe'),'btit'); $bt='';
			$ret.=input('subschan','').' ';
			$ret.=aj($rid.'|telex,follow|usr='.$usr.',rid='.$rid.'|subschan',lang('ok'),'btsav');
			foreach($r as $k=>$v)
				$bt.=aj($rid.'|telex,follow|usr='.$usr.',rid='.$rid.',follow='.$k,$k);
				return div($ret.div($bt,'list'),'pane',$rid);}}
		
	static function followbt($p){$usr=val($p,'usr'); $rid=val($p,'rid',randid('flw'));
		$id=Sql::read('id','telex_ab','v','where usr="'.ses('user').'" and ab="'.$usr.'"');
		if($id)$ret=aj($rid.'|telex,follow|usr='.$usr.',unfollow='.$id.',rid='.$rid,lang('unfollow'),'btdel');
		else $ret=dropdown('telex,follow|chan=1,usr='.$usr.',rid='.$rid,lang('follow'),'btsav');
		return span($ret,'followbt',$rid);}
	
	#subscriptions
	static function subscrptn($p){$type=val($p,'type'); $usr=val($p,'usr'); $tit=''; $ret='';
		if($type=='ber')$r=Sql::read('usr','telex_ab','k','where ab="'.$usr.'"');
		elseif($type=='ption')$r=Sql::read('ab','telex_ab','k','where usr="'.$usr.'"');
		$n=isset($r)?count($r):'';
		//if($usr!=ses('user'))$tit=self::profile(['usr'=>$usr,'big'=>1]);
		$tit.=div($n.' '.lang('subscri'.$type.plurial($n)),'btit');
		//new
		if($type=='ber'){
			$rb=Sql::read('txid,id','telex_ntf','kv','where 4usr="'.$usr.'" and typntf=4 and state=1');
			if($n=count($rb)){$newabs=implode(', ',array_keys($rb));
				$tit.=tag('h3','',$n.' '.lang('new subscriber'.plurial($n)).': '.$newabs);}
			if($rb)foreach($rb as $k=>$v){$ret.=self::profile(['usr'=>$k]);
				unset($r[$k]); Sql::update('telex_ntf','state','0',$v);}}
		if($r)foreach($r as $k=>$v)$ret.=self::profile(['usr'=>$k]);
		return $tit.div($ret,'cols');}
	
	static function subscribt($usr,$uid){
		if(!$uid)$uid=Sql::read('id','login','v','where name="'.$usr.'"');
		$n0=Sql::read('count(id)',self::$db,'v','where uid="'.$uid.'"');
		$n1=Sql::read('count(id)','telex_ab','v','where usr="'.$usr.'"');
		$n2=Sql::read('count(id)','telex_ab','v','where ab="'.$usr.'"');
		$bt=div(div(lang('published telex'),'subscrxt').div($n0,'subscrnb'),'subscrbt');
		$ret=self::loadtm('tm='.$usr.',noab=1',$bt);
		$bt=div(div(lang('subscriptions'),'subscrxt').div($n1,'subscrnb'),'subscrbt');
		$ret.=aj('tlxbck|telex,subscrptn|type=ption,usr='.$usr,$bt);
		$bt=div(div(lang('subscribers'),'subscrxt').div(span($n2,'','tlxsub'),'subscrnb'),'subscrbt');
		$ret.=aj('tlxbck|telex,subscrptn|type=ber,usr='.$usr.'|tlxsub',$bt);
		$ret.=hidden('tlxsubnb',$n2).div('','clear');
		return $ret;}
	
	#profile	
	static function profile($p){$usr=val($p,'usr'); $uid=val($p,'uid'); $ret='';
		if($usr){$ret=div(profile::read($p),'','prfl');
			//if(ses('user')!=$usr)$ret.=div(self::followbt(['usr'=>$usr]),'subscrbt');
			if(ses('user')==$usr)$ret.=div(self::subscribt($usr,$uid),'subscrban');}
	if($ret)return div($ret,'profile');}
	
	static function avatar($p){$im=val($p,'avatar');
		$clr=sesif('clr'.val($p,'name'),Clr::random());
		$f=profile::avatar_im($im,'mini',$clr);
		return profile::divim($f,'avatarsmall',$clr);}
	
	#notifications
	static function saventf($id,$type,$o){$r=$_POST[$o];
		if($r)foreach($r as $k=>$v)if($k!=ses('user'))$sql[]=[$k,ses('user'),$type,$id,'1'];
		if(isset($sql))Sql::insert2('telex_ntf',$sql); $_POST[$o]='';}
	
	static function readntf($v){$n=$v['typntf']; $by='@'.$v['byusr'];
		//$uname=Sql::read('name','login','v','where usr="'.$v['byusr'].'"');
		if($v['state']==1)Sql::update('telex_ntf','state','0',$v['ntid']);
		if($n==1 && $v['ib'])$ret=$by.' '.lang('has_reply',1); 
		elseif($n==1)$ret=$by.' '.lang('has_sent',1);
		elseif($n==2)$ret=$by.' '.lang('has_repost',1); 
		elseif($n==3)$ret=$by.' '.lang('has_liked',1);
		return div($ret,'ntftit');}
	
	#channels
	static function chanread($usr){
		return Sql::read('distinct(list)','telex_ab','rv','where usr="'.$usr.'"');}
	static function chanbt(){$ret=self::loadtm('tm='.ses('user'),lang('all'));
		$r=sesclass('telex','chanread',ses('user'));
		if($r)foreach($r as $v)$ret.=self::loadtm('tm='.ses('user').',list='.$v,$v);
		return div($ret,'list');}
	
	#labels
	static function lablread(){
		return Sql::read('labels.id,ref','labels','kv','inner join '.self::$db.' on lbl=labels.id');}
	static function lablbt(){$ret=self::loadtm('labl=',lang('all'));
		$r=self::lablread();//sesclass('telex','lablread');
		if($r)foreach($r as $k=>$v)$ret.=self::loadtm('labl='.$k,$v);
		return div($ret,'list');}
	
	#desktop
	static function desktop($p){
		$ret=aj('popup|desktop,content|dir=/documents',lang('desktop'),'btit');
		$r=Sql::read('id,dir,type,com,picto,bt,auth','desktop','id','where uid="'.ses('uid').'" and auth<="'.ses('auth').'" and dir="/documents" order by id desc');
		//$j=Ajax::js(array('com'=>'popup','app'=>'tlxcall,deskedt','prm'=>'id='.$r[0].',dir='.$r[1]));
		//$prm=['oncontextmenu'=>$j];
		if($r)foreach($r as $k=>$v){
			//$bt=span(ajax($k,'desktop,modifbt','id=dskbt'.$k,'',$v[4]),'','dskbt'.$k);
			if($v[1]=='img'){$f='img/telex/full/'.$v[2];
				if(is_file($f))$ret.=imgup($f,self::playthumb($v[2],'micro',1).span($v[4]),'licon');}
			elseif($v[1]=='pop')$ret.=aj('popup,,,1|'.$v[2].',headers=1',pic($v[3],24).span($v[4]),'licon');
			elseif($v[1]=='pag')$ret.=aj('pagup,,,1|'.$v[2].',headers=1',pic($v[3],24).span($v[4]),'licon');
			elseif($v[1]=='lk')$ret.=href('/app'.$v[2],'','licon','',1);
			else $ret.=aj($v[2],pic($v[3],24).span($v[4]),'licon');}
		return $ret;}
	
	#read
	static function relativetime($sec){$time=ses('time')-$sec;
		$ret=lang('there_was').' ';
		if($time>864000)$ret=strftime('%a %d %b',$sec);
		if($time>86400)$ret=strftime('%d %b',$sec);
		elseif($time>3600)$ret.=floor($time/3600).'h ';
		elseif($time>60)$ret.=floor($time/60).'min ';
		else $ret.=$time.'s';
		return span($ret,'small');}
	
	static function clr(){$d='mintcream thistle olivedrab lightyellow lightsteelblue lightblue lavender greenyellow darkseagreen darkkhaki cornflowerblue cadetblue blanchedalmond ThreeDLightShadow scrollbar';}
	
	//pane
	static function panehead($v,$tg){$id=$v['id']; $idv=$v['idv']; $usr=$v['name'];
		$name=href('/'.$usr,tag('strong','',$v['pname']),'btxt').' ';
		$usrnm=span('@'.$usr,'grey'); if(ses('user'))
		$usrnm=aj('panedit'.$idv.'|telex,editor|idv='.$idv.',to='.$usr,span('@'.$usr,'grey'));
		$time=self::relativetime($v['now']).' ';
		$url=href('/id/'.$id,pic('link',12),'grey');
		$date=pagup('telex,read|popwidth=500px,th='.$id,$time,'grey');
		$ico=$v['icon']?pic($v['icon']):'';
		if($v['ref'])$label=span(span(ucfirst($v['ref']),'tx').$ico,'label right'); else $label='';
		if(auth(6))$url.=aj('popup|telex,editor|id='.$id,picit('eye','view'));
		if($v['ib']){
			$to=Sql::read_inner('name',self::$db,'login','uid','v','where '.self::$db.'.id='.$v['ib']);
			$url.=' '.pagup('telex,read|th='.$id,lang('in-reply to').' '.$to,'grey').' ';}
		if($nb=Sql::read('count(id)',self::$db,'v','where ib='.$id))
			$url.=pagup('telex,read|th='.$id,$nb.' '.lang($nb>1?'replies':'reply'),'grey');
		return div($name.' '.$usrnm.' - '.$date.' '.$url.' '.$label,'username');}
	
	static function panefoot($v,$tg){$id=$v['id']; $idv=$v['idv']; $pr='panedit'.$idv; $ret='';
		$ret.=toggle($pr.'|telex,editor|idv='.$idv.',to='.$v['name'].',ib='.$id,picit('reply','to reply'));
		$ret.=toggle($pr.'|telex,editor|idv='.$idv.',quote='.$id,picit('retweet','quote'));
		$ret.=self::likebt($v);
		$ret.=toggle($pr.'|tlxcall,share|id='.$id.',txt='.$v['txt'],picit('share-alt','share'));
		if($v['name']==ses('user'))
			$ret.=toggle($pr.'|telex,del|idv='.$idv.',did='.$id,picit('bolt','delete','del'));
		if(self::$objects)
			$ret.=toggle($pr.'|tlxcall,keep|idv='.$idv.',id='.$id,picit('download','keep'));
		return $ret;}
	
	static function pane($v,$current=''){$id=$v['id']; $usr=$v['name'];
		if($v['privacy'] && $usr!=ses('user'))return;
		if($current){$v['idv']='plx'.$id; $tg='popup,,x';}
		else{$v['idv']='tlx'.$id; $tg='popup';}
		$avatar=self::avatar($v);
		$avatar=bubble('telex,profile|usr='.$usr,$avatar,'btxt');
		$head=self::panehead($v,$tg);
		self::$objects='';
		$msg=Conn::load(['msg'=>$v['txt'],'app'=>'telex','mth'=>'reader','ptag'=>0]);
		$msg.=div(self::objects(),'objects');
		$msg=div($msg,'message');
		if(ses('uid'))$foot=div(self::panefoot($v,$tg),'actions'); else $foot='';
		$ret=div($avatar,'bloc_left').div($head.$msg.$foot,'bloc_content');
		$ret.=div('','','panedit'.$v['idv']);
		if($current==$id)$css='pane hlight';
		elseif(isset($v['typntf']))$css=$v['state']==1?'pane hlight':'pane';
		else $css='pane';
		$sty='border:4px solid #'.$v['clr'].';'; $sty='';
		if(isset($v['typntf']))$ret=self::readntf($v).$ret;
		$ret=div($ret,$css,$v['idv'],$sty);
		if($current==$id){self::$title='telex.fr/'.$id.' by @'.$usr; self::$description=strip_tags($msg);
			self::$image='http://tlex.fr/img/profile/mini/'.$v['avatar'];}
		return $ret;}
	
	static function read($p){$ret=''; $id='';//$id will be in popup
		$last=val($p,'from'); $th=val($p,'th'); $id=val($p,'id');
		$rs=val($p,'rs'); $ib=val($p,'ib'); 
		if($th && !$last)$id=$p['th']=$th;//thread
		elseif($id && !$last)$p['id']=$id;//one
		elseif($rs && !$last)$id=$p['rs']=$rs;//current+childs
		elseif($ib && !$last)$p['ib']=$ib;//childs
		else{$usr=val($p,'usr',ses('user')); $tm=val($p,'tm');//timeline
			$p['from']=$last; $p['tm']=$tm?$tm:$usr;}
		if(isset($p))$r=self::api($p); //pr($r);
		if(isset($r))foreach($r as $k=>$v)$ret.=self::pane($v,$id);
		return $ret;}
	
	#api
	static function sql_timeline($usr,$from,$list,$noab,$since,$labl,$count){$sq='';
		if(!$noab && !$labl){$sqa=$list?' and list="'.$list.'"':'';
			$r=Sql::read('ab','telex_ab','rv','where usr="'.$usr.'"'.$sqa,0);
			if($r)$sq=' or name="'.implode('" or name="',$r).'"';}
		if($labl)$ret='where labels.id="'.$labl.'"';
		elseif($list && !$noab)$ret='where ('.substr($sq,4).')';
		elseif($usr==ses('user'))$ret='where (txt like "%@'.$usr.' %" or name="'.$usr.'"'.$sq.')';
		else $ret='where (((txt like "%@'.$usr.' %" or name="'.$usr.'") and privacy=0)'.$sq.')';
		if($from)$from='and '.self::$db.'.id<'.$from.'';
		elseif($since)$from='and '.self::$db.'.id>'.$since.'';
		$limit=$count?'':'order by '.self::$db.'.id desc limit 20';
		return $ret.' '.$from.' '.$limit;}
	
	static function api($p){
		$p=vals($p,['tm','th','id','ib','sr','ntf','from','list','noab','since','labl','count']);
		if($p['count']){$cols='count('.self::$db.'.id)'; $vmode='v';}
		else{$cols=''.self::$db.'.id,uid,name,txt,unix_timestamp('.self::$db.'.up) as now,ib,telex_lik.id as lid,pname,avatar,clr,ref,icon,privacy'; $vmode='rr';}
		$inn='left join login on login.id=uid left join profile on puid=uid left join telex_lik on '.self::$db.'.id=lik left join labels on lbl=labels.id ';
		if($p['id'])$where='where '.self::$db.'.id='.$p['id'];
		elseif($p['ib'])$where='where ib='.$p['ib'];
		elseif($p['th'])$where=self::sql_thread($p['th']);
		elseif($p['sr'])$where='where txt like "%'.$p['sr'].'%"';// and privacy="0"
		elseif($p['ntf']){$cols.=',telex_ntf.id as ntid,byusr,typntf,state';
			$inn.='inner join telex_ntf on txid='.self::$db.'.id ';
			$minid=$p['since']?' and '.self::$db.'.id>'.$p['since']:'';
			$limit=$p['count']?'':' order by '.self::$db.'.up desc limit 20';
			$where='where 4usr="'.ses('user').'"'.$minid.''.$limit;}
		else $where=self::sql_timeline($p['tm'],$p['from'],$p['list'],$p['noab'],$p['since'],$p['labl'],$p['count']);
		return Sql::read($cols,self::$db,$vmode,$inn.$where,0);}
	
	//http://tlex.fr/api.php?app=telex&mth=call&prm=tm:dav
	static function call($p){$r=self::api($p);
		foreach($r as $k=>$v){self::$objects='';
			$r[$k]['avatar']='http://tlex.fr/img/profile/full/'.$v['avatar'];
			$r[$k]['txt']=Conn::load(['msg'=>$v['txt'],'app'=>'telex','mth'=>'reader']);
			$r[$k]['objects']=self::objects();}
		return json_r($r);}
	
	//thread
	static function thread_parents($id,$ret=''){
		$ib=Sql::read('ib',self::$db,'v','where '.self::$db.'.id="'.$id.'"',0);
		if($ib){$ret[$ib]=1; $ret=self::thread_parents($ib,$ret);}
		return $ret;}
	static function thread_childs($id){
		return Sql::read('id',self::$db,'k','where ib='.$id,0);}
	static function sql_thread($id){
		$ids=self::thread_childs($id);
		$ids=self::thread_parents($id,$ids);
		$ids[$id]=1; ksort($ids);
		if($ids)$r=array_keys($ids);
		if(isset($r))return 'where '.self::$db.'.id='.implode(' or '.self::$db.'.id=',$r);}
	
	//load button
	static function loadtm($p,$t,$c=''){
		if($c)$r['class']=$c;
		$r['onclick']='refresh();';
		$r['data-prmtm']=$p; $r['onmousedown']='ajbt(this)';
		$r['data-j']='div,tlxbck,,resetscroll|telex,read|'.($p=='current'?'':$p);
		return tag('a',$r,$t);}
		
	static function vrfusr($d){return Sql::read('id','login','v','where name="'.$d.'"');}
	static function vrfid($d){return Sql::read_inner('name',self::$db,'login','uid','v','where '.self::$db.'.id="'.$d.'"');}
	
	#content
	static function content($p){$badusr=''; $badid=''; $dsk='';
		$own=ses('user'); $desk=val($p,'desk'); $chat=val($p,'chat'); $ntf=val($p,'ntf'); $art=val($p,'art');
		//self::install();
		sesif('lng',Lang::$lang);
		if(!$own)$login=span(Auth::logbt('1'),'bub');
		//else $login=ajtime('telex,profilemenu','',pic('user').' '.$own,'btn abbt');
		else $login=Menu::call(array('app'=>'tlxcall','method'=>'profilemenu'));
		//usr
		$usr=val($p,'usr'); $id=val($p,'id',val($p,'th')); if(is_numeric($usr)){$id=$usr; $usr='';}
		if($usr){$okusr=self::vrfusr($usr); if(!$okusr){$badusr=1; $usr='';} else $p['usr']=$usr;}
		if(!$usr && $own)$p['usr']=$own;
		//id
		if($id){$okid=self::vrfid($id); if(!$okid){$badid=1; $id='';} else{$p['id']=$id; $usr=$okid;}}
		//nav
		$nav=span($login,'right');
		$nav.=span(href('/',langph('home'),'btn abbt'),'').' ';
		//$nav.=self::loadtm('tm='.$usr,langph('home'),'btn abbt');
		//$nav.=pagup('Help,com|ref=telex_nfo',pic('info'),'btn').' ';
		if(ses('uid')){
			$bt=langph('notifications').span('','nbntf','tlxntf');
			//$nav.=aj('tlxbck|telex,read|ntf=1|tlxntf',$bt,'btn abbt');
			$nav.=self::loadtm('ntf=1',$bt,'btn abbt');
			$bt=langph('messages').span('','nbntf','tlxmsg');
			$nav.=pagup('chat,com|headers=1',$bt,'btn abbt');
			$nav.=ajtime('telex,lablbt','',langph('labels'),'btn abbt');}
		if($own)$nav.=ajtime('telex,chanbt','usr='.$usr.',list='.ses('list'),langph('lists'),'btn abbt');
		if(ses('uid'))$nav.=pagup('telex,searchbt',langph('search'),'btn abbt');
		//profile
		$bigprofile='';
		if($badid or $badusr)$profile=div(help('telex'),'board');
		elseif($usr && !$badusr)list($bigprofile,$profile)=profile::read(['usr'=>$usr,'big'=>'1']);
		elseif(!$own or $usr or $id or $desk)$profile='';//div(help('welcome'),'board');
		else $profile=self::profile(['usr'=>$own]);
		//dashboard
		if(auth(1) && !$desk)$dsk=div(self::desktop($p),'board','dsk');
		$credits=div(help('credits'),'board');
		//publish
		if($own && !$id && !$usr && !$badid && !$badusr)
			$publish=div(self::editor(''),'pblshcnt','pblshcnt');
		else $publish='';
		//refreshbt
		$bt=span('','nbntf','tlxrec').lang('new telex');
		if($own && $own==$p['usr'])$refresh=self::loadtm('current',$bt,'refreshbt');
		elseif($own && $own!=$usr)$refresh=href('/'.$own,lang('back'),'refreshbt'); else $refresh='';
		//stream
		if($badid)$stream=help('404iderror');
		elseif($badusr)$stream=help('404usrerror');
		elseif($art)$stream=App::open('article',['id'=>$art,'appFrom'=>'telex']);
		elseif(!$usr && !$own && !$id)$stream=App::open('article',['id'=>6]).Auth::logbt(0);
		elseif($desk)$stream=Desk::load('desktop','com',val($p,'dir','/documents'));
		elseif($chat)$stream=App::open('chat','');
		else $stream=self::read($p);
		$stream=div($stream,'','tlxbck');
		//render
		if(get('popup'))$rs=['position:relative','margin-top:10px']; else $rs=['',''];
		$ret=div(div($nav,'navigation'),'topbar','',$rs[0]);
		$hdash=''; $htime='';
		if($bigprofile){$ret.=div($bigprofile,'bigprofile'); $hdash=' hdash'; $htime=' htime';}
		else $ret.=div('','hfixer');
		$cnt=div($profile.$dsk.$credits,'dashboard'.$hdash,'',$rs[1]);
		$cnt.=div($publish.$refresh.$stream,'timeline'.$htime,'timbck',$rs[1]);
		//tmprm
		if(val($p,'th'))$pmtm='th='.$usr; elseif($ntf)$pmtm='ntf=1,usr='.$usr; 
		elseif(!$id && !$desk && !$chat && !$art)$pmtm='tm='.($usr?$usr:$own); else $pmtm='';
		$cnt.=hidden('prmtm',$pmtm);
	return $ret.div($cnt,'container');}
}
?>