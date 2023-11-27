/*
 * Inspector helper functions.
 *
 */
+function ($) { "use strict";

    // NAMESPACES
    // ============================

    if ($.oc === undefined)
        $.oc = {}

    if ($.oc.inspector === undefined)
        $.oc.inspector = {}

    $.oc.inspector.helpers = {}

    $.oc.inspector.helpers.generateElementUniqueId = function(element) {
        if (element.hasAttribute('data-inspector-id')) {
            return element.getAttribute('data-inspector-id');
        }

        var id = $.oc.inspector.helpers.generateUniqueId();
        element.setAttribute('data-inspector-id', id);

        return id;
    }

    $.oc.inspector.helpers.generateUniqueId = function() {
        return "inspectorid-" + Math.floor(Math.random() * new Date().getTime());
    }

    $.oc.inspector.helpers.getEventHandler = function(element, handler) {
        if (element instanceof jQuery){
            element = element.get(0);
        }

        var handlerAlias = element.dataset.inspectorHandlerAlias;
        if (handlerAlias) {
            return handlerAlias + '::' + handler;
        }

        return handler;
    }

}(window.jQuery)