<?php
//fractalframework/tlex
if(isset($p['api']))$admin='';
else $admin=App::open('Admin',['app'=>$app]);
Head::add('code','<base'.atb('href',$_SERVER['HTTP_HOST']).' />');
$enc=ses('enc')?'utf8':'iso-8859-1';//according to cfng
Head::add('meta',['attr'=>'http-equiv','prop'=>'Content-Type','content'=>'text/html; charset='.$enc]);
Head::add('tag',['title','',lang('Tlex').($app?'-'.$app:'')]);
Head::add('rel',['name'=>'shortcut icon','value'=>'/favicon.ico']);
Head::add_name('generator','fractalframework');
Head::add_name('version','1704');
Head::add_name('viewport','user-scalable=no, initial-scale=1, width=device-width');
Head::add('csslink','/css/global.css');
Head::add('csslink','/css/apps.css');
Head::add('csslink','/css/pictos.css');
Head::add('csslink','/css/fa.css');
Head::add('jslink','/js/ajax.js');
Head::add('jslink','/js/utils.js');
if(!ses('updated') && auth(6))App::open('upgrade','');
#content
$content=App::open($app,$p);
stats::save($app,$p);
#render
$ret=Head::generate();
$ret.='<body onmousemove="popslide(event)" onmouseup="closebub(event)">'."\n";//
$ret.=tag('div',['id'=>'closebub','onclick'=>'bubClose()'],'');
$ret.=tag('div',['id'=>'admin'],$admin);
$ret.=tag('div',['id'=>'page'],$content);
$ret.=tag('div',['id'=>'popup'],'');
$ret.='</body>';
echo encode($ret);
?>