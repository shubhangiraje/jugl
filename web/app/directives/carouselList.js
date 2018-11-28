app.directive('carouselList', function($document, $timeout, $window) {
    return {
        restrict: 'C',
        scope: {
            carouselAfterSlide: '&'
        },
        link: function(scope, element, $attrs) {
            var $items = $([]),
                $list = $('.carousel-list-box', element),
                $nextButton = $('.carousel-nav-next', element),
                $prevButton = $('.carousel-nav-prev', element),
                animation = false,
                startVisibleIndex = 0,
                visibleCount = -1,
                resizeHandler = false;

            $nextButton.on('click', next);
            $prevButton.on('click', prev);

            $(window).on('resize', function() {
                if (resizeHandler) {
                    clearTimeout(resizeHandler);
                    resizeHandler = null;
                }
                resizeHandler = setTimeout(function() {
                    updateVisibleCount();
                    resizeHandler = null;
                }, 100);
            });

            scope.$on('$destroy',function() {
                $nextButton.off('click');
                $prevButton.off('click');
            });

            $timeout(function() {
                updateItems();
                updateVisibleCount();
            });

            function updateItems() {
                $items = $('.carousel-list-box .carousel-list-item', element);
            }

            function updateVisibleCount() {
                visibleCount = parseInt($list.outerWidth() / $items.eq(startVisibleIndex).outerWidth() + 0.5);
                if(visibleCount<$items.length) {
                    element.parent().addClass('carousel-control-show');
                }
            }

            function canSlide(direction) {
                return direction < 0 ? startVisibleIndex + visibleCount < $items.length : startVisibleIndex > 0;
            }

            function slide(direction) {
                if (animation || !canSlide(direction)) {
                    return false;
                }
                var listLeftPos = parseFloat($list.css('left')),
                    offset = direction * $items.eq(startVisibleIndex).outerWidth();

                animation = true;
                $list.animate({left: listLeftPos + offset}, 300, function() {
                    animation = false;
                    startVisibleIndex -= direction;
                        if (scope.carouselAfterSlide !== undefined && scope.carouselAfterSlide() !== undefined) {
                            scope.carouselAfterSlide()(
                                startVisibleIndex,
                                visibleCount,
                                direction,
                                function() {
                                    $timeout(function() {
                                        updateItems();
                                    }, 200);
                                }
                            );
                        }
                    }
                );
            }

            function next() {
                slide(-1);
            }

            function prev() {
                slide(1);
            }
        }
    };
});