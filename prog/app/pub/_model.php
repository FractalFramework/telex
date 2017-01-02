<?php

/*
To open an App :
- on window, use the url /app/model
- in code, use App::open('model',['method'=>'com','prm1'=>1]);
- using Ajax, use aj(). ex: aj('popup|model,com|prm1=1,prm2=2','click!');
*/

class _model{	
	#set this auth level required to acceed to this App
	#0=public,1:no edition,2:loged user,3,4,5,6:admin,7:superadmin
	static $private='0';
	#name of mysql table
	static $db='model';

	#js to append to the header of the parent page (who call this App by Ajax)
	//to do that, add the 4th indicator ,,,1 in aj()
	//aj('popup,,,1|model,com|prm1=1','click!');
	static function injectJs(){
		return '';
	}
	
	#header to display with the App
	//to do that, add param headers=1
	//aj('popup|model,com|headers=1,prm1=1','click!');
	static function headers(){
		Head::add('csscode','.board{
		color:#424242; background-color:#dbdbdb; border:1px solid #aaa;
		padding:7px 10px; border-radius:2px; box-shadow: 2px 2px 4px #aaa;
		}');
		Head::add('jscode',self::injectJs());
		Head::add('meta',array('attr'=>'property','prop'=>'description','content'=>'object _model for Ph1'));
	}
	
	#menus to add to the admin of /app or of popup
	static function admin(){//see core/Menus
		$r[]=array('','j','popup|_model,content','plus',lang('open'));
		return $r;
	}
	
	#install table
	//you can update your table here while development
	static function install(){
		Sql::create(self::$db,array('mid'=>'int','mname'=>'var'),0);}//1=update
	
	#titles to display in popup for each method
	static function titles($d){
		$d=val($p,'appMethod');
		$r['content']='welcome';
		$r['build']='model';
		if(isset($r[$d]))return lang($r[$d]);//vocable
	}
	
	#here is the real code of your app
	static function read(){
		#Sql called with methode 'v' (simple value)
		#note ses('uid') is the loged user.
		$r=Sql::read('mname',self::$db,'v','where mid='.ses('uid'));
		return $r;
	}
	
	#called by telex
	static function call($p){
		$ret=self::read($p);
		return $ret;
	}
	
	#interface with other Apps
	static function com(){
		return $p['msg'].': '.$p['inp1'];
	}
	
	#content
	//url: 'app/_model/p1=val1,p2=val2
	static function content($p){$ret='';
		//self::install();
		$p1=val($p,'p1');
		$p2=val($p,'p2');
		$p['rid']=randid('md');
		$p['p1']=val($p,'param',val($p,'p1'));//unamed param
		//$ret=hlpbt('underbuilding');
		$ret=input('inp1','value1','','1');
		$ret.=aj('popup|_model,com|msg=text|inp1',lang('send'),'btn');
		$ret=div($ret,'board');
	return $ret;}
}
?>
