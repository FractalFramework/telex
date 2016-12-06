@fractalFrameWork 2016
Free License GNU/GPL
====================

**AJAX MVC FRAMEWORK**

This is a platform to build web applications.
The architecture is based on the the Ajax process.

STRUCTURE
----------
2 folders :
/app: create your Apps here
/core: classes of the System

INSTALLATION
-------------

- copy files on your server
- chmod -R 777 /var/www (or other dir)
- param files of /cnfg (mysql access) ; rename site.com.txt -> [your domaine].txt and set it;
- rename htaccess.txt -> .htaccess
- /app/install will create all needed mysql tables (temporaly change var private=6 -> private=0)
- /app/apisql will fill some needed tables (lang, help, icons)
- create first account, set it's auth to 6 on mysql table 'login' (as superadmin)

DEV MODE
--------

/?dev== will switch to dev mode, a new dropmenu apperar, you can dev offline, and push to prod


HOW IT'S WORKS
---------------
Load Apps in chains from any other App.
The application collect the specifics headers of the App.
The Ajax process let you call your Apps in a new html page, or by ajax inside a div, a popup, a bubble, a pagup, or as a menu.

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
	$prm=array('key1'=>'val1');//params of App
	$content=App::open('myApp',$prm);

BASIC STRUCTURE OF AN APP
--------------------------
Callable components are recognizable because of their alone "$prm".
They mean this function can be interfaced.
$prm contain these variables :
- [appName], [appMethod] //params of com
- [key1], [...] //params sent to the App
- [pagewidth] //from javascript

//basic App
class App{
	public static $private='0';//public access
	public static function injectJs(){return self::js();}//loadable js
	public static function headers(){Head::add('jscode',self::injectJs());}//css and js
	public static function admin(){return $r[]=array('','lk','/','home','');}//add to admin
	public static function build(){}//process
	public static function call(){}//called from process
	public static function content($prm){}//called by default
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
	public static function content($prm){
		//$prm incoming associative array of parameters, include from inputs
		$text=val($prm,'text');//verif if isset()
		
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
	$r[]=array('menu1/menu2','j','popup|pictos','map','pictos');
	return $r;}
public static function content(){
	return Menu::call(array('app'=>'demo_menu','method'=>'menus'));}

DESKTOP
-------
Like Menus, but using folders.

//this will display a link to open pictos inside a submenus:
public static function structure(){
	$r[]=array('menu1/menu2','','pictos','map','pictos');
	return $r;}
public static function content(){
	return Desk::load('desktop','structure');}

BOOT
------
in cfng/site.com.txt :
- param 1 enable utf8 (1/0)
- param 2 select app used on boot (index/telex)

SAMPLES
------
See more examples in /app/pub

================
Credits Dav 2016
http://ph1.fr