<h2><?= __('Password Restore') ?></h2>

<?= Form::open() ?>
    <input type="hidden" name="postback" value="1" />

    <p><?= __('Please enter your login to restore the password.') ?></p>

    <div class="form-elements" role="form">

        <!-- Username -->
        <div class="form-group">
            <label class="form-label" for="login-input">
                <?= __('Username') ?>
            </label>

            <input
                type="text"
                name="login"
                id="login-input"
                value="<?= e(post('login')) ?>"
                class="form-control"
                placeholder=""
                autocomplete="off"
                maxlength="255" />
        </div>

        <button type="submit" class="btn btn-primary restore-button">
            <?= __('Restore') ?>
        </button>

        <p class="pull-right forgot-password">
            <a href="<?= Backend::url('backend/auth') ?>" class="text-muted">
                <?= e(trans('backend::lang.form.cancel')) ?>
            </a>
        </p>
    </div>
<?= Form::close() ?>

<?= $this->fireViewEvent('backend.auth.extendRestoreView') ?>
