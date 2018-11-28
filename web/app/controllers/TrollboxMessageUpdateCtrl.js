app.controller('TrollboxMessageUpdateCtrl', function ($scope,Uploader,userStatus,$rootScope,jsonPostDataPromise,modal) {

    var self = this;
    $scope.trollboxMessage=angular.copy($scope.modalService.data.trollboxMessage);
    $scope.trollboxCategoryList=angular.copy($scope.modalService.data.trollboxCategoryList);
    $scope.uploader=Uploader(['trollboxSmall']);


    console.log($scope.trollboxCategoryList);



    $scope.trollboxMessage.saving = false;

    $scope.fileUploadOptions={
        onSuccess: function(response,status,headers) {
            $scope.trollboxMessage.file_id = response.id;

            var img = new Image();
            img.onload = function() {
                if ($scope.trollboxMessage.file_id !== null) {
                    $scope.trollboxMessage.image = response.thumbs.trollboxSmall;
                }
            };
            img.src = response.thumbs.trollboxSmall;
        }
    };

    this.deleteTrollboxImage=function() {
        delete $scope.trollboxMessage.file_id;
        delete $scope.trollboxMessage.image;
    };

    this.save=function() {
        $scope.trollboxMessage.saving = true;
        jsonPostDataPromise('/api-trollbox/save', {trollboxMessage: $scope.trollboxMessage})
            .then(function (data) {
                if (angular.isArray(data.trollboxMessage.$allErrors) && data.trollboxMessage.$allErrors.length > 0) {
                    $scope.trollboxMessage = data.trollboxMessage;
                    $scope.trollboxMessage.saving = false;
                    return;
                } else {
                    modal.data.updateTrollboxMessage(data);
                    modal.hide();
                }
            }, function(data) {
                $scope.trollboxMessage.saving = false;
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
