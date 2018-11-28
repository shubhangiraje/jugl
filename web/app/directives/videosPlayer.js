app.directive('videosPlayer', function($document) {
    return {
        restrict: 'C',
        link: function(scope, element, $attrs) {
			
	
			if(scope.video.provider=="glomex"){
			//glomex Player

			   $('.videos-player').glomexPlayer({
					videoId: scope.video.clip_id,
					duration: scope.video.clip_duration,
					nextVideoId: scope.video.clip_id,
					load: function() { 
						
					},
					start: function() { },
								break: function() {  },
					stop: function() {  },
					complete: function() {
						scope.video.complete = true;
						$('.nextVideo').show();
					}
				});
			$('.nextVideo').html('<h2>'+scope.video_list[0].full_name+'</h2><a class="btn btn-submit" href="#/videos/details/'+scope.video_list[0].video_id+'">Video anschauen</a>');
			
			}
			else if(scope.video.provider=="dailymotion"){
				
				window.dmAsyncInit = function()
				  {
					DM.init({ apiKey: 'aebe16247c4b812e469b', status: true, cookie: true });

					var adwatched=false;
					var endreached=false;

					var player = DM.player(document.getElementById('videosplayer'), {
					
					
					video: scope.video.clip_id,
					width: "100%",
					height: "500",
					params: {
						endscreen_enable : false
					}
					});
					

					player.addEventListener('ad_end',function (){
						adwatched = true;
						if(endreached){
							scope.video.complete = true;
						}
					});
					player.addEventListener('video_end',function (){
						endreached=true;
						if(adwatched){
						scope.video.complete = true;	
						}
					});
					
				  };
				  
				  (function() {
					var e = document.createElement('script');
					e.async = true;
					e.src = 'https://api.dmcdn.net/all.js';

					var s = document.getElementsByTagName('script')[0];
					s.parentNode.insertBefore(e, s);
				}());
				
				
			}
        }
    };
});