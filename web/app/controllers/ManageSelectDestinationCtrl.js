app.controller('ManageSelectDestinationCtrl', function ($scope,manageSelectDestinationData,jsonPostDataPromise,userStatus,$filter,modal,$state,$timeout) {

    angular.extend($scope, manageSelectDestinationData);

    var self = this;
    $scope.state={pageNum:1};
    $scope.filter = {
        name: ''
    };

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getUsersLog(function(data) {
            scrollLoadCallback(data.log.hasMore);
        });
    };

    $scope.$watch('filter',function(newValue,oldValue) {
        if (newValue!=oldValue) {
            $scope.log.items=[];
            $scope.state.pageNum = 1;
            self.getUsersLog();
        }
    },true);


    this.getUsersLog = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonPostDataPromise('/api-manage-network/hierarchy-list',{
            move_id: $state.params.move_id,
            id: $state.params.id,
            filter: $scope.filter,
            pageNum: $scope.state.pageNum
        })
            .then(function (data) {
                $scope.state.loading=false;

                if ($scope.state.pageNum > 1) {
                    data.log.items = $scope.log.items.concat(data.log.items);
                }

                angular.extend($scope, data);

                if ($scope.state.modifiedWhileLoading) {
                    $scope.state.modifiedWhileLoading=false;
                    self.getUsersLog(callback);
                }
                callback(data);
            });
    };



    this.select = function(user) {
        var config={
            template:'/app-view/manage-network-confirm-popup',
            users: {
                'src_id': $scope.user.id,
                'src_name': $filter('userName')($scope.user),
                'dst_id': user.id,
                'dst_name': $filter('userName')(user)
            }
        };

        modal.show(config);
    };





});