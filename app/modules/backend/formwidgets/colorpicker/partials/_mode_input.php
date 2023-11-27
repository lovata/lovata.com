<div class="input-group">
    <ul class="picker-options">
        <li
            class="custom-color <?= $isCustomColor == $value ? 'active' : null ?>"
            data-hex-color="<?= $isCustomColor ? e($value) : '#ffffff' ?>"
            data-custom-color>
            <span
                class="<?php if (!$value): ?>is-empty<?php endif ?> <?php if ($readOnly || $disabled): ?>disabled<?php endif ?>"
                style="background: <?= $isCustomColor ? e($value) : '#ffffff' ?>"></span>
        </li>
    </ul>

    <input
        type="text"
        class="form-control"
        id="<?= $this->getId('input') ?>"
        name="<?= $name ?>"
        value="<?= e($value) ?>" />
</div>
