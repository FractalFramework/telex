<?php
class model{
static $private='0';
static $a='model';
static $db='model';
static $cb='mdl';
static $cols=['tit','txt','pub'];
static $typs=['var','var','int'];//var,text,int,date
static $conn=0;//0,1(ptag),2(brut),no(br), while using 'txt'
static $db2='model_vals';//second db
static $open=1;//open directly in tlex

function __construct(){
	$r=['a','db','cb','cols','db2','conn'];
	foreach($r as $v)appx::$$v=self::$$v;}

/*specific cols:
- first col is actually used for title ['t']
- col "txt" (var) will accept connectors ['conn']
- col "answ" will assume choices
- col "com" will assume settings
- col "day" is a date
- col "nb" number 1-10
- col "cl" mean close
- col "pub" will assume privacy
$db2 must use col "bid" <-linked to-> id*/
static function install($p=''){
	appx::install(array_combine(self::$cols,self::$typs));}
	//Sql::create(self::$db2,['bid'=>'int','uid'=>'int','val'=>'var'],1);

static function admin($rid=''){
	$p['rid']=$rid; $p['o']='1';
	return appx::admin($p);}

static function titles($p){return appx::titles($p);}
static function injectJs(){return '';}
static function headers(){
	Head::add('csscode','');
	Head::add('jscode',self::injectJs());}

#edit
static function collect($p){
	return appx::collect($p);}

static function del($p){//->stream
	//$p['db2']=self::$db2;//second db
	return appx::del($p);}

static function save($p){//->edit
	return appx::save($p);}

static function modif($p){//->edit
	return appx::modif($p);}

//static function fc_tit($k,$v){}
static function form($p){
	//$p['html']='txt';//contenteditable for txt
	//$p['fctit']=1;//form col call fc_tit();
	//$p['barfunc']='barlabel';//function for bar()
	return appx::form($p);}

static function edit($p){//->form, ->call
	//$p['collect']=self::$db2;//second db
	//$p['help']='model_edit';//ref of help
	//$p['sub']=1;//edit sub-entries in $a::subform()
	return appx::edit($p);}

static function create($p){//->form
	//$p['pub']=0;//default privacy
	return appx::create($p);}

#build
static function build($p){//datas
	return appx::build($p);}

static function template(){
	//return appx::template();
	return '[[(tit)*class=tit:div][(txt)*class=txt:div]*class=paneb:div]';}

static function play($p){//->build, ->template
	//$r=self::build($p);
	return appx::play($p);}

static function stream($p){
	$p['t']=self::$cols[0];//used col as title
	return appx::stream($p);}

#call (read)
static function tit($p){
	$p['t']=self::$cols[0];//used col as title
	return appx::tit($p);}

static function call($p){//->play
	return appx::call($p);}

#com (edit)
static function com($p){//->content
	return appx::com($p);}

#interface
static function content($p){//->stream, ->call
	//self::install();
	return appx::content($p);}
}
?>