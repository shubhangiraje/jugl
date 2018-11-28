app.directive('messengerTextarea', function($document, $window, $timeout) {
    return {
        restrict: 'A',
        link: function($scope, element, $attrs) {
            $('textarea.fake-area').remove();
            var $copy = $(element).clone().appendTo($('#wrapper')).addClass('fake-area');

            var originTextareaHeight = $(element).outerHeight(),
                $originMessagesContainer = $('.account-messenger .messenger-conversation-inner'),
                originMessagesContainerHeight = $originMessagesContainer.outerHeight(),
                heightFix = 0,
                messagesTextareaHeightFix = 0;

            function keydownHandler(event) {
                var keyCode = (event.which ? event.which : event.keyCode);
                if (!event.shiftKey && (keyCode == 13 || keyCode == 10)) {
                    $(element).siblings('button').click();
                    event.stopImmediatePropagation();
                    event.preventDefault();
                }
            }

            function keyupHandler(event) {
                $copy.val($(this).val());
                var lines = getLinesCount($copy);

                if (lines > 1) {
                    var textareaHeight = lines * 20 + 2;
                    $(this).css({
                        'line-height': '20px',
                        'height': textareaHeight + 'px'
                    });
                    messagesTextareaHeightFix = textareaHeight - originTextareaHeight;
                } else {
                    $(this).css({
                        'line-height': '41px',
                        'height': ''
                    });
                    messagesTextareaHeightFix = 0;
                }
                setMessagesContainerHeight();
            }

            function getLinesCount($elem) {
                var lineHeight = parseInt($elem.css('line-height'));
                var scrollHeight = parseInt($elem[0].scrollHeight);
                $elem[0].style.height = scrollHeight;
                return Math.floor(scrollHeight / lineHeight);
            }

            function windowResizeHandler() {
                heightFix = 0;
                setMessagesContainerHeight();

                var windowHeight = $(window).height(),
                    messengerHeight = $('.account-messenger').outerHeight();

                if (messengerHeight > windowHeight) {
                    heightFix = messengerHeight - windowHeight;
                } else {
                    heightFix = 0;
                }

                setMessagesContainerHeight();
            }

            function setMessagesContainerHeight() {
                $originMessagesContainer.css({
                    height: originMessagesContainerHeight - heightFix - messagesTextareaHeightFix
                });
            }

            setTimeout(windowResizeHandler, 100);

            $(element).on('keydown', keydownHandler);
            $(element).on('keyup', keyupHandler);
            $(window).on('resize', windowResizeHandler);

            $scope.$on('$destroy',function() {
                $('textarea.fake-area').remove();
                $(element).off('keydown', keydownHandler);
                $(element).off('keyup', keyupHandler);
                $(window).off('resize', windowResizeHandler);
            });
        }
    };
});
