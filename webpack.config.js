const Encore = require('@symfony/webpack-encore');


Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/js/app.js') // Fichier d'entrée
    .enableSourceMaps(!Encore.isProduction()) // Maps pour le développement
    .enableVersioning(Encore.isProduction()) // Versioning pour production
    .enableSingleRuntimeChunk();

module.exports = Encore.getWebpackConfig();
