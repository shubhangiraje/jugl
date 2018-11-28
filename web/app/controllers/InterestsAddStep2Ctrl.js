app.controller('InterestsAddStep2Ctrl', function ($state, $scope, $stateParams, $rootScope, $http, modal, interestsAddStep2Data, type, gettextCatalog,interestType) {

    angular.extend($scope, interestsAddStep2Data);

    $scope.type=type;

    this.save = function(level1Id) {
        var params = {
            level1Id:level1Id,type:interestType
        };

        $http.post('/api-user-interests/save-level1-interest', params)
            .error(function (data, status, headers, config) {
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                if (data.result===true) {
                    modal.alert({message:gettextCatalog.getString('Du hast erfolgreich neue Interessen hinzugef&uuml;gt.')},function(){
                        $state.go('^.index');
                    });
                    return;
                }

            });
    };


    this.add = function(level1_id) {
        $state.go('^.add', {ids: level1_id});
    };

    this.saveInterest = function(level1_id, level2_id) {

        if($scope.type == 'addInterest') {
            $http.post('/api-user-interests/save-level2-interest', {level1Id:level1_id, level2Id:level2_id,type:interestType})
                .error(function (data, status, headers, config) {
                    modal.httpError(data, status, headers, config);
                })
                .success(function (data, status, headers, config) {
                    if (data.result===true) {
                        modal.alert({message:gettextCatalog.getString('Du hast erfolgreich neue Interessen hinzugef&uuml;gt.')},function(){
                            $state.go('^.index');
                        });
                        return;
                    }
                });
        } else {

            if($rootScope.offerDraftId) {
                $state.go('^.draft-update', {id: $rootScope.offerDraftId, ids: level2_id});
                $rootScope.offerDraftId = null;
                return;
            }

            if($rootScope.searchRequestDraftId) {
                $state.go('^.draft-update', {id: $rootScope.searchRequestDraftId, ids: level2_id});
                $rootScope.searchRequestDraftId = null;
                return;
            }

            $state.go('^.add',{ids:level2_id});

        }

    };


});