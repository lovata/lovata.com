<?php
    $options = $this->getPaletteOptions();
?>
<select name="<?= $field->getName() ?>" class="oc-hide" data-palette-preset-selection>
    <?php foreach ($options as $code => $label): ?>
        <option <?= $presetValue == $code ? 'selected="selected"' : '' ?> value="<?= $code ?>"><?= e($label) ?></option>
    <?php endforeach ?>
    <option <?= $presetValue == 'custom' ? 'selected="selected"' : '' ?> value="custom">— Custom —</option>
</select>

<div class="control-simplelist color-preset-selector is-selectable-box is-flush">
    <ul>
        <?php foreach ($this->getPaletteDefinitions() as $code => $info): ?>
            <li class="<?= $presetValue == $code ? 'active"' : '' ?>" data-color-preset="<?= e($code) ?>">
                <a href="javascript:;">
                    <div class="color-preset-box">
                        <div class="mode-image">
                            <?= $this->makePartial('preset_preview', [
                                'preset' => $code,
                                'vars' => $this->getPaletteStyleVarsFor($code, 'light')
                                + $this->getPaletteStyleVarsFor($code, 'dark')
                            ]) ?>
                        </div>
                        <h5 class="heading">
                            <?= e(__($info['name'] ?? '???')) ?>
                        </h5>
                    </div>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
</div>

<script>
    $(document).on('click', '[data-color-preset]', function() {
        backendBrandSettingSetColorPreset($(this).data('color-preset'));
    });

    $(document).on('change', '[data-palette-preset-selection]', function() {
        if ($(this).val() === 'custom') {
            $('[data-color-preset]').removeClass('active');
        }
    });

    function backendBrandSettingSetColorPreset(preset) {
        $('[data-color-preset]').removeClass('active');
        $('[data-color-preset="'+preset+'"]').addClass('active');

        var $element = document.querySelector('#<?= $field->getId() ?> [data-palette-preset-selection]');
        if ($element) {
            $element.value = preset;
            oc.Events.dispatch('change', { target: $element });
        }
    }
</script>
