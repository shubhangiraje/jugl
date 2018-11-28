app.controller('MessengerCtrl', function ($scope,messengerService,userStatus, userNameFilter, $window, modal, gettextCatalog, jsonPostDataPromise, $rootScope) {
    var self = this;

    $scope.status=userStatus.status;
    $scope.currentDate = new Date();
    this.filterStatus = '';
    $scope.userConversations = {};

    this.displayChats='chats';

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

    $scope.$watch('messenger.conversations', function() {
        var userConversations = {};
        var conversations = $scope.messenger.conversations || [];
        angular.forEach(conversations, function(conversation) {
            userConversations[conversation.user_id] = conversation;
        });
        $scope.userConversations = userConversations;
    }, true);


    this.showConversationsList=function() {
        this.mode='conversations';
    };

    this.showConversationsList();

    this.showContactsList=function() {
        this.mode='contacts';
    };

    this.visibleConversationsList=function() {
        return this.mode=='conversations';
    };

    this.visibleContactsList=function() {
        return this.mode=='contacts';
    };

    $scope.userStatusFilter = function(isConversation) {
        return function(obj) {
            var user = obj;
            if (isConversation)
                try {
                    user = messengerService.users[obj.user_id];
                } catch (e) {
                    return false;
                }

            var filterStatus=self.filterStatus === '' || user.status>0;
            var filterChat=(self.displayChats==='chats' && user.id>0) || (self.displayChats==='forumChats' && user.id<0);

            return filterStatus && filterChat;
        };
    };

    this.showGeoposition=function(message) {
        $window.open('http://www.google.com/maps/place/'+message.geopos_lattitude+','+message.geopos_longitude);
    };

    $scope.rawProperty = function(key) {
        return function(item) {
            var props = key.split('.');
            while(props.length && (item = item[props.shift()]));
            return item;
        };
    };

    $scope.userOrderBy = function(user) {
        return userNameFilter(user);
    };


    $scope.userOrderDt = function(user) {
        if($scope.userConversations[user.id]) {
            return $scope.userConversations[user.id].message.dt;
        } else {
            return '';
        }
    };

    this.deleteMessage=function(message) {
        messengerService.deleteMessage(message.id);
    };

    this.deleteConversationMessages=function(id, event) {
        modal.confirmation({message:gettextCatalog.getString('Willst Du wirklich diese Chat entfernen?')},function(result) {
            if (!result) return;

            jsonPostDataPromise('/api-user-profile/delete-contact-history',{userId:id}).then(function(data) {
                messengerService.deleteConversationHistory(id);
                if (messengerService.conversation !== undefined && messengerService.conversation !== null && messengerService.conversation.user_id == id) {
                    messengerService.closeConversation();
                }
            },function(){
                modal.alert({message:data.result});
            });
        });

        event.preventDefault();
        if (event.stopPropagation) {
            event.stopPropagation();
        } else if(window.event) {
            window.event.cancelBubble = true;
        }
        return false;
    };


});
