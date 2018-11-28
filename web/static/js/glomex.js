(function ( $ ) {
    $.fn.glomexPlayer = function( options ) {
        return this.each(function() {
            var $this = $(this);
            var tracker = 0;
            var fullscreen = 0;
            var play = null;
            var settings = $.extend({
                videoId: '',
                duration: '',
                load: null,
                start : null,
                complete: null
            }, options );
            var countdownDuration = Math.round(settings.duration/1000+3);

            initPlayer();

            function initPlayer(){
                $this.html('<glomex-player data-player-id="2b9h4wtj2ryvj87" data-playlist-id="'+settings.videoId+'" data-width="588" data-height="330"></glomex-player> <div class="videoEl videoTracker" data-track="video"></div> <div class="timelineEl videoTracker" data-track="timeline"></div><div class="timelineBlockEl" data-track="timelineBlock"></div> <div class="controlEl videoTracker" data-track="video"></div><div class="placeholderEl1 videoTracker" data-track="placeholder"></div><div class="placeholderEl2 videoTracker" data-track="palceholder"></div><div class="nextVideo" data-track="nextVideoButton"></div>');
				
				events();
                if ( $.isFunction( settings.load ) ) {
                    settings.load.call(this);
                }

            }
			

					
            function events(){
                $('.videoTracker').on('mouseenter', function() {

                    tracker = $(this).data('track');

                    $('.videoTracker').show();
                    $(this).hide();
                });

                document.addEventListener("fullscreenchange", fullscreenChange, false);
                document.addEventListener("webkitfullscreenchange", fullscreenChange, false);
                document.addEventListener("mozfullscreenchange", fullscreenChange, false);

                setInterval(function(){
                    var elem = document.activeElement;

                    if(elem && elem.tagName == 'IFRAME'){
                        switch(tracker){
                            case 'video':
                                video();
                                break;
                        }
                        document.activeElement.blur();
                    }
                }, 100);

                var countdownInterval = setInterval(function(){
                    if(play==1){
                        countdownDuration--;
                        if(countdownDuration<=0){
                            if ( $.isFunction( settings.complete ) ) {
                                settings.complete.call(this);
                                $this.find('.videoEl').removeClass('videoTracker');
                                $this.find('.videoEl').show();
								$this.find('.nextVideoButton').show();
                                clearInterval(countdownInterval);
                            }
                        }
						var domElement = document.getElementsByClassName('k-player')[0];
						console.log(domElement);
                        $this.find('.timelineBlockEl').attr('style', 'width: '+Math.round((countdownDuration/(Math.round(settings.duration/1000+3))*100))+'%;');

                    }
                }, 1000);
            }

            function video(){
                if(!play){
                    if ( $.isFunction( settings.start ) ) {
                        settings.start.call(this);
                    }
                }
                if(play==1){
                    play = 2; //Video break
                    if ( $.isFunction( settings.break ) ) {
                        settings.break.call(this);
                    }
                }else{
                    play = 1; //Video play
                    if ( $.isFunction( settings.start ) ) {
                        settings.start.call(this);
                    }
                }
            }

            function fullscreenChange(){
			
                if(fullscreen){
                    fullscreen = 0;
                    $this.removeClass('fullscreen');
                }else{
                    fullscreen = 1;
                    $this.addClass('fullscreen');
                }
            }
						
        });

    };
}( jQuery ));
