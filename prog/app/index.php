<?php

class index{
	
#content
static function content($prm){
	return div(Desk::load('desktop','com',val($prm,'dir')),'','wrapper');}
	
}

?>