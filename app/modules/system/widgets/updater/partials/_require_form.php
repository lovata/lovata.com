<?php if (!$this->fatalError): ?>

    <?= Form::open() ?>
        <div class="modal-header">
            <h4 class="modal-title"><?= __("Check Dependencies") ?></h4>
            <button type="button" class="btn-close" data-dismiss="popup"></button>
        </div>
        <div class="modal-body">
            <p class="form-text before-field">
                <?= __("There are missing dependencies needed for the system to run correctly.") ?>
            </p>
            <div class="control-simplelist with-checkboxes" data-control="simplelist">
                <ul>
                    <?php foreach ($deps as $idx => $dep): ?>
                        <li>

                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    id="<?= $this->getId('requireCheckbox-'.$idx) ?>"
                                    name="deps[]"
                                    value="<?= e($dep) ?>"
                                    checked="checked"
                                    type="checkbox" />
                                <label
                                    class="form-check-label"
                                    for="<?= $this->getId('requireCheckbox-'.$idx) ?>">
                                        <?= e($dep) ?>
                                </label>
                            </div>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
        <div class="modal-footer">
            <button
                type="button"
                class="btn btn-primary"
                data-dismiss="popup"
                data-control="popup"
                data-handler="<?= $this->getEventHandler('onInstallDependencies') ?>"
                data-keyboard="false">
                <?= __("Install Dependencies") ?>
            </button>
            <button
                type="button"
                class="btn btn-default"
                data-dismiss="popup">
                <?= e(trans('backend::lang.form.cancel')) ?>
            </button>
        </div>
    <?= Form::close() ?>

<?php else: ?>

    <div class="modal-body">
        <p class="flash-message static error"><?= e(__($this->fatalError)) ?></p>
    </div>
    <div class="modal-footer">
        <button
            type="button"
            class="btn btn-default"
            data-dismiss="popup">
            <?= e(trans('backend::lang.form.close')) ?>
        </button>
    </div>

<?php endif ?>
