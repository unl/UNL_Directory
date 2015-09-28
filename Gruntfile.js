var every = require('lodash/collection/every');
var fs = require('fs');

module.exports = function (grunt) {
  var lessDir = 'www/less';
  var lessVendorDir = lessDir + '/lib';
  var cssDir = 'www/css';
  var jsDir = 'www/js';

  var cssFiles = [
    'directory'
  ];

  var jsFiles = [
    jsDir + '/directory.js'
  ];

  var wdnMixinLibBaseUrl = 'https://raw.githubusercontent.com/unl/wdntemplates/master/wdn/templates_4.0/less/_mixins/';
  var wdnMixins = [
    'breakpoints.less',
    'colors.less',
    'fonts.less',
  ];
  var allMixinsExist = every(wdnMixins, function(value) {
    return fs.existsSync(lessVendorDir + '/' + value);
  });

  var lessFiles = {};
  cssFiles.forEach(function(file) {
    lessFiles[cssDir + '/' + file + '.css'] = lessDir + '/' + file + '.less';
  });

  var builtJsFiles = {};
  builtJsFiles[jsDir + '/directory.min.js'] = jsFiles;

  var autoprefixPlugin = new (require('less-plugin-autoprefix'))({browsers: ["last 2 versions"]});
  var cleanCssPlugin = new (require('less-plugin-clean-css'))();

  // load all grunt tasks matching the ['grunt-*', '@*/grunt-*'] patterns
  require('load-grunt-tasks')(grunt);

  grunt.initConfig({
    'curl-dir': {
      'less-libs': {
        src: wdnMixins.map(function(file) {
          return wdnMixinLibBaseUrl + file;
        }),
        dest: lessVendorDir
      }
    },
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
  		}
    }
  });

  // establish grunt default
  var defaultTasks = ['less', 'uglify'];
  var localTasks = defaultTasks.slice();
  if (!allMixinsExist) {
    defaultTasks.unshift('curl-dir');
  }
  grunt.registerTask('default', defaultTasks);
  grunt.registerTask('all-local', localTasks);
};
