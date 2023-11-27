import Data from './util/data';
import Config from './util/config';
import FoundationBase from './foundation.base';

export default class FoundationPlugin extends FoundationBase
{
    constructor(element, config) {
        super();
        this.element = getElement(element);
        this.config = this.getConfig(config);
        Data.set(this.element, this.constructor.DATANAME, this);
    }

    // Public
    dispose() {
        Data.remove(this.element, this.constructor.DATANAME);
        oc.Events.off(this.element, 'october:dispose', this.proxy(this.dispose));
        super.dispose();
    }

    // Static
    static getInstance(element) {
        return Data.get(getElement(element), this.DATANAME);
    }

    static getOrCreateInstance(element, config = {}) {
        return this.getInstance(element) || new this(element, typeof config === 'object' ? config : null);
    }

    // Private
    markDisposable() {
        this.element.setAttribute('data-disposable', '')
        oc.Events.on(this.element, 'october:dispose', this.proxy(this.dispose));
    }

    onDispose(callback) {
        oc.Events.on(this.element, 'october:dispose', this.proxy(callback));
    }

    getConfig(config) {
        return {
            ...this.constructor.DEFAULTS,
            ...Config.getDataAttributes(this.element),
            ...(typeof config === 'object' ? config : {})
        }
    }
}

function getElement(element) {
    if (typeof object === 'string' && object.length > 0) {
        return document.querySelector(object);
    }

    return element;
}
