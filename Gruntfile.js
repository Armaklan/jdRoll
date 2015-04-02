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
            'js/controller/*.js',
            'js/tools.js',
            'js/angular/notes/notes.module.js',
            '!js/controller.min.js',
            '!js/composant.min.js',
            '!js/flot.min.js',
            '!js/jdroll.angular.min.js'
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
          'js/jdroll.angular.min.js': [
            'vendor/underscore/underscore.js',
            'vendor/leaflet/dist/leaflet.js',
            'vendor/angular-dragdrop/src/angular-dragdrop.js',
            'vendor/angular-leaflet/dist/angular-leaflet-directive.js',
            'vendor/angular-route/angular-route.js',
            'vendor/angular-promise-tracker/promise-tracker.js',
            'vendor/angular-promise-tracker/promise-tracker-http-interceptor.js',
            'vendor/angular-strap/dist/angular-strap.js',
            'vendor/angular-strap/dist/angular-strap.tpl.js',
            'vendor/angular-growl-v2/build/angular-growl.js',
            'js/angular/application.js',
            'js/angular/carte/*.js'
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
            'css/datepicker.css',
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
