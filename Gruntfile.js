module.exports = function(grunt) {

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');

    var request = require('request');

    grunt.initConfig({
        watch: {
            src: {
                files: ['web/js/**/*.js','web/css/**/*.css','web/views/**/*.html', 'api/**/*.php'],
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


    grunt.registerTask('dist', ['less']);
    grunt.registerTask('default', ['less','watch']);

};

