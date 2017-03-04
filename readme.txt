@fractalFrameWork 2015-2017
Free License GNU/GPL
====================

Thank you to download and use TELEX !
Telex is a Twitter-Like open source
It works under this fractalFrameWork environment

====================

**AJAX MVC FRAMEWORK**

This is a platform to build web applications.
The architecture is based on the the Ajax process.

REQUIREMENTS
----------
Server Apache PHP>=5.4 MYSQL 5 and mailing abilities

INSTALLATION
-------------
- copy files on your server
- chmod -R 777 /var/www/[your dir] (to ALL)
- set the config : rename /cnfg/site.com.php to [your domaine].php and fill the variables.
- rename htaccess.txt -> .htaccess
- /app/install will create all needed mysql tables (!! temporaly change var private=6 -> private=0 to access it while you are not again registered !!)
- /app/apisql will import all needed datas (lang, help, icons, desktop)
- /app/update will import most recents files from server.
- create first account, it will have the column 'auth' of mysql table 'login' set to 7 (as superadmin). Others accounts will have auth=2.

STRUCTURE
----------
2 folders (in /prod and /prog):
/app: create your Apps here
/core: classes of the System

DEV MODE
--------
/?dev== will switch to dev mode, a new dropmenu apperar
you can dev online, using files of /prog, and push them to /prod

HOW THE FRAMEWORK WORKS
---------------
/Core contain usable module for Apps.
/App contain unitaries Apps that contain PHP, CSS and JavaScript.

We can load Apps in chains from any other App.
The application collect the specifics headers and JS of the App.
The Ajax process let you call your Apps in a new page, or by ajax inside a div, a popup, a bubble, a pagup, or as a menu.

Ajax do that :
/app/appName 
	-> open popup 
		-> use javascript (ajax.js)
			-> call another App (/call.php)
				-> return result to javascript 
					-> return result to page 
				-> second call for headers (option)
					-> return spectific javascript in headers of page.

IN PHP
-------
To load an App :
	$p=array('key_1'=>'val_1');//params of App
	$content=App::open('myApp',$p);

You can target another than 'content' like this : 
	$content=App::open('myApp',['appMethod'=>'call','key_1'=>'val_1']);

BASIC STRUCTURE OF AN APP
--------------------------
Callable components are recognizable because of their alone "$p" (Array of Params).
They mean this function can be interfaced.
$p contain these variables :
- [appName], [appMethod] //params of com
- [key1], [...] //params sent to the App, directly or from some fields
- [pagewidth] //from javascript

//basic App
class App{
	public static $private='0';//public access
	public static function injectJs(){return self::js();}//loadable js
	public static function headers(){Head::add('jscode',self::injectJs());}//css and js
	public static function admin(){return $r[]=array('','lk','/','home','');}//add to admin
	public static function build($p){}//process
	public static function call($p){}//called from process
	public static function content($p){}//called by default
}

ON USE
------
Create your application as an Object in the folder /app.

//in /app/myApp.php 
class myApp{
	
	//used to append this in the headers of the parent page who call this in ajax
	public static function injectJs(){return 'alert('js added too headers';}
	
	//specific headers
	public static function headers(){
		//there are 4 methods : csscode, jscode, csslink, jslink
		Head::add('csscode','.btn2{text-shadow:0 0 10px #aaa;}');
		Head::add('jscode',self::injectJS());
	}
	
	//default method loaded by the App
	public static function content($p){
		//$p incoming associative array of parameters, include from inputs
		$text=val($p,'text');//verif if isset()
		
		//ajax button
		$params=array(
			'com'=>'popup',
			'app'=>'tests,result',
			'prm'=>'message='.$text; //',verbose=1,no-headers=1'
			'prm'=>array('message'=>$text); //alternative
			'inp'=>'inp1');
		return Ajax::call($params,lang('send'),'btn');//lang() word in good language
		
		//fast method (command in one line)
		return aj('popup|tests,result|message='.$text.'|inp1',lang('send'),'btn');
	}
}

URL
----
To join the Apps and their params there is a syntax for urls (url is a console) :
Url : /appName/p1=v1,p2=v2...

HTML FRAMEWORK
---------------
An HTML Framework makes it (very) easier and faster to write code.
Constantly keep the lib.php on eyes to help you to write code.
(And it will be memorized fastly !)

//make tag div with class deco:
$ret=tag('div',array('class'=>'deco'),'hello');

CONNECTORS
---------------

//make tag div with class deco:
$ret='[hello|class=deco:div]');

SQL CLASS
---------
A very useful class for Sql make it easy to create and update formated tables.
Each table have an ID ans an UPDATE column.
The creator will create tables at the init of the App.
A indicator will specify the format of your datas:

//give a string (v)
$data=Sql::read('id','login','v','where id=1'); //$data;

//give a simple associative array (a)
$data=Sql::read('id,user','login','a','where id=1'); //$data['id'];

//give all rows and columns ('')
$data=Sql::read('id,user','login','',''); //$data[0][0];

AJAX MENUS
-----------
A very exciting feature is the system of menus. 
You can build hierarchical ajax/html actions using a simple array and a loader.

//this will display a link to open pictos inside a submenus:
public static function menus(){
	$r[]=array('menu1/submenu1','j','popup|pictos','map','pictos');
	$r[]=array('menu1/submenu2','j','popup|pictos','map','pictos');
	return $r;}
public static function content(){
	return Menu::call(array('app'=>'demo_menu','method'=>'menus'));}

DESKTOP
-------
Works like Menus, but using folders.

//this will display a link to open pictos inside a submenus:
public static function structure(){
	$r[]=array('menu1/menu2','','pictos','map','pictos');
	return $r;}
public static function content(){
	return Desk::load('desktop','structure');}

SAMPLES
------
See more examples in /app/pub
Decline new Aps from /app/model.php

================
Credits FractalFramework 2017
http://tlex.fr