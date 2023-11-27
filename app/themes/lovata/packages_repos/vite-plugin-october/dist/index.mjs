import fs from 'node:fs/promises';
import path from 'node:path';
import { createHash } from 'node:crypto';
import os from 'node:os';
import fs$1 from 'node:fs';
import c from 'chalk';
import makeDebugger from 'debug';
import { defu } from 'defu';
import { loadEnv } from 'vite';
import { sync } from 'execa';
export { resolvePageComponent } from './inertia.mjs';

const PREFIX$2 = "vite:laravel:config";
function manifest() {
  const manifest2 = /* @__PURE__ */ new Map();
  let config;
  return {
    name: PREFIX$2,
    apply: "build",
    enforce: "post",
    configResolved(resolved) {
      config = resolved;
    },
    async generateBundle(_, bundle) {
      const entrypoints = getEntrypoints(config);
      if (!entrypoints) {
        return;
      }
      const values = Object.values(bundle);
      const assets = values.filter((c) => c.type === "asset");
      const cssEntrypoints = entrypoints.filter((entry) => isStylesheet(entry));
      const cssAssets = assets.filter((asset) => isStylesheet(asset.name));
      if (config.build.cssCodeSplit) {
        for (const chunk of cssAssets) {
          if (!chunk.name) {
            continue;
          }
          const name = removeExtension(chunk.name);
          for (const entry of cssEntrypoints) {
            if (removeExtension(path.basename(entry)) === name) {
              manifest2.set(entry, { file: chunk.fileName, src: entry, isEntry: true });
            }
          }
        }
      } else {
        const chunk = assets.find((asset) => asset.name === "style.css");
        if (chunk) {
          manifest2.set(chunk.name, { file: chunk.fileName, src: chunk.name });
        }
      }
      const remaining = entrypoints.filter((entry) => isAssetEntrypoint(entry));
      for (const entry of remaining) {
        const fullPath = path.join(config.root, entry);
        const source = await fs.readFile(fullPath);
        const hash = getAssetHash(source);
        const ext = path.extname(entry);
        const name = removeExtension(entry);
        const fileName = path.posix.join(
          config.build.assetsDir,
          `${path.basename(name)}.${hash}${ext}`
        );
        manifest2.set(entry, { file: fileName, src: entry, isEntry: true });
        if (!bundle[fileName]) {
          this.emitFile({ name: entry, fileName, source, type: "asset" });
        }
      }
    },
    async writeBundle(_opts, bundle) {
      if (!bundle["manifest.json"]) {
        return;
      }
      const manifestPath = path.resolve(config.root, config.build.outDir, "manifest.json");
      const viteManifest = JSON.parse(await fs.readFile(manifestPath, "utf8"));
      for (const [key, value] of Object.entries(viteManifest)) {
        manifest2.set(key, value);
      }
      await fs.writeFile(
        manifestPath,
        JSON.stringify(Object.fromEntries(manifest2), null, 2)
      );
    }
  };
}
function getEntrypoints(config) {
  let input = config.build.rollupOptions.input;
  if (!input) {
    return null;
  }
  if (typeof input === "string") {
    input = [input];
  }
  if (typeof input === "object" && !Array.isArray(input)) {
    const keys = Object.keys(input);
    if (keys.length === 0) {
      return null;
    }
    input = keys;
  }
  if (input.length === 0) {
    return null;
  }
  return input.map((entry) => path.relative(config.root, entry).replaceAll("\\", "/"));
}
function removeExtension(filename) {
  return filename.replace(/\.[^.]*$/, "");
}
function isStylesheet(filename) {
  return /\.(css|less|sass|scss|styl|stylus|pcss|postcss)$/.test(filename);
}
function isAssetEntrypoint(filename) {
  if (isStylesheet(filename)) {
    return false;
  }
  return !/\.(htm|html|jsx?|tsx?)$/.test(filename);
}
function getAssetHash(content) {
  return createHash("sha256").update(content).digest("hex").slice(0, 8);
}

const version = "1.0.0";

function parseUrl(urlString) {
  if (!urlString) {
    return;
  }
  try {
    return new URL(urlString);
  } catch {
  }
}
function finish(str, character, _default = "") {
  if (!str) {
    return _default;
  }
  if (!str.endsWith(character)) {
    return str + character;
  }
  return str;
}
function wrap(input, _default) {
  if (!input) {
    return _default;
  }
  if (Array.isArray(input)) {
    return input;
  }
  return [input];
}
function findPhpPath(options = {}) {
  if (options.path) {
    return options.path;
  }
  if (!options.env) {
    options.env = loadEnv(options.mode ?? process.env.NODE_ENV ?? "development", process.cwd(), "");
  }
  return options.env.PHP_EXECUTABLE_PATH || "php";
}
function callArtisan(executable, ...params) {
  if (process.env.VITEST && process.env.TEST_ARTISAN_SCRIPT) {
    return sync(process.env.TEST_ARTISAN_SCRIPT, [executable, "../../artisan", ...params], { encoding: "utf-8" })?.stdout;
  }
  return sync(executable, ["../../artisan", ...params])?.stdout;
}
function callShell(executable, ...params) {
  if (process.env.VITEST && process.env.TEST_ARTISAN_SCRIPT) {
    return sync(process.env.TEST_ARTISAN_SCRIPT, [executable, ...params])?.stdout;
  }
  return sync(executable, [...params])?.stdout;
}
function warn(prefix, message, ...args) {
  console.warn(c.yellow.bold(`(!) ${c.cyan(prefix)} ${message}`, ...args));
}

const PREFIX$1 = "vite:laravel:config";
const CONFIG_ARTISAN_COMMAND = "vite:config";
const debug$1 = makeDebugger(PREFIX$1);
const config = (options = {}) => {
  let serverConfig;
  let env;
  return {
    name: "laravel:config",
    enforce: "post",
    config: (baseConfig, { command, mode }) => {
      env = loadEnv(mode, process.cwd(), "");
      const configName = findConfigName();
      debug$1("Config name:", configName ?? "not specified");
      serverConfig = readConfig(options, env, configName);
      serverConfig.build_path ?? (serverConfig.build_path = "build");
      debug$1("Configuration from PHP:", serverConfig);
      const base = finish(`${finish(env.ASSET_URL, "/", "/")}${command === "build" ? `${serverConfig.build_path}/` : ""}`, "/");
      debug$1("Base URL:", base || "<empty>");
      const { protocol, hostname, port } = new URL(serverConfig.dev_server.url || "http://0.0.0.0:5173");
      const { key, cert } = findCertificates(serverConfig, env, env.APP_URL);
      const usesHttps = key && cert && protocol === "https:";
      debug$1("Uses HTTPS:", usesHttps, { key, cert, protocol, hostname, port });
      const ssr = process.argv.includes("--ssr");
      const entrypoints = ssr ? serverConfig.entrypoints.ssr : serverConfig.entrypoints.paths;
      const executable = findPhpPath({ env, path: options.php, mode });
      Object.entries(serverConfig.commands?.artisan ?? {}).forEach(([command2, args]) => {
        if (!isNaN(+command2)) {
          debug$1("Running artisan command without arguments:", executable, "artisan", args);
          debug$1(callArtisan(executable, args));
          return;
        }
        debug$1("Running artisan command:", executable, "artisan", command2, ...args);
        debug$1(callArtisan(executable, command2, ...args));
      });
      Object.entries(serverConfig.commands?.shell ?? {}).forEach(([command2, args]) => {
        if (!isNaN(+command2)) {
          debug$1("Running shell command without arguments:", args);
          debug$1(callShell(args));
          return;
        }
        debug$1("Running shell command:", command2, ...args);
        debug$1(callShell(command2, ...args));
      });
      if (command !== "build" && options.updateTsConfig) {
        warn(PREFIX$1, "To update the tsconfig.json file, use php artisan vite:tsconfig instead. You can add it in your vite.php artisan commands.");
      }
      const resolvedConfig = {
        envPrefix: wrap(serverConfig.env_prefixes, ["MIX_", "VITE_", "SCRIPT_"]),
        base,
        publicDir: false,
        server: {
          host: hostname,
          https: usesHttps ? { maxVersion: "TLSv1.2", key, cert } : protocol === "https:",
          port: port ? Number(port) : 5173,
          strictPort: !process.argv.includes("--no-strict-port"),
          origin: `${protocol}//${hostname}:${port}`,
          hmr: {
            host: hostname,
            port: Number(port) || 5173
          }
        },
        build: {
          assetsDir: "build/assets",
          ssrManifest: ssr,
          manifest: !ssr,
          ssr,
          outDir: `./`,
          rollupOptions: {
            input: entrypoints
          }
        },
        css: { postcss: options.postcss ? { plugins: options.postcss } : baseConfig.css?.postcss }
      };
      const finalConfig = options.allowOverrides === false ? resolvedConfig : defu(baseConfig, resolvedConfig);
      debug$1("Initial config:", baseConfig);
      debug$1("Resolved config:", resolvedConfig);
      debug$1("Final config:", finalConfig);
      return finalConfig;
    },
    configureServer: (server) => {
      server.httpServer?.once("listening", () => {
        setTimeout(() => {
          server.config.logger.info(`
  ${c.magenta(`${c.bold("LARAVEL")} v${version}`)}  ${c.dim(`using ${c.white.bold(serverConfig.configName)} config`)}
`);
          server.config.logger.info(`  ${c.magenta("\u279C")}  ${c.bold("Application")}: ${c.cyan(env.APP_URL)}`);
          server.config.logger.info(`  ${c.magenta("\u279C")}  ${c.bold("Environment")}: ${c.dim(env.APP_ENV)}`);
          if (serverConfig.dev_server.enabled === false) {
            const buildPath = `${server.config.root}/${serverConfig.build_path}`;
            const isBuilt = fs$1.existsSync(buildPath);
            const color = isBuilt ? "yellow" : "red";
            const hint = isBuilt ? `the ${c.bold(serverConfig.build_path)} directory will be used instead` : `run ${c.bold("vite build")} to be able to preview your application`;
            server.config.logger.info(c[color](`    ${c[color]("\u279C")}  ${c.bold("dev_server.enabled")} is set to ${c.bold("false")}, ${hint}`));
          }
          if (!serverConfig.entrypoints.paths?.length) {
            server.config.logger.info(c.red(`    ${c.red("\u279C")}  ${c.bold("entrypoints.paths")} is empty, no assets will be served and the production build will fail`));
          }
          server.config.logger.info("");
        }, 25);
      });
    }
  };
};
function readConfig(options, env, name) {
  const executable = findPhpPath({ env, path: options.php });
  const configFromJson = (json, name2) => {
    if (!json) {
      throw new Error("The configuration object is empty");
    }
    if (!json.configs) {
      throw new Error('The configuration object do not contain a "configs" property. Is innocenzi/laravel-vite up-to-date?');
    }
    if (name2 && !(name2 in json.configs)) {
      throw new Error(`"${name2}" is not defined in "config/vite.php"`);
    }
    return {
      configName: name2 ?? json.default,
      commands: json.commands,
      aliases: json.aliases,
      ...json.configs[name2 ?? json.default]
    };
  };
  try {
    if (!options.config && options.config !== false && env.CONFIG_PATH_VITE) {
      debug$1("Setting configuration file path to CONFIG_PATH_VITE.");
      options.config = env.CONFIG_PATH_VITE;
    }
    if (typeof options.config === "string") {
      if (fs$1.existsSync(options.config)) {
        debug$1(`Reading configuration from ${options.config}`);
        const json3 = JSON.parse(fs$1.readFileSync(options.config, { encoding: "utf-8" }));
        return configFromJson(json3, name);
      }
      const json2 = JSON.parse(callArtisan(executable, CONFIG_ARTISAN_COMMAND));
      debug$1("Using specified configuration name:", options.config);
      return configFromJson(json2, options.config);
    }
    if (typeof options.config === "object") {
      debug$1("Reading configuration from the given object.");
      return options.config;
    }
    debug$1("Reading configuration from PHP.");
    const json = JSON.parse(callArtisan(executable, CONFIG_ARTISAN_COMMAND));
    return configFromJson(json, name);
  } catch (error) {
    throw new Error(`[${PREFIX$1}] Could not read configuration: ${error.message}`);
  }
}
function findConfigName() {
  const configIndex = process.argv.findIndex((arg) => ["-c", "--config"].includes(arg));
  if (!configIndex) {
    return;
  }
  const fileNameRegex = /vite\.([\w-]+)\.config\.ts/;
  const configFile = process.argv.at(configIndex + 1);
  return fileNameRegex.exec(configFile || "")?.at(1)?.trim();
}
function findCertificates(cfg, env, appUrl) {
  let key = cfg.dev_server.key || env.DEV_SERVER_KEY || "";
  let cert = cfg.dev_server.cert || env.DEV_SERVER_CERT || "";
  if (!key || !cert) {
    switch (os.platform()) {
      case "darwin": {
        const home = os.homedir();
        const domain = parseUrl(appUrl)?.hostname;
        const valetPath = "/.config/valet/Certificates/";
        key || (key = `${home}${valetPath}${domain}.key`);
        cert || (cert = `${home}${valetPath}${domain}.crt`);
        debug$1("Automatically set certificates for Valet:", {
          home,
          domain,
          valetPath,
          key,
          cert
        });
        break;
      }
      case "win32": {
        let laragonDirectory = process.env.PATH?.split(";").find((l) => l.toLowerCase().includes("laragon"));
        if (!laragonDirectory) {
          break;
        }
        laragonDirectory = laragonDirectory.split("\\bin")[0];
        if (laragonDirectory.endsWith("\\")) {
          laragonDirectory = laragonDirectory.slice(0, -1);
        }
        key || (key = `${laragonDirectory}\\etc\\ssl\\laragon.key`);
        cert || (cert = `${laragonDirectory}\\etc\\ssl\\laragon.crt`);
        debug$1("Automatically set certificates for Laragon:", {
          laragonDirectory,
          key,
          cert
        });
        break;
      }
    }
  }
  return {
    key,
    cert
  };
}

const PREFIX = "vite:laravel:reload";
const debug = makeDebugger(PREFIX);
const reload = (options = {}) => {
  const watchOptions = {
    input: [],
    reloadOnBladeUpdates: true,
    reloadOnConfigUpdates: true,
    ...Array.isArray(options.watch) ? { input: options.watch } : options.watch
  };
  debug("Given options:", options);
  debug("Resolved options:", watchOptions);
  if (watchOptions.reloadOnConfigUpdates) {
    watchOptions.input.push({
      condition: (file) => file.endsWith("config/vite.php"),
      handle: ({ server }) => {
        debug("Configuration file changed, invalidating module graph and reloading");
        server.moduleGraph.invalidateAll();
        server.ws.send({ type: "full-reload", path: "*" });
      }
    });
  }
  if (watchOptions.reloadOnBladeUpdates) {
    watchOptions.input.push({
      condition: (file) => file.endsWith(".htm"),
      handle: ({ server }) => {
        debug("Htm file changed, reloading");
        server.ws.send({ type: "full-reload", path: "*" });
      }
    });
  }
  function handleReload(file, server) {
    file = file.replaceAll("\\", "/");
    watchOptions.input.forEach((value) => {
      if (value.condition(file)) {
        debug(`${file} changed, applying its handler`);
        try {
          value.handle({ file, server });
        } catch (error) {
          warn(PREFIX, `Handler failed for ${file}: ${error.message}`);
          debug("Full error:", error);
        }
      }
    });
  }
  return {
    name: "vite:laravel:reload",
    configureServer(server) {
      server.watcher.on("add", (path) => handleReload(path, server)).on("change", (path) => handleReload(path, server)).on("unlink", (path) => handleReload(path, server));
    }
  };
};

function defineConfig(base = {}) {
  return {
    ...base,
    plugins: [
      ...base?.plugins,
      config(),
      reload(),
      manifest()
    ]
  };
}
const laravel = (options = {}) => [
  reload(options),
  config(options),
  manifest()
];

export { callArtisan, callShell, config, laravel as default, defineConfig, findPhpPath, laravel, manifest, reload };
