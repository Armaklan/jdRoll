var gulp = require('gulp');
var bower = require('gulp-bower');
var gutil = require('gulp-util');
var lr = require('tiny-lr');
var livereload = require('gulp-livereload');
var nodemon = require('gulp-nodemon');
var wait = require('gulp-wait');
var open = require('gulp-open');
var less = require('gulp-less');
var path = require('path');


var server = lr();


var htmlDir = 'public/**/*.html';
var indexDir = './public/index.html';
var nodeDir = 'lib/**/*.js';
var cssDir = 'public/**/*.css';
var lessDir = 'public/less/*.less';
var jsDir = 'public/**/*.js';
var vendorDir = '!public/vendor/**';


gulp.task('default', function() {
	gulp.run('less', 'reload', 'watch');
	gulp.run('open');
});

gulp.task('open', function() {
	var openOption = {
    	url: "http://localhost:3000"
  	};

  	gulp.src(indexDir)
		.pipe(open("", openOption));
});

gulp.task('prepare', function() {
	bower()
		.pipe(gulp.dest('./public/vendor/'));
});

gulp.task('less', function () {
  gulp.src('./public/less/*.less')
    .pipe(less({
      paths: [ path.join(__dirname, 'less', 'includes') ]
    }))
    .pipe(gulp.dest('./public/css'));
});


gulp.task('refreshNodeJs', function(){
	gulp.src(nodeDir)
		.pipe(wait(1500))
		.pipe(livereload(server));
});

gulp.task('refreshHtml', function(){
	gulp.src([htmlDir,vendorDir])
	 	.pipe(livereload(server));
});

gulp.task('refreshCss', function(){
	gulp.src([cssDir,vendorDir]).
	 	pipe(livereload(server));
});


gulp.task('refreshJs', function(){
	gulp.src([jsDir,vendorDir]).
	 	pipe(livereload(server));
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

		gulp.watch(lessDir, function() {
			gulp.run('less');
		});

		gulp.watch(jsDir, function() {
			gulp.run('refreshJs');
		});
	});
})

gulp.task('reload', function() {
	nodemon({ script: 'app.js', options: '-e js'});
})
