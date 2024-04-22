<?php namespace System\Classes;

use Str;
use App;
use Request;

/**
 * RateLimiter prevents too many attempts at logging in
 */
class RateLimiter
{
    /**
     * @var string throttleKey
     */
    protected $throttleKey;

    /**
     * @var \Illuminate\Cache\RateLimiter limiter instance
     */
    protected $limiter;

    /**
     * @var \Illuminate\Http\Request request instance
     */
    protected $request;

    /**
     * __construct a new login rate limiter instance.
     */
    public function __construct(string $throttleKey)
    {
        $this->throttleKey = $throttleKey;
        $this->limiter = App::make(\Illuminate\Cache\RateLimiter::class);
        $this->request = Request::instance();
    }

    /**
     * attempts gets the number of attempts for the given key.
     */
    public function attempts()
    {
        return $this->limiter->attempts($this->throttleKey($this->request));
    }

    /**
     * tooManyAttempts determines if the user has too many failed login attempts.
     */
    public function tooManyAttempts($maxAttempts = 5)
    {
        return $this->limiter->tooManyAttempts($this->throttleKey($this->request), $maxAttempts);
    }

    /**
     * increment the login attempts for the user.
     */
    public function increment($decaySeconds = 60)
    {
        $this->limiter->hit($this->throttleKey($this->request), $decaySeconds);
    }

    /**
     * availableIn determines the number of seconds until logging in is available again.
     */
    public function availableIn()
    {
        return $this->limiter->availableIn($this->throttleKey($this->request));
    }

    /**
     * clear the login locks for the given user credentials.
     */
    public function clear()
    {
        $this->limiter->clear($this->throttleKey($this->request));
    }

    /**
     * throttleKey gets the throttle key for the given request.
     * @return string
     */
    protected function throttleKey()
    {
        return Str::transliterate(Str::lower($this->throttleKey)).'|'.$this->request->ip();
    }
}
