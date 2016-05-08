module.exports = function(grunt) {

  grunt.loadNpmTasks('grunt-php');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-bower-task');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-concurrent');
  grunt.loadNpmTasks('grunt-contrib-connect');
  grunt.loadNpmTasks('grunt-connect-proxy');


  grunt.initConfig({
    connect: {
      server: {
        options: {
          port: 8000,
          base: '.',
          logger: 'dev',
          keepalive: true,
          hostname: 'localhost',
          middleware: function (connect, options, defaultMiddleware) {
             var proxy = require('grunt-connect-proxy/lib/utils').proxyRequest;
             return [
                // Include the proxy first
                proxy
             ].concat(defaultMiddleware);
          }
        },
        proxies: [{
            context: '/socket.io',
            host: 'localhost',
            port: 5000,
            changeOrigin: false
        },{
            context: '/apiv2',
            host: 'localhost',
            port: 5000,
            changeOrigin: false
        },{
            context: '/',
            host: 'localhost',
            port: 8010,
            changeOrigin: false
        }]
      }
    },
    php: {
      test: {
        options: {
          keepalive: true,
          open: false,
          port:8010
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
          'dist/js/controller.min.js': [
            'js/tinymce_conf.js',
            'js/tools.js',
            'js/controller/onload.js',
            'js/controller/*.js',
            'js/angular/**/*.js'
          ],
          'dist/js/composant.min.js': [
            'vendor/angular/angular.min.js',
            'vendor/angular-sanitize/angular-sanitize.min.js',
            'vendor/bootbox/bootbox.js',
            'vendor/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.js',
            'vendor/select2/select2.min.js',
            'vendor/d3/d3.js',
            'vendor/c3/c3.js',
            'vendor/c3-angular/c3-angular.js',
            'vendor/zeroclipboard/ZeroClipboard.min.js',
            'js/bootstrap/bootstrap-fileupload.min.js',
            'vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.js',
            'vendor/bootstrap-datepicker/dist/locales/bootstrap-datepicker.fr.min.js',
            'vendor/angular-promise-tracker/promise-tracker.js',
            'vendor/angular-promise-tracker/promise-tracker-http-interceptor.js',
            'vendor/angular-growl-v2/build/angular-growl.js',
            'vendor/underscore/underscore.js',
            'vendor/ui-router/release/angular-ui-router.js',
            'vendor/angular-strap/dist/angular-strap.js',
            'vendor/angular-strap/dist/angular-strap.tpl.js',
            'vendor/angular-ui-select/dist/select.js',
            'vendor/angular-ui-tinymce/src/tinymce.js',
            'vendor/socket.io-client/socket.io.js'
          ],
          'dist/js/flot.min.js': [
            'vendor/flot/jquery.flot.js',
            'vendor/flot/jquery.flot.time.js',
            'vendor/flot/jquery.flot.pie.js',
            'vendor/flot/jquery.flot.navigate.js'
          ],
          'dist/js/jdroll.angular.min.js': [
            'vendor/leaflet/dist/leaflet.js',
            'vendor/angular-dragdrop/src/angular-dragdrop.js',
            'vendor/angular-leaflet/dist/angular-leaflet-directive.js',
            'vendor/angular-route/angular-route.js',
            'js/angular/application.js',
            'js/angular/carte/*.js',
            'js/angular/chat/*.js',
            'js/angular/stat/*.js'
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
          'dist/css/theme.min.css': [
            'css/main.css',
            'vendor/leaflet/dist/leaflet.css',
            'vendor/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.css',
            'vendor/select2/select2.css',
            'vendor/c3/c3.css',
            'css/bootstrap/*.css',
            'vendor/bootstrap-datepicker/dist/css/bootstrap-datepicker.css',
            'vendor/angular-growl-v2/build/angular-growl.min.css',
            'vendor/angular-ui-select/dist/select.min.css'
          ],
          'dist/css/jdroll.angular.min.css': [
             'vendor/bootstrap-datepicker/dist/css/bootstrap-datepicker.css',
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
        dev: ['serverp', 'watch', 'php']
    }
  });

  grunt.registerTask('serverp', ['configureProxies:server', 'connect:server']),
  grunt.registerTask('prepare', ['bower']);
  grunt.registerTask('dev', ['less', 'concurrent:dev']);
  grunt.registerTask('dist', ['less', 'cssmin', 'uglify']);
  grunt.registerTask('default', ['dev']);

};
