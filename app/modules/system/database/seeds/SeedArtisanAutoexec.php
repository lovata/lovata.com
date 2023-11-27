<?php namespace System\Database\Seeds;

use File;
use Seeder;
use Artisan;

/**
 * SeedArtisanAutoexec
 */
class SeedArtisanAutoexec extends Seeder
{
    /**
     * run
     */
    public function run()
    {
        $this->line('');

        $seedFile = storage_path('cms/autoexec.json');
        if (!File::exists($seedFile)) {
            return;
        }

        $contents = json_decode(File::get($seedFile), true);
        if (!$contents || !is_array($contents)) {
            return;
        }

        try {
            $out = isset($this->command) ? $this->command->getOutput() : null;
            foreach ($contents as $artisanCmd) {
                Artisan::call($artisanCmd, [], $out);
            }
        }
        finally {
            File::delete($seedFile);
        }
    }
}
