app.controller('FavoritesCtrl', function ($scope, favoritesData, jsonDataPromise, jsonPostDataPromise, modal, gettextCatalog) {

    var self=this;

    angular.extend($scope, favoritesData);

    $scope.filter={
        type:''
    };

    $scope.state={pageNum:1};

    $scope.$watch('filter',function(newValue,oldValue) {
        if (newValue != oldValue) {
            $scope.state.pageNum = 1;
            self.getResults();
        }
    },true);

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getResults(function(data) {
            scrollLoadCallback(data.results.hasMore);
        });
    };


    this.delete=function(id, type) {

        modal.confirmation({message:gettextCatalog.getString('Delete favorite?')},function(result) {
            if (!result) {
                return;
            }

            jsonPostDataPromise('/api-favorites/delete',{id:id, type:type})
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


    this.getResults = function(callback) {

        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-favorites/search',{filter:$scope.filter, pageNum:$scope.state.pageNum})
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

});