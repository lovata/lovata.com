import { defineConfig } from 'vite';
import october from 'vite-plugin-october';
import { resolve } from 'path'

console.log(resolve(__dirname, './'));
export default defineConfig({
  plugins: [
    october(),
  ]
});
