const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const cleanCSS = require('gulp-clean-css');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');

// CSS 任务
gulp.task('css', function() {
    return gulp.src(['assets/scss/**/*.scss', 'assets/css/**/*.css'])
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(concat('combined.min.css'))
        .pipe(cleanCSS())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('assets/dist'));
});

// JavaScript 任务
gulp.task('js', function() {
    return gulp.src(['assets/js/src/**/*.js', 'assets/js/*.js', '!assets/js/*.min.js'])
        .pipe(sourcemaps.init())
        .pipe(concat('combined.min.js'))
        .pipe(uglify())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('assets/dist'));
});

// 监视文件变化
gulp.task('watch', function() {
    gulp.watch('assets/scss/**/*.scss', gulp.series('css'));
    gulp.watch('assets/css/**/*.css', gulp.series('css'));
    gulp.watch('assets/js/**/*.js', gulp.series('js'));
});

// 默认任务
gulp.task('default', gulp.series('css', 'js', function(done) {
    console.log('默认任务执行中...');
    done();
}));

// 生产构建任务
gulp.task('build', gulp.series('css', 'js', function(done) {
    console.log('生产构建任务执行中...');
    done();
}));