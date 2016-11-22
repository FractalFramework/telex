<?php

class tlxcall{
	static $private='0';
	
	//menu apps
	static function menuapps($p){$ret='';$rid=val($p,'rid'); $css='cicon';
	$rb=array('article','chat','gps','tabler','poll','slide','forms','petition');//'telex',
	$tg='tlxapps,,,1';//'vote',
	$prm['onclick']='closebub(event);';
	foreach($rb as $k=>$v){$ico=pico($v,32);
		if($v=='article')$com='|article,edit_telex|headers=1,';
		elseif($v=='chat')$com='|chat,comtlx|';
		elseif($v=='gps')$com='|map,gps|';
		//elseif($v=='telex')$com='|telex,publish|';
		else $com='|'.$v.',com|headers=1,';
		$ret.=aj($tg.$com.'rid='.$rid,$ico.div(lang($v)),$css,$prm);}
		return $ret;}
	
	//$ok=insertbt(langp('use'),$id.':article',$rid);
	
	//keep
	static function keepsave($p){//dir,type,com,picto,bt
		$id=val($p,'id'); $com=val($p,'com'); $d=val($p,'p1'); $ic='';
		$o=val($p,'p2'); $t=val($p,'ict',val($p,'tit'));
		if($com=='img'){$ty='img'; $ncom=$d; $ic='image';}
		elseif($com=='web'){$ty='pag'; $ncom='telex,objplayer|obj=playweb,p1='.$d.',p2='.$o;}
		elseif($com=='video'){$ty='pag'; $ncom='Video,call|p='.$d.',id='.$id; $ic='video';}
		elseif($com=='article'){$ty='pag'; $ncom='article,read|tlx=1,id='.$d; $ic='file';}//dont'change it!
		elseif($com=='chat'){$ty='pag'; $ncom='chat|param='.$d; $ic='comments';}
		elseif($com=='gps'){$ty='pag'; $ncom='map,com|coords='.$d; $ic='map';}
		elseif($com=='poll'){$ty='pag'; $ncom='poll,readtlx|id='.$d;}
		elseif($com=='slide'){$ty='pag'; $ncom='slide,call|tid='.$d;}
		elseif($com=='tabler'){$ty='pag'; $ncom='tabler,call|id='.$d;}
		else{$ty='pag'; $ncom=$com.',call|id='.$d;}
		$ic=$ic?$ic:$ic=ics($com);
		$nid=Sql::insert('desktop',[ses('uid'),'/documents',$ty,$ncom,$ic,$t,2]);
		$bt=div(lang('added to desktop'),'valid').div(pic($ic).' '.$t,'tit');
		return aj('dsk|telex,desktop',$bt,'',['onclick'=>'Close(\'popup\')']);
	}
	
	static function keep($p){
		$id=val($p,'id'); $idv=val($p,'idv'); $com=val($p,'conn'); $ret=''; $ex=''; $txt='';
		//if($conn)$ex=Sql::read('id','desktop','v','where id='.ses('uid').' and com="'.$com.'"');
		//if($ex)return lang('already exists');
		$dir=val($p,'dir'); $pic=val($p,'pic'); $bt=val($p,'bt'); $auth=val($p,'auth');
		if(!$dir){
			if($id)$txt=Sql::read('txt',telex::$db,'v','where id='.$id);
			telex::$objects='';
			if($txt)$msg=Conn::load(['msg'=>$txt,'app'=>'telex','mth'=>'reader']); $r=telex::$objects;
			if($r)foreach($r as $kr=>$vr)foreach($vr as $k=>$v){$im=''; $pic=''; $t=''; list($p,$o)=$v;
			switch($kr){
			case('img'):$t=after($p,'/'); $im=telex::thumb($t,'micro'); break;
			case('video'):$rt=telex::playmetas($p); $t=isset($rt[0])?$rt[0]:nohttp($p);
				if(isset($rt[2]))$im=telex::thumb($rt[2],'micro'); $pic='youtube'; break;
			case('web'):$rt=telex::playmetas($p); $t=isset($rt[0])?$rt[0]:nohttp($p);
				if(isset($rt[2]))$im=telex::thumb($rt[2],'micro'); $pic='newspaper-o';  break;
			case('chat'):$rb=Sql::read_inner('name','chatlist','login','ruid','rv','where roid='.$p);
				if($rb)$t=lang('with').' '.implode(', ',$rb); else $t='#'.$p; $pic='comments'; break;
			case('article'):$t=Sql::read('tit','articles','v','where id='.$p);
				$pic='file-text-o'; break;
			case('gps'):$t=Gps::com(['coords'=>$p]); $pic='map-marker'; break;
			//case('poll'):$t=poll::tit(['id'=>$p]); $pic=ics('poll'); break;
			//case('slide'):$t=slide::tit(['id'=>$p]); $pic=ics('slide'); break;
			default: $pic=ics($kr);
				if(method_exists($kr,'tit'))$t=$kr::tit(['id'=>$p]); else $t=$p; break;}
			if($pic)$pic=pic($pic,24); if($im)$im=img('/'.$im,45); $rid=randid('imk');
			$logo=($im?$im:$pic).' '.input($rid,$t,'40').' '; $bt=pic('save',24).' ';
			$bt=pagup('tlxcall,keepsave|com='.$kr.',p1='.nohttp($p).',p2='.$o.',tit='.$t.'|'.$rid,$bt,'');
			$ret.=div($logo.$bt);}
			//p(telex::$objects);
		}
		return div(lang('add2desktop'),'grey').div($ret,'bloc_content objects');}
	
	//share
	static function share($p){$id=val($p,'id'); $txt=val($p,'txt'); $root=host().'/id/'.$id;
		$tw='http://twitter.com/intent/tweet?original_referer='.$root.'&url='.$root.'&text='.utf8_encode($txt).' #telex'.'&title=Telex:'.$id; $fb='http://www.facebook.com/sharer.php?u='.$root;
		$gp='https://plusone.google.com/_/+1/confirm?hl=fr-FR&url='.$root;
		$st='http://wd.sharethis.com/api/sharer.php?destination=stumbleupon&url='.$root;
		$ptw=pic('twitter-square','24','twitter'); $pfb=pic('facebook-official','24','facebook'); 
		$pgp=pic('google-plus-official','24','gplus'); $pst=pic('stumbleupon-circle','24','stumble');
		$ret=href($tw,$ptw,'','',1).href($fb,$pfb,'','',1);
		$ret.=href($gp,$pgp,'','',1).href($st,$pst,'','',1);
		$ret.=aj('sndml|tlxcall,sendmail|id='.$id,pic('envelope-o',24)).span('','','sndml');
		return $ret;}
	
	static function sendmail($p){$id=val($p,'id');
		$ret=input('to','','40',lang('to'));
		$ret.=hidden('subject',ses('user').' '.lang('send you',1).' '.lang('a',1).' '.lang('telex'));
		$ret.=hidden('message','http://'.$_SERVER['HTTP_HOST'].'/'.$id);
		$ret.=aj('sndml|sendmail,send||subject,message,to',lang('send'),'btn');
		return $ret;}
	
	//labels		
	static function labels_in($p){$id=val($p,'lbl'); if(!$id)return;
		list($ico,$ref)=Sql::read('icon,ref','labels','rw','where id="'.$id.'"');
		return span(pic($ico).$ref,'','lblxt').hidden('lbl',$id);}
	
	static function labels($p){$rid=val($p,'rid');
		//$ret=input('lbladd','',28);
		//$ret=btj(lang('use'),'innfromval(\'lbladd\',\'lbl\');','btsav');
		$call='lbcbk|tlxcall,labels_in|';
		$prm='';//['onclick'=>'toggle_close(\'tlxapps\');'];
		$ret=aj($call.'lbl=0',lang('none'),'',$prm);
		$r=Sql::read('id,ref,icon','labels','','order by icon desc');
		foreach($r as $k=>$v)$rb[$v[2]][]=aj($call.'lbl='.$v[0],ucfirst($v[1]),'',$prm);
		foreach($rb as $k=>$v)$ret.=pic($k,'36').implode('',$v);
		return div($ret,'list');}
	
	//ascii
	static function ascii($p){
		$id=val($p,'rid'); $all=val($p,'all'); $ret='';
		$r=explode(' ',ascii::vars());
		foreach($r as $v)$ret.=btj($v,'insert(\''.$v.'\',\''.$id.'\');','btn').' ';
		return $ret;}
	
	//profilemenu
	static function profilemenu(){
		$r[]=array('profile','j','popup,,,1|profile,profile_edit','user','edit profile');
		$r[]=array('profile/lang','j','returnVar,lng,reload|Lang,set|lang=fr','flag','fr');
		$r[]=array('profile/lang','j','returnVar,lng,reload|Lang,set|lang=en','flag','en');
		if(auth(6) or ses('dev')=='prog'){
		$r[]=array(''.ses('dev').'/mode','j','ses,,reload||k=dev,v=prog','dev','prog');
		$r[]=array(''.ses('dev').'/mode','j','ses,,reload||k=dev,v=prog','prod','prod');
		$r[]=array(''.ses('dev').'','j','popup|dev2prod','update','publish');}
		$r[]=array('profile','j',',,reload|login,disconnect','power-off','logout');
		return $r;
	}
}
?>
