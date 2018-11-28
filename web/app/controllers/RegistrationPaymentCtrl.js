app.controller('RegistrationPaymentCtrl', function (isUpgrade,userStatus,$state,$scope,registrationPaymentData,jsonPostDataPromise,modal,gettextCatalog,$anchorScroll,$timeout,$pixel) {

    angular.extend($scope, registrationPaymentData);
    $scope.isUpgrade=isUpgrade;

    $scope.VIP={packet:1};

    this.selectVIP = function() {
        $scope.packet='VIP';
        if (!$scope.registeredByCode) {
            $timeout(function() {
                $anchorScroll('paymentMethod');
            });
        } else {
            this.saveVIP();
        }

    };

    this.selectVIPPlus = function() {
        $scope.packet='VIP_PLUS';
        $timeout(function() {
            $anchorScroll('paymentMethod');
        });
    };

    this.saveSTD = function () {
        modal.confirmation({
                message: gettextCatalog.getString('Möchtest Du wirklich das Standard-Paket wählen? Dadurch verzichtest Du auf viele Vorteile des Premium-Pakets wie z.B. höhere Provisionen und geringere Abgaben. Bitte beachte, das eine nachträgliche Änderung des gewählten Pakets möglich ist, jedoch nicht mehr zu diesem Preis.'),
                buttons: [
                    {
                        caption: gettextCatalog.getString('Premium-Paket wählen'),
                        class: 'packet ok',
                        onClick: function() {
                            modal.hide();
                            $scope.packet='VIP';
                            $timeout(function() {
                                $anchorScroll('paymentMethod');
                            });
                        }
                    },
                    {
                        caption: gettextCatalog.getString('Standard-Paket wählen'),
                        class: 'cancel',
                        onClick: function() {
                            modal.hide();
                            jsonPostDataPromise('/api-registration-payment/save-std').then(function(data) {
                                if(data.result===true){
                                    userStatus.update();

                                    $pixel.purchase({
                                        content_name: 'STANDARD',
                                        value: '0',
                                        currency: 'EUR'
                                    });

                                    $timeout(function(){
                                        $state.go('dashboard');
                                    });
                                } 
                            });
                        }
                    }

                ]
            },function(result) {
        });

    };

    this.saveVIP = function () {
        if ($scope.packet==='VIP') {
            $scope.VIP.saving = true;
            jsonPostDataPromise('/api-registration-payment/save-vip', {VIP: $scope.VIP}).then(
                function(data) {
                    $scope.VIP.saving = false;

                    $pixel.purchase({
                        content_name: 'VIP',
                        value: $scope.VIPPrice,
                        currency: 'EUR'
                    });

                    if ($scope.registeredByCode) {
                        $state.go('welcome');
                    } else {
                        $state.go('.data', {requestId: data.VIP.id});
                    }
                },
                function() {
                    $scope.VIP.saving = false;
                }
            );
        }

        if ($scope.packet==='VIP_PLUS') {
            $scope.VIP.saving = true;
            jsonPostDataPromise('/api-registration-payment/save-vip-plus', {VIP: $scope.VIP}).then(
                function(data) {
                    $scope.VIP.saving = false;

                    $pixel.purchase({
                        content_name: 'VIP_PLUS',
                        value: $scope.VIPPlusPrice,
                        currency: 'EUR'
                    });

                    $state.go('.data', {requestId: data.VIP.id});
                },
                function() {
                    $scope.VIP.saving = false;
                }
            );
        }
    };
});
