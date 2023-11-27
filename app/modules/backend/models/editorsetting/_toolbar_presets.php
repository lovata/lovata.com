<div>
    <?php foreach ($this->formWidget->model->getEditorToolbarPresets() as $name => $preset): ?>
        <button
            class="btn btn-default btn-sm"
            data-preset="<?= e($preset) ?>"
            onclick="onClickSetToolbarPresets(this)"
            type="button">
            <?= e(trans('backend::lang.editor.toolbar_buttons_presets.' . $name)) ?>
        </button>
    <?php endforeach ?>
</div>
<script>
    function onClickSetToolbarPresets(el) {
        document.querySelector('#Form-field-EditorSetting-html_toolbar_buttons').value = el.dataset.preset;
    }
</script>
