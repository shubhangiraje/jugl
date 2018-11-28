app.directive('audioPlayer', function(messengerService) {

    return {
        restrict: 'A',
        link: function($scope, element, $attrs) {

            var playButton = element.find('.audio-message-button');
            var time = element.find('.audio-message-time');
            var duration = 0;
            var playhead = element.find('.audio-message-play-head')[0];
            var timeline = element.find('.audio-message-time-line')[0];

            var message = $scope.$eval($attrs.audioPlayer);
            var audioSrc = message.file.url;
            var audio = new Audio(audioSrc);

            playButton.on('click', function() {
                if (audio.paused) {
                    audio.play();
                    $(this).removeClass('play');
                    $(this).addClass('pause');
                } else {
                    audio.pause();
                    $(this).removeClass('pause');
                    $(this).addClass('play');
                }
            });

            function timeUpdate() {
                var playPercent = 100 * (audio.currentTime / duration);
                playhead.style.left = playPercent + '%';

                var currentSeconds = (Math.floor(audio.currentTime % 60) < 10 ? '0' : '') + Math.floor(audio.currentTime % 60);
                var currentMinutes = Math.floor(audio.currentTime / 60);

                time.html(currentMinutes + ":" + currentSeconds + ' / ' + Math.floor(audio.duration / 60) + ":" + (Math.floor(audio.duration % 60) < 10 ? '0' : '') + Math.floor(audio.duration % 60));
            }

            audio.addEventListener('timeupdate', timeUpdate, false);

            audio.addEventListener('loadedmetadata', function () {
                time.html("0:00" + ' / ' + Math.floor(audio.duration / 60) + ":" + (Math.floor(audio.duration % 60) < 10 ? '0' : '') + Math.floor(audio.duration % 60));
            }, false);

            audio.addEventListener('canplaythrough', function () {
                duration = audio.duration;
            }, false);

            audio.addEventListener('ended', function () {
                //var message = $scope.$eval($attrs.audioPlayer);
                //messengerService.messageClicked(message, true);
                playButton.removeClass('pause');
                playButton.addClass('play');
                audio.currentTime = 0;
                audio.pause();
            }, false);

            timeline.addEventListener('click', function(e) {
                var offsetX = e.offsetX;
                var newPos = (offsetX * 100)/timeline.offsetWidth;
                playhead.style.left = newPos + "%";
                audio.currentTime = (duration * newPos)/100;
            });


        }
    };

});