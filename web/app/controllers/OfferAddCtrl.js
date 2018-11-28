app.controller('OfferAddCtrl', function ($scope, offerAddData, Uploader, $http, modal, $state, $rootScope, jsonPostDataPromise, gettextCatalog, $interval) {

    angular.extend($scope, offerAddData);

    $scope.state = {
        isSaveDraft: false
    };

    if($state.params.ids) {
        angular.extend($scope.offer,$rootScope.oldOfferAddData);
        $rootScope.oldOfferAddData = null;
        $scope.state.isSaveDraft = true;
    } else { 
        if($rootScope.offerSaveData) {
            angular.extend($scope.offer,$rootScope.offerSaveData);
            $rootScope.offerSaveData = null;
        } else {
            $rootScope.oldOfferAddData = {};
        }
        $rootScope.offerDraftId = null;
    }

    if($state.params.id) {
        $rootScope.offerDraftId = $state.params.id;
    }

    this.addInterests = function() {
        $rootScope.oldOfferAddData = angular.copy($scope.offer);
        delete $rootScope.oldOfferAddData.offerInterests;
        delete $rootScope.oldOfferAddData.offerParamValues;
        delete $rootScope.oldOfferAddData.$allErrors;
        delete $rootScope.oldOfferAddData.$errors;
        $state.go('offers.addStep1');
    };

    /*$scope.$watch('offer',function(newValue,oldValue) {
        if (oldValue.without_view_bonus) {
            var showWarning=false;
            for(var key in newValue) {
                if (key.indexOf("uf_")===0 && newValue[key]) {
                    newValue[key]='';
                    showWarning=true;
                }
            }

            if (showWarning) {
                modal.alert({message:gettextCatalog.getString('Nur mit Werbebudget möglich')});
            }
        }
    },true);*/

    $scope.$watch('offer.without_view_bonus',function(newVal,oldVal) {
        if (!oldVal && newVal) {
            $scope.offer.view_bonus=null;
            $scope.offer.view_bonus_total=null;

            for(var key in $scope.offer) {
                if (key.indexOf("uf_")===0) {
                    delete $scope.offer[key];
                }
            }
        }
    });

  /*  function splitParams() {
        var half=Math.ceil($scope.offer.offerParamValues.length/2);
        $scope.params1=$scope.offer.offerParamValues.slice(0,half);
        $scope.params2=$scope.offer.offerParamValues.slice(half);
    }

    splitParams();*/

    $scope.uploader=Uploader(['imageBig']);

    $scope.fileUploadOptions={
        onSuccess: function(response,status,headers) {
            $scope.offer.files.push(response);
        }
    };


    var loadingReceiversCount=false;
    var loadReceiversCountAfterLoading=false;

    function loadReceiversCount() {
        loadingReceiversCount=true;

        jsonPostDataPromise('/api-offer/get-receivers-count', {offer: $scope.offer}).then(function(data){
            loadingReceiversCount=false;
            if (loadReceiversCountAfterLoading) {
                loadReceiversCountAfterLoading=false;
                loadReceiversCount();
            }
            $scope.offer.receiversCount=data.receiversCount;
        },function(){
            loadingReceiversCount=false;
            if (loadReceiversCountAfterLoading) {
                loadReceiversCountAfterLoading=false;
                loadReceiversCount();
            }
        });
    }

	
	var loadingReceiversAllCount=false;
    var loadReceiversAllCountAfterLoading=false;

    function loadReceiversAllCount() {
        loadingReceiversAllCount=true;

        jsonPostDataPromise('/api-offer/get-receivers-all-count', {offer: $scope.offer}).then(function(data){
            loadingReceiversAllCount=false;
            if (loadReceiversAllCountAfterLoading) {
                loadReceiversAllCountAfterLoading=false;
                loadReceiversAllCount();
            }
            $scope.offer.receiversAllCount=data.receiversAllCount;

        },function(){
            loadingReceiversAllCount=false;
            if (loadReceiversAllCountAfterLoading) {
                loadReceiversAllCountAfterLoading=false;
                loadReceiversAllCount();
            }
        });
    }


    function syncPrice(offer,oldValue) {
        if (oldValue && Math.abs(oldValue-offer.notify_if_price_bigger*2)>0.01) {
            return;
        }
        if (!offer.price) {
            delete offer.notify_if_price_bigger;
        } else {
            offer.notify_if_price_bigger=Math.floor(offer.price/2*100+0.5)/100;
        }
    }

    $scope.$watch('offer.type',function(newValue) {
        var offer=$scope.offer;
        if (newValue!='AUCTION') {
            delete $scope.offer.notify_if_price_bigger;
        } else {
            syncPrice(offer);
        }
    });

    $scope.$watch('offer.buy_bonus',function(newValue) {
        $scope.offer.buy_bonus_provision=Math.floor($scope.SELLBONUS_SELLER_PARENTS_PERCENT*newValue+0.5)/100;
    });

    $scope.pricePopup = true;

    $scope.$watch('offer.price',function(newValue,oldValue){

        if(newValue!=oldValue && $scope.pricePopup) {
            showPricePopup();
            $scope.pricePopup = false;
        }

        var offer=$scope.offer;
        if (offer.type!='AUCTION') return;
        syncPrice(offer,oldValue);
    });

    $scope.$watch(function(){
        var obj={};
        for(var key in $scope.offer) {
            if (key.startsWith('uf_')) {
                obj[key]=$scope.offer[key];
            }
        }
        return angular.toJson(obj);
    },function(){
        if (loadingReceiversCount) {
            loadReceiversCountAfterLoading=true;
            return;
        }

		loadReceiversCount();
		
		if (loadingReceiversAllCount) {
            loadReceiversAllCountAfterLoading=true;
            return;
        }
		loadReceiversAllCount();

    });


    this.deleteFile=function(id) {
        for(var i in $scope.offer.files) {
            if ($scope.offer.files[i].id==id) {
                $scope.offer.files.splice(i,1);
                break;
            }
        }

        if($scope.uploader.queue.length !== 0) {
            $scope.uploader.queue.length = $scope.uploader.queue.length-1;
        }

    };

    this.save = function () {
        $scope.offer.saving = true;
        $http.post('/api-offer/save', {offer: $scope.offer})
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

    function showPricePopup() {
        modal.alert({message:gettextCatalog.getString('Hinweis für Händler! Bitte achte darauf, dass Du bei Jugl nichts teurer anbietest, als Du es auf anderen Websites tust.')});
    }


    if($scope.offer.offerInterests[0].level2Interest.offer_view_bonus) {
        $scope.offer.view_bonus_interest = $scope.offer.offerInterests[0].level2Interest.offer_view_bonus;
    } else {
        $scope.offer.view_bonus_interest = $scope.offer.offerInterests[0].level1Interest.offer_view_bonus;
    }

    if(!$scope.offer.view_bonus_interest) {
        $scope.offer.view_bonus_interest = 1;
    }
	
	if($scope.offer.offerInterests[0].level2Interest.offer_view_total_bonus) {
        $scope.offer.view_bonus_total_interest = $scope.offer.offerInterests[0].level2Interest.offer_view_total_bonus;
    } else {
        $scope.offer.view_bonus_total_interest = $scope.offer.offerInterests[0].level1Interest.offer_view_total_bonus;
    }

    if(!$scope.offer.view_bonus_total_interest) {
        $scope.offer.view_bonus_total_interest = 1;
    }

    this.preview = function () {
        $scope.state.isSaveDraft = false;
        $http.post('/api-offer/preview', {offer: $scope.offer})
            .error(function (data, status, headers, config) {
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                if (data.result===true) {
                    $interval.cancel(intervalSaveDraft);
                    $rootScope.offerSaveData = angular.copy($scope.offer);
                    delete $rootScope.offerSaveData.$allErrors;
                    delete $rootScope.offerSaveData.$errors;
                    $rootScope.offerPreviewData = {offer: data.offer};
                    $state.go('offers.preview');
                }

                if (angular.isArray(data.offer.$allErrors) && data.offer.$allErrors.length > 0) {
                    $scope.offer = data.offer;
                    $scope.offer.saving = false;
                    return;
                }
            });

    };

    $scope.$watch('offer',function(newValue, oldValue) {
        if (newValue!=oldValue) {
            if(!$scope.offer.saving) {
                $scope.state.isSaveDraft = true;
            }
        }
    }, true);


    var intervalSaveDraft=$interval(function(){
        if($scope.state.isSaveDraft) {
            if($rootScope.offerDraftId) {
                $http.post('/api-offer-draft/update', {
                    id: $rootScope.offerDraftId,
                    offer: $scope.offer
                })
                    .error(function (data, status, headers, config) {})
                    .success(function (data, status, headers, config) {

                    });
            } else {
                $http.post('/api-offer-draft/save', {offer: $scope.offer})
                    .error(function (data, status, headers, config) {})
                    .success(function (data, status, headers, config) {
                        $rootScope.offerDraftId = data.id;
                    });
            }
            $scope.state.isSaveDraft = false;
        }
    },10*1000);

    $scope.$on('$destroy',function() {
        $interval.cancel(intervalSaveDraft);
    });





});