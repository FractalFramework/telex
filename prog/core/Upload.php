<?php 

class Upload{

static function goodir($xt){
	if(stristr('.m4a.mpg.mp4.wmv.flv',$xt)!==false)$dir='video/';
	elseif(stristr('.rar.swf.txt.pdf',$xt)!==false)$dir='docs/';
	elseif(stristr('.txt.docx.pdf',$xt)!==false)$dir='text/';
	elseif(stristr('.jpg.png.gif',$xt)!==false)$dir='img/';
	elseif(stristr('.mp3',$xt)!==false)$dir='mp3/'; 
	return 'usr/'.$dir;}

static function save($p){$error=''; $rid='upfile'.val($p,'rid');
	$f=$_FILES[$rid]['name']; $f_tmp=$_FILES[$rid]['tmp_name'];
	if(!$f)return 'no file uploaded ';
	$xt=extension($f); $f=normalize(before($f,'.'));
	$goodxt='.mp4.m4a.mov.mpg.mp3.wav.wmv.swf.flv.jpg.png.gif.pdf.txt.docx.rar.zip.tar.gz';
	if(stristr($goodxt,$xt)===false)$error=$xt.'=forbidden; authorized='.$goodxt.br();
	$fsize=$_FILES[$rid]['size']/1024; $uplimit=25000;
	if($fsize>=$uplimit || $fsize==0)$error.=$fsize.'<25Mo ';
	//$fa=substr(md5($f),0,8).$xt;//defined by js
	//$dir=self::goodir($xt);
	$dir='img/full/'; $fa=$f.$xt; $fb=$dir.$fa;
	if(!is_dir($dir))Dir::mkdir_r($dir);
	if(is_uploaded_file($f_tmp) && !$error){
		if(!move_uploaded_file($f_tmp,$fb))$error.='not saved';}
		//if($xt=='.tar' or $xt=='.gz')unpack_gz($fb,$rep);
	else $error.='upload refused: '.$fb;
	File::mkthumb($fa,590);
	//if(val($p,'getinp'))return $fb;
	//if($rid)return;
	return img('/img/mini/'.$fa,72,72);}

static function call($rid){
	return '<form id="upl'.$rid.'" action="" method="POST" onchange="upload_profile(\''.$rid.'\')"><label class="uplabel btn"><input type="file" id="upfile'.$rid.'" name="upfile'.$rid.'" multiple />'.ico('image').'</label></form>'.span('','',$rid.'up');}
	
}
?>