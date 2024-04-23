import { defineConfig } from 'vite';
import tailwindcss from 'tailwindcss';
import { resolve, basename, join } from 'path';
import { globSync } from 'glob';

const input = {}

const pagesPath = join(__dirname, 'pages');
let fileList= globSync(`${pagesPath}/**/*.+(js|css)`);

const assetsPath = join(__dirname, 'assets');
const assetCssFileList= globSync([`${assetsPath}/css/*.+(js|css)`, `${assetsPath}/css/**/*.+(js|css)`]);
const assetJSFileList= globSync(`${assetsPath}/js/*.+(js|css)`);


fileList = [...fileList, ...assetCssFileList, ...assetJSFileList];
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
