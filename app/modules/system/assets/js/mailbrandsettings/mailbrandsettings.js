/*
 * MailBrandSettings page
 */
'use strict';

class MailBrandSettings extends oc.FoundationPlugin
{
    constructor(element, config) {
        super(element, config);

        this.createPreviewContainer();

        // Change color picker
        $(document).on('change', '.field-colorpicker', this.proxy(this.onChangeColorPicker));

        // Auto adjust height
        $(document).on('render', this.proxy(this.adjustPreviewHeight));
        $(window).on('resize', this.proxy(this.adjustPreviewHeight));

        setTimeout(function() {
            $(window).trigger('resize');
        }, 250);

        this.markDisposable();
    }

    dispose() {
        // Change color picker
        $(document).off('change', '.field-colorpicker', this.proxy(this.onChangeColorPicker));

        // Auto adjust height
        $(document).off('render', this.proxy(this.adjustPreviewHeight));
        $(window).off('resize', this.proxy(this.adjustPreviewHeight));

        super.dispose();
    }

    static get DATANAME() {
        return 'ocMailPreview';
    }

    static get DEFAULTS() {
        return {
            previewTemplateId: ''
        }
    }

    createPreviewContainer() {
        var previewTemplate = document.querySelector('#' + this.config.previewTemplateId);
        if (!previewTemplate) {
            console.error('Missing preview template html');
            return;
        }

        var content = previewTemplate.innerHTML;
        var previewIframe = this.previewIframe = document.createElement('iframe');

        this.updatePreviewContent(content);

        previewIframe.style.width = '100%';
        previewIframe.setAttribute('frameborder', 0);
        previewIframe.setAttribute('id', this.element.id);
        previewIframe.onload = this.adjustPreviewHeight.bind(this);

        this.element.appendChild(previewIframe);

        return previewIframe;
    }

    onChangeColorPicker() {
        var self = this;
        $('#brandSettingsForm').request('onUpdateSampleMessage').done(function(data) {
            self.updatePreviewContent(data.previewHtml);
        });
    }

    updatePreviewContent(content) {
        'srcdoc' in this.previewIframe
            ? this.previewIframe.srcdoc = content
            : this.previewIframe.src = 'data:text/html;charset=UTF-8,' + content;
    }

    adjustPreviewHeight() {
        // Fudge factor for retina displays
        var offset = 1;
        this.previewIframe.style.height = (this.previewIframe.contentWindow.document.getElementsByTagName('body')[0].scrollHeight) +
            offset +
            'px';
    }
}

addEventListener('render', function() {
    document.querySelectorAll('[data-control=mailpreview]').forEach(function(el) {
        MailBrandSettings.getOrCreateInstance(el);
    });
});
