/*nvii-media*/	  
app.directive('dashboardCarousel', function($document, $window, $timeout) {
    return {
        restrict: 'AC',
		scope: {
            dashboardCarouselAfterSlide: '&',
			change:'=change',
			loading:'=',
			delay:'=',
			reload:'=reload'
        },		
        link: function(scope, element, $attrs) {
			var $items = $([]),
				
			$list = $('.dashboard-carousel-list', element),
			$nextButton = $('.dashboard-carousel-nav-next', element),
			$prevButton = $('.dashboard-carousel-nav-prev', element),
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
					fixItemsHeight();
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

			$timeout(function() {
				fixItemsHeight();
			}, 200);

			function updateItems() {
				$items = $('.dashboard-carousel-list .dashboard-carousel-item', element);
			}

			function updateVisibleCount() {
				visibleCount = parseInt($list.outerWidth() / $items.eq(startVisibleIndex).outerWidth() + 0.5);
			}

			function fixItemsHeight() {
				var maxHeight = 0;
				$items.each(function() {
					var outerHeight = $(this).outerHeight();
					if (outerHeight > maxHeight) {
						maxHeight = outerHeight;
					}
				});
				$items.each(function() {
					$('> div', this).css('min-height', maxHeight);
				});
			}

			function canSlide(direction) {
				// wenn direction kleiner 0 gib true zurück wenn visibleIndex + visibleCount kleiner als item länge ist. 
				return direction < 0 ? startVisibleIndex + visibleCount < $items.length : startVisibleIndex > 0;
			}

			function slide(direction) {
				if (animation || !canSlide(direction)) {
					return false;
				}
				var listLeftPos = parseFloat($list.css('left')),
					offset = direction * $items.eq(startVisibleIndex).outerWidth();

				animation = true;
				$list
					.animate(
						{
							left: listLeftPos + offset
						},
						400,
						function() {
							animation = false;
							startVisibleIndex -= direction;
							if (scope.dashboardCarouselAfterSlide !== undefined && scope.dashboardCarouselAfterSlide() !== undefined) {
								scope.dashboardCarouselAfterSlide()(
									startVisibleIndex,
									visibleCount,
									direction,
									function() {
										$timeout(function() {
											updateItems();
											fixItemsHeight();
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

			scope.$watch(function() { return scope.delay; }, 
				
				function(newVal, oldVal) {
					if (newVal === 0) {
						startVisibleIndex = 0;
						visibleCount = -1;
						$list.animate({left: 0});
						$timeout(function() {
							updateItems();
							updateVisibleCount();
							fixItemsHeight();
						});
					}
			});
			
			
		
		
			scope.$watch(function() { return scope.change; }, 
				
				function(newVal, oldVal) {
				
					if ( newVal != oldVal ) {
						$timeout(function(){
						$items = $([]);					
							updateItems();
							updateVisibleCount();
							fixItemsHeight();
						},500);
					}
			});	
			
			scope.$watch(function() { return scope.reload; }, 
				
				function(newVal, oldVal) {
				
					if ( newVal != oldVal ) {
						$timeout(function(){
						$items = $([]);
						startVisibleIndex = 0;
						visibleCount = -1;
						$list.animate({left: 0});
							updateItems();
							updateVisibleCount();
							fixItemsHeight();
						},500);
					}
			});	
        }
    };
});