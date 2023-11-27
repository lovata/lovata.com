import { defineConfig } from 'vite';
import october from 'vite-plugin-october';
import tailwindcss from 'tailwindcss';
import { resolve } from 'path'

console.log(resolve(__dirname, './'));
export default defineConfig({
  plugins: [
    tailwindcss(),
    october(),
  ]
});

