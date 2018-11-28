app.directive('carouselGallery', function($timeout) {
    return {
        restrict: 'A',
        link: function($scope, element, $attrs) {

            $timeout(function(){
                var itemElemCarousel = element.find('li');

                itemElemCarousel.on('click', function() {
                    itemElemCarousel.removeClass('carousel-active');
                    $(this).addClass('carousel-active');
                    var src = $(this).find('img').attr('data-big-src');
                    // $('.box-preview-details-image').html('<img src="'+src+'">');
                    $('.box-preview-details-image').find('img').attr('src', src);
                    $('.box-preview-details-image').find('a').attr('href', src);

                    var callback=$attrs.carouselGalleryChangeImageCallback;
                    if (callback) {
                        $scope.$eval(callback,{idx: $(this).find('img').attr('data-id')});
                    }
                });

                if (itemElemCarousel.size()>0) {
                    itemElemCarousel[0].click();
                }
            });
        }
    };
});