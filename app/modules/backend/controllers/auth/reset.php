<h2><?= __("Password Reset") ?></h2>

<?= Form::open() ?>
    <input type="hidden" name="postback" value="1" />
    <input type="hidden" name="id" value="<?= e($id) ?>" />
    <input type="hidden" name="code" value="<?= e($code) ?>" />

    <p><?= __('Please enter a new password.') ?></p>

    <div class="form-elements" role="form">

        <!-- Password -->
        <div class="form-group">
            <label class="form-label" for="password-input">
                <?= __('Password') ?>
            </label>

            <input
                type="password"
                name="password"
                id="password-input"
                value=""
                class="form-control password"
                autocomplete="off"
                maxlength="255" />
        </div>

        <!-- Submit Login -->
        <button type="submit" class="btn btn-primary">
            <?= __('Reset') ?>
        </button>

        <p class="pull-right forgot-password">
            <a href="<?= Backend::url('backend/auth') ?>" class="text-muted">
                <?= e(trans('backend::lang.form.cancel')) ?>
            </a>
        </p>
    </div>
<?= Form::close() ?>
