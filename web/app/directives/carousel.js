app.directive('carousel', function($timeout) {
    return {
        restrict: 'A',
        link: function($scope, element, $attrs) {
            var options=null;

            $scope.$watch($attrs.carousel,function(newValue){
                options=newValue;
            });

            function onClick() {
                var container = $(options.container);

                if (container.is(':animated')) {
                    return;
                }

                var containerSize,contentSize,pos;

                if (options.type=='horizontal') {
                    containerSize=container.width();
                    contentSize=container[0].scrollWidth;
                    pos=container[0].scrollLeft;
                } else {
                    containerSize=container.height();
                    contentSize=container[0].scrollHeight;
                    pos=container[0].scrollTop;
                }

                newPos=pos+options.move;
                if (newPos+containerSize>contentSize) {
                    newPos=contentSize-containerSize;
                }

                if (newPos<0) {
                    newPos=0;
                }

                var time=options.time*Math.abs(newPos-pos)/Math.abs(options.move);

                if (options.type=='horizontal') {
                    container.animate({scrollLeft:newPos},time);
                } else {
                    container.animate({scrollTop:newPos},time);
                }


                //var parentBox = $(this).parent();
                //if($(this).hasClass('scroll-left')) {
                //    var prevElement = parentBox.find('.list-details-carousel > li.carousel-active').prev();
                //    if(prevElement.is('li')) {
                //        parentBox.find('.list-details-carousel > li').removeClass('carousel-active');
                //        prevElement.addClass('carousel-active');
                //
                //        var srcPrev = prevElement.find('img').attr('data-big-src');
                //        $('.box-preview-details-image').html('<img src="'+srcPrev+'">');
                //
                //    }
                //}
                //
                //if($(this).hasClass('scroll-right')) {
                //    var nextElement = parentBox.find('.list-details-carousel > li.carousel-active').next();
                //    if(nextElement.is('li')) {
                //        parentBox.find('.list-details-carousel > li').removeClass('carousel-active');
                //        nextElement.addClass('carousel-active');
                //
                //        var srcNext = nextElement.find('img').attr('data-big-src');
                //        $('.box-preview-details-image').html('<img src="'+srcNext+'">');
                //
                //    }
                //}

            }

            element.on('click',onClick);

            $scope.$on('$destroy',function(){
                element.off('click',onClick);
            });

            $timeout(function() {
                var parentBox = element.parent().find('.list-details-carousel');
                var widthElem = parentBox.find('li').outerWidth();
                var cntElem = parentBox.find('li').length;
                var offsetRight = parseInt(parentBox.find('li').css('margin-right'));

                widthElem = widthElem + offsetRight;
                var widthContainer = (widthElem * cntElem) - offsetRight;
                parentBox.width(widthContainer);
            });

        }
    };
});