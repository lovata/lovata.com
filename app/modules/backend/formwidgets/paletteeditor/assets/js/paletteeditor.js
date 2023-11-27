/*
 * PaletteEditor plugin
 *
 * Data attributes:
 * - data-control="colorpicker" - enables the plugin on an element
 * - data-data-locker="input#locker" - Input element to store and restore the chosen color
 *
 * JavaScript API:
 * PaletteEditorFormWidget.getOrCreateInstance(el, { dataLocker: 'input#locker' })
 */

'use strict';

class PaletteEditorFormWidget extends oc.FoundationPlugin
{
    constructor(element, config) {
        super(element, config);

        this.presetDefinitions = window.backendPaletteEditorFormWidgetPresetDefinitions;
        this.isUserEvent = true;

        this.$presetSelect = this.element.querySelector('[data-palette-preset-selection]');
        this.$previewStylesheet = this.element.querySelector('[data-palette-stylesheet]');
        this.$activeColorModeSelector = this.element.querySelector('[data-palette-color-mode]');
        this.$colorModeSelector = document.querySelector(this.config.colorModeSelector);

        oc.Events.on(document, 'change', this.config.colorModeSelector, this.proxy(this.onChangeColorMode));
        oc.Events.on(this.element, 'change', '[data-palette-preset-selection]', this.proxy(this.onChangePreset));
        oc.Events.on(this.element, 'change', '.field-colorpicker input.form-control', this.proxy(this.onChangeColorPicker));
        oc.Events.on(this.element, 'click', '.palette-show-custom', this.proxy(this.onClickCustomPalette));

        this.markDisposable();
    }

    dispose() {
        oc.Events.off(document, 'change', this.config.colorModeSelector, this.proxy(this.onChangeColorMode));
        oc.Events.off(this.element, 'change', '[data-palette-preset-selection]', this.proxy(this.onChangePreset));
        oc.Events.off(this.element, 'change', '.field-colorpicker input.form-control', this.proxy(this.onChangeColorPicker));
        oc.Events.off(this.element, 'click', '.palette-show-custom', this.proxy(this.onClickCustomPalette));

        super.dispose();
    }

    static get DATANAME() {
        return 'ocPaletteEditor';
    }

    static get DEFAULTS() {
        return {
            colorModeSelector: '#selector',
        }
    }

    onChangePreset(event) {
        if (!this.isUserEvent) {
            return;
        }

        this.backendBrandSettingSetColorPreset(event.target.value);
    }

    onChangeColorMode(event) {
        if (!this.isUserEvent) {
            return;
        }

        setTimeout(() => {
            this.$activeColorModeSelector.value = this.getCurrentColorMode();
            this.backendBrandSettingSetColorPreset(this.$presetSelect.value);
        }, 0);
    }

    onChangeColorPicker(event) {
        if (!this.isUserEvent) {
            return;
        }

        if (this.$presetSelect.value != 'custom') {
            this.$presetSelect.value = 'custom';
            this.dispatchNoReplicate('change', { target: this.$presetSelect });
        }

        this.previewStylesheet();
    }

    onClickCustomPalette(event) {
        event.target.closest('[data-custom-palette-button]').remove();
        this.element.querySelector('[data-custom-palette]').style.display = 'block';
    }

    backendBrandSettingSetColorPreset(mode) {
        var colorMode = this.getCurrentColorMode();
        if (!this.presetDefinitions[mode] || !this.presetDefinitions[mode][colorMode]) {
            return;
        }

        var palette = this.presetDefinitions[mode][colorMode];

        // The change event is used to interact with the color picker UI events
        for (const varName in palette) {
            var $colorPicker = this.element.querySelector('[name="PaletteEditor[palette]['+varName+']"]');
            $colorPicker.value = palette[varName];
            this.dispatchNoReplicate('change', { target: $colorPicker });
        }

        this.previewStylesheet();
    }

    getCurrentColorMode() {
        return document.body.getAttribute('data-bs-theme') || document.documentElement.getAttribute('data-bs-theme') || 'light';
    }

    previewStylesheet() {
        var styles = '';
        var self = this;

        this.element.querySelectorAll('.field-colorpicker input.form-control').forEach((el) => {
            styles += self.convertInputNameToCssVar(el.name, 'oc')+':'+el.value+';';
            styles += self.convertInputNameToCssVar(el.name, 'bs')+':'+el.value+';';
        });

        this.$previewStylesheet.textContent = 'body > * { '+styles+' }';
    }

    convertInputNameToCssVar(name, prefix) {
        return '--'+prefix+'-' + name
            .replace(/\[(\w+)]/g, '.$1')
            .split('.').at(-1)
            .replace(/_/g, '-');
    }

    dispatchNoReplicate(eventName, detail) {
        this.isUserEvent = false;
        oc.Events.dispatch(eventName, detail);
        this.isUserEvent = true;
    }
}

addEventListener('render', function() {
    document.querySelectorAll('[data-control=paletteeditor]').forEach(function(el) {
        PaletteEditorFormWidget.getOrCreateInstance(el);
    });
});
