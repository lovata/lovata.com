export class Controller
{
    constructor() {
        this.started = false;
    }

    start() {
        if (!this.started) {
            this.started = true;
        }

        this.bindAutoDisposal();
    }

    stop() {
        if (this.started) {
            this.started = false;
        }
    }

    // Automatically dispose controls in an element before the element contents is replaced.
    // The ajaxBeforeReplace event is triggered by the AJAX framework.
    bindAutoDisposal() {
        addEventListener('page:before-render', function(ev) {
            Controller.disposeControls(ev.target);
        });

        addEventListener('ajax:before-replace', function(ev) {
            Controller.disposeControls(ev.target);
        });
    }

    // Destroys all disposable controls in a container.
    // The disposable controls should watch the dispose-control event.
    static disposeControls(container) {
        if (container === document) {
            container = document.documentElement;
        }

        container.querySelectorAll('[data-disposable]').forEach(function(control) {
            oc.Events.dispatch('october:dispose', { target: control, bubbles: false });
        });

        if (container.hasAttribute('data-disposable')) {
            oc.Events.dispatch('october:dispose', { target: container, bubbles: false });
        }
    }
}
