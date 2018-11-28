app.controller('SearchRequestDraftCtrl', function ($scope, searchRequestDraftData, modal, jsonDataPromise, jsonPostDataPromise, gettextCatalog) {

    var self = this;

    angular.extend($scope, searchRequestDraftData);

    $scope.state={
        pageNum: 1
    };

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getResults(function(data) {
            scrollLoadCallback(data.results.hasMore);
        });
    };

    this.getResults = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-search-request-draft/list',{pageNum:$scope.state.pageNum})
            .then(function (data) {
                $scope.state.loading=false;

                if ($scope.state.pageNum > 1) {
                    data.results.items = $scope.results.items.concat(data.results.items);
                }

                angular.extend($scope, data);

                if ($scope.state.modifiedWhileLoading) {
                    $scope.state.modifiedWhileLoading=false;
                    self.getResults(callback);
                }
                callback(data);
            });
    };


    this.delete=function(id) {

        modal.confirmation({message:gettextCatalog.getString('Möchtest Du Deine Suchanzeige endgültig löschen?')},function(result) {
            if (!result) {
                return;
            }

            jsonPostDataPromise('/api-search-request-draft/delete',{id:id})
                .then(function (data) {
                    if (data.result===true) {
                        for (var idx in $scope.results.items) {
                            if ($scope.results.items[idx].id == id) {
                                $scope.results.items.splice(idx, 1);
                            }
                        }
                    }
                });
        });
    };



});