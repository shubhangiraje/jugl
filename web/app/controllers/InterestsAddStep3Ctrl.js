app.controller('InterestsAddStep3Ctrl', function ($q, $state, $scope, $rootScope, $http, modal, $stateParams, interestsAddStep3Data, type, gettextCatalog,interestType) {

    angular.extend($scope, interestsAddStep3Data);

    $scope.type=type;

    if (angular.isArray($scope.level3Interests)) {
        $scope.level3Interests = {};
    }

    //if($scope.interests.length === 0) {
    //    if($scope.type == 'addInterest') {
    //        save().then(function(){
    //            $state.go('interests.index');
    //        });
    //    } else {
    //        $state.go('^.add',{ids:$scope.level_interests.level2_id});
    //    }
    //
    //}


    function save() {
        var defer=$q.defer();

        var params = {
            level1Id:$scope.level_interests.level1_id,
            level2Id:$scope.level_interests.level2_id,
            level3Interests: $scope.level3Interests,
            type:interestType
        };

        $http.post('/api-user-interests/save-level2-interest', params)
            .error(function (data, status, headers, config) {
                modal.httpError(data, status, headers, config);
                defer.reject(true);
            })
            .success(function (data, status, headers, config) {
                if (data.result===true) {
                    modal.alert({message:gettextCatalog.getString('Du hast erfolgreich neue Interessen hinzugef&uuml;gt.')},function(){
                        defer.resolve(true);
                    });
                    return;
                }
            });

        return defer.promise;
    }

    this.saveInterestLevel2 = function() {
        save().then(function(){
            $state.go('^.index');
        });
    };

    this.saveAndCreateSearchRequest = function() {
        if(!$.isEmptyObject($scope.level3Interests)) {
            save().then(function () {
                var ids = [];
                for (var id in $scope.level3Interests) {
                    if ($scope.level3Interests[id] === true) {
                        ids.push(id);
                    }
                }

                if((ids.length) > 0) {
                    $state.go('searches.add', {ids: ids.join(',')});
                } else {
                    $state.go('searches.add',{ids:$scope.level_interests.level2_id});
                }
            });
        } else {
            $state.go('searches.add',{ids:$scope.level_interests.level2_id});
        }
    };

    if (type!='addInterest') {
        $scope.level3Interests = {};
    }

    this.saveSearchAndOffer = function() {
        var interstsIds = '';

        if(!$.isEmptyObject($scope.level3Interests)) {
            var ids = [];
            for (var id in $scope.level3Interests) {
                if ($scope.level3Interests[id] === true) {
                    ids.push(id);
                }
            }

            interstsIds = ids.length > 0 ? ids.join(',') : $scope.level_interests.level2_id;

        } else {
            interstsIds = $scope.level_interests.level2_id;
        }


        if($rootScope.offerDraftId) {
            $state.go('^.draft-update', {id: $rootScope.offerDraftId, ids: interstsIds});
            $rootScope.offerDraftId = null;
            return;
        }

        if($rootScope.searchRequestDraftId) {
            $state.go('^.draft-update', {id: $rootScope.searchRequestDraftId, ids: interstsIds});
            $rootScope.searchRequestDraftId = null;
            return;
        }

        $state.go('^.add', {ids: interstsIds});

    };

});