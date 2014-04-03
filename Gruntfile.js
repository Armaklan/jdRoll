module.exports = function(grunt) {

    grunt.loadNpmTasks('grunt-php');
    grunt.loadNpmTasks('grunt-bower-task');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-concurrent');
    grunt.loadNpmTasks('grunt-contrib-connect');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');

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
                middleware : function(connect, options) {
                    return [
                        function(req,res,next) {
                            if (req.url.substring(0,5) == '/api/'){
                                var targetUrl = 'http://localhost:8001/'+req.url.substring(5);
                                request(targetUrl, function (err, response, body) {
                                    if (!err && response.statusCode == 200) {
                                        res.end(body);
                                    } else {
                                        res.statusCode = response.statusCode;
                                        res.end();
                                    }
                                });
                            } else {
                                return next();
                            }
                        },
                        connect.static(require('path').resolve('web'))
                    ];
                  }
              }
            }
        },
        concurrent: {
            serve: ['watch', 'connect', 'php'],
            options: {
                limit: 5,
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

    grunt.registerTask('prepare', ['bower']);
    grunt.registerTask('dev', ['php']);
    grunt.registerTask('dist', ['less','uglify']);
    grunt.registerTask('default', ['less','concurrent:serve']);

};

