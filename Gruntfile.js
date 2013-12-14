module.exports = function(grunt) {

	grunt.loadNpmTasks('grunt-bower-task');

	grunt.initConfig({
		bower: {
		    install: {
		        options: {
			        targetDir:'./public/vendor/',
			        layout: 'byType'
			    }
		    }
		  }

	});

	grunt.registerTask('prepare', ['bower']);

};

