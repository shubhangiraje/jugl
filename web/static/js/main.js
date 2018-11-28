$(document).ready(function() {

    if(!navigator.cookieEnabled) {
        $('.cookies-popup').show();
    }

    sliderHeader();

    // iChek chekbox and radio
    $('input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox-square-green'
    });

    $('input[type="radio"]').iCheck({
        radioClass: 'iradio-circle-green'
    });


    //select
    $('select').selectric({
        maxHeight: 300,
        disableOnMobile: false
    });


    //nav_menu
    $('.icon-nav-splash-menu, .icon-nav-menu').on('click', function() {
        $('.nav-menu').toggle();
    });


    $('.accordion-section-title').on('click', function() {
        var contentAccordion = $(this).closest('.accordion').find('.accordion-section-content');
        var titleAccordion = $(this).closest('.accordion').find('.accordion-section-title');
        contentAccordion.slideUp(300);

        if(!$(this).hasClass('opened')) {
            titleAccordion.removeClass("opened");
            var currentAttrValue = $(this).attr('href');
            $(this).addClass('opened');
            $(currentAttrValue).slideDown(300);
        } else {
            titleAccordion.removeClass("opened");
        }
        return false;
    });

    $('.open-popup').on('click', function() {
        var popup = $(this).attr('data-open');
        $('.'+popup).show();
    });

    $('.popup-close').on('click', function() {
        $('.popup-wrapper:visible').hide();
    });

    $('.registration-stage-btn').on('click',function() {
        $('#request_help').val(1);
        $('#registration-data, #registration-activation-code').submit();
    });

    $('.popup-registration-help-close, .popup-registration-help-btn-ok').on('click', function() {
        $('.popup-registration-help-wrap').hide();
    });


    $('.registration-submit-btn').on('click', function () {
        $('.popup-wrapper:visible').hide();
        $('#registration-form').submit();
    });


    $('#registration-referral-submit-btn').on('click', function() {
        $('.popup-wrapper:visible').hide();
        $('#registration-data').submit();
    });

	videoShowContent();




});



$(window).resize(function(){
    if($(window).width() > 759) {
        $('.nav-menu').show();
    } else {
        $('.nav-menu').hide();
    }
});



function sliderHeader() {
    $('.slide').css({opacity: 0.0, display: 'block'});
    $('.slide:first').css({opacity: 1.0});
    setInterval('slides()', 10500);
}

function slides() {
    var current;
    var next;

    if($('.slide.show')) current = $('.slide.show');
    else current = $('.slide:first');

    if (current.next().length) {
        if (current.next().hasClass('show')) next = $('.slide:first');
        else next = current.next();
    } else {
        next = $('.slide:first');
    }

    next.css({opacity: 0.0}).addClass('show').animate({opacity: 1.0}, 2000);
    current.animate({opacity: 0.0}, 2000).removeClass('show');
}
function videoShowContent(){
	if($('.videos-description').length > 0){
		console.log('test12');
		$('.videos-description').css({height: '200px'});
	}
	$()
}








