var every = require('lodash.every');
var fs = require('fs');

module.exports = function (grunt) {
	var lessDir = 'www/less';
	var lessVendorDir = lessDir + '/lib';
	var cssDir = 'www/css';
	var jsDir = 'www/js';

	var cssFiles = [
		'directory',
		'directory-print',
	];

	var jsFiles = [
		jsDir + '/directory.js',
		jsDir + '/directory-person-info.js'
	];

	var lessFiles = {};
	cssFiles.forEach(function(file) {
		lessFiles[cssDir + '/' + file + '.css'] = lessDir + '/' + file + '.less';
	});

	var builtJsFiles = {};
	builtJsFiles[jsDir + '/directory.min.js'] = jsFiles[0];
	builtJsFiles[jsDir + '/directory-person-info.min.js'] = jsFiles[1];

	var autoprefixPlugin = new (require('less-plugin-autoprefix'))({browsers: ["last 2 versions"]});
	var cleanCssPlugin = new (require('less-plugin-clean-css'))();

	// load all grunt tasks matching the ['grunt-*', '@*/grunt-*'] patterns
	require('load-grunt-tasks')(grunt);

	grunt.initConfig({
		less: {
			all: {
				options: {
						paths: [lessDir],
						plugins: [
							autoprefixPlugin,
							cleanCssPlugin
						]
					},
					files: lessFiles
			}
		},
		uglify: {
			options: {
				sourceMap: true
			},
			all: {
				files: builtJsFiles
			}
		},
		clean: {
			css: Object.keys(lessFiles).concat(lessVendorDir),
			js: Object.keys(builtJsFiles).concat(jsDir + '/**/*.map')
		},
		watch: {
			less: {
				files: lessDir + '/**/*.less',
				tasks: ['less']
			},
			js: {
				files: [
					jsDir + '/**/*.js',
					'!' + jsDir + '/**/*.min.js'
				],
				tasks: ['uglify']
			}
		}
	});

	// establish grunt default
	var defaultTasks = ['less', 'uglify'];
	var localTasks = defaultTasks.slice();
	grunt.registerTask('default', defaultTasks);
	grunt.registerTask('all-local', localTasks);
};
