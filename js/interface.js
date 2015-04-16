$(document).ready(function(){
	var show = false;
	var img_manu_ori = false;
	var $manu_list = $('#manufacturers_list_img');
	
	if($manu_list.hasClass('orientation-vertical')){
		img_manu_ori = true;
	}
	
	if($manu_list.hasClass('orientation-vertical') || $manu_list.hasClass('orientation-horizontal') ){
		show = true;
	}
	
	if(show){
		$(".manu-slider").jcarousel({
			vertical: img_manu_ori,
			wrap: 'circular'
		});
	}
});
