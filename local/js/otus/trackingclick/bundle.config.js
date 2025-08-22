module.exports = {
	input: 'src/main.js',
	output: './dist/trackingclick.bundle.js',
	namespace: 'Otus.Trackingclick',
	sourceMaps: false,
	adjustConfigPhp: false,
	minification: true,
	browserslist: true,
	plugins: {
		resolve: true,
	},
};
