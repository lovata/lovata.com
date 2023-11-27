<h2><?= __("Password Reset") ?></h2>

<?= Form::ajax('onSubmit') ?>
    <p><?= __("Your password has expired, please create a new one for security reasons.") ?></p>

    <div class="form-elements" role="form">

        <!-- Current Password -->
        <div class="form-group">
            <label class="form-label" for="currentPasswordInput">
                <?= __("Current Password") ?>
            </label>

            <input
                type="password"
                name="current_password"
                id="currentPasswordInput"
                value="<?= e(post('current_password')) ?>"
                class="form-control password"
                autocomplete="off"
                maxlength="255" />
        </div>

        <!-- Password -->
        <div class="form-group">
            <label class="form-label" for="passwordInput">
                <?= __("New Password") ?>
            </label>

            <input
                type="password"
                name="password"
                id="passwordInput"
                value="<?= e(post('password')) ?>"
                class="form-control password"
                autocomplete="off"
                maxlength="255" />
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label class="form-label" for="passwordConfirmationInput">
                <?= __("Confirm Password") ?>
            </label>

            <input
                type="password"
                name="password_confirmation"
                id="passwordConfirmationInput"
                value="<?= e(post('password_confirmation')) ?>"
                class="form-control password"
                autocomplete="off"
                maxlength="255" />
        </div>

        <!-- Submit Login -->
        <button type="submit" class="btn btn-primary">
            <?= __("Reset") ?>
        </button>

        <p class="pull-right forgot-password">
            <a href="<?= Backend::url('backend/auth/signout') ?>" class="text-muted">
                <?= e(trans('backend::lang.account.sign_out')) ?>
            </a>
        </p>
    </div>
<?= Form::close() ?>
