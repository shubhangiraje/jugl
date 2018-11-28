app.controller('OfferPreviewCtrl', function ($scope,$state,$rootScope,$http,modal,gettextCatalog) {

    if(!$rootScope.offerPreviewData) {
        $state.go('offers.add');
    }

    angular.extend($scope, $rootScope.offerPreviewData);

    this.activeImageChanged = function(idx) {
        if($scope.offer.bigImages) {
            $scope.offer.fancyboxImages = [];
            for (var i = idx; i < $scope.offer.bigImages.length; i++) {
                $scope.offer.fancyboxImages.push($scope.offer.bigImages[i]);
            }

            for (var y = 0; y < idx; y++) {
                $scope.offer.fancyboxImages.push($scope.offer.bigImages[y]);
            }
        }
    };

    this.openAgb = function() {
        var config={
            template:'/app-view/user-agb-popup',
            classes: {'modal-agb':true},
            offer: $scope.offer
        };
        modal.show(config);
    };

    this.save = function () {

        var params = {
            offer: $rootScope.offerSaveData
        };

        if ($rootScope.offerDraftId) {
            params.draftId = $rootScope.offerDraftId;
            $rootScope.offerDraftId = null;
        }

        $scope.offer.saving = true;
        $http.post('/api-offer/save', params)
            .error(function (data, status, headers, config) {
                $scope.offer.saving = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.offer.saving = false;

                if (data.result===true) {
                    modal.alert({
                        message:
                            data.willBeValidated ?
                                gettextCatalog.getString('Vielen Dank, dass Sie bei Jugl annoncieren. Ihre Anzeige wird schnellstmöglich geprüft. Mit freundlichen Grüßen, Ihr Jugl-Team.')
                                :
                                gettextCatalog.getString('Du hast Deine Werbung erfolgreich geschaltet. Du findest Deine Werbung unter "Werbung schalten - Werbung verwalten"')
                    },function(){
                        $rootScope.offerSaveData = null;
                        $rootScope.offerPreviewData = null;
                        $state.go('offers.myList');
                    });
                    return;
                }

                if (angular.isArray(data.offer.$allErrors) && data.offer.$allErrors.length > 0) {
                    $scope.offer = data.offer;
                    $scope.offer.saving = false;
                    return;
                }
            });
    };


    this.back = function() {
        if ($rootScope.offerDraftId) {
            $state.go('offers.draft-update', {id: $rootScope.offerDraftId});
        } else {
            $state.go('offers.add');
        }
    };





});
