var messengerService=angular.module('MessengerService', []);

messengerService.factory('messengerService',function($log,$rootScope,$interval,modal,jsonDataPromise,jsonPostDataPromise,gettextCatalog,userStatus) {
    var factory={
        conversationsData: {},
        isShowChat: false,
        compactMode: false,
        windowSize: {
            width: 2000,
            height: 2000
        },
        canShowChat: true
    };

    factory.moderatorDeleteMessage=function(id) {
        modal.confirmation({message:gettextCatalog.getString('Willst Du wirklich diese Nachricht entfernen?')},function(result){
            if (!result) return;

            jsonPostDataPromise('/api-moderator/delete-message',{id:id}).then(function(data){
                if (data.result===true) {
                    for (var idx = 0; idx < factory.conversation.log.length; idx++) {
                        if (factory.conversation.log[idx].id == id) {
                            factory.conversation.log.splice(idx, 1);
                            idx--;
                        }
                    }
                } else {
                    modal.alert({message:data.result});
                }
            });
        });
    };

    factory.deleteMessage=function(id) {
        modal.confirmation({message:gettextCatalog.getString('Möchtest Du wirklich die gewählten Nachrichten löschen?')},function(result){
            if (!result) return;

            socket.json.send({
                type: 'deleteMessage',
                message_id: id
            });
        });
    };

    factory.deleteConversationHistory=function(userId) {
        for(var i in factory.conversations) {
            if (userId==factory.conversations[i].user_id) {
                delete factory.conversations[i];
            }
        }
        delete factory.conversationsData[userId];
        updateUnreadedMessages();
    };

    factory.moderatorBlockUser=function(user_id) {
        modal.confirmation({message:gettextCatalog.getString('Willst Du wirklich den Benutzer für dieses Gruppenchat sperren?')},function(result){
            if (!result) return;

            jsonPostDataPromise('/api-moderator/block-user',{groupChatId:factory.conversation.user_id,userId:user_id}).then(function(data){
                if (data.result===true) {
                    for (var idx = 0; idx < factory.conversation.log.length; idx++) {
                        if (factory.conversation.log[idx].user.id == user_id) {
                            var msg=angular.copy(factory.conversation.log[idx]);
                            msg.visible_only_for_moderator=1;
                            msg.user.is_blocked_in_this_chat=1;
                            factory.conversation.log[idx]=msg;
                        }
                    }
                } else {
                    modal.alert({message:data.result});
                }
            });
        });
    };

    factory.moderatorUnblockUser=function(user_id) {
        modal.confirmation({message:gettextCatalog.getString('Willst Du wirklich den Benutzer für dieses Gruppenchat entsperren?')},function(result){
            if (!result) return;

            jsonPostDataPromise('/api-moderator/unblock-user',{groupChatId:factory.conversation.user_id,userId:user_id}).then(function(data){
                if (data.result===true) {
                    for (var idx = 0; idx < factory.conversation.log.length; idx++) {
                        if (factory.conversation.log[idx].user.id == user_id) {
                            var msg=angular.copy(factory.conversation.log[idx]);
                            msg.user.is_blocked_in_this_chat=0;
                            factory.conversation.log[idx]=msg;
                        }
                    }
                } else {
                    modal.alert({message:data.result});
                }
            });
        });
    };

    factory.moderatorBlockUserInTrollbox=function(user_id) {
        modal.confirmation({message:gettextCatalog.getString('Willst Du wirklich den Benutzer für alle Foren sperren?')},function(result){
            if (!result) return;

            jsonPostDataPromise('/api-moderator/block-user-in-trollbox',{groupChatId:factory.conversation.user_id,userId:user_id}).then(function(data){
                if (data.result===true) {
                    for (var idx = 0; idx < factory.conversation.log.length; idx++) {
                        if (factory.conversation.log[idx].user.id == user_id) {
                            var msg=angular.copy(factory.conversation.log[idx]);
                            msg.user.is_blocked_in_trollbox=1;
                            msg.user.is_blocked_in_this_chat=1;
                            factory.conversation.log[idx]=msg;
                        }
                    }
                } else {
                    modal.alert({message:data.result});
                }
            });
        });
    };

    factory.moderatorUnblockUserInTrollbox=function(user_id) {
        modal.confirmation({message:gettextCatalog.getString('Willst Du wirklich den Benutzer für alle Foren entsperren?')},function(result){
            if (!result) return;

            jsonPostDataPromise('/api-moderator/unblock-user-in-trollbox',{groupChatId:factory.conversation.user_id,userId:user_id}).then(function(data){
                if (data.result===true) {
                    for (var idx = 0; idx < factory.conversation.log.length; idx++) {
                        if (factory.conversation.log[idx].user.id == user_id) {
                            var msg=angular.copy(factory.conversation.log[idx]);
                            msg.user.is_blocked_in_trollbox=0;
                            msg.user.is_blocked_in_this_chat=0;
                            factory.conversation.log[idx]=msg;
                        }
                    }
                } else {
                    modal.alert({message:data.result});
                }
            });
        });
    };

    factory.decisionAddToFriends=function() {
        jsonPostDataPromise('/api-user-profile/decision-add-to-friends',{userId:factory.conversation.user_id}).then(function(){

        });
    };

    factory.decisionSkip=function() {
        jsonPostDataPromise('/api-user-profile/decision-skip',{userId:factory.conversation.user_id}).then(function(){

        });
    };

    factory.decisionSpam=function() {
        var config={
            template:'/app-view/spam-report-popup',
            classes: {'modal-offer':true},
            spamReport: {
                user_id: factory.conversation.user_id,
                okCallback: function() {
                    jsonPostDataPromise('/api-user-profile/decision-spam',{userId:factory.conversation.user_id}).then(function(){
                        factory.conversation=null;
                    });
                }
            }
        };

        //angular.extend(config,data);

        modal.show(config);

        /*
                $rootScope.$broadcast('spamReportPopup', {user_id:factory.conversation.user_id,okCallback:function(){
                    jsonPostDataPromise('/ext-api-user-profile/decision-spam',{userId:factory.conversation.user_id}).then(function(){
                        kendo.mobile.application.replace('#view-contacts');
                    });
                }});
                $('#view-spam-report-popup').kendoMobileModalView('open');
        */

        /*
        jsonPostDataPromise('/api-user-profile/decision-spam',{userId:factory.conversation.user_id}).then(function(){
            factory.conversation=null;
        });
        */
    };

    var uuid = (function() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000)
                .toString(16)
                .substring(1);
        }
        return function() {
            return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
            s4() + '-' + s4() + s4() + s4();
        };
    })();

    var socket = io.connect(config.chat.connect);

    function unserializeMessageExtra(msg) {
        if (angular.isString(msg.extra)) {
            msg.extra=angular.fromJson(msg.extra);
        }

        return msg;
    }

    function updateUnreadedMessages() {
        factory.user.unreaded_messages=0;
        factory.user.unreaded_chat_messages=0;
        factory.user.unreaded_group_chat_messages=0;

        for(var i in factory.conversations) {
            factory.user.unreaded_messages+=factory.conversations[i].unreaded_messages;
            if (factory.conversations[i].user_id>0) {
                factory.user.unreaded_chat_messages += factory.conversations[i].unreaded_messages;
            } else {
                factory.user.unreaded_group_chat_messages += factory.conversations[i].unreaded_messages;
            }
        }
    }

    factory.addSystemMessage=function(user_id,url,params) {
        factory.systemMessage={
            user_id:user_id,
            url:url,
            params: params
        };
    };

    factory.recipientIgnoredMe = function() {
        if (!factory.conversation) return false;
        var ignored=factory.ignored_ids.indexOf(+factory.conversation.user_id)>=0;

        if (ignored && factory.conversation.user_id<0 && +userStatus.status.is_blocked_in_trollbox>0) {
            modal.alert({message:"Du wurdest für alle Foren von einem Moderator gesperrt"});
            return ignored;
        }

        if (ignored) {
            modal.alert({message:"Der Empfänger will derzeit keine Nachrichten empfangen"});
            return ignored;
        }

        return ignored;
    };

    socket.on('message',function(msg) {
        if (msg.status!==true) {
            $log.error(msg);
            return;
        }
        switch (msg.type) {
            case 'statusUpdate':
                $rootScope.$broadcast('statusUpdateRequested',msg.options);
                break;

            case 'authResult':
                break;

            case 'initInfo':
                delete msg.type;
                delete msg.status;
                angular.extend(factory,msg);
                updateUnreadedMessages();
                //console.log(msg);

                $rootScope.$broadcast('reinit-pane', {id: 'messenger-contacts-list'});
                break;

            case 'updateUsersInfo':
                for(var userIdx in msg.users) {
                    var user=msg.users[userIdx];
                    if (factory.users[user.id]) {
                        angular.extend(factory.users[user.id],user);
                    }
                }
                $rootScope.$broadcast('reinit-pane', {id: 'messenger-contacts-list'});

                break;

            case 'updateConversationsInfo':
                if (msg.flags && msg.flags.setOthersUnreadedMessagesToZero) {
                    for (var fConversation in factory.conversations) {
                        factory.conversations[fConversation].unreaded_messages=0;
                    }
                }

                var requestInitInfo=false;

                for(var msgConversationIdx in msg.conversations) {
                    var found=false;
                    for(var conversationIdx in factory.conversations) {
                        if (msg.conversations[msgConversationIdx].user_id==factory.conversations[conversationIdx].user_id) {
                            angular.extend(factory.conversations[conversationIdx],msg.conversations[msgConversationIdx]);
                            found=true;
                            break;
                        }
                    }
                    if (!found) {
                        requestInitInfo=true;
                    }
                }

                if (requestInitInfo) {
                    socket.json.send({type:'getInitInfo'});
                }

                updateUnreadedMessages();
                break;

            case 'updateMessageInfo':
                processUpdateMessageInfo(msg);
                break;

            case 'sendMessageAck':
                for(var conversationsDataIdx in factory.conversationsData) {
                    if (factory.conversationsData[conversationsDataIdx].message.uuid===msg.uuid) {
                        factory.conversationsData[conversationsDataIdx].message.chat_file_id=null;
                        if (msg.status===true) {
                            factory.conversationsData[conversationsDataIdx].message.uuid=null;
                            //console.log(factory.conversationsData[conversationsDataIdx].message.content_type);
                            if (factory.conversationsData[conversationsDataIdx].message.content_type=='TEXT') {
                                factory.conversationsData[conversationsDataIdx].message.text = null;
                            }
                        }
                        factory.conversationsData[conversationsDataIdx].message.sending = false;
                    }
                }
                break;

            case 'history':
                processMessageHistory(msg);
                break;

            case 'getHistoryResponse':
                processMessageGetHistoryResponse(msg);
                break;

            default:
                $log.error('unknown message');
                $log.error(msg);
        }
        $rootScope.$apply();
    });

    function windowHasFocus() {
        if (typeof document.hidden !== "undefined") { // Opera 12.10 and Firefox 18 and later support
            hidden = "hidden";
        } else if (typeof document.mozHidden !== "undefined") {
            hidden = "mozHidden";
        } else if (typeof document.msHidden !== "undefined") {
            hidden = "msHidden";
        } else if (typeof document.webkitHidden !== "undefined") {
            hidden = "webkitHidden";
        }

        return !document[hidden];
    }

    function markActiveConversationMessagesAsReaded() {
        if (!factory.conversation || !windowHasFocus) return;
        readedIds=[];
        for(var logIdx in factory.conversation.log) {
            var msg=factory.conversation.log[logIdx];
            if (msg.type=='INCOMING_UNREADED') {
                readedIds.push(msg.id);
            }
        }

        if (readedIds.length>0) {
            socket.json.send({
                type:'markMessagesAsReaded',
                message_id:readedIds
            });
        }
    }

    $interval(markActiveConversationMessagesAsReaded,1000*5);

    function processMessageGetHistoryResponse(msg) {
        for(var conversationsDataIdx in factory.conversationsData) {
            var conversationsData=factory.conversationsData[conversationsDataIdx];
            if (conversationsData.user_id==msg.user_id) {
                conversationsData.loadingHistory=false;
                for (var logIdx in msg.log) {
                    if (!msg.log[logIdx].deleted) {
                        conversationsData.log.push(unserializeMessageExtra(msg.log[logIdx]));
                    }
                }
            }
        }
        markActiveConversationMessagesAsReaded();
    }

    function processUpdateMessageInfo(msg) {
        for(var conversationsDataIdx in factory.conversationsData) {
            var conversationsData=factory.conversationsData[conversationsDataIdx];
            for (var logIdx in conversationsData.log) {
                for(var msgIdx in msg.messages) {
                    if (msg.messages[msgIdx].id==conversationsData.log[logIdx].id) {
                        angular.extend(conversationsData.log[logIdx],unserializeMessageExtra(msg.messages[msgIdx]));

                        // force chat's ngRepeat to rebuild message's html code
                        conversationsData.log[logIdx]=angular.fromJson(angular.toJson(conversationsData.log[logIdx]));
                    }
                }
            }
        }
    }

    function processMessageHistory(msg) {
        // send ack for received message
        if (msg.log.length==1 && (msg.log[0].type=='INCOMING_UNREADED' || msg.log[0].type=='INCOMING_UNDELIVERED')) {
            socket.json.send({
                type:'messageReceivedAck',
                msgId: msg.log[0].id,
                resendByPush: false
            });
        }

        if (msg.log.length==1 && msg.log[0].type !== undefined) {
            var params = {user_id: msg.user_id};
            params = angular.extend(params, angular.copy(msg.log[0]));
            $rootScope.$broadcast('messageBalloon', params);
        }

        // add message to conversation history log
        for(var conversationsDataIdx in factory.conversationsData) {
            var conversationsData=factory.conversationsData[conversationsDataIdx];
            if (conversationsData.user_id==msg.user_id) {
                for (var logIdx in msg.log) {
                    if (msg.log[logIdx].deleted) {
                        for (var logId in conversationsData.log) {
                            if (conversationsData.log[logId].id == msg.log[logIdx].id) {
                                conversationsData.log.splice(logId,1);
                                break;
                            }
                        }
                    } else {
                        conversationsData.log.push(unserializeMessageExtra(msg.log[logIdx]));
                    }
                }
            }
        }

        // set message as last for conversation
        if (msg.log.length>0) {
            var found = false;
            for (var conversationsIdx in factory.conversations) {
                var conversation = factory.conversations[conversationsIdx];
                if (conversation.user_id == msg.user_id) {
                    found = true;
                    if (conversation.message.id<msg.log[0].id) {
                        conversation.message=msg.log[0];
                    }

                    if ((msg.log[0].type=='INCOMING_UNREADED' || msg.log[0].type=='INCOMING_UNDELIVERED') && (!factory.conversation || factory.conversation.user_id!=msg.user_id)) {
                        conversation.unreaded_messages++;
                        updateUnreadedMessages();
                    }
                }
            }
            if (!found) {
                socket.json.send({type:'getInitInfo'});
            }
        }
        markActiveConversationMessagesAsReaded();
    }

    socket.on('connect',function() {
        socket.json.send({type:'auth',key:config.chat.authorizationKey});
    });

    factory.sendMessage=function() {
        if (!factory.conversation.message.chat_file_id && !factory.conversation.message.text)
            return;

        function send() {
            factory.conversation.message.uuid=uuid();
            factory.conversation.message.content_type=factory.conversation.message.chat_file_id ? 'IMAGE':'TEXT';
            if (angular.isObject(factory.conversation.message.extra)) {
                factory.conversation.message.extra=angular.toJson(factory.conversation.message.extra);
            }

            socket.json.send({
                type: 'sendMessage',
                message: {
                    user_id: factory.conversation.user_id,
                    content_type: factory.conversation.message.content_type,
                    text: factory.conversation.message.chat_file_id ? null:factory.conversation.message.text,
                    chat_file_id: factory.conversation.message.chat_file_id,
                    uuid: factory.conversation.message.uuid,
                    extra: factory.conversation.message.extra
                }
            });
            factory.conversation.message.sending=true;
        }

        if (factory.systemMessage && factory.systemMessage.user_id==factory.conversation.user_id) {
            jsonPostDataPromise(factory.systemMessage.url,factory.systemMessage.params).then(function(){
                send();
            },function(){
                send();
            });
            delete factory.systemMessage;
        } else {
            send();
        }
    };

    factory.getLastUnreadedConversationUser = function() {
        var userId = 0;

        var maxTimestamp = 0;
        for (var i = 0; i < factory.conversations.length; i++) {
            if (factory.conversations[i].unreaded_messages > 0) {
                if (Date.parse(factory.conversations[i].message.dt) > maxTimestamp) {
                    userId = factory.conversations[i].user_id;
                }
            }
        }
        return userId;
    };

    factory.rollupChat=function() {
        if (factory.conversation) {
            factory.closeConversation();
        } else {
            factory.compactMode = true;
        }
    };

    factory.expandChat = function() {
        factory.compactMode = false;
    };

    factory.talkWithUser = function(user_id,message) {
        if (!factory.checkWindowSizeCompatibility(true))
            return false;

        factory.openConversation(user_id);
        if (angular.isString(message)) factory.conversation.message.text=message;
        factory.showChat(true, false);
    };

    factory.showChat=function(value, checkUnreaded) {


        if (value !== null && value !== undefined) {
            if (!factory.checkWindowSizeCompatibility(!!value))
                return false;
        }

        if (value && factory.isShowChat !== value && checkUnreaded) {
            var unreadedConversationUser = factory.getLastUnreadedConversationUser();
            if (unreadedConversationUser)
                factory.openConversation(unreadedConversationUser);
            else
                factory.closeConversation();
        }
        if (value !== null && value !== undefined) {
            factory.isShowChat = !!value;
            if (!value) {
                factory.closeConversation();
            }
        }

        return factory.isShowChat;
    };

    factory.closeConversation=function() {
        factory.conversation = null;
        delete factory.systemMessage;
        $rootScope.$broadcast('messenger.conversation.user_changed');
    };


    factory.setWindowSize=function(size) {
        factory.windowSize = size;
        factory.checkWindowSizeCompatibility(factory.isShowChat);
    };

    factory.checkWindowSizeCompatibility=function(isShowChat) {
        factory.canShowChat = factory.windowSize.width > 1100;
        if (!factory.canShowChat && isShowChat) {
            modal.alert({
                message:'Hast Du gewusst, dass wir eine Mobile App haben? Mit dieser App kannst Du bequem und einfach alle Funktionen unseres Jugl-Messengers von deinem mobilen Gerät nutzen.',
                buttons: [
                    {
                        caption: 'Android App holen',
                        class: 'ok mobile-app-link',
                        onClick: function() {
                            location.href = 'https://play.google.com/store/apps/details?id=com.kreado.jugl2&hl=de';
                        }
                    },
                    {
                        caption: 'iOS App holen',
                        class: 'cancel mobile-app-link ios',
                        onClick: function() {
                            location.href = 'https://itunes.apple.com/app/id978284701';
                        }
                    }
                ]
            });
        }
        return factory.canShowChat;
    };

    factory.openConversation=function(user_id) {

        if (!factory.conversationsData[user_id]) {
            factory.conversationsData[user_id]={
                user_id:user_id,
                log:[],
                message:{},
                loadingHistory: true
            };

            socket.json.send({type:'getHistory',user_id:user_id});
        }

        factory.conversation=factory.conversationsData[user_id];

        if (!factory.users[user_id]) {
            jsonDataPromise('/api-user-profile/open-conversation',{userId:user_id}).then(function(res){
                factory.conversationsData[res.id].user=res;
            });
        }

        markActiveConversationMessagesAsReaded();

        factory.expandChat();

        $rootScope.$broadcast('messenger.conversation.user_changed');
    };

    return factory;
});
