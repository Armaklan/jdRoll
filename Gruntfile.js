module.exports = function(grunt) {

  grunt.loadNpmTasks('grunt-php');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-bower-task');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-concurrent');

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
      options: {
        mangle: false
      },
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
            'js/controller/forum.js',
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
          ],
          'js/cadrach.angular.min.js': [
            'vendor/underscore/underscore.js',
            'vendor/leaflet/dist/leaflet.js',
            'vendor/angular-dragdrop/src/angular-dragdrop.js',
            'vendor/angular-leaflet/dist/angular-leaflet-directive.js',
            'vendor/angular-route/angular-route.js',
            'vendor/angular-promise-tracker/promise-tracker.js',
            'vendor/angular-strap/dist/angular-strap.js',
            'vendor/angular-strap/dist/angular-strap.tpl.js',
            'js/angular/application.js',
            'js/angular/carte/ctrlCarteCreator.js',
            'js/angular/carte/ctrlCarteManager.js'
          ]
        }
      }
    },
    less: {
      production: {
        options: {
          paths: ['less/']
        },
        files: {
          "css/main.css": "less/main.less",
          "css/carte.css": "less/carte.less"
        }
      }
    },
    cssmin: {
      theme: {
        files: {
          'css/theme.min.css': [
            'css/main.css',
            'vendor/leaflet/dist/leaflet.css',
            'vendor/bootstrap-colorpicker/css/colorpicker.css',
            'vendor/select2/select2.css',
            'css/bootstrap/*.css',
            'css/datepicker.css'
          ],
          'css/cadrach.angular.min.css': [
            'vendor/leaflet/dist/leaflet.css',
            'css/carte.css'
          ]
        }
      }
    },
    watch: {
      style: {
        files: 'less/*.less',
        tasks: ['less']
      }
    },
    concurrent: {
        dev: ['watch', 'php'],
    }
  });

  grunt.registerTask('prepare', ['bower']);
  grunt.registerTask('dev', ['less', 'concurrent:dev']);
  grunt.registerTask('dist', ['less', 'cssmin', 'uglify']);
  grunt.registerTask('default', ['dev']);

};
