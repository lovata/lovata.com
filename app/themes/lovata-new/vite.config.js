import { defineConfig } from 'vite';
import tailwindcss from 'tailwindcss';
import { resolve, basename, join } from 'path';
import { globSync } from 'glob';

const pagesPath = join(__dirname, 'pages');
const input = {
    main: resolve(__dirname, 'assets/js/index.js'),
    css: resolve(__dirname, 'assets/css/common.css'),
}

const fileList= globSync(`${pagesPath}/**/*.+(js|css)`);
fileList.forEach((filePath) => {
    const fileName = filePath.replace(join(__dirname, '/'), '');
    input[fileName] = filePath;
});

const themeName = 'lovata-new'

export default defineConfig({
    base: `/themes/${themeName}/assets/build/`,
    plugins: [
        tailwindcss(),
    ],
    build: {
        rollupOptions: { input },
        manifest: true,
        emptyOutDir: true,
        outDir: resolve(__dirname, 'assets/build'),
    },
    server: {
        hmr: {
            protocol: 'ws',
        },
    }
})
