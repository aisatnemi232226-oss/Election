import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/admin/election-articles.js',
        'resources/js/admin/candidates.js',
        'resources/js/components/search-box.js',
      ],
      refresh: true,
    }),
    tailwindcss(),
  ],
});
