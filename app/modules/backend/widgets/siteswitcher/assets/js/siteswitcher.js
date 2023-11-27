/*
 * Site Switcher logic
 */
'use strict';

oc.Modules.register('backend.widget.siteswitcher', function() {
    class SiteSwitcherWidget extends oc.FoundationPlugin
    {
        constructor(element, config) {
            super(element, config);

            oc.Events.on(this.element, 'click', 'a[data-siteswitcher-link]', this.proxy(this.onClickLink));
            this.markDisposable();
        }

        dispose() {
            oc.Events.off(this.element, 'click', 'a[data-siteswitcher-link]', this.proxy(this.onClickLink));
            super.dispose();
        }

        static get DATANAME() {
            return 'ocSiteSwitcherWidget';
        }

        static get DEFAULTS() {
            return {
            }
        }

        onClickLink(ev) {
            ev.preventDefault();
            var $anchor = ev.target.closest('a');

            if ($anchor.dataset.handler) {
                this.onClickHandler(ev);
            }
            else {
                oc.visit(this.makeAnchorLink($anchor));
            }
        }

        onClickHandler(ev) {
            ev.preventDefault();

            var self = this;
            var $anchor = ev.target.closest('a');
            oc.request($anchor, $anchor.dataset.handler).done(function(data) {
                oc.Events.dispatch('backend:hidemenus');

                if (data.confirm) {
                    $.oc.confirm(data.confirm, function() {
                        oc.visit(self.makeAnchorLink($anchor));
                    });
                }
                else {
                    oc.visit(self.makeAnchorLink($anchor));
                }
            });
        }

        makeAnchorLink($anchor) {
            return $anchor.href + window.location.hash;
        }
    }

    addEventListener('render', function() {
        document.querySelectorAll('[data-control=siteswitcher]').forEach(function(el) {
            SiteSwitcherWidget.getOrCreateInstance(el);
        });
    });
});
