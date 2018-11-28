app.directive('advertising', function($document) {
    return {
        restrict: 'ACE',
		scope: {
			adFunction: '&'
		},
        link: function(scope, element, $attrs) {

			
			$('.advertising-items .advertising-item').each(function(){
				var id = $(this).attr('data-id');
				var user_bonus = $(this).attr('data-user-bonus');
				var scriptTmp = $(this)[0].innerHTML;
				var script = scriptTmp.replace('href="https','href="http');

				var str = '<div id="advertising-'+id+'" a-data-id="'+id+'" a-data-user-bonus="'+user_bonus+'" class="advertising_script">'+script+'</div>';
				$(element).find('.advertising-'+$(this).attr('data-position')).append(str);
				
				
			});
			
			$(element).click(function(){
				setFunction($(element).find('.advertising_script').attr('a-data-id'), $(element).find('.advertising_script').attr('a-data-user-bonus'));		
			});
		
			function setFunction(id, user_bonus) {
				if (scope.adFunction !== undefined && scope.adFunction() !== undefined) {
					scope.adFunction()(
						id=id,
						user_bonus = user_bonus
					);
					return false;
				}

            }
			
	
        }
    };
});


