//tlex
var activelive=0; var nbnew=0; var reloadtime=10000;
function lastelex(){var mnu=getbyid("tlxbck"); var firstchild=mnu.childNodes[0];
	if(firstchild)if(mnu){var first=firstchild.id; return id=first.substr(3);}}
function recbt(nbnew){if(nbnew)var rn=nbnew.split("-");
	var btref=getbyid("tlxrec"); var btntf=getbyid("tlxntf"); var btmsg=getbyid("tlxmsg");
	var tlxsub=getbyid("tlxsub"); var tlxabs=getbyid("tlxabs");
	var styon="padding:0 3px;"; var styoff="padding:0;";
	if(btref){var div=btref.parentNode;
	if(rn[0]>=1){div.style="display:block;";//new posts
		btref.innerHTML=rn[0]; btref.style=styon;}
	else{div.style="display:none;"; btref.innerHTML=""; btref.style=styoff;}}
	if(rn[1]>=1){btntf.parentNode.className="btn abbt hlight";//notifications
		btntf.innerHTML=rn[1]; btntf.style=styon;}
	else{btntf.parentNode.className="btn abbt"; btntf.innerHTML=""; btntf.style=styoff;}
	if(rn[2]>=1)//subscriptions
		//var tlxsubnb=getbyid("tlxsubnb").value;
		tlxsub.style.color="#ff4000"; else tlxsub.style.color="#1da1f2";
	if(rn[3]>=1)//approbations
		//var tlxabsnb=getbyid("tlxabsnb").value;
		tlxabs.style.color="#ff4000"; else tlxabs.style.color="#1da1f2";
	if(rn[4]>=1){btmsg.parentNode.className="btn abbt hlight";//messages
		btmsg.innerHTML=rn[4]; btmsg.style=styon;}
	else{btmsg.parentNode.className="btn abbt"; btmsg.innerHTML=""; btmsg.style=styoff;}}
function refresh(){var id=lastelex(); var prmtm=getbyid("prmtm").value;//String()
	if(id)ajaxCall("returnVar,nbnew|tlex,refresh","since="+id+","+prmtm);
	if(nbnew)setTimeout("recbt(nbnew)",200);}
function tlexlive(ok){if(ok)activelive=0;
	if(activelive){refresh(); xa=setTimeout("tlexlive(0)",reloadtime);}}
addEvent(document,"scroll",function(event){loadscroll("tlex,read","tlxbck")});

//search
function search2(id){
	var d=getbyid(id).value; if(d){getbyid('prmtm').value='srh='+d;
	ajaxCall("div,tlxbck|tlex,search_txt","",id);}}
function Search(old,id){
	var ob=getbyid(id); if(ob!=null)var src=ob.value;
	if(!src||src.length<2)return;
	if(src!=old){if(!old)return SearchT(id); else return;}
	if(src)search2(id);}
function SearchT(id){var ob=getbyid(id); 
	if(ob!=null)var old=ob.value; else var old='';
	setTimeout(function(){Search(old,id)},1000);}

//utils
function invertclr(clr){
	var vclr=parseInt(clr,16);//var nclr=16777215-vclr;
	if(vclr>8388607)return 'black'; else return 'white';}
function affectclr(e){
	e.style.backgroundColor='#'+e.value;
	e.style.color=invertclr(e.value);}