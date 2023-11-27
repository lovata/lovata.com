<?php namespace System\Console;

use System\Classes\PluginManager;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * PluginTest command
 */
class PluginTest extends Command
{
    /**
     * @var string signature for the console command.
     */
    protected $signature = 'plugin:test
        {namespace : App or Plugin Namespace. <info>(eg: Acme.Blog)</info>}
        {--without-tty : Disable output to TTY}
        {--pest : Run the tests using Pest}';

    /**
     * @var string description for the console command.
     */
    protected $description = 'Run unit tests for an October CMS plugin';

    /**
     * handle executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->isValidNamespace()) {
            $name = $this->pluginCode();
            return $this->output->error("Unable to find plugin [{$name}]");
        }

        $options = collect($_SERVER['argv'])
            ->slice(3)
            ->diff(['--browse', '--without-tty'])
            ->values()
            ->all();

        $command = array_merge($this->binary(), $this->phpunitArguments($options));

        $process = (new Process($command, null, $this->env()))->setTimeout(null);

        try {
            $process->setTty(!$this->option('without-tty'));
        }
        catch (RuntimeException $e) {
            $this->output->writeln('Warning: '.$e->getMessage());
        }

        try {
            return $process->run(function ($type, $line) {
                $this->output->write($line);
            });
        }
        catch (ProcessSignaledException $e) {
            if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                throw $e;
            }
        }
    }

    /**
     * binary of PHP to execute.
     * @return array
     */
    protected function binary()
    {
        $binaryPath = 'vendor/phpunit/phpunit/phpunit';

        if ($this->option('pest')) {
            $binaryPath = 'vendor/pestphp/pest/bin/pest';
        }

        if ('phpdbg' === PHP_SAPI) {
            return [PHP_BINARY, '-qrr', $binaryPath];
        }

        return [PHP_BINARY, $binaryPath];
    }

    /**
     * phpunitArguments gets the array of arguments for running PHPUnit.
     *
     * @param  array  $options
     * @return array
     */
    protected function phpunitArguments($options)
    {
        $options = array_values(array_filter($options, function ($option) {
            return !Str::startsWith($option, ['--env=', '--pest']);
        }));

        $lookupMethod = $this->isAppNamespace() ? 'app_path' : [$this, 'pluginPath'];

        if (!file_exists($file = $lookupMethod('phpunit.xml'))) {
            $file = $lookupMethod('phpunit.xml.dist');
        }

        return array_merge(['-c', $file], $options);
    }

    /**
     * env gets the PHP binary environment variables.
     *
     * @return array|null
     */
    protected function env()
    {
        return null;
    }

    /**
     * isAppNamespace
     */
    protected function isAppNamespace(): bool
    {
        return mb_strtolower(trim($this->argument('namespace'))) === 'app';
    }

    /**
     * isValidNamespace
     * @return bool
     */
    protected function isValidNamespace(): bool
    {
        if ($this->isAppNamespace()) {
            return true;
        }

        return PluginManager::instance()->hasPlugin($this->pluginCode());
    }

    /**
     * pluginPath
     */
    protected function pluginPath($path = '')
    {
        return PluginManager::instance()->getPluginPath($this->pluginCode()) . '/' . $path;
    }

    /**
     * pluginCode
     */
    protected function pluginCode()
    {
        return PluginManager::instance()->normalizeIdentifier($this->argument('namespace'));
    }
}
