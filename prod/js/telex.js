//telex
var activelive=0; var nbnew=0; var reloadtime=10000;
function lastelex(){var mnu=getbyid("tlxbck"); var firstchild=mnu.childNodes[0];
	if(firstchild)if(mnu){var first=firstchild.id; return id=first.substr(3);}}
function recbt(nbnew){if(nbnew)var rn=nbnew.split("-");
	var btref=getbyid("tlxrec"); var btntf=getbyid("tlxntf");
	var tlxsubnb=getbyid("tlxsubnb").value; var btmsg=getbyid("tlxmsg");
	var sty='padding:0 3px; margin-left:4px;';
	if(btref){var div=btref.parentNode;
	if(rn[0]>=1){div.style="display:block;";//new posts
		btref.innerHTML=rn[0]; btref.style=sty;}
		else{div.style="display:none;"; btref.innerHTML=""; btref.style="padding:0;";}}
	if(rn[1]>=1){btntf.parentNode.className="btn abbt hlight";//notifications
		btntf.innerHTML=rn[1]; btntf.style=sty;}
		else{btntf.parentNode.className="btn abbt"; btntf.innerHTML=""; btntf.style="padding:0";}
	if(rn[2]){//subscriptions
		if(rn[2]>tlxsubnb){tlxsub.parentNode.color="#0088e6"; tlxsub.innerHTML=rn[2];}
		else{tlxsub.parentNode.color="#1da1f2"; tlxsub.innerHTML=tlxsubnb;}}
	if(rn[3]>=1){btmsg.parentNode.className="btn abbt hlight";//messages
		btmsg.innerHTML=rn[3]; btmsg.style=sty;}
		else{btmsg.parentNode.className="btn abbt"; btmsg.innerHTML=""; btmsg.style="padding:0";}}
function refresh(){var id=lastelex(); var prmtm=String(getbyid("prmtm").value);
	if(id)ajaxCall("returnVar,nbnew|telex,refresh","since="+id+","+prmtm);
	if(nbnew)setTimeout("recbt(nbnew)",200);}
function telexlive(ok){if(ok)activelive=0;
	if(activelive){refresh(); xa=setTimeout("telexlive(0)",reloadtime);}}
addEvent(document,"scroll",function(event){loadscroll("telex,read","tlxbck")});

//search
function search2(id){
	var d=getbyid(id).value; if(d){getbyid('prmtm').value='sr='+d;
	ajaxCall("div,timbck,resetform|telex,search_txt","",id);}}

//chat
chatliv=1;
function chatlive(){
	if(getbyid('chtbck')){
		var room=getbyid('chtroom').value;
		ajaxCall("div,chtbck,"+room+"|chat,read");}
	setTimeout("chatlive()",4000);}
if(chatliv)chatlive();

//gps telex
rid=0;
function gps_paste(position){
	var	gpsav=position.coords.latitude+"/"+position.coords.longitude;
	//ajaxCall('div,tlxbck|telex,save','ids=pubt,pubt='+gpsav+',conn=gps,lbl=map','');
	insert("["+gpsav+":gps]",rid);}
function geo2(id){rid=id;
	if(navigator.geolocation)navigator.geolocation.getCurrentPosition(gps_paste,gps_ko,{enableHighAccuracy:true,timeout:10000,maximumAge:600000});}

//profile
function geo(){
	if(navigator.geolocation)navigator.geolocation.getCurrentPosition(gps_ok,gps_ko, {enableHighAccuracy:true, timeout:10000, maximumAge:600000});
	else console.log("need html5");}
function gps_ok(position){
	var gpsav=position.coords.latitude+"/"+position.coords.longitude;
	ajaxCall("div,gpsloc|profile,gpsav","gps="+gpsav);}

function invertclr(clr){
	var vclr=parseInt(clr,16);//var nclr=16777215-vclr;
	if(vclr>8388607)return 'black'; else return 'white';}
function affectclr(e){
	e.style.backgroundColor='#'+e.value;
	e.style.color=invertclr(e.value);}