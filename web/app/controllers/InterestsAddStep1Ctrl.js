app.controller('InterestsAddStep1Ctrl', function ($state, $scope, $rootScope, interestsAddStep1Data, $http, type, modal, gettextCatalog,interestType) {

    angular.extend($scope, interestsAddStep1Data);
    $scope.type=type;


    this.saveInterest = function(id) {

        if($scope.type == 'addInterest') {

            $http.post('/api-user-interests/save-level1-interest', {level1Id:id,type:interestType})
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
                $state.go('^.draft-update', {id: $rootScope.offerDraftId, ids: id});
                $rootScope.offerDraftId = null;
                return;
            }

            if($rootScope.searchRequestDraftId) {
                $state.go('^.draft-update', {id: $rootScope.searchRequestDraftId, ids: id});
                $rootScope.searchRequestDraftId = null;
                return;
            }

            $state.go('^.add', {ids: id});

        }
    };

});