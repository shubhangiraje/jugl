app.filter('encodeURI', function() {

    return function(refLink) {
        return encodeURIComponent(refLink);
    };

});