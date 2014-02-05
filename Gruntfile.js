var htmlDir = 'public/**/*.html';
var indexDir = './public/index.html';
var nodeDir = 'lib/**/*.js';
var nodeReloadFile = '.grunt/rebooted';
var cssDir = 'public/**/*.css';
var lessDir = 'public/less/*.less';
var jsDir = 'public/**/*.js';
var vendorDir = 'public/vendor';

module.exports = function(grunt) {

	grunt.initConfig({
		bower: {
			install: {
		       options: {
		       		targetDir: vendorDir
		       }
		    }
		},
	    concurrent: {
		  dev: {
		    tasks: ['nodemon', 'node-inspector', 'watch'],
		    options: {
		      logConcurrentOutput: true
		    }
		  }
		},
	    nodemon: {
		  dev: {
		    script: 'app.js',
		    options: {
		      nodeArgs: ['--debug'],
		      callback: function (nodemon) {
		        nodemon.on('log', function (event) {
		          console.log(event.colour);
		        });

		        // opens browser on initial server start
		        nodemon.on('config:update', function () {
		          // Delay before server listens on port
		          setTimeout(function() {
		            require('open')('http://localhost:3000');
		          }, 1000);
				  setTimeout(function() {
		            require('open')('http://localhost:8080/debug?port=5858');
		          }, 2000); 
		        });

		        // refreshes browser when server reboots
		        nodemon.on('restart', function () {
		          // Delay before server listens on port
		          setTimeout(function() {
		            require('fs').writeFileSync(nodeReloadFile, 'rebooted');
		          }, 1000);
		        });
		      }
		  	}
		  }
		},
		'node-inspector': {
		  custom: {
		    options: {
		      'save-live-edit': true,
		      'stack-trace-limit': 4
		    }
		  }
		},
		watch: {
		  src: {
		    files: [jsDir, htmlDir, nodeReloadFile, cssDir],
		    tasks: [],
		    options: {
		      livereload: true,
		    },
		  },
		  less: {
		    files: [lessDir],
		    tasks: ['less']
		  }
		},
		less: {
		  development: {
		    files: {
		      "public/css/layout.css": 'public/less/layout.less'
		    }
		  }
		}
	});

	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-node-inspector');
	grunt.loadNpmTasks('grunt-concurrent');
	grunt.loadNpmTasks('grunt-nodemon');
	grunt.loadNpmTasks('grunt-bower-task');
	grunt.registerTask('default', ['less','concurrent:dev']);
	grunt.registerTask('prepare', ['bower']);

}