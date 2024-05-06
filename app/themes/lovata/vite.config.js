import { defineConfig } from 'vite';
import october from 'vite-plugin-october';
import { resolve } from 'path'

export default defineConfig({
  plugins: [
    october(),
  ]
});
