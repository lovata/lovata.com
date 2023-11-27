<?php
    $checkboxMode = !($this->mode === 'radio');

    switch ($this->mode) {
        case 'radio':
            $permissionValue = array_key_exists($permission->code, $permissionsData)
                ? $permissionsData[$permission->code]
                : 0;
            break;
        case 'switch':
            $isChecked = !((int) @$permissionsData[$permission->code] === -1);
            break;
        case 'checkbox':
        default:
            $isChecked = array_key_exists($permission->code, $permissionsData);
            break;
    }
?>
<li class="permission-item
    <?= $checkboxMode ? 'mode-checkbox' : 'mode-radio' ?>
    <?= $checkboxMode && !$isChecked ? 'disabled' : '' ?>
    <?= !$checkboxMode && $permissionValue === -1 ? 'disabled' : '' ?>
">
    <div class="item-content">
        <?php if ($this->mode === 'radio'): ?>
            <div class="item-value">
                <input
                    class="form-check-input"
                    name="<?= e($baseFieldName) ?>[<?= e($permission->code) ?>]"
                    value="1"
                    type="radio"
                    <?= $permissionValue == 1 ? 'checked="checked"' : '' ?>
                    data-radio-color="green"
                    title="<?= e(trans('backend::lang.user.allow')) ?>"
                />
                <input
                    class="form-check-input"
                    name="<?= e($baseFieldName) ?>[<?= e($permission->code) ?>]"
                    value="0"
                    <?= $permissionValue == 0 ? 'checked="checked"' : '' ?>
                    type="radio"
                    title="<?= e(trans('backend::lang.user.inherit')) ?>"
                />
                <input
                    class="form-check-input"
                    name="<?= e($baseFieldName) ?>[<?= e($permission->code) ?>]"
                    value="-1"
                    <?= $permissionValue == -1 ? 'checked="checked"' : '' ?>
                    type="radio"
                    data-radio-color="red"
                    title="<?= e(trans('backend::lang.user.deny')) ?>"
                />
            </div>
        <?php elseif ($this->mode === 'switch'): ?>
            <div class="item-value">
                <div class="form-check form-switch">
                    <input
                        type="hidden"
                        name="<?= e($baseFieldName) ?>[<?= e($permission->code) ?>]"
                        value="-1"
                    >
                    <input
                        class="form-check-input"
                        name="<?= e($baseFieldName) ?>[<?= e($permission->code) ?>]"
                        value="1"
                        type="checkbox"
                        <?= $isChecked ? 'checked="checked"' : '' ?>
                    >
                </div>
            </div>
        <?php else: ?>
            <div class="item-value">
                <input
                    type="hidden"
                    name="<?= e($baseFieldName) ?>[<?= e($permission->code) ?>]"
                    value="0"
                >
                <input
                    class="form-check-input"
                    name="<?= e($baseFieldName) ?>[<?= e($permission->code) ?>]"
                    value="1"
                    type="checkbox"
                    <?= $isChecked ? 'checked="checked"' : '' ?>
                    title="<?= e(trans('backend::lang.user.allow')) ?>"
                />
            </div>
        <?php endif ?>

        <label class="item-name">
            <?= e(__($permission->label)) ?>
            <?php if ($permission->comment): ?>
                <i class="icon-info-circle" title="<?= e(__($permission->comment)) ?>" data-bs-toggle="tooltip"></i>
            <?php endif ?>
        </label>
    </div>
    <?php if ($permission->children): ?>
        <ul class="child-items">
            <?php foreach ($permission->children as $childPrmission): ?>
                <?= $this->makePartial('permission_item', ['permission' => $childPrmission]) ?>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</li>
