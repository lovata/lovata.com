/*
 * Site Switcher logic
 */
'use strict';

oc.Modules.register('backend.widget.siteswitcher', function() {
    oc.registerControl('siteswitcher', class extends oc.ControlBase {
        connect() {
            this.listen('click', 'a[data-siteswitcher-link]', this.onClickLink);
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

            var $anchor = ev.target.closest('a');
            oc.request($anchor, $anchor.dataset.handler).done((data) => {
                oc.Events.dispatch('backend:hidemenus');
                if (data.confirm) {
                    oc.confirm(data.confirm, (isConfirm) => {
                        if (isConfirm) {
                            oc.visit(this.makeAnchorLink($anchor));
                        }
                    });
                }
                else {
                    oc.visit(this.makeAnchorLink($anchor));
                }
            });
        }

        makeAnchorLink($anchor) {
            return $anchor.href + window.location.hash;
        }
    });
});
