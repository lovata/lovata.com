<?php
declare(strict_types=1);

namespace Lovata\ViteJsintegration\Classes;

use Cms\Classes\Theme;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Innocenzi\Vite\EntrypointsFinder\EntrypointsFinder;
use October\Rain\Support\Facades\Str;

final class OctoberEntrypointsFinder implements EntrypointsFinder
{
    public function find(string|array $paths, string|array $ignore): Collection
    {
        return collect($paths)
            ->flatMap(function (string $fileOrDirectory) {
//                $fileOrDirectory = rtrim(Theme::getActiveTheme()->getPath(), '/') .'/'. $fileOrDirectory;

                if (!file_exists($fileOrDirectory)) {
                    $fileOrDirectory = base_path($fileOrDirectory);
                }

                if (!file_exists($fileOrDirectory)) {
                    return [];
                }

                if (is_dir($fileOrDirectory)) {
                    return File::files($fileOrDirectory);
                }

                return [new \SplFileInfo($fileOrDirectory)];
            })
            ->unique(fn (\SplFileInfo $file) => $file->getPathname())
            ->filter(function (\SplFileInfo $file) use ($ignore) {
                return !collect($ignore)->some(fn ($pattern) => preg_match($pattern, $file->getFilename()));
            });
    }
}
