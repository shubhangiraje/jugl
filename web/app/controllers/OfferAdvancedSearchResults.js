app.controller('OfferAdvancedSearchResults', function ($scope, modal, offerAdvancedSearchResultData, $timeout, jsonDataPromise, jsonPostDataPromise, $stateParams, $rootScope, $localStorage) {
    var self=this;

    angular.extend($scope, offerAdvancedSearchResultData);

    $scope.state={pageNum:1};

    this.addFavorite = function(id) {
        jsonPostDataPromise('/api-favorites/add',{id:id, type:'offer'})
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

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getResults(function(data) {
            scrollLoadCallback(data.results.hasMore);
        });
    };

    this.getResults = function(callback) {
        callback = callback || function() {};
        if ($scope.state.loading) {
            return;
        }
        $scope.state.loading=true;
        jsonDataPromise('/api-offer-advanced-search/search',{
            filter: $localStorage['offersAdvancedSearchFilter'],
            pageNum:$scope.state.pageNum
        })
            .then(function (data) {
                $scope.state.loading=false;

                if ($scope.state.pageNum > 1) {
                    data.results.items = $scope.results.items.concat(data.results.items);
                }

                angular.extend($scope, data);
                callback(data);
            });
    };
	
	 this.setAdvertising = function (id, user_bonus, click_interval, popup_interval) {
        var advertisements = {};
        advertisements.id = id;
        advertisements.user_bonus = user_bonus;
		advertisements.click_interval = click_interval;
		advertisements.popup_interval = popup_interval;
        jsonDataPromise('/api-advertising/set-advertising-user', { advertising_id: id, advertising_click_interval: click_interval })
            .then(function (data) {
                if (user_bonus > 0 && data.result === true) {
                    timeoutPromise = $timeout(function () {
                        var config = {
                            template: '/app-view/advertising-view-bonus-popup',
                            advertising: advertisements
                        };

                        modal.show(config);
                    }, 1000);
                }

            });
    };
});
