/*
 * PageLookup page
 */
'use strict';

oc.Modules.register('cms.widget.pagelookup', function() {
    class PageLookupWidget extends oc.FoundationBase
    {
        constructor(config) {
            super(config);

            if (this.config.alias === null) {
                throw new Error('Page Lookup popup option "alias" is not set.');
            }

            this.$rootElement = $('<div />');
            this.$rootElement.one('hide.oc.popup', this.proxy(this.onPopupHidden));
            this.$rootElement.one('shown.oc.popup', this.proxy(this.onPopupShown));

            this.show();
        }

        dispose() {
            super.dispose();
        }

        static processLink(url) {
            return processResolverLink(url);
        }

        static processLinks(content) {
            return processResolverLinks(content);
        }

        static popup(config) {
            return new this(config);
        }

        static get DEFAULTS() {
            return {
                alias: null,
                value: '',
                onInsert: null,
                onShown: null,
                singleMode: false,
                includeTitle: false
            }
        }

        onRefreshReference = function(event) {
            var $select = $(event.target),
                val = $select.val();

            if (!val) {
                return;
            }

            // type::reference ID
            var parts = val.split('::', 2);
            var extraData = {
                type: parts[0],
                reference: parts[1]
            };

            oc.request(this.$popupElement.get(0), this.config.alias + '::onRefreshReference', {
                data: {
                    PageLookupItem: extraData
                }
            });

            $select.empty().trigger('change.select2');
        }

        onInsertReference = function(event) {
            const { data, context } = event.detail;

            if (context.handler !== this.config.alias + '::onInsertReference') {
                return;
            }

            if (!data) {
                return;
            }

            if (this.config.onInsert) {
                this.config.onInsert.call(this, data);
            }
        }

        onPopupShown = function(event, element, popup) {
            this.$popupElement = popup;
            oc.Events.on(this.$popupElement.get(0), 'change', 'select[name=referenceSearch]', this.proxy(this.onRefreshReference));
            oc.Events.on(this.$popupElement.get(0), 'ajax:done', 'form', this.proxy(this.onInsertReference));

            if (this.config.onShown) {
                this.config.onShown.call(this);
            }
        }

        onPopupHidden = function(event, element, popup) {
            oc.Events.off(this.$popupElement.get(0), 'change', 'select[name=referenceSearch]', this.proxy(this.onRefreshReference));
            oc.Events.off(this.$popupElement.get(0), 'ajax:done', 'form', this.proxy(this.onInsertReference));

            this.dispose();
        }

        show() {
            var data = {
                pagelookup_flag: true,
                pagelookup_title: this.config.includeTitle,
                pagelookup_single: this.config.singleMode,
                value: this.config.value
            };

            this.$rootElement.popup({
                extraData: data,
                size: 'large',
                handler: this.config.alias + '::onLoadPopup'
            });
        }

        hide() {
            if (this.$rootElement) {
                this.$rootElement.trigger('close.oc.popup');
            }
        }
    }

    oc.pageLookup = PageLookupWidget;

    if ($.FE) {
        $.FE.RegisterInsertPageLinkCommand();
    }

    //
    // Resolver logic
    //

    function processResolverLink(url) {
        return window.location.origin + window.location.pathname +'?_lookup_link='+encodeURIComponent(url);
    }

    function processResolverLinks(content) {
        return processResolverLinksForHtml(
            processResolverLinksForMarkdown(content)
        );
    }

    // [a link](october://static-pages@link/some/reference?cmsPage=foobar)
    function processResolverLinksForMarkdown(markdownText) {
        const elements = markdownText.match(/\[.*?\]\(october:\/\/.*?\)/g);
        if (elements != null && elements.length > 0) {
            for (const el of elements) {
                const txt = el.match(/\[(.*?)\]/);
                const url = el.match(/\((october:\/\/.*?)\)/);
                if (txt !== null && url !== null) {
                    const processedUrl = processResolverLink(url[1]);
                    markdownText = markdownText.replace(el,'['+txt[1]+']('+processedUrl+')');
                }
            }
        }
        return markdownText;
    }

    // <a href="october://static-pages@link/some/reference?cmsPage=foobar"></a>
    function processResolverLinksForHtml(htmlText) {
        const elements = htmlText.match(/(<a.*?october:\/\/.*?[^a]>)+?/g);
        if (elements != null && elements.length > 0) {
            for (const el of elements) {
                const url = el.match(/href="(.*?)"/);
                if (url !== null) {
                    const processedUrl = processResolverLink(url[1]);
                    htmlText = htmlText.replace(el, el.replace(url[1], processedUrl));
                }
            }
        }
        return htmlText;
    }
});
