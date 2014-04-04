module.exports = function(grunt) {

    grunt.loadNpmTasks('grunt-php');
    grunt.loadNpmTasks('grunt-bower-task');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-concurrent');
    grunt.loadNpmTasks('grunt-contrib-connect');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-connect-proxy');

    var request = require('request');
    
    grunt.initConfig({
        php: {
            test: {
                options: {
                    keepalive: true,
                    open: false,
                    port: 8001,
                    base: 'api'
                }
            }
        },
        bower: {
            install: {
                options: {
                    copy: false,
                }
            }
        },
        connect: {
            server: {          
              options: {
                port: 8000,
                base: 'web',
                keepalive: true,
                  middleware: function (connect, options) {
                     var proxy = require('grunt-connect-proxy/lib/utils').proxyRequest;
                     return [
                        // Include the proxy first
                        proxy,
                        // Serve static files.
                        connect.static(options.base),
                        // Make empty directories browsable.
                        connect.directory(options.base)
                     ];
                  }
              },
              proxies: [
                {
                    context: '/api',
                    host: 'localhost',
                    port: 8001,
                    rewrite: {
                        '^/api': '',
                    }
                }
              ]
            }
        },
        concurrent: {
            serve: ['server', 'php'],
            options: {
                limit: 3,
                logConcurrentOutput: true
            }
        },
        watch: {
            src: {
                files: ['web/**/*.*', 'api/**/*.php'],
                tasks: [],
                options: {
                    livereload: true,
                },
            },
            less: {
                files: ['web/less/*.less'],
                tasks: ['less']
            }

        },
        less: {
          development: {
            files: {
              "web/css/layout.css": 'web/less/layout.less'
            }
          }
        },
        uglify: {
            my_target: {
                files: {
                    'web/js/jdroll-angular.min.js': [
                        'js/tinymce_conf.js',
                        'js/app/**/*.js'                    ]
                }
            }
        }

    });

    grunt.registerTask('server', function (target) {
        grunt.task.run([
            'configureProxies:server',
            'connect:server'
        ]);
    });


    grunt.registerTask('prepare', ['bower']);
    grunt.registerTask('dev', ['php']);
    grunt.registerTask('dist', ['less','uglify']);
    grunt.registerTask('default', ['less','concurrent:serve']);

};

