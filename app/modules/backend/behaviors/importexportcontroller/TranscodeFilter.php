<?php namespace Backend\Behaviors\ImportExportController;

use php_user_filter;

// phpcs:ignoreFile
stream_filter_register(TranscodeFilter::FILTER_NAME . "*", TranscodeFilter::class);

/**
 * TranscodeFilter converts CSV source files from one encoding to another.
 */
class TranscodeFilter extends php_user_filter
{
    const FILTER_NAME = 'october.csv.transcode.';

    /**
     * @var string encodingFrom
     */
    protected $encodingFrom = 'auto';

    /**
     * @var string encodingTo
     */
    protected $encodingTo;

    /**
     * filter
     */
    public function filter($in, $out, &$consumed, $closing): int
    {
        while ($resource = stream_bucket_make_writeable($in)) {
            $resource->data = @mb_convert_encoding(
                $resource->data,
                $this->encodingTo,
                $this->encodingFrom
            );

            $consumed += $resource->datalen;

            stream_bucket_append($out, $resource);
        }

        return PSFS_PASS_ON;
    }

    /**
     * onCreate
     */
    public function onCreate(): bool
    {
        if (strpos($this->filtername, self::FILTER_NAME) !== 0) {
            return false;
        }

        $params = substr($this->filtername, strlen(self::FILTER_NAME));
        if (!preg_match('/^([-\w]+)(:([-\w]+))?$/', $params, $matches)) {
            return false;
        }

        if (isset($matches[1])) {
            $this->encodingFrom = $matches[1];
        }

        $this->encodingTo = mb_internal_encoding();
        if (isset($matches[3])) {
            $this->encodingTo = $matches[3];
        }

        $this->params['locale'] = setlocale(LC_CTYPE, '0');
        if (stripos($this->params['locale'], 'UTF-8') === false) {
            setlocale(LC_CTYPE, 'en_US.UTF-8');
        }

        return true;
    }

    /**
     * onClose
     */
    public function onClose(): void
    {
        setlocale(LC_CTYPE, $this->params['locale']);
    }
}
