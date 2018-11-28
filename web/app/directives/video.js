app.directive('video', function($timeout) {
    return {
        restrict: 'A',
        link: function($scope, element, $attrs) {

            var video = element[0];
            var videoBox = element.closest('.video-box');
            var isPlaying = false;
            var spinnerTemplate = '<span class="video-spinner"></span>';
            var videoBarTemplate = '<div class="video-bar"><div class="video-muted on"></div><div class="video-time"></div></div>';

            videoBox.append(videoBarTemplate);
            var timeoutHandler = null;

            if (isInViewport()) {
                console.log('isInViewport');
                video.play();
            }

            function isInViewport() {
                var rect = element[0].getBoundingClientRect();
                var html = document.documentElement;
                return (
                    rect.top >= 0 &&
                    rect.left >= 0 &&
                    rect.bottom <= (window.innerHeight || html.clientHeight) &&
                    rect.right <= (window.innerWidth || html.clientWidth)
                );
            }

            $(document).on('scroll', function() {
                if (isInViewport()) {
                    if (!isPlaying) {
                        timeoutHandler = $timeout(function() {
                            video.play();
                            isPlaying = true;
                        }, 300);
                    }
                } else {
                    if (isPlaying) {
                        $timeout.cancel(timeoutHandler);
                        isPlaying = false;
                        video.currentTime = 0;
                        video.muted = true;
                        video.pause();
                        videoBox.find('.video-muted').removeClass('off');
                        videoBox.find('.video-muted').addClass('on');
                    }
                }
            });

            video.addEventListener('loadstart', function (event) {
                videoBox.append(spinnerTemplate);
            }, false);

            video.addEventListener('canplaythrough', function (event) {
                videoBox.find('.video-spinner').remove();
            }, false);

            video.addEventListener('timeupdate', function() {
                if (video.duration > 0) {
                    var currentSeconds = (Math.floor(video.currentTime % 60) < 10 ? '0' : '') + Math.floor(video.currentTime % 60);
                    var currentMinutes = Math.floor(video.currentTime / 60);
                    var time = currentMinutes + ":" + currentSeconds + ' / ' + Math.floor(video.duration / 60) + ":" + (Math.floor(video.duration % 60) < 10 ? '0' : '') + Math.floor(video.duration % 60);
                    videoBox.find('.video-time').show().text(time);
                    videoBox.find('.video-muted').show();
                }
            }, false);

            element.parent().on('click', function() {
                video.muted = !video.muted;
                if (video.muted) {
                    videoBox.find('.video-muted').removeClass('off');
                    videoBox.find('.video-muted').addClass('on');
                } else {
                    videoBox.find('.video-muted').removeClass('on');
                    videoBox.find('.video-muted').addClass('off');
                }
            });

            $scope.$on('$destroy',function() {
                $(document).off('scroll');
            });

        }
    };
});