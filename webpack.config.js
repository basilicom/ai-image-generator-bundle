const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/')
    .setPublicPath('/bundles/aiimagegenerator/')
    .setManifestKeyPrefix('ai-image-generator')

    .addEntry('app', './assets/app.js')

    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(false)
    .enableVersioning(false)
    .enableSassLoader();

module.exports = Encore.getWebpackConfig();
