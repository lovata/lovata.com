/*
 * Client side translations
 */
if (!window.oc) {
    window.oc = {};
}

if (!window.oc.langMessages) {
    window.oc.langMessages = {};
}

window.oc.lang = (function(lang, messages) {
    lang.load = function(locale) {
        if (messages[locale] === undefined) {
            messages[locale] = {};
        }

        lang.loadedMessages = messages[locale];
    }

    lang.set = function(name, value) {
        if (name.constructor === {}.constructor) {
            lang.loadedMessages = {
                ...name,
                ...lang.loadedMessages
            };
        }
        else {
            lang.loadedMessages[name] = value;
        }
    }

    lang.get = function(name, defaultValue) {
        if (!name) {
            return;
        }

        var result = lang.loadedMessages;

        if (!defaultValue) {
            defaultValue = name;
        }

        $.each(name.split('.'), function(index, value) {
            if (result[value] === undefined) {
                result = defaultValue;
                return false;
            }

            result = result[value];
        });

        return result;
    }

    if (lang.locale === undefined) {
        lang.locale = $('html').attr('lang') || 'en';
    }

    if (lang.loadedMessages === undefined) {
        lang.load(lang.locale);
    }

    return lang;
})(window.oc.lang || {}, window.oc.langMessages);

// Migrate jQuery
if ($.oc === undefined) {
    $.oc = {};
}

$.oc.lang = oc.lang;
$.oc.langMessages = oc.langMessages;
