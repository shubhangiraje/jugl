yii.confirm = function (message, okCallback) {
    bootbox.confirm(message, function(result) {
        if (result) {
            okCallback();
        }
    });

    return false;
};


yii.pjaxConfirm=function(message,el,event) {
    var obj=$(el);

    console.log('here');
    if (obj.data('pjaxConfirm')) {
        console.log('passed');
        return;
    }

    bootbox.confirm(message, function(result) {
        if (result) {
            obj.data('pjaxConfirm',true);
            obj.click();
        }
    });

    event.preventDefault();
    event.stopPropagation();
};



$(document).ready(function() {
    $(".fancybox").fancybox({
        loop: false
    });
});
