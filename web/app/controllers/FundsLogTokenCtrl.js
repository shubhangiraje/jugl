app.controller('FundsLogTokenCtrl', function ($scope, fundsLogTokenData, jsonDataPromise,$rootScope) {

    angular.extend($scope, fundsLogTokenData);

    var self = this;

    $scope.state={
        pageNum:1,
        sort: '-dt'
    };

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getFundsLog(function(data) {
            scrollLoadCallback(data.log.hasMore);
        });
    };

    this.resetFilter = function() {
        this.filters = {
            month: 0,
            year: '',
            status: 'all'
        };
    };

    this.resetFilter();
    this.filters.year=$scope.currentYear;

    $scope.$watch('fundsLogTokenCtrl.filters',function(new_value, old_value) {
        if (new_value != old_value) {
            $scope.state.pageNum = 1;
            self.getFundsLog();
        }
    }, true);

    this.resetFundsLog = function() {
        this.resetFilter();
    };

    this.getFundsLog = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-funds-log-token/log',{
            year: self.filters.year,
            month: self.filters.month,
            status: self.filters.status,
            pageNum: $scope.state.pageNum,
            sort: $scope.state.sort
        })
            .then(function (data) {
                $scope.state.loading=false;

                if ($scope.state.pageNum > 1) {
                    data.log.items = $scope.log.items.concat(data.log.items);
                }

                angular.extend($scope, data);

                if ($scope.state.modifiedWhileLoading) {
                    $scope.state.modifiedWhileLoading=false;
                    self.getFundsLog(callback);
                }
                callback(data);
            });
    };


    this.setSort=function (field) {
        if ($scope.state.sort==field) {
            $scope.state.sort='-'+field;
            return;
        }
        if ($scope.state.sort=='-'+field) {
            $scope.state.sort=field;
            return;
        }
        $scope.state.sort=field;
    };


    $scope.$watch('state.sort',function(newValue,oldValue) {
        if (newValue!=oldValue) {
            self.getFundsLog();
        }
    },true);



});
