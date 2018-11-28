$(document).ready(function(){
	$($('body').find('a')).each(function(){
		
		if(typeof($(this).attr('href'))  !== 'undefined'){
		
			obj = $(this).attr('href').split('https://www.sponsorads.de');

			if($(this).attr('href') == 'https://www.sponsorads.de'+obj[1]){
				$(this).css('display', 'none');
			}
		}
	});
});