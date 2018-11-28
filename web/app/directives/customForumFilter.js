app.filter('customForumFilter', function(){
    return function (items,selectboxValue) {
        var filtered = [];
        for (var i = 0; i < items.length; i++) {
            var item = items[i];

            if (item.user.country_id==selectboxValue && item.user.country_id!==null && selectboxValue!==null) {
                filtered.push(item);
            } else if (selectboxValue===null||selectboxValue===undefined){
                filtered.push(item);
            }
        }
        return filtered;
     };
});