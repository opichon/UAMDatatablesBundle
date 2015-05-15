var bower = require('gulp-bower');
var gulp = require('gulp');
var jsmin = require('gulp-jsmin');
var mainBowerFiles = require('main-bower-files');
var rename = require('gulp-rename');

gulp.task('bower', function() {
	bower();
});

gulp.task('bower-prune', ['bower'], function() {
	return bower({ cmd: 'prune' })
});

gulp.task('bower-files', ['bower-prune'], function() {
	return gulp.src(mainBowerFiles(), { base: 'bower_components' })
		.pipe(gulp.dest('../public/vendor'));

});

gulp.task('minify', ['bower-files'], function() {
	return gulp.src([
			'../public/vendor/datatables-plugins/pagination/*.js',
			'../public/vendor/datatables-plugins/pagination/jPaginator/*.js',
			'!../public/vendor/**/*min.js'
		], { base: '../public/vendor' })
		.pipe(jsmin())
		.pipe(rename({suffix: '.min'}))
		.pipe(gulp.dest('../public/vendor'));
});

gulp.task('default', ['minify'])
