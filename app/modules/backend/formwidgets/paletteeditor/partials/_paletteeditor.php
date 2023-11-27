<?php
    $isCustomSelected = $presetValue == 'custom';
?>
<div
    id="<?= $this->formField->getId() ?>"
    class="field-paletteeditor"
    data-control="paletteeditor"
    data-color-mode-selector="#<?= $colorModeField->getId() ?>"
    <?= $this->formField->getAttributes() ?>>

    <!-- Passable fields -->
    <input type="hidden" name="PaletteEditor[color_mode]" value="<?= e($colorModeValue) ?>" data-palette-color-mode />

    <div class="paletteeditor-preset-selection">
        <?= $this->makePartial('preset_selection') ?>
    </div>

    <?php if (!$isCustomSelected): ?>
        <div data-custom-palette-button>
            <div class="field-horizontalrule my-4">
                <hr />
            </div>

            <div class="palette-show-custom">
                <a href="javascript:;">
                    <i class="octo-icon-magic-wand"></i>
                    <?= __("Create a Custom Palette") ?>
                </a>
            </div>
        </div>
    <?php endif ?>

    <div class="palette-custom-form pt-3" style="<?= $isCustomSelected ? '' : 'display: none' ?>" data-custom-palette>
        <div class="field-section mb-3">
            <h4><?= __("Custom Palette") ?></h4>
        </div>
        <?= $this->colorsFormWidget->render(['useContainer' => false]) ?>
    </div>

    <style data-palette-stylesheet></style>
</div>
<script>
    window.backendPaletteEditorFormWidgetPresetDefinitions = <?= json_encode($this->getPresetDefinitions()) ?>;
</script>
