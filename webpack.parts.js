
exports.lintJavaScript = ({ include, exclude, options }) => ({
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

exports.loadCSS = ({ include, exclude } = {}) => ({
    module: {
        rules: [
            {
                test: /\.css$/,
                include,
                exclude,

                use: ['style-loader', 'css-loader'],
            },
        ],
    },
});