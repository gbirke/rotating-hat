const merge = require('webpack-merge')

module.exports = merge(require('./webpack.config.base'), {
	devtool: 'inline-source-map'
});