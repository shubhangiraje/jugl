app.controller('OfferAdvancedSearch', function ($scope, offerAdvancedSearchData, $timeout, jsonDataPromise, jsonPostDataPromise, $stateParams, $rootScope, $localStorage, $state) {
    var self=this;

    $scope.data = {
        filter: $localStorage['offersAdvancedSearchFilter'] || {advanced:{}},
        interests: {}
    };

    angular.extend($scope.data, offerAdvancedSearchData.results);
    $scope.data.interestsChecks={};
    for(var idx in $scope.data.interests) {
        var id=$scope.data.interests[idx].interest_id;

        if (!$scope.data.filter.excludeInterests || $scope.data.filter.excludeInterests.indexOf(id)<0) {
            $scope.data.interestsChecks[id]=true;
        }
    }

    $scope.$watch('data.filter',function(newVal,oldVal) {
        $localStorage['offersAdvancedSearchFilter']=newVal;
    },true);

    $scope.$watch('data.interestsChecks',function(newVal,oldVal) {
        if (newVal==oldVal) return;

        $scope.data.filter.excludeInterests=[];
        for(var idx in $scope.data.interests) {
            var id=$scope.data.interests[idx].interest_id;
            if (!$scope.data.interestsChecks[id]) {
                $scope.data.filter.excludeInterests.push(id);
            }
        }
    },true);

    this.search = function() {
        if ($scope.data.filter.advancedEnabled && $scope.data.filter.advanced.distance) {
            $state.go('offers.advancedSearchResults');
        } else {
            $state.go('offers.advancedSearchResults');
        }
    };

});
