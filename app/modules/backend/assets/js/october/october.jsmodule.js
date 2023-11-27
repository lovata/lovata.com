+(function($) {
    'use strict';

    function OctoberModuleRegistry() {
        this.moduleMap = new Map();

        this.register = function(namespace, registrationFn) {
            if (this.moduleMap.has(namespace)) {
                console.info('Module namespace is already registered: ' + namespace);
                return;
            }

            this.moduleMap.set(namespace, registrationFn());
        };

        this.import = function(namespace) {
            if (!this.exists(namespace)) {
                throw new Error('Module namespace is not registered: ' + namespace);
            }

            return this.moduleMap.get(namespace);
        };

        this.exists = function(namespace) {
            return this.moduleMap.has(namespace);
        };
    }

    if (!window.oc) {
        window.oc = {};
    }

    window.oc.Modules = new OctoberModuleRegistry();

    // oc.Module and $.oc.module is @deprecated -sg
    window.oc.Module = $.oc.module = window.oc.Modules;
})($);
