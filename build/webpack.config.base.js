const path = require('path');
const { SRC, DIST, ASSETS } = require('./paths');
const webpack = require('webpack');

module.exports = {
	entry: {
		scripts: path.resolve(SRC, 'js', 'index.js')
	},
	output: {
		// Put all the bundled stuff in your dist folder
		path: DIST,

		// Our single entry point from above will be named "scripts.js"
		filename: '[name].js',

		// The output path as seen from the domain we're visiting in the browser
		publicPath: ASSETS
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
};