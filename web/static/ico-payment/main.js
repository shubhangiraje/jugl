$('.navbar-nav li').addClass('nav-item');
$('.navbar-nav li a').addClass('nav-link menu-link');
$('.navbar-nav').attr('data-animate', 'fadeInDown');
$('.navbar-nav').attr('data-delay', '.9');

$('.popup-close').on('click', function() {
    $('.popup-wrapper').hide();
});

// iChek chekbox and radio
$('input[type="checkbox"]').iCheck();

$(".fancybox").fancybox({
    loop: false
});