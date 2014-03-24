module.exports = function(grunt) {

    grunt.loadNpmTasks('grunt-php');
    grunt.loadNpmTasks('grunt-bower-task');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-concurrent');
    grunt.loadNpmTasks('grunt-contrib-connect');
    grunt.loadNpmTasks('grunt-contrib-watch');

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
                                console.log(targetUrl);
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
            }
        },
        uglify: {
            my_target: {
                files: {
                    'js/controller.min.js': [
                        'js/tinymce_conf.js',
                        'js/controller/onload.js',
                        'js/controller/campagne.js',
                        'js/controller/chat.js',
                        'js/controller/draft.js',
                        'js/controller/notification.js',
                        'js/controller/ui.js',
                        'js/controller/dicer.js',
                        'js/controller/campagne_modal.js',
                        'js/tools.js'
                    ]
                }
            }
        }

    });

    grunt.registerTask('prepare', ['bower']);
    grunt.registerTask('dev', ['php']);
    grunt.registerTask('dist', ['uglify']);
    grunt.registerTask('default', ['concurrent:serve']);

};

