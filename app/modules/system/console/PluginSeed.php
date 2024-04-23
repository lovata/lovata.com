<?php namespace System\Console;

use Str;
use File;
use System\Classes\PluginManager;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

/**
 * PluginSeed
 */
class PluginSeed extends Command
{
    use \Illuminate\Console\ConfirmableTrait;

    /**
     * @var string signature for the command
     */
    protected $signature = 'plugin:seed
        {namespace : Plugin Namespace. <info>(eg: Acme.Blog)</info>}
        {class : Class name of the root seeder. <info>(default: Acme\Blog\Updates\Seeders\DatabaseSeeder)</info>}
        {--f|force : Force the operation to run.}
        {--c|class : Class name of the root seeder. <info>(default: Acme\Blog\Updates\Seeders\DatabaseSeeder)</info>}';

    /**
     * @var string description of the console command
     */
    protected $description = 'Seed the database with records for a plugin.';

    /**
     * handle the console command.
     * @return int
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return 1;
        }

        $this->components->info('Seeding database.');

        Model::unguarded(function () {
            if ($this->isAppNamespace()) {
                $this->getAppSeeder()->__invoke();
            }
            else {
                $this->getSeeder()->__invoke();
            }
        });

        return 0;
    }

    /**
     * getSeeder instance from the container.
     * @return \Illuminate\Database\Seeder
     */
    protected function getSeeder()
    {
        $class = $this->input->getArgument('class') ?? $this->input->getOption('class');

        if (!$class) {
            $class = 'DatabaseSeeder';
        }

        $manager = PluginManager::instance();
        $name = $manager->normalizeIdentifier($this->argument('namespace'));

        $file = str_replace('\\_', '/', $class) . '.php';
        $path = $manager->getPluginPath($name) . '/updates/seeders/' . $file;

        if (is_file($path)) {
            require_once $path;
            $namespace = $manager->getPluginNamespace($name);
            $class = "{$namespace}\\Updates\\Seeders\\{$class}";
        }

        if (!class_exists($class) && !is_file($path)) {
            $nicePath = File::nicePath($path);
            $this->output->error("Seed file at [{$nicePath}] not found.");
            exit(1);
        }

        return $this->laravel->make($class)
            ->setContainer($this->laravel)
            ->setCommand($this);
    }

    /**
     * getAppSeeder instance directed at the app context.
     * @return \Illuminate\Database\Seeder
     */
    protected function getAppSeeder()
    {
        $class = $this->input->getArgument('class') ?? $this->input->getOption('class');

        if (!$class) {
            $class = 'DatabaseSeeder';
        }

        if (!str_contains($class, '\\')) {
            $class = 'App\\Database\\Seeders\\'.$class;
        }

        if ($class === 'App\\Database\\Seeders\\DatabaseSeeder' && !class_exists($class)) {
            $class = 'DatabaseSeeder';
        }

        return $this->laravel->make($class)
            ->setContainer($this->laravel)
            ->setCommand($this);
    }

    /**
     * isAppNamespace
     */
    protected function isAppNamespace(): bool
    {
        return mb_strtolower(trim($this->argument('namespace'))) === 'app';
    }
}
