export default class FoundationBase
{
    static proxyCounter = 0;

    constructor(config) {
        this.config = this.getConfig(config);
        this.proxiedMethods = {};
    }

    // Public
    dispose() {
        for (const key in this.proxiedMethods) {
            this.proxiedMethods[key] = null;
        }

        for (const propertyName of Object.getOwnPropertyNames(this)) {
            this[propertyName] = null;
        }
    }

    // Private
    proxy(method) {
        if (method.ocProxyId === undefined) {
            FoundationBase.proxyCounter++;
            method.ocProxyId = FoundationBase.proxyCounter;
        }

        if (this.proxiedMethods[method.ocProxyId] !== undefined) {
            return this.proxiedMethods[method.ocProxyId];
        }

        this.proxiedMethods[method.ocProxyId] = method.bind(this);

        return this.proxiedMethods[method.ocProxyId];
    }

    getConfig(config) {
        return {
            ...this.constructor.DEFAULTS,
            ...(typeof config === 'object' ? config : {})
        }
    }
}
