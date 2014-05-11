var gulp = require('gulp');
var less = require('gulp-less');
var watch = require('gulp-watch');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var path = require('path');
var minifyCSS = require('gulp-minify-css');
var livereload = require('gulp-livereload');
var templates = require('gulp-angular-templatecache');
var minifyHTML = require('gulp-minify-html');


/**************************************
*  Main Task
************************************* */

gulp.task('default', ['less'], function(){

    // Survey change
     gulp.watch('./web/less/*.less', function() {
         gulp.start('less');
     });

    gulp.start('livereload');
});

gulp.task('dist', ['less', 'templates'], function() {
    gulp.src([
      'web/js/**/*.js',
      'web/js/*.js',
      '!web/js/jdroll.min.js'
    ])
    .pipe(concat('jdroll.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest('./web/js'));
});


/**************************************
*  Utility Task
************************************* */

gulp.task('less', function () {
    gulp.src('./web/less/*.less')
    .pipe(less({
        paths: [ path.join(__dirname, 'less', 'includes') ]
    }))
    .pipe(minifyCSS({keepBreaks:false}))
    .pipe(gulp.dest('./web/css'));
});

gulp.task('livereload', function() {
  var server = livereload();

  gulp.watch('web/**/*.js').on('change', function(file) {
      server.changed(file.path);
  });

  gulp.watch('web/**/*.css').on('change', function(file) {
      server.changed(file.path);
  });

  gulp.watch('api/**/*.php').on('change', function(file) {
      server.changed(file.path);
  });

  gulp.watch('web/**/*.html').on('change', function(file) {
      server.changed(file.path);
  });
});

gulp.task('templates', function () {
  gulp.src([
      './web/views/**/*.html',
      './web/views/*.html'
    ])
    .pipe(minifyHTML({
      quotes: true
    }))
    .pipe(templates('templates.min.js'))
    .pipe(gulp.dest('./web/js'));
});

