app.filter('objectToList', function() {
    return function(obj) {
        return $.map(obj, function(value, index) {
            return [value];
        });
    };
});
