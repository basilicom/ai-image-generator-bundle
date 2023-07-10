const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/')
    .setPublicPath('/bundles/aiimagegenerator/')
    .setManifestKeyPrefix('ai-image-generator')

    .addEntry('editmode', './assets/editmode.js')
    .addEntry('backend', './assets/backend.js')

    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(false)
    .enableVersioning(false)
    .configureTerserPlugin((options) => {
        options.terserOptions = {
            output: {
                comments: false,
            },
            keep_fnames: true,
            mangle: {
                reserved: ['pimcore', '$super']
            }
        }
    })
    .enableSassLoader();

module.exports = Encore.getWebpackConfig();
