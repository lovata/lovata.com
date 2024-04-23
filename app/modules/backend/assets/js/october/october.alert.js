/*
 * Alerts
 *
 * Displays alert and confirmation dialogs
 *
 * JavaScript API:
 * oc.alert()
 * oc.confirm()
 *
 * Dependencies:
 * - Translations (october.lang.js)
 */
(function($){
    if (window.oc === undefined) {
        window.oc = {};
    }

    oc.alert = function(message, title) {
        var messageTitle = typeof title !== 'string' ?  $.oc.lang.get('alert.error') : title;

        if (!$.oc.vueComponentHelpers || !$.oc.vueComponentHelpers.modalUtils) {
            alert(message);
            return;
        }

        $.oc.vueComponentHelpers.modalUtils.showAlert(messageTitle, message, {
            buttonText: $.oc.lang.get('alert.dismiss')
        });
    };

    oc.confirm = function(message, callback, title) {
        oc.confirmPromise(message, title).then(function () {
            callback(true);
        }, function () {
            callback(false);
        });
    }

    oc.confirmPromise = function(message, title) {
        var messageTitle = typeof title !== 'string'
            ? $.oc.lang.get('alert.confirm')
            : title;

        return $.oc.vueComponentHelpers.modalUtils.showConfirm(messageTitle, message, {});
    }

    // @deprecated
    if ($.oc === undefined) {
        $.oc = {};
    }
    $.oc.alert = oc.alert;
    $.oc.confirm = oc.confirm;
    $.oc.confirmPromise = oc.confirmPromise;
})(jQuery);

/*
 * Implement alerts with AJAX framework
 */

$(window).on('ajaxErrorMessage', function(event, message) {
    if (!message) {
        return;
    }

    oc.alert(message);

    // Prevent the default alert() message
    event.preventDefault();
})

$(window).on('ajaxConfirmMessage', function(event, message, promise) {
    if (!message) {
        return;
    }

    oc.confirm(message, function(isConfirm) {
        isConfirm ? promise.resolve() : promise.reject();
    });

    // Prevent the default confirm() message
    event.preventDefault();
    return true;
});
