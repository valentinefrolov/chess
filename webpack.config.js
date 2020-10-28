//const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const webpack = require('webpack');
require("babel-polyfill");
require("raf/polyfill");

module.exports = {
    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.esm.js'
        }
    },
    entry: {
        app: ['babel-polyfill', 'raf/polyfill', './js/app.js']
    },
    output: {
        filename: '[name].bundle.js'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: "babel-loader"
            },
        ]
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: "jquery"
        }),
        new webpack.DefinePlugin({
            'process.env': {
                'NODE_ENV': JSON.stringify(process.env.NODE_ENV)
            }
        })
        //new UglifyJsPlugin({sourceMap: process.env.NODE_ENV !== 'production'})
    ],
    devtool: 'source-map'
};
