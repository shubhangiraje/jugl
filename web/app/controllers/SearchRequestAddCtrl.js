app.controller('SearchRequestAddCtrl', function ($scope, searchRequestAddData, Uploader, $http, modal, $state, gettextCatalog, $rootScope, $interval) {

    angular.extend($scope, searchRequestAddData);

    $scope.state = {
        isSaveDraft: false
    };

    if($state.params.ids) {
        angular.extend($scope.searchRequest,$rootScope.oldSearchRequestAddData);
        $rootScope.oldSearchRequestAddData = null;
        $scope.state.isSaveDraft = true;
    } else {
        $rootScope.oldSearchRequestAddData = {};
        $rootScope.searchRequestDraftId = null;
    }

    if($state.params.id) {
        $rootScope.searchRequestDraftId = $scope.searchRequest.draft_id;
    }

    this.addInterests = function() {
        $rootScope.oldSearchRequestAddData = angular.copy($scope.searchRequest);
        delete $rootScope.oldSearchRequestAddData.searchRequestInterests;
        delete $rootScope.oldSearchRequestAddData.searchRequestParamValues;
        delete $rootScope.oldSearchRequestAddData.$allErrors;
        delete $rootScope.oldSearchRequestAddData.$errors;
        $state.go('searches.addStep1');
    };

    function splitParams() {
        var half=Math.ceil($scope.searchRequest.searchRequestParamValues.length/2);
        $scope.params1=$scope.searchRequest.searchRequestParamValues.slice(0,half);
        $scope.params2=$scope.searchRequest.searchRequestParamValues.slice(half);
    }

    splitParams();

    $scope.uploader=Uploader(['imageBig']);

    $scope.fileUploadOptions={
        onSuccess: function(response,status,headers) {
            $scope.searchRequest.files.push(response);
        }
    };

    this.deleteFile=function(id) {
        for(var i in $scope.searchRequest.files) {
            if ($scope.searchRequest.files[i].id==id) {
                $scope.searchRequest.files.splice(i,1);
                break;
            }
        }

        if($scope.uploader.queue.length !== 0) {
            $scope.uploader.queue.length = $scope.uploader.queue.length-1;
        }

    };

    this.save = function () {

        var params = {
            searchRequest: $scope.searchRequest
        };

        if ($rootScope.searchRequestDraftId) {
            params.draftId = $rootScope.searchRequestDraftId;
        }

        $scope.state.isSaveDraft = false;
        $scope.searchRequest.saving = true;

        $http.post('/api-search-request/save', params)
            .error(function (data, status, headers, config) {
                $scope.searchRequest.saving = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.searchRequest.saving = false;

                if (data.result===true) {
                    $rootScope.searchRequestDraftId = null;
                    $interval.cancel(intervalSaveDraft);

                    modal.alert({message:
                        data.willBeValidated ?
                            gettextCatalog.getString('Vielen Dank. Ihr Suchauftrag wird schnellstmöglich geprüft. Mit freundlichen Grüßen, Ihr Jugl-Team.')
                            :
                            gettextCatalog.getString('Du hast Deine Suchanzeige erfolgreich erstellt. Du findest Deine Anzeige unter "Suchauftrag -> Was wird mir angeboten"')
                    },function(){
                        $state.go('searches.myList');
                    });
                    return;
                }

                if (angular.isArray(data.searchRequest.$allErrors) && data.searchRequest.$allErrors.length > 0) {
                    $scope.searchRequest = data.searchRequest;
                    $scope.searchRequest.saving = false;
                    return;
                }
            });
    };


    if($scope.searchRequest.searchRequestInterests[0].level2Interest.search_request_bonus) {
        $scope.searchRequest.view_bonus_interest = $scope.searchRequest.searchRequestInterests[0].level2Interest.search_request_bonus;
    } else {
        $scope.searchRequest.view_bonus_interest = $scope.searchRequest.searchRequestInterests[0].level1Interest.search_request_bonus;
    }

    if(!$scope.searchRequest.view_bonus_interest) {
        $scope.searchRequest.view_bonus_interest = 1;
    }


    $scope.$watch('searchRequest',function(newValue, oldValue) {
        if (newValue!=oldValue) {
            if(!$scope.searchRequest.saving) {
                $scope.state.isSaveDraft = true;
            }
        }
    }, true);


    var intervalSaveDraft=$interval(function(){
        if($scope.state.isSaveDraft) {
            if($rootScope.searchRequestDraftId) {
                $http.post('/api-search-request-draft/update', {
                    id: $rootScope.searchRequestDraftId,
                    searchRequest: $scope.searchRequest
                })
                    .error(function (data, status, headers, config) {})
                    .success(function (data, status, headers, config) {
                        $rootScope.searchRequestDraftId = data.id;
                    });
            } else {
                $http.post('/api-search-request-draft/save', {searchRequest: $scope.searchRequest})
                    .error(function (data, status, headers, config) {})
                    .success(function (data, status, headers, config) {
                        $rootScope.searchRequestDraftId = data.id;
                    });
            }
            $scope.state.isSaveDraft = false;
        }
    },10*1000);

    $scope.$on('$destroy',function() {
        $interval.cancel(intervalSaveDraft);
    });

});
