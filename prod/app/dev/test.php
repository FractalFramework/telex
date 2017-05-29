<?php
class test extends model{
static $private='6';
static $a='test';
static $db='test';
static $cb='tst';
static $cols=['tit','txt','tim'];
static $typs=['var','var','date'];
static $conn=0;

function __construct(){
	$r=['a','db','cb','cols','typs'];
	foreach($r as $v){appx::$$v=self::$$v; parent::$$v=self::$$v;}}

static function content($p){
	self::install();
	return appx::content($p);}
}

?>