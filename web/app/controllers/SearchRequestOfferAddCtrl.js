app.controller('SearchRequestOfferAddCtrl', function ($scope, searchRequestOfferAddData, Uploader, $http, modal, $state, gettextCatalog) {

    angular.extend($scope, searchRequestOfferAddData);

    $scope.uploader=Uploader(['imageBig']);
    $scope.detailsUploader=Uploader(['imageBig']);

    $scope.fileUploadOptions={
        onSuccess: function(response,status,headers) {
            $scope.searchRequestOffer.files.push(response);
        }
    };

    $scope.detailsFileUploadOptions={
        onSuccess: function(response,status,headers) {
            $scope.searchRequestOffer.details_files.push(response);
        }
    };

    this.deleteFile=function(id) {
        for(var i in $scope.searchRequestOffer.files) {
            if ($scope.searchRequestOffer.files[i].id==id) {
                $scope.searchRequestOffer.files.splice(i,1);
                break;
            }
        }
    };

    this.detailsDeleteFile=function(id) {
        for(var i in $scope.searchRequestOffer.details_files) {
            if ($scope.searchRequestOffer.details_files[i].id==id) {
                $scope.searchRequestOffer.details_files.splice(i,1);
                break;
            }
        }
    };

    this.save = function () {
        $scope.searchRequestOffer.saving = true;
        $http.post('/api-search-request-offer/save', {searchRequestOffer: $scope.searchRequestOffer})
            .error(function (data, status, headers, config) {
                $scope.searchRequestOffer.saving = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.searchRequestOffer.saving = false;

                if (data.result===true) {
                    modal.alert({message:gettextCatalog.getString('Du hast ein Angebot auf diese Suchanzeige erfolgreich abgegeben!')},function(){
                        $state.go('searches.details',{id:$scope.searchRequestOffer.search_request_id});
                    });
                    return;
                }

                if (angular.isArray(data.searchRequestOffer.$allErrors) && data.searchRequestOffer.$allErrors.length > 0) {
                    $scope.searchRequestOffer = data.searchRequestOffer;
                    $scope.searchRequestOffer.saving = false;
                    return;
                }
            });
    };

});
