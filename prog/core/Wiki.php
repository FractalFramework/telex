<?php

class Wiki{
//https://en.wikipedia.org/w/api.php

static function api($word){
	if(!$word)$word='Main Page';
	$u='https://en.wikipedia.org/w/api.php?action=query&titles='.rawurlencode($word).'&prop=revisions&rvprop=content&format=json';
	$d=File::curl($u);
	$r=json_decode($d,true);
	return $r;}

#reader
static function build($p){
	$r=self::api(val($p,'word')); //pr($r);
	return $r;}

static function read($p){
	$r=self::build($p);
	$rb=$r['query']['pages']; 
	$k=key($rb); //pr($rb[$k]);
	$text=$rb[$k]['revisions'][0]['*'];
	$text=decode($text);
	return div($text,'pane');}
	
//com (apps)
static function com($p){
	$r=self::build($p);
	return $r['text'][0];}
	
//interface
static function content($p){
	$rid=randid('yd');
	$p['txt']=val($p,'txt',val($p,'param'));
	$ret=input('word',$p['txt']);
	$ret.=aj($rid.'|Wikipedia,read||word',lang('open'),'btn');
	return $ret.div('','board',$rid);}
}
?>