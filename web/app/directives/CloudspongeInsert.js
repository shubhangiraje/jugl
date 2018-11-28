app.directive('cloudspongeInsert', function($timeout, $document) {
    return {
        restrict: 'A',
        link: function($scope, element, $attrs, ngModel) {
            var options = {};

            if ($attrs.cloudspongeInsert)
                options = $scope.$eval($attrs.cloudspongeInsert);

            if (options.textarea_id)
                options.afterSubmitContacts = function(contacts) {
                    var emails = [];
                    for (var i = 0; i < contacts.length; i++)
                        emails.push(contacts[i].selectedEmail());
                    $('#' + options.textarea_id).val(emails.join(', '));
                };

            function updateCloudspongeOptions() {
                cloudsponge.init(options);
            }

            if ($('#loadcloudsponge-api').size() === 0) {
                var cloudspongeScript = $document[0].createElement('script');
                cloudspongeScript.id = "loadcloudsponge-api";
                cloudspongeScript.type = "text/javascript";
                cloudspongeScript.onload = function() {
                    updateCloudspongeOptions();
                };
                cloudspongeScript.src = "https://api.cloudsponge.com/address_books.js";
                $document[0].body.appendChild(cloudspongeScript);
            } else {
                updateCloudspongeOptions();
            }
        }
    };
});
