<h2><?= e(Backend\Models\BrandSetting::get('app_tagline')) ?></h2>

<p><?= e(Backend\Models\BrandSetting::get('login_prompt')) ?></p>

<?= Form::open() ?>
    <input type="hidden" name="postback" value="1" />

    <div class="form-elements" role="form">
        <!-- Login -->
        <div class="form-group">
            <label class="form-label" for="login-input">
                <?= __('Username') ?>
            </label>

            <input
                type="text"
                name="login"
                value="<?= e(post('login')) ?>"
                class="form-control"
                id="login-input"
                autocomplete="off"
                maxlength="255" />
        </div>

        <!-- Password -->
        <div class="form-group">
            <label class="form-label" for="password-input">
                <?= __('Password') ?>
            </label>

            <input
                type="password"
                name="password"
                value=""
                id="password-input"
                class="form-control"
                autocomplete="off"
                maxlength="255" />
        </div>

        <?php if (Config::get('backend.force_remember') === null): ?>
            <!-- Remember Checkbox -->
            <div class="form-group">
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="remember"
                        name="remember"
                        <?= post('remember') ? 'checked' : '' ?> />
                    <label class="form-check-label" for="remember">
                        <?= __('Stay logged in') ?>
                    </label>
                </div>
            </div>
        <?php endif ?>

        <!-- Submit Login -->
        <button type="submit" class="btn btn-primary login-button">
            <?= __('Login') ?>
        </button>

        <p class="pull-right forgot-password">
            <!-- Forgot password? -->
            <a href="<?= Backend::url('backend/auth/restore') ?>">
                <?= __('Forgot password?') ?>
            </a>
        </p>

    </div>
<?= Form::close() ?>

<?= $this->fireViewEvent('backend.auth.extendSigninView') ?>
