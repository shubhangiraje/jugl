app.controller('ViewVideoIdentificationPopupCtrl', function ($scope,$state,modal,$rootScope,jsonDataPromise,jsonPostDataPromise,$timeout,messengerService) {

    var self = this;

    $scope.trollboxMessage = modal.dataInfo.trollboxMessage;

    $scope.state = {
        voting: false
    };

    this.votesViewVideo=function(id, type) {
        jsonDataPromise('api-trollbox/votes',{id:id, type: type})
            .then(function(data){
                var config={
                    template:'/app-view/view-votes-trollbox-popup',
                    classes: {'modal-info':true}
                };
                angular.extend(config,data);
                modal.show(config);
            });
    };

    function trollboxVote(id,vote) {
        $scope.trollboxMessage.voting = true;
        jsonPostDataPromise('/api-trollbox/vote-message', {id: id, vote: vote})
            .then(function (res) {
                $scope.state.voting = false;
                $scope.trollboxMessage=res.message;
                modal.alert({message:res.result});
            },function(){
                $scope.state.voting = false;
            });
    }

    this.trollboxVoteUp=function(id) {
        if ($scope.state.voting) {
            return;
        }
        trollboxVote(id,1);
    };

    this.trollboxVoteDown=function(id) {
        if ($scope.state.voting) {
            return;
        }
        trollboxVote(id,-1);
    };


    this.enterGroupChat=function(id) {
        $timeout(function(){
            jsonPostDataPromise('/api-trollbox/enter-group-chat', {id: id})
                .then(function (res) {
                    if(res.result===true) {
                        messengerService.talkWithUser(res.groupChatId);
                    }
                });
        });
    };

    function escapeRegExp(str) {
        return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    }

    this.emoticonsList = $rootScope.emoticonsList;
    this.emoticonsListInversion={};
    this.emoticonsRegExp=new RegExp(
        res=this.emoticonsList.map(function(emoticon) {
            return emoticon.codes.map(function(code) {
                self.emoticonsListInversion[code]=emoticon.num;
                return escapeRegExp(code);
            }).join('|');
        }).join('|'),'g'
    );

});