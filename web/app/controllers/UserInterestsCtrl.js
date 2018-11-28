app.controller('UserInterestsCtrl', function ($state, $scope, $http, modal, $stateParams, userInterestsData, gettextCatalog) {

    angular.extend($scope, userInterestsData);

    this.deleteFromInterest = function(interestId) {
        modal.confirmation({message:gettextCatalog.getString('You really want to delete this user from interest?')},function(result){
            if (!result) return;

            var params = {
                interestId:interestId
            };

            $http.post('/api-user-interests/delete-interest', params)
                .error(function (data, status, headers, config) {
                    modal.httpError(data, status, headers, config);
                })
                .success(function (data, status, headers, config) {
                    angular.extend($scope,data);
                });
        });
    };


});