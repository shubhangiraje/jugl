app.directive('fancybox', function($timeout) {
    return {
        restrict: 'A',
        link: function(scope, element, attr) {
            element.addClass('fancybox');
            if (scope.$last || scope.$eval(element.attr('fancybox-force-init'))) $timeout(function() {

                if(element.attr('fancybox-data')) {
                    element.on('click', function() {
                        $.fancybox.open(scope.$eval(element.attr('fancybox-data')), {
                            helpers:  {
                                title : {
                                    type : 'inside'
                                },
                                overlay : {
                                    showEarly : false,
                                    locked: false
                                }
                            }
                        });
                        return false;
                    });

                } else {
                    $('.fancybox').fancybox({
                        title : function(event) {
                            return $(this).attr('data-desc');
                        },
                        helpers:  {
                            title : {
                                type : 'inside'
                            },
                            overlay : {
                                showEarly : false,
                                locked: false
                            }
                        }
                    });
                }


            });
        }
    };
});
