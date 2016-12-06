<?php

class barter{
	static $private='0';
	static $unit=3;
	static $length=86400;
	
	//install
	static function install(){
		Sql::create('barter_lead',array('uid'=>'int','txt'=>'var','answ'=>'var','closed'=>'int'),1);
		Sql::create('barter_valid',array('idbarter'=>'int','uid'=>'int','txt'=>'text'),1);}

	static function headers(){
		Head::add('csscode','
	.anscnt{margin:4px 0; padding:2px; display:table-row;}
	.anscnt:hover{background:#ffffff;}
	.anstit{padding:2px 10px; display:table-cell; min-width:120px; max-width:340px;}
	.anscell{padding:2px 10px; display:table-cell;}
	.tot{padding:2px 10px; background:#dcdcdc;}');}
	
	//generics
	static function userdate($ts,$name){
		$date=span(date('d/m/Y',$ts),'small');
		return $date.' '.small(lang('by').' '.$name).' ';}
	
	private static function security($table,$id){
		$uid=Sql::read('uid',$table,'v','where id='.$id);
		if($uid==ses('uid'))return 1;}
	
	static function textarea($v=''){
		$r=array('id'=>'text','style'=>'width:500px; height:100px;','onkeyup'=>'strcount(\'text\',255)');
		return tag('textarea',$r,$v).br().span('','right','strcnttext');}
	
	//connectors
	static function reader($d,$p=''){$ret='';
		list($p,$o,$c)=readconn($d); $atb=array('class'=>$o);
		$b='b,i,u,h1,h2,h3,h4,span,div,small,big';
		if(strpos($b,$c.',')!==false)return tag($c,$atb,$p);
		switch($c){
			//this will be added to default connectors
			case('art'):return App::open($c,['param'=>$p,'headers'=>1]); break;
			case('web'):$ret=telex::playweb($p); break;
			case('pub'):$t=Sql::read('tit',article::$db,'v','where id="'.$p.'"');
				return aj('popup|art|param='.$p,pic('file-text-o').' '.$t,'btn'); break;
			default:$ret=Conn::reader($d,$p); break;}//default connectors
	return $ret;}
	
	#create
	static function update($p){
		if($p['idbarter'])Sql::update('barter_lead','txt',$p['text'],$p['idbarter']);
		if(val($p,'mnu'))return self::com($p);
		return self::build($p);}
	
	static function modif($p){$id=val($p,'idbarter'); $mnu=val($p,'mnu');
		$txt=Sql::read('txt','barter_lead','v','where id='.$id);
		$ret=self::textarea($txt);
		$ret.=aj('pllscnt,,x|barter,update|mnu='.$mnu.',idbarter='.$id.'|text',lang('save'),'btsav');
		return div($ret,'pane');}
	
	static function del($p){$closed=val($p,'closed');
		if(!self::security('barter_lead',$p['idbarter']))return;
		if($p['idbarter'] && val($p,'del')){Sql::delete('barter_lead',$p['idbarter']);
			Sql::delete('barter_valid',$p['idbarter'],'idbarter');}
		elseif($p['idbarter'] && $closed==1){$p['closed']=0;//open
			Sql::update('barter_lead','closed','0',$p['idbarter']);}
		elseif($p['idbarter']){$p['closed']=1;//close
			Sql::update('barter_lead','closed','1',$p['idbarter']);}
		//if(val($p,'mnu'))return self::build($p);
		return self::build($p);}
	
	static function save($p){
		for($i=0;$i<10;$i++)if($v=val($p,'answ'.$i))$answ[]=$v;
		if(!isset($answ))return help('barter empty');
		$answers=implode('|',$answ);
		if($p['text'])$p['idbarter']=Sql::insert('barter_lead',array(ses('uid'),$p['text'],$answers,''));
		if(val($p,'mnu'))return self::com($p);
		if($p['idbarter'])return self::build($p);}
	
	#add
	static function create($p){$nb=val($p,'nb',2); $rid=val($p,'rid'); $mnu=val($p,'mnu');
		$inp[]='text'; 
		for($i=1;$i<=$nb;$i++)$inp[]='answ'.$i; $inps=implode(',',$inp);
		$ret=span(aj('pllscnt,,x|barter,save|mnu='.$mnu.',rid='.$rid.'|'.$inps,lang('save'),'btsav'),'right');
		$ret.=div(lang('description').' :','stit');
		$txt=val($p,'text');
		$ret.=self::textarea(val($p,'text'));
		for($i=1;$i<=$nb;$i++)$ret.=div(input('answ'.$i,val($p,'answ'.$i),'',lang('choice').' '.$i));
		$ret.=aj('newbarter|barter,create|rid='.$rid.',nb='.($nb+1).'|'.$inps,langp('add choice'),'btn').br();
		return div($ret,'','newbarter');}
	
	static function leftime($end){$time=$end-ses('time');
		if($time>3600)$ret=floor($time/3600).'h ';
		elseif($time>60)$ret=floor($time/60).'min ';
		else $ret=$time.'s';
		return span($ret,'small');}
	
	#vote
	static function vote($p){$id=$p['idbarter'];
		$idVote=Sql::read('id','barter_valid','v','where idbarter="'.$id.'" and uid="'.ses('uid').'" and choice="'.$p['choice'].'"');
		if(isset($idVote))$p['val']=$p['val']?$p['val']:'0';
		if(!isset($idVote))
			$p['idVote']=Sql::insert('barter_valid',array($id,ses('uid'),$p['choice'],$p['val']));
		else Sql::update('barter_valid','val',$p['val'],$idVote);
		return self::read($p);}
	
	static function pane($rb,$i,$closed,$vote,$nb,$com){$ret='';
		$answer=pic('square-o').' '.val($rb,$i);
		$voted=count($vote)==$nb?1:0;
		if(auth(6))$voted=0;
		$votedcase=val($vote,$i);
		for($k=1;$k<=$votedcase;$k++){
			if($closed or $voted)$ret.=span(pic('star'));
			else $ret.=aj($com.',choice='.$i.',val='.$k,pic('star'));}
		for($k=$votedcase+1;$k<=5;$k++){
			if($closed or $voted)$ret.=span(pic('star-o'));
			else $ret.=aj($com.',choice='.$i.',val='.$k,pic('star-o'));}
		return div(span($answer,'anstit').span($ret,'anscell'),'anscnt');}
	
	static function pane_results($rb,$id){$ret='';
		$r=Sql::read('choice,val','barter_valid','kkc','where idbarter="'.$id.'"');
		//collect scores
		foreach($r as $k=>$v){$stot=0; $tot=array_sum($v);
			for($i=5;$i>0;$i--){
				$ratio=isset($v[$i])?round($v[$i]/$tot,2):0; $stot+=$ratio;
				$rd[$k][$i]=$ratio; $re[$i][$k]=$stot;}}
		//define order recursively
		$rok='';
		for($i=5;$i>0;$i--){arsort($re[$i]);
			if($rok)foreach($rok as $v)unset($re[$i][$v]);
			$max=max($re[$i]); $mxk=in_array_k($re[$i],$max); $rf='';
			while($mxk){unset($re[$i][$mxk]); $rf[]=$mxk; $mxk=in_array_k($re[$i],$max);}
			if(count($rf)==1)$rok[]=$rf[0];}
		//build
		foreach($rok as $k=>$v){$stot=0; $rt='';
			for($i=5;$i>0;$i--){$ratio=$rd[$v][$i]; $stot+=$ratio; $css=$stot>0.5?'':' active';
				$rt.=span($ratio,'anscell'.$css);}
			$ret.=div(span(val($rb,$v),'anstit').span($k+1,'anscell').$rt,'anscnt');}
		return $ret;}
	
	static function read($p){$id=$p['idbarter']; $mnu=val($p,'mnu'); $ret='';
		$date=val($p,'date'); $answers=val($p,'answ'); if(!$date)list($answers,$date)=Sql::read('answ,UNIX_TIMESTAMP(up) as date','barter_lead','rw','where id='.$id);
		$vote=Sql::read('choice,val','barter_valid','kv','where idbarter="'.$id.'" and uid="'.ses('uid').'"');
		$rs=Sql::read('distinct(uid)','barter_valid','k','where idbarter="'.$id.'" order by val');//all votes
		$rb=explode('|',$answers); $nb=count($rb); array_unshift($rb,'null');
		$endtime=$date+self::$length;
		$leftime=ses('time')-$endtime;
		if($leftime>0)$closed=1; else $closed=0;
		//vote button
		$com='pb'.$id.',,,1|barter,vote|mnu='.$mnu.',idbarter='.$id;
		//pane
		//$closed=1;
		if($closed){$rt=span('','anstit').span('N°','anscell'); 
			for($i=5;$i>0;$i--)$rt.=span(str_pad('',$i,'*'),'anscell'); $ret.=div($rt,'anscnt');
			$ret.=self::pane_results($rb,$id);}
		else for($i=1;$i<=$nb;$i++)$ret.=self::pane($rb,$i,$closed,$vote,$nb,$com);
		//footer
		$sum=array_sum($rs);
		$ret.=span($sum.' '.lang('voter'.plurial($sum),1),'tot');
		if($closed)$state=lang('barter closed').' '.lang('the',1).' '.date('d/m/Y',$endtime);
		else $state=lang('time left').' : '.self::leftime($endtime);
		$ret.=div($state,'grey');
		return div($ret,'','pb'.$id);}
	
	#barter
	static function build($p){$bt=''; $id=$p['idbarter']; $mnu=val($p,'mnu'); $closed=val($p,'closed');
		$cols='name,txt,answ,UNIX_TIMESTAMP(barter_lead.up) as date';
		$where='where barter_lead.id='.$id.' order by barter_lead.id desc';
		$r=Sql::read_inner($cols,'barter_lead','login','uid','ra',$where);
		if(!$r)return lang('not exists').br();
		//admin
		if($mnu)$go=aj('pllscnt|barter,com|rid='.val($p,'rid'),'#'.$id,'btn');
		else $go=href('/app/barter/'.$id,pic('link').' '.$id,'btn').' ';
		if($mnu)$go.=insertbt(lang('use'),$id.':barter',val($p,'rid'));
		//if($mnu)$go.=telex::publishbt($id,'barter');
		$by=self::userdate($r['date'],$r['name']);
		/**/if($r['name']==ses('user') && auth(4)){
			$bt.=aj('pagup|barter,modif|mnu='.$mnu.',idbarter='.$id,lang('modif'),'btn');
			if(auth(6)){
			$prm='pol'.$id.'|barter,del|rid='.val($p,'rid').',mnu='.$mnu.',idbarter='.$id.',closed=';
			if(!$closed)$bt.=aj($prm.'0',lang('close'),'btdel');
			else{$bt.=aj($prm.'1',lang('open'),'btsav');
				$bt.=aj($prm.',del=1',lang('del'),'btdel');}}}
		$bt=div($bt,'right');
		$txt=div(nl2br($r['txt']),'text');
		//results
		$r['idbarter']=$id;
		$results=self::read($r);
		//render
		$ret=div($go.' '.$by.$bt.br().$txt.$results,'pane');
		return div($ret,'','pol'.$id);}
	
	#barters
	static function barters(){$ret='';
		$r=Sql::read('id,closed','barter_lead','kv','where uid="'.ses('uid').'" order by id desc');
		if($r)foreach($r as $k=>$v)$ret.=self::build(array('idbarter'=>$k,'closed'=>$v));
		return $ret;}
	
	#call
	static function tit($p){$id=val($p,'id');
		return Sql::read('txt','barter_lead','v','where id='.$id);}
	
	static function call($p){$id=val($p,'id');
		$r=Sql::read('txt,answ,UNIX_TIMESTAMP(up) as date','barter_lead','ra','where id='.$id);
		//$ret=div(langp('barter'),'stit');
		$ret=div(nl2br($r['txt']),'tit'); $r['idbarter']=$id;
		$ret.=self::read($r);
		return div($ret,'paneb');}
	
	static function menu($p){$id=val($p,'id'); $rid=val($p,'rid'); $ret='';
		$cols='id,UNIX_TIMESTAMP(up) as date,closed';
		$r=Sql::read($cols,'barter_lead','','where uid="'.ses('uid').'" order by id desc');
		if($r)foreach($r as $k=>$v){
			$p['idbarter']=$v[0]; $p['closed']=$v[2]; $date=date('d/m/Y',$v[1]);
			if(ses('time')-$v[1]>self::$length)$closed=1; else $closed=0;
			//if($v[2]==1)$closed=1; else $closed=0;
			$prms='mnu=1,rid='.$rid.',idbarter='.$v[0].',closed='.$v[2];
			$ret.=aj('pllscnt|barter,com|'.$prms,'#'.$v[0].' '.span($date,'date'),$closed?'':'active').' ';
			//$sav=telex::publishbt($v[0],'barter');
			//$ret.=div($bt.$sav,'menu');
			}
		return div($ret,'list');}

	static function com($p){$rid=val($p,'rid');
		$id=val($p,'idbarter'); $edt=val($p,'edt'); $mnu=val($p,'mnu'); $p['mnu']=$mnu;
		$bt=aj('pllscnt|barter,com|edt=1,rid='.$rid,langp('new'),'btsav').' ';
		$bt.=aj('pllscnt|barter,com|mnu=1,rid='.$rid,langp('menu'),'btn');
		$ret=div($bt);//div(help('vote'),'btit')..br()
		if($id)$ret.=self::build($p);
		elseif($mnu)$ret.=self::menu($p);
		elseif($edt)$ret.=div(self::create($p),'pane','');
		return div($ret,'','pllscnt');}
	
	#content
	static function content($p){$ret='';
		self::install();
		if(isset($p['param']))$p['idbarter']=$p['param'];
		$ret=aj('pllscnt|barter,barters',pic('list'),'btn').' ';
		$ret.=hlpbt('barter_app').' ';
		if(ses('uid'))$ret.=aj('pllscnt|barter,create',pic('plus').' '.lang('new'),'btn').br().br();
		//root
		if(isset($p['idbarter']))$res=self::build($p);
		else $res=self::barters();
		$ret.=tag('div',array('id'=>'pllscnt'),$res);
		return $ret;}
}

?>