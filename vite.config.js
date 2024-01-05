/**
 * View your website at your own local server.
 * Example: if you're using WP-CLI then the common URL is: http://localhost:8080.
 *
 * http://localhost:5173 is serving Vite on development. Access this URL will show empty page.
 *
 */

import { defineConfig } from "vite";
import { resolve } from 'path';
import { glob } from 'glob';

export default defineConfig({
    plugins: [
        {
            handleHotUpdate({ file, server }) {
                if (file.endsWith('.php')) {
                    server.ws.send({ type: 'full-reload', path: '*' });
                }
            }
        },
    ],

    css: {
        devSourcemap: true,
        // preprocessorOptions: {
        //     scss: {
        //         additionalData: '@import "./src/scss/sass_imports.scss";',
        //     }
        // }
    },

    build: {
        // emit manifest so PHP can find the hashed files
        manifest: true,

        outDir: resolve(__dirname, 'assets/dist/'),

        rollupOptions: {
            input: {
                'js/main': resolve(__dirname + '/assets/src/js/main.js'),
                ...(
                  () => glob
                    .sync(resolve(__dirname,'assets/src/scss/[!_]*.scss'))
                    .reduce((entries, filename) => {
                        const [, name] = filename.match(/([^/]+)\.scss$/)
                        return { ...entries, [name]: filename }
                    }, {})
                )()
            },
            output: {
                entryFileNames: '[name]-[hash].js',
                chunkFileNames: '[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    let extType = assetInfo.name.split('.');
                    let extSuffix = extType[extType.length - 1];
                    let name = assetInfo.name.split('/').pop();
                    // stip extension
                    name = name.replace(/\.[^/.]+$/, "");
                    console.log(extSuffix);

                    return `${extSuffix}/${name}-[hash][extname]`;
                },
            },
        },
    },

    server: {
        // required to load scripts from custom host
        cors: {
            origin: "*"
        },

        // We need a strict port to match on PHP side.
        // You can change it. But, please update it on your .env file to match the same port
        strictPort: true,
        port: 5173
    },

    resolve: {
        alias: {
            "@": resolve(__dirname, '../static/'),
        }
    },
});
