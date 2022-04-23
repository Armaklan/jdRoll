module.exports = function(grunt) {

    grunt.loadNpmTasks('grunt-php');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-bower-task');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-concurrent');
    grunt.loadNpmTasks('grunt-contrib-connect');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-connect-proxy');
    grunt.loadNpmTasks("grunt-rollup");

    grunt.initConfig({
      connect: {
        server: {
          options: {
            port: 7000,
            base: ".",
            logger: "dev",
            keepalive: true,
            hostname: "localhost",
            middleware: function (connect, options, defaultMiddleware) {
              var proxy = require("grunt-connect-proxy/lib/utils").proxyRequest;
              return [
                // Include the proxy first
                proxy,
              ].concat(defaultMiddleware);
            },
          },
          proxies: [
            {
              context: "/socket.io",
              host: "localhost",
              port: 5000,
              changeOrigin: false,
            },
            {
              context: "/apiv2",
              host: "localhost",
              port: 5000,
              changeOrigin: false,
            },
            {
              context: "/",
              host: "localhost",
              port: 8010,
              changeOrigin: false,
            },
          ],
        },
      },
      php: {
        test: {
          options: {
            keepalive: true,
            open: false,
            port: 8010,
          },
        },
      },
      bower: {
        install: {
          options: {
            copy: false,
          },
        },
      },
      watch: {
        style: {
          files: "less/*.less",
          tasks: ["less"],
        },
        js: {
          files: "**/*.js",
          tasks: ["concat:dev"],
        },
      },
      concurrent: {
        dev: ["serverp", "watch", "php"],
      },
    });

    grunt.registerTask('serverp', ['configureProxies:server', 'connect:server']);
    grunt.registerTask('prepare', ['bower']);
    grunt.registerTask("dev", ["concurrent:dev"]);
    grunt.registerTask('dist', ['cssmin']);
    grunt.registerTask('default', ['dev']);

};
