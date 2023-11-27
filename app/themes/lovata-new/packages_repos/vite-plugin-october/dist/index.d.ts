import { Plugin, SSROptions, ViteDevServer, UserConfig, PluginOption } from 'vite';
export { resolvePageComponent } from './inertia.js';

/**
 * Creates a plugin for patching Vite's manifest file.
 */
declare function manifest(): Plugin;

interface CommandsConfiguration {
    artisan: Record<string, string[]> | string[];
    shell: Record<string, string[]> | string[];
}
interface ViteConfiguration {
    entrypoints: {
        paths: string | string[];
        ssr?: string;
        ignore?: string | string[];
    };
    build_path: string;
    dev_server: {
        enabled?: boolean;
        url: string;
        cert?: string;
        key?: string;
    };
    commands?: CommandsConfiguration;
    env_prefixes?: string[];
}
type ResolvedConfiguration = ViteConfiguration & {
    configName?: string;
    aliases: Record<string, string>;
};
interface Options {
    /**
     * Path to PHP executable.
     */
    php?: string;
    /**
     * A configuration object or a path to a configuration file.
     * Setting to false disables reading the configuration file path from the `CONFIG_PATH_VITE` environment variable.
     */
    config?: ResolvedConfiguration | string | false;
    /**
     * Post CSS plugins.
     */
    postcss?: any[];
    /**
     * SSR-specific options.
     */
    ssr?: SSROptions;
    /**
     * Whether to automatically update the tsconfig.json file with aliases.
     *
     * @deprecated Use `vite.commands.artisan` => `vite:tsconfig` instead.
     */
    updateTsConfig?: boolean;
    /**
     * Whether to allow overrides from the base configuration. If false, base
     * options will be ignored, so stuff like `--host 0.0.0.0` won't work.
     *
     * @default true
     */
    allowOverrides?: boolean;
    /**
     * List of file changes to listen to.
     */
    watch?: WatchInput[] | WatchOptions;
}
interface WatchOptions {
    reloadOnBladeUpdates?: boolean;
    reloadOnConfigUpdates?: boolean;
    input?: WatchInput[];
}
interface WatchInputHandlerParameters {
    file: string;
    server: ViteDevServer;
}
interface WatchInput {
    condition: (file: string) => boolean;
    handle: (parameters: WatchInputHandlerParameters) => void;
}
interface PhpFinderOptions {
    /**
     * Actual path to PHP. This will be used instead of
     */
    path?: string;
    /**
     * Custom environment variables.
     */
    env?: any;
    /**
     * Either `production` or `development`. Used for loading the environment.
     */
    mode?: string;
}

/**
 * Loads the Laravel Vite configuration.
 */
declare const config: (options?: Options) => Plugin;

/**
 * Reload when some files are changed.
 */
declare const reload: (options?: Options) => Plugin;

/**
 * Finds the path to PHP.
 */
declare function findPhpPath(options?: PhpFinderOptions): string;
/**
 * Calls an artisan command.
 */
declare function callArtisan(executable: string, ...params: string[]): string;
/**
 * Calls a shell command.
 */
declare function callShell(executable: string, ...params: string[]): string;

declare function defineConfig(base?: UserConfig): UserConfig;
declare const laravel: (options?: Options) => PluginOption[];

export { Options, callArtisan, callShell, config, laravel as default, defineConfig, findPhpPath, laravel, manifest, reload };
