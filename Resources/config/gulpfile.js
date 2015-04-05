var gulp = require('gulp');
var bower = require('gulp-bower');
var mainBowerFiles = require('main-bower-files');
var jsmin = require('gulp-jsmin');
var rename = require('gulp-rename');

gulp.task('bower', function() {
	bower();
});

gulp.task('bower-files', ['bower'], function() {
	return gulp.src(mainBowerFiles(), { base: 'bower_components' })
		.pipe(gulp.dest('../public/vendor'));

});

gulp.task('minify', ['bower-files'], function() {
	return gulp.src([
			'../public/vendor/datatables-plugins/**/*.js',
			'!../public/vendor/**/*min.js'
		], { base: '../public/vendor' })
		.pipe(jsmin())
		.pipe(rename({suffix: '.min'}))
		.pipe(gulp.dest('../public/vendor'));
});

gulp.task('default', ['minify'])
