/*
 * SnippetLookupWidget
 */
'use strict';

oc.registerControl('snippetlookup', class extends oc.ControlBase
{
    init() {
        this.$popupElement = null;
        this.insertCallback = null;
    }

    connect() {
        this.listen('click', '[data-snippet]', this.onClickSnippet)
        this.dispatch('ready');
    }

    disconnect() {
        this.$popupElement = null;
        this.insertCallback = null;
    }

    hide() {
        $(this.element).trigger('close.oc.popup');
    }

    onClickSnippet(ev) {
        var $item = ev.target.closest('li');
        if (this.insertCallback) {
            this.insertCallback.bind(this)($item.dataset);
        }
    }

    setContext(insertCallback) {
        this.insertCallback = insertCallback;
    }
});

