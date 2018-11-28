app.filter('userCountryId', function() {
    return function(userObject) {
        if (!userObject)
            return '';
        return userObject.country_id;
    };
});