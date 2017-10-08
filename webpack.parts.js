
module.exports.lintJavaScript = ({ include, exclude, options }) => ({
	module: {
		rules: [
			{
				test: /\.js$/,
				include,
				exclude,
				enforce: 'pre',

				loader: 'eslint-loader',
				options,
			},
		],
	},
});