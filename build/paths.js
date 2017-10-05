const path = require('path')

module.exports = {
	SRC: path.resolve(__dirname, '..', 'app'),

	// The path to put the generated bundle(s)
	DIST: path.resolve(__dirname, '..', 'web', 'dist'),

	/*
	Directory as seen from the web
	*/
	ASSETS: '/dist'
}