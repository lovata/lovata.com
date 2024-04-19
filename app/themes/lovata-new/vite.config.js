import { defineConfig } from 'vite';
import october from 'vite-plugin-october';
import tailwindcss from 'tailwindcss';
import { ViteMinifyPlugin } from 'vite-plugin-minify'

export default defineConfig({
  server: {
    port: 3000,
  },
  plugins: [
    tailwindcss(),
    october(),
    ViteMinifyPlugin({}),
  ]
});

