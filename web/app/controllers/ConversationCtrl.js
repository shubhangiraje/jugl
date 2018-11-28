app.controller('ConversationCtrl', function ($scope,$rootScope,messengerService,ChatUploader) {

    $scope.chatUploader=ChatUploader(['avatarBig']);

    $scope.chatUploader.filters.push({
        name: 'recipientIgnoreFilter',
        fn: function() {
            return !messengerService.recipientIgnoredMe();
        }
    });

    $scope.chatUploadOptions={
        onSuccess: function(response,status,headers) {
            messengerService.conversation.message.chat_file_id=response.id;
            messengerService.sendMessage();
        }
    };

    function initConversation() {

    }

    initConversation();

    $rootScope.$on('messenger.conversation.user_changed',initConversation);

    this.sendMessage=function() {
        if (messengerService.recipientIgnoredMe()) return;
        messengerService.sendMessage();
    };


});
