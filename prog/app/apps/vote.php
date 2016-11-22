<?php

class vote{
	static $private='0';
	
	static function headers(){
		Head::add('csscode','
	.text {background:white; border-radius:2px; padding:10px; margin:10px 0; font-size:medium;}
	.score {background:white; border-radius:2px; padding:2px 10px 0; font-size:medium; border:1px solid gray;}
	.argyes {border:1px solid #22dd22;}
	.argno {border:1px solid #dd2222;}
	.for:hover {border:1px solid #22dd22;}
	.against:hover {border:1px solid #dd2222;}
	.resyes {background:#22dd22; color:white;}
	.resno {background:#dd2222; color:white;}
		');
	}
	
	//install
	static function install($init=0){//return;
		Sql::create('vote_lead',array('uid'=>'int','txt'=>'var','closed'=>'int'),$init);
		Sql::create('vote_args',array('idPoll'=>'int','uid'=>'int','position'=>'int','txt'=>'var'),$init);
		Sql::create('vote_chat',array('idArg'=>'int','uid'=>'int','txt'=>'var'),$init);
		Sql::create('vote_valid',array('idArg'=>'int','uid'=>'int','val'=>'int'),$init);
	}
	
	//generics
	static function linktopoll($idPoll,$idArg){
		if($idArg)$idArg=',idArg='.$idArg;
		return href('/app/vote/'.$idPoll.$idArg,pic('link'),'btn').' ';}
	
	static function userdate($date,$name){
		$date=span(date('Y-m-d',strtotime($date)),'small');
		return $date.' '.small(lang('by').' '.$name).' ';}
	
	static function textarea($v=''){
		$r=array('id'=>'text','cols'=>60,'rows'=>4,'onkeyup'=>'strcount(\'text\',255)');
		return tag('textarea',$r,$v).br().span('','small right','strcount');}
	
	static function edit($com,$prm,$v){
		$r=array('id'=>'text','cols'=>60,'rows'=>4,'onkeyup'=>'strcount(\'text\',255)');
		$ret=tag('textarea',$r,$v).br().span('','small right','strcount');
		$ret.=Ajax::j($com.'|'.$prm.'|text',lang('save'),'btsav');
		return $ret;}
	
	private static function security($table,$id){
		$uid=Sql::read('uid',$table,'v','where id='.$id);
		if($uid==ses('uid'))return 1;}
	
	static function add(){
		$com='pllscnt,,x|vote,pollSave'; $prm='';
		return self::edit($com,$prm,'');
	}
	
	/*---*/
	
	#vote
	static function voteSave($prm){
		$idVote=Sql::read('id','vote_valid','v','where idArg="'.$prm['idArg'].'" and uid="'.ses('uid').'"');
		if(isset($idVote))$prm['val']=$prm['val']!=$prm['current']?$prm['val']:'0';
		if(!isset($idVote))
			$prm['idChat']=Sql::insert('vote_valid',array($prm['idArg'],ses('uid'),$prm['val']));
		else Sql::update('vote_valid','val',$prm['val'],$idVote);
		return self::voteRead($prm);
	}
	
	static function voteRead($prm){
		$idArg=$prm['idArg'];
		$rid=randid('vote');
		$vote=Sql::read('val','vote_valid','v','where idArg="'.$idArg.'" and uid="'.ses('uid').'"');
		//tots
		$yes=Sql::read('count(val)','vote_valid','v','where idArg="'.$idArg.'" and val="1"');
		$no=Sql::read('count(val)','vote_valid','v','where idArg="'.$idArg.'" and val="2"');
		$p=$rid.'|vote,voteSave|idArg='.$idArg.',position='.$prm['position'].',current='.$vote;
		//vote buttons
		$cs1=$vote==1?' active':''; $cs2=$vote==2?' active':'';
		if($prm['position']==1){$cp1='for'; $cp2='against';} else{$cp1='against'; $cp2='for';}
		$ck1=$vote==1?pic('check'):''; $ck2=$vote==2?pic('check'):'';
		$bt1=$ck1.' '.lang('agree',1).' ('.$yes.')';
		$bt2=$ck2.' '.lang('not agree',1).' ('.$no.')';
		if(ses('uid')){
			$ret=Ajax::j($p.',val=1',$bt1,'btn'.$cs1.' '.$cp1);
			$ret.=Ajax::j($p.',val=2',$bt2,'btn'.$cs2.' '.$cp2);}
		else $ret=span($bt1,'btn').span($bt2,'btn');
		return span($ret,'',$rid);
	}
	
	#chat
	static function chatDel($prm){
		if(!self::security('vote_chat',$prm['idChat']))return;
		if($prm['idChat'])Sql::delete('vote_chat',$prm['idChat']);
		return self::chatRead($prm);
	}
	static function chatSave($prm){
		$prm['idChat']=Sql::insert('vote_chat',array($prm['idArg'],ses('uid'),$prm['text']));
		return self::chatRead($prm);
	}
	static function chatAdd($prm){
		$ret=self::textarea();
		$ret.=Ajax::j($prm['rid'].',,x|vote,chatSave|idPoll='.$prm['idPoll'].',idArg='.$prm['idArg'].'|text',lang('save'),'btsav');
		return $ret;
	}
	static function chatRead($prm){$ret='';
		$rid=randid('chat');
		$b='vote_chat';
		$cols=$b.'.id as id,name,txt,'.$b.'.up as date';
		$where='where '.$b.'.idArg='.$prm['idArg'];
		$r=Sql::read_inner($cols,$b,'login','uid','rr',$where);
		//add
		if(ses('uid'))$ret.=Ajax::j('popup|vote,chatAdd|rid='.$rid.',idPoll='.$prm['idPoll'].',idArg='.$prm['idArg'],langs('add,comment'),'btn').br().br();
		//read
		if($r)foreach($r as $v){
			$by=span('#'.$v['id'],'btn').' '.self::userdate($v['date'],$v['name']);
			if($v['name']==ses('user'))$by.=Ajax::j($rid.'|vote,chatDel|idPoll='.$prm['idPoll'].',idArg='.$prm['idArg'].',idChat='.$v['id'].'',lang('del'),'btdel right');
			$txt=div($v['txt'],'text');
			$ret.=div($by.$txt,'pane').br();}
		return div($ret,'',$rid);
	}
	
	#argument
	static function argumentDel($prm){
		if(isset($prm['idArg'])){
			if(!self::security('vote_args',$prm['idArg']))return;
			Sql::delete('vote_args',$prm['idArg']);
			Sql::delete('vote_valid',$prm['idArg'],'idArg');}
		return self::poll($prm);
	}
	
	static function argumentSave($prm){
		$prm['idArg']=Sql::insert('vote_args',array($prm['idPoll'],ses('uid'),$prm['position'],$prm['text']));
		return self::argumentPane($prm);
	}
	
	static function argumentEdit($prm){
		$ret=self::textarea();
		$ret.=Ajax::j('pllscnt,,x|vote,argumentSave|idPoll='.$prm['idPoll'].',position='.$prm['position'].'|text',lang('save'),'btsav');
		return $ret;
	}
	
	static function argumentAdd($prm){$ret='';
		$opts=array('1'=>lang('positive'),'2'=>lang('negative'));
		$ret=radio('position',$opts,'1',' ');
		$ret.=Ajax::j('popup|vote,argumentEdit|idPoll='.$prm['idPoll'].'|position',lang('create'),'btsav');
		return $ret;
	}
	
	#pane
	static function argumentPane($prm){//idPoll,idArg
		if(!isset($prm['idPoll']) && $prm['idArg'])
			$prm['idPoll']=Sql::read('idPoll','vote_args','v','where id='.$prm['idArg']);
		$cols='position,name,txt,vote_args.up as date';
		$where='where vote_args.id='.$prm['idArg'];
		$r=Sql::read_inner($cols,'vote_args','login','uid','ra',$where);
		//header
		$rt=array('1'=>'argYes','2'=>'argNo');
		$cs=$r['position']==1?'argyes':'argno';
		$bt='#'.$prm['idPoll'];//pic('arrow-left').
		$back=Ajax::j('pllscnt|vote,poll|idPoll='.$prm['idPoll'],$bt,'btn');
		$here=Ajax::j('pllscnt|vote,argumentPane|idPoll='.$prm['idPoll'].',idArg='.$prm['idArg'],'#'.$prm['idArg'].' '.langs('arg,'.$rt[$r['position']]),'btn '.$cs);
		$by=self::userdate($r['date'],$r['name']);
		$bt=self::linktopoll($prm['idPoll'],$prm['idArg']);
		//admin
		if($r['name']==ses('user'))$bt.=Ajax::j('pllscnt|vote,argumentDel|idArg='.$prm['idArg'].',idPoll='.$prm['idPoll'],lang('delete'),'btdel');
		$ret=$back.' '.$here.' '.$by.' '.span($bt,'right');
		//txt
		$ret.=div($r['txt'],'text');
		//vote
		$prm['position']=$r['position'];
		$ret.=self::voteRead($prm);
		//chat
		$n=Sql::read('count(id)','vote_chat','v','where idArg="'.$prm['idArg'].'"');
		$p='popup|vote,chatRead|idPoll='.$prm['idPoll'].',idArg='.$prm['idArg'];
		$bt=pic('comment').' '.lang('comments').' ('.$n.')';
		$ret.=Ajax::j($p,$bt,'btn right');
		if(isset($prm['pagewidth']))$s='width:'.$prm['pagewidth'].';'; else $s='';
		return div($ret,'pane','',$s);
	}
	
	/*---*/
	
	#edit
	static function pollUpdate($prm){
		if($prm['idPoll'])Sql::update('vote_lead','txt',$prm['text'],$prm['idPoll']);
		return self::poll($prm);
	}
	static function pollEdit($prm){
		$txt=Sql::read('txt','vote_lead','v','where id='.$prm['idPoll']);
		$ret=self::textarea($txt);
		$ret.=Ajax::j('pllscnt,,x|vote,pollUpdate|idPoll='.$prm['idPoll'].'|text',lang('save'),'btsav');
		return $ret;
	}
	static function pollDel($prm){
		if(!self::security('vote_lead',$prm['idPoll']))return;
		if($prm['idPoll'])Sql::update('vote_lead','closed','1',$prm['idPoll']);//close
		//if($prm['idPoll']){Sql::delete('vote_lead',$prm['idPoll']); self::argumentDel($prm);}
		return self::polls();
	}
	static function pollSave($prm){
		if($prm['text'])$nid=Sql::insert('vote_lead',array(ses('uid'),$prm['text'],''));
		return self::poll(array('idPoll'=>$nid));
	}
	/*
	static function add_x(){
		return self::edit($com,$prm,$v);
		$ret=self::textarea();
		$ret.=Ajax::j('pllscnt,,x|vote,pollSave||text',lang('add'),'btsav');
		return $ret;
	}*/
	
	//read
	static function arguments($prm){$ret='';//todo
		$r=Sql::read('id','vote_args','rv','where idPoll='.$prm['idPoll'].' order by id');
		if($r)foreach($r as $k=>$v)//if(isset($rt[$v['position']])){
			$ret.=self::argumentPane($prm+array('idArg'=>$v)).br();
		return $ret;
	}
	
	//algo
	static function algo($ra,$r){$ret=0;
		//stats
		foreach($r as $id=>$v){$tot=array_sum($v); $pos=$ra[$id];
			$agree=isset($v[1])?$v[1]:0; $disagree=isset($v[2])?$v[2]:0;
			$rt[$pos][$id]=$tot; $rs[$pos][$id]=($agree-$disagree)*$tot;}
		///ponderation of arguments
		//tot votes by type of arg
		if(isset($rt[1]))$tt[1]=array_sum($rt[1]); else $tt[1]=0;
		if(isset($rt[2]))$tt[2]=array_sum($rt[2]); else $tt[2]=0;
		//tot scores
		if(isset($rs[1]))$ts[1]=array_sum($rs[1]); else $ts[1]=0;
		if(isset($rs[2]))$ts[2]=array_sum($rs[2]); else $ts[2]=0;;
		///calc
		if($tt[1])$rs[1]=$ts[1]/$tt[1]; else $rs[1]=0;
		if($tt[2])$rs[2]=$ts[2]/$tt[2]; else $rs[2]=0;
		//poonderation of positions
		$tv=array_sum($tt);//tot votes
		if($tv)$ret=($rs[1]-$rs[2])/$tv;
		$score=$ts[1]-$ts[2];
		return array('ts'=>$ts,'tt'=>$tt,'res'=>$ret,'global'=>round($ret*100,1),'score'=>$score);
	}
	
	static function argsResume($prm){$ret=''; 
		//args by position [position]=>array([idArg])
		$ra=Sql::read('id,position','vote_args','kv','where idPoll='.$prm['idPoll']); //pr($ra);
		//votes by args [idArg]=>array([position]=>cumul)
		$cols='vote_args.id,val';
		$r=Sql::read_inner($cols,'vote_valid','vote_args','idArg','kkc','where idPoll='.$prm['idPoll'].' and (val=1 or val=2) order by idArg');
		if($ra)$res=self::algo($ra,$r); //pr($res);
		//$help=' '.hlpbt('vote_algo');
		$css=isset($res['global'])?'resyes':'resno';
		if(isset($res))$ret=span($res['global'].'%','score '.$css);
		if($ret)return div(lang('score').' '.$ret,'right');
	}
	
	#polls
	static function poll($prm){//idPoll
		$cols='name,txt,vote_lead.up as date';
		$where='where vote_lead.id='.$prm['idPoll'].' and closed!=1';
		$r=Sql::read_inner($cols,'vote_lead','login','uid','ra',$where);
		if(!$r)return lang('not exists');
		//admin
		$go=Ajax::j('pllscnt|vote,poll|idPoll='.$prm['idPoll'],'#'.$prm['idPoll'],'btn');
		$by=self::userdate($r['date'],$r['name']);
		$bt=self::linktopoll($prm['idPoll'],'');
		if(ses('uid')){//add argument
			$bt.=Ajax::j('bubble,,1|vote,argumentAdd|idPoll='.$prm['idPoll'],langs('add,arg'),'btok');}
		if($r['name']==ses('user')){
			$bt.=Ajax::j('popup|vote,pollEdit|idPoll='.$prm['idPoll'],lang('modif'),'btn');
			$bt.=Ajax::j('pllscnt|vote,pollDel|idPoll='.$prm['idPoll'],lang('del'),'btdel');}
		$bt=div($bt,'right');
		$txt=div(nl2br($r['txt']),'text');
		//args
		//if(isset($prm['list'])){}
		$n=Sql::read('count(id)','vote_args','v','where idPoll="'.$prm['idPoll'].'"');
		$args=Ajax::j('popup|vote,arguments|idPoll='.$prm['idPoll'],langs('args').' ('.$n.')','btn');
		//$args.=hlpbt('vote_args');
		//resume
		$resume=self::argsResume($prm);
		//render
		$ret=div($go.' '.$by.$bt.br().$txt.$args.$resume,'pane').br();
		//if(!isset($prm['list']))$ret.=self::arguments($prm);
		return $ret;
	}
	
	static function polls(){$ret='';
		$r=Sql::read('id','vote_lead','rv','where closed!=1');
		if($r)foreach($r as $v)$ret.=self::poll(array('idPoll'=>$v,'list'=>1));
		return $ret;
	}
	
	#tlx
	static function com(){
	
	}
	
	#content
	static function content($prm){$ret='';
		//self::install(1);
		//Lang::$app='vote';//context for new voc
		if(isset($prm['param']))$prm['idPoll']=$prm['param'];
		$logbt=ses('user')?ses('user'):'login';
		$ret.=span(Auth::logbt(),'right');
		//$ret=div(App::open('login'),'right');
		$ret.=Ajax::j('pllscnt|vote,polls',pic('list'),'btn').' ';
		$bt=pic('plus').' '.langs('new,referendum');
		if(ses('uid'))
			$ret.=Ajax::j('popup|vote,add',$bt,'btn').' ';
		$ret.=hlpbt('vote_app').' ';
		$ret.=br().br();
		//root
		if(isset($prm['idArg']))$res=self::argumentPane($prm);
		elseif(isset($prm['idPoll']))$res=self::poll($prm);
		else $res=self::polls();
		$ret.=tag('div',array('id'=>'pllscnt'),$res);
		return $ret;
	}
}

?>