const merge = require('webpack-merge');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');

module.exports = merge(require('./webpack.config.base.js'), {
	devtool: 'source-map',
	plugins: [
		new UglifyJSPlugin()
	]
});