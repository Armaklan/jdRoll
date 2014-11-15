module.exports = function(grunt) {

    grunt.loadNpmTasks('grunt-php');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-bower-task');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.initConfig({
        php: {
            test: {
                options: {
                    keepalive: true,
                    open: true
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
                        'js/controller/persoPopup.js',
                        'js/controller/theme-preview.js',
                        'js/controller/campagne-config.js',
                        'js/controller/feedback.js',
                        'js/tools.js'
                    ],
                    'js/composant.min.js': [
                        'vendor/bootbox/bootbox.js',
                        'vendor/bootstrap-colorpicker/js/bootstrap-colorpicker.js',
                        'vendor/select2/select2.min.js',
                        'vendor/zeroclipboard/ZeroClipboard.min.js',
                        'js/bootstrap/bootstrap-fileupload.min.js',
                        'js/bootstrap/bootstrap-datepicker.js'
                    ],
                    'js/flot.min.js': [
                        'vendor/flot/jquery.flot.js',
                        'vendor/flot/jquery.flot.time.js',
                        'vendor/flot/jquery.flot.pie.js',
                        'vendor/flot/jquery.flot.navigate.js'
                    ]
                }
            }
        },
        cssmin: {
          theme: {
            files: {
              'css/theme.min.css': [
                  'vendor/bootstrap-colorpicker/css/colorpicker.css',
                  'vendor/select2/select2.css',
                  'css/bootstrap/*.css',
                  'css/datepicker.css',
                  'css/main.css'
              ]
            }
          }
        }

    });

    grunt.registerTask('prepare', ['bower']);
    grunt.registerTask('dev', ['php']);
    grunt.registerTask('dist', ['cssmin','uglify']);
    grunt.registerTask('default', ['php']);

};
