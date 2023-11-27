<?php namespace Backend\Controllers;

use Flash;
use Backend;
use Request;
use Validator;
use BackendAuth;
use Backend\Classes\Controller;
use ApplicationException;
use ValidationException;

/**
 * AuthGates is authentication services for the logged in users, isolated from the Auth controller
 * for security reasons, which has public actions, but also gated from all other protected actions.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 *
 */
class AuthGates extends Controller
{
    /**
     * @var array vueComponents classes to implement
     */
    public $vueComponents = [];

    /**
     * __construct is the constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->layout = 'auth';
    }

    /**
     * expired shows a password reset screen when the password has expired
     */
    public function expired()
    {
    }

    /**
     * expired_onSubmit submits the expired password form
     */
    protected function expired_onSubmit()
    {
        $rules = [
            'current_password' => 'required|between:4,255',
            'password' => 'required|between:4,255|confirmed|different:current_password',
            'password_confirmation' => 'required_with:password|between:4,255'
        ];

        $validation = Validator::make(post(), $rules, [], [
            'current_password' => __("Current Password"),
            'password' => __("New Password"),
            'password_confirmation' => __("Confirm Password"),
        ]);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        $user = $this->user;

        if (!$user || !$user->checkPassword(post('current_password'))) {
            throw new ApplicationException(__("Current password does not match. Please try again!"));
        }

        // Validate password against policy
        $user->validatePasswordPolicy(post('password'));

        // Reset the user password and clear any code used to reset the password
        $user->password = post('password');
        $user->password_changed_at = $user->freshTimestamp();
        $user->is_password_expired = false;
        $user->reset_password_code = null;
        $user->forceSave();

        // Clear throttles
        BackendAuth::clearThrottleForUserId($user->id);

        Flash::success(__("Password has been reset. You may now sign in."));

        return Backend::redirect('backend/auth/signin');
    }
}
