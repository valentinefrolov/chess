process.env.NODE_ENV = 'production';
process.env.NODE_ENV = 'development';

const gulp = require('gulp');
//const uglify = require('gulp-uglify');
const webpack = require('webpack-stream');
const sass = require('gulp-sass');
const minify = require('gulp-clean-css');
const autoprefixer = require('gulp-autoprefixer');
const sourcemaps = require('gulp-sourcemaps');
const bulkSass = require('gulp-sass-bulk-import');
const livereload = require('gulp-livereload');
const inlineSvg = require('gulp-inline-svg');
const image = require('gulp-image');
const cache = require('gulp-cached');
const include = require('gulp-include');


function swallowError (error) {
    console.error(error.toString());
    this.emit('end');
}

gulp.task('inline-svg', function(done) {
    return gulp.src("svg/**/*.svg")
        .pipe(inlineSvg({filename: '_svg.scss', template: 'inline-svg.mustache'})).on('error', swallowError)
        .pipe(gulp.dest("scss")).on('error', swallowError)
        .pipe(livereload());
});

gulp.task('default', function(done) {
    return gulp.src('js/**/*.js')
        .pipe(webpack(require('./webpack.config'))).on('error', swallowError)
        .pipe(gulp.dest('docs/js')).on('error', swallowError)
        .pipe(livereload({start: true}));
});

gulp.task('compress', function(done) {
    return gulp.src(['i/**/*.jpeg', 'i/**/*.jpg', 'i/**/*.png', 'i/**/*.svg'])
        .pipe(cache('compress')).on('error', swallowError)
        .pipe(image()).on('error', swallowError)
        .pipe(gulp.dest('docs/i')).on('error', swallowError)
        .pipe(livereload({start: true}));
});

gulp.task('scss', function(done){
    return gulp.src('scss/**/*.scss')
        .pipe(sourcemaps.init())
        .pipe(bulkSass()).on('error', swallowError)
        .pipe(sass({includePaths: ['scss']})).on('error', swallowError)
        .pipe(autoprefixer()).on('error', swallowError)
        .pipe(minify()).on('error', swallowError)
        .pipe(sourcemaps.write('maps')).on('error', swallowError)
        .pipe(gulp.dest('docs/css')).on('error', swallowError)
        .pipe(livereload());
});

gulp.task('reload', function(done){
    livereload.reload();
    done();
});

gulp.task('watch', function(){
    livereload.listen();
    gulp.watch('scss/**/*.scss', {usePolling: true}, gulp.series('scss'));
    gulp.watch('js/**/*.js', {usePolling: true}, gulp.series('default'));
    gulp.watch(['i/**/*.jpeg', 'i/**/*.jpg', 'i/**/*.png', 'i/**/*.svg'], {usePolling: true}, gulp.series('compress'));
    gulp.watch("svg/**/*.svg", {usePolling: true}, gulp.series('inline-svg'));
    gulp.watch(['docs/**/*.html', 'docs/**/*.php'], {usePolling: true}, gulp.series('reload'));
});


