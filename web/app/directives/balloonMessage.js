app.directive('balloonMessage', function($rootScope, messengerService, userStatus, $timeout, userSettings) {

    return {
        restrict: 'A',
        link: function ($scope, element, $attrs) {
            $scope.balloons=[];
            $rootScope.$on('messageBalloon',function(event,data){
                var type = data.type.match(/INCOMING/g);

                if(type == 'INCOMING') {
                    $scope.balloons.push({
                        user: messengerService.users[data.user_id],
                        text: data.text,
                        sound: true
                    });

                    $timeout(function() {
                        $scope.balloons.splice(0, 1);
                    }, 3000);

                    if (userSettings.get('sounds') !== 0) {
                        var soundMessage = new Audio('/static/sound/message.mp3');
                        soundMessage.play();
                    }

                }
            });

        }
    };
});
