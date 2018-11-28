app.controller('UserInterestsUpdateCtrl', function ($state, $scope, $stateParams, $http, modal, userInterestsUpdateData,gettextCatalog) {

    angular.extend($scope, userInterestsUpdateData);


    this.deleteFromLevel3Interest = function(interestLevel1Id, interestLevel2Id, interestLevel3Id) {

        modal.confirmation({message:gettextCatalog.getString('You really want to delete this user from interest?')},function(result){
            if (!result) return;

            var params = {
                interestLevel1Id: interestLevel1Id,
                interestLevel2Id: interestLevel2Id,
                interestLevel3Id: interestLevel3Id
            };

            $http.post('/api-user-interests/delete-level3-interest', params)
                .error(function (data, status, headers, config) {
                    modal.httpError(data, status, headers, config);
                })
                .success(function (data, status, headers, config) {
                    angular.extend($scope,data);
                });
        });
    };


    this.deleteFromLevel2Interest = function(interestLevel1Id, interestLevel2Id) {

        modal.confirmation({message:gettextCatalog.getString('You really want to delete this user from interest?')},function(result){
            if (!result) return;

            var params = {
                interestLevel1Id: interestLevel1Id,
                interestLevel2Id: interestLevel2Id
            };

            $http.post('/api-user-interests/delete-level2-interest', params)
                .error(function (data, status, headers, config) {
                    modal.httpError(data, status, headers, config);
                })
                .success(function (data, status, headers, config) {
                    angular.extend($scope,data);
                });
        });

    };


});