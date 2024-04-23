/*
 * PaletteEditor plugin
 *
 * Data attributes:
 * - data-control="colorpicker" - enables the plugin on an element
 * - data-data-locker="input#locker" - Input element to store and restore the chosen color
 *
 * Config:
 * - colorModeSelector: '#selector'
 */

'use strict';

oc.registerControl('paletteeditor', class extends oc.ControlBase {
    init() {
        this.presetDefinitions = window.backendPaletteEditorFormWidgetPresetDefinitions;
        this.isUserEvent = true;

        this.$presetSelect = this.element.querySelector('[data-palette-preset-selection]');
        this.$previewStylesheet = this.element.querySelector('[data-palette-stylesheet]');
        this.$activeColorModeSelector = this.element.querySelector('[data-palette-color-mode]');
        this.$colorModeSelector = document.querySelector(this.config.colorModeSelector || '#selector');
    }

    connect() {
        if (this.$colorModeSelector) {
            this.listen('change', this.$colorModeSelector, this.onChangeColorMode);
        }

        this.listen('change', '[data-palette-preset-selection]', this.onChangePreset);
        this.listen('change', '.field-colorpicker input.form-control', this.onChangeColorPicker);
        this.listen('click', '.palette-show-custom', this.onClickCustomPalette);
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
});
