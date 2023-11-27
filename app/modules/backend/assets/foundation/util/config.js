function normalizeData(value) {
    if (value === 'true') {
        return true
    }

    if (value === 'false') {
        return false
    }

    if (value === Number(value).toString()) {
        return Number(value)
    }

    if (value === '' || value === 'null') {
        return null
    }

    if (typeof value !== 'string') {
        return value
    }

    try {
        return JSON.parse(decodeURIComponent(value));
    }
    catch {
        return value;
    }
}

function normalizeDataKey(key) {
    return key.replace(/[A-Z]/g, chr => `-${chr.toLowerCase()}`)
}

const Config = {
    setDataAttribute(element, key, value) {
        element.setAttribute(`data-${normalizeDataKey(key)}`, value);
    },

    removeDataAttribute(element, key) {
        element.removeAttribute(`data-${normalizeDataKey(key)}`);
    },

    getDataAttributes(element) {
        if (!element) {
            return {};
        }

        const attributes = {};
        const dataKeys = Object.keys(element.dataset);

        for (const key of dataKeys) {
            attributes[key] = normalizeData(element.dataset[key]);
        }

        return attributes;
    },

    getDataAttribute(element, key) {
        return normalizeData(element.getAttribute(`data-${normalizeDataKey(key)}`))
    }
}

export default Config
