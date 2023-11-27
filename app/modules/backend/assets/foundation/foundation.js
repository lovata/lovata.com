import { Controller } from './controller';
import FoundationBase from './foundation.base';
import FoundationPlugin from './foundation.plugin';

// Self starting instance
const instance = new Controller;

if (window.oc) {
    window.oc.Foundation = instance;
    window.oc.FoundationBase = FoundationBase;
    window.oc.FoundationPlugin = FoundationPlugin;
}

instance.start();

/*
 |--------------------------------------------------------------------------
 | Foundational Components
 |--------------------------------------------------------------------------
 |
 */

export { default as Select } from './controls/select/select';
