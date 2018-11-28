app.controller('SearchRequestSearchCtrl', function ($scope,$rootScope, searchRequestSearchData, $timeout, jsonDataPromise, jsonPostDataPromise, $stateParams) {

    var self=this;

    angular.extend($scope, searchRequestSearchData);

    $scope.filter={
        level1_interest_id:0,
        level2_interest_id:0,
        level3_interest_id:0,
        params:{},
        sort:'create_dt'
    };

    $scope.emptyOption={id:0,title:''};
    $scope.state={pageNum:1};
    $scope.countryIds = $scope.currentCountry[0].id;

    this.paramFilter=function(param) {
        // if themefinder is not specified, exit
        if (!$scope.filter.level3_interest_id) return false;

        if (param.interest_id==$scope.filter.level1_interest_id ||
            param.interest_id==$scope.filter.level2_interest_id ||
            param.interest_id==$scope.filter.level3_interest_id) {
            if (!$scope.filter.params[param.id]) {
                $scope.filter.params[param.id]=0;
            }
            return true;
        }

        return false;
    };

    this.filterInterestComparator=function(actual, expected) {
        return actual === expected;
    };

    $scope.$watch('filter',function(newValue,oldValue) {
        if (newValue != oldValue) {
            $scope.state.pageNum = 1;
            self.getResults();
        }
    },true);

	$scope.$watch('currentCountry',function(newValue,oldValue) {
		if (newValue != oldValue) {

            $scope.countryIds = '';
            if($scope.currentCountry.length > 0) {
                $scope.countryIds = [];
                angular.forEach($scope.currentCountry,function(item,index){
                    $scope.countryIds.push(item.id);
                });
                $scope.countryIds = $scope.countryIds.join(',');
            }

            $scope.state.pageNum = 1;
            $scope.results.items = [];
            self.getResults();
        }
    },true);

    $scope.$watch('filter.level1_interest_id',function(newValue,oldValue) {
        if (newValue != oldValue) {
            $scope.filter.level2_interest_id = 0;
        }
    });

    $scope.$watch('filter.level2_interest_id',function(newValue,oldValue) {
        if (newValue != oldValue) {
            $scope.filter.level3_interest_id = 0;
        }
    });

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

        jsonDataPromise('/api-search-request-search/search',{
            filter:$scope.filter,
            pageNum:$scope.state.pageNum,
            user_id:$stateParams.id,
            country_ids:$scope.countryIds
        }).then(function (data) {
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


    this.addFavorite = function(id) {

        jsonPostDataPromise('/api-favorites/add',{id:id, type:'search_request'})
            .then(function (data) {
                if (data.result===true) {
                    for (var idx in $scope.results.items) {
                        if ($scope.results.items[idx].id == id) {
                            $scope.results.items[idx].favorite = true;
                        }
                    }
                }
            });

    };

	/*jsonDataPromise('/api-country/get-country-list-searches')
        .then(function (res) {
        angular.extend($scope.countryArraySearches, res);
        $scope.country_ids = [{id:$scope.currentCountry.country_id}];
	});*/

	$scope.labels=$rootScope.status.labels;


});