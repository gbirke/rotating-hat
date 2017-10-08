const path = require('path');
const webpack = require('webpack');
const merge = require('webpack-merge');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');

const parts = require('./webpack.parts');

const PATHS = {
	app: path.join(__dirname, 'app/js'),
	build: path.join(__dirname, 'web/dist'),
	publicPath: '/dist' // absolute path from webroot
};

const commonConfig = merge([
	{
		entry: {
			scripts: PATHS.app
		},
		output: {
			// Put all the bundled stuff in your dist folder
			path: PATHS.build,

			// Our single entry point from above will be named "scripts.js"
			filename: '[name].js',

			// The output path as seen from the domain we're visiting in the browser
			publicPath: PATHS.publicPath
		},
		module: {
			rules: [
				{
					test: /\.(scss)$/,
					use: [
						{
							loader: 'style-loader', // inject CSS to page
						}, {
							loader: 'css-loader', // translates CSS into CommonJS modules
						}, {
							loader: 'postcss-loader', // Run post css actions
							options: {
								plugins: function () { // post css plugins, can be exported to postcss.config.js
									return [
										//require('precss'),
										require('autoprefixer')
									];
								}
							}
						}, {
							loader: 'sass-loader' // compiles SASS to CSS
						}
					]
				},
				{
					test: /\.css$/,
					use: [ 'style-loader', 'css-loader' ]
				}
			]
		},
		plugins: [
			new webpack.ProvidePlugin({
				$: 'jquery',
				jQuery: 'jquery',
				'window.jQuery': 'jquery',
				Popper: ['popper.js', 'default']
			})
		]
	},
	parts.lintJavaScript({ include: PATHS.app }),
]);

const productionConfig = merge([
	{
		devtool: 'source-map',
		plugins: [
			new UglifyJSPlugin()
		]
	},
]);

const developmentConfig = merge([
	{
		devtool: 'inline-source-map'
	},
]);

module.exports = (env) => {
	if (env === 'production') {
		return merge(commonConfig, productionConfig);
	}

	return merge(commonConfig, developmentConfig);
};

