<div
    data-control="sensitive"
    data-sensitive-clean="true"
    data-reveal-handler="<?= $this->getEventHandler('onShowValue') ?>"
    <?php if ($hideOnTabChange): ?>data-hide-on-tab-change="true"<?php endif ?>
>
    <div class="loading-indicator-container size-form-field">
        <div class="input-group">
            <input
                type="password"
                name="<?= $this->getFieldName() ?>"
                id="<?= $this->getId() ?>"
                value="<?= $hasValue ? $hiddenPlaceholder : '' ?>"
                placeholder="<?= e(__($this->formField->placeholder)) ?>"
                class="form-control"
                autocomplete="off"
                data-sensitive-input
                <?= $this->previewMode ? 'disabled' : '' ?>
            />
            <?php if ($allowCopy): ?>
                <a
                    href="javascript:;"
                    class="input-group-addon btn btn-secondary"
                    data-sensitive-copy
                >
                    <i class="icon-copy"></i>
                </a>
            <?php endif ?>
            <a
                href="javascript:;"
                class="input-group-addon btn btn-secondary"
                data-sensitive-toggle
            >
                <i class="icon-eye" data-sensitive-icon></i>
            </a>
        </div>
        <div class="loading-indicator oc-hide" data-sensitive-loader>
            <span class="p-4"></span>
        </div>
    </div>
</div>
