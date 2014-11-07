module.exports = function(grunt) {

    grunt.loadNpmTasks('grunt-php');
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
                        'vendor/magicsuggest/bin/magicsuggest-1.3.1-min.js',
                        'vendor/select2/select2.min.js',
                        'vendor/zeroclipboard/ZeroClipboard.min.js',
                        'js/bootstrap/bootstrap-fileupload.min.js',
                        'js/bootstrap/bootstrap-datepicker.js'
                    ]
                }
            }
        }

    });

    grunt.registerTask('prepare', ['bower']);
    grunt.registerTask('dev', ['php']);
    grunt.registerTask('dist', ['uglify']);
    grunt.registerTask('default', ['php']);

};
