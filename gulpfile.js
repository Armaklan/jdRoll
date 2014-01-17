var gulp = require('gulp');
var bower = require('gulp-bower');
var gutil = require('gulp-util');
var lr = require('tiny-lr');
var livereload = require('gulp-livereload');
var nodemon = require('gulp-nodemon');
var wait = require('gulp-wait');
var open = require('gulp-open');

var server = lr();


var htmlDir = 'public/**/*.html';
var nodeDir = 'lib/**/*.js';
var cssDir = 'public/**/*.html';


gulp.task('default', function() {
	gulp.run('reload', 'watch');
	gulp.run('open');
});

gulp.task('open', function() {
	var openOption = {
    	url: "http://localhost:3000",
    	app: "firefox"
  	};

  	gulp.src("./public/index.html")
		.pipe(open("", openOption));
});

gulp.task('prepare', function() {
	bower()
		.pipe(gulp.dest('./public/vendor/'));
});

gulp.task('refreshNodeJs', function(){
	gulp.src(nodeDir)
		.pipe(wait(1500))
		.pipe(livereload(server));
});

gulp.task('refreshHtml', function(){
	gulp.src(htmlDir).
	 	pipe(livereload(server));
});

gulp.task('refreshCss', function(){
	gulp.src(cssDir).
	 	pipe(livereload(server));
});

gulp.task('test', function(){
	console.log('restart');
});

gulp.task('watch', function() {
	server.listen(35729, function(err) {
		if (err) return console.log(err);


		gulp.watch(nodeDir, function() {
			gulp.run('refreshNodeJs');
		});

		gulp.watch(htmlDir, function() {
			gulp.run('refreshHtml');
		});

		gulp.watch(cssDir, function() {
			gulp.run('refreshCss');
		});
	});
})

gulp.task('reload', function() {
	nodemon({ script: 'app.js', options: '-e js'});
})
