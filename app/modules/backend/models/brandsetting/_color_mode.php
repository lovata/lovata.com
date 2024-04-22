<?php
    use Backend\Models\BrandSetting;
?>
<div class="control-simplelist color-mode-selector is-selectable-box is-flush">
    <ul>
        <li class="<?= $field->value === BrandSetting::COLOR_LIGHT ? 'active' : ''?>" data-color-mode="<?= BrandSetting::COLOR_LIGHT ?>">
            <a href="javascript:;">
                <div class="color-mode-box">
                    <h5 class="heading d-flex">
                        <span class="flex-grow-1"><?= __("Light Mode") ?></span>
                        <i class="icon icon-sun"></i>
                    </h5>
                </div>
            </a>
        </li>
        <li class="<?= $field->value === BrandSetting::COLOR_DARK ? 'active' : ''?>" data-color-mode="<?= BrandSetting::COLOR_DARK ?>">
            <a href="javascript:;">
                <div class="color-mode-box">
                    <h5 class="heading d-flex">
                        <span class="flex-grow-1"><?= __("Dark Mode") ?></span>
                        <i class="icon icon-moon"></i>
                    </h5>
                </div>
            </a>
        </li>
        <li class="<?= $field->value === BrandSetting::COLOR_AUTO ? 'active' : ''?>" data-color-mode="<?= BrandSetting::COLOR_AUTO ?>">
            <a href="javascript:;">
                <div class="color-mode-box">
                    <h5 class="heading d-flex">
                        <span class="flex-grow-1"><?= __("OS Default") ?></span>
                        <i class="icon icon-adjust"></i>
                    </h5>
                </div>
            </a>
        </li>
    </ul>
</div>

<input
    type="hidden"
    name="<?= $field->getName() ?>"
    value="<?= e($field->value) ?>"
    id="<?= $field->getId() ?>"
/>

<script>
    $(document).on('click', '[data-color-mode]', function() {
        backendBrandSettingSetColorMode($(this).data('color-mode'));

        // Reset the color switcher preference cookie
        Cookies.remove('admin_color_mode_user');

        // Always force a reload of the next page
        oc.AjaxTurbo && oc.AjaxTurbo.controller.disable();
    });

    function backendBrandSettingSetColorMode(mode) {
        $('[data-color-mode]').removeClass('active');
        $('[data-color-mode="'+mode+'"]').addClass('active');

        var $element = document.querySelector('#<?= $field->getId() ?>');
        if ($element) {
            $element.value = mode;
            oc.Events.dispatch('change', { target: $element });
        }

        if (mode === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.body.setAttribute('data-bs-theme', 'dark');
        }
        else {
            document.body.setAttribute('data-bs-theme', mode);
        }
    }
</script>
