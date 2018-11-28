app.filter('unshiftArray', [ function( $filter ) {
    return function( input, value ) {
        input.unshift(value);

        return input;
    };
}]);
