const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const imagemin = require('gulp-imagemin');
const webp = require('gulp-webp');
const critical = require('critical').stream;

// CSS任务
gulp.task('styles', () => {
    return gulp.src('assets/scss/**/*.scss')  // 修改为正确的 SCSS 源文件路径
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(cleanCSS())
        .pipe(concat('combined.min.css'))
        .pipe(gulp.dest('assets/css'));
});

// JavaScript任务
gulp.task('scripts', () => {
    return gulp.src([
        'assets/js/src/header.js',       // 添加 header.js
        'assets/js/src/utils.js',
        'assets/js/src/performance-monitor.js',
        'assets/js/src/interactions.js',
        'assets/js/src/image-preview.js',
        'assets/js/src/social-share.js'
    ])
    .pipe(concat('combined.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest('assets/js'));
});

// 图片优化任务
gulp.task('images', () => {
    return gulp.src('assets/images/**/*')  // 修改为正确的图片源文件路径
        .pipe(imagemin([
            imagemin.mozjpeg({quality: 75, progressive: true}),
            imagemin.optipng({optimizationLevel: 5}),
            imagemin.svgo({
                plugins: [
                    {removeViewBox: false},
                    {cleanupIDs: false}
                ]
            })
        ]))
        .pipe(gulp.dest('assets/images/optimized'))  // 输出到优化后的目录
        .pipe(webp())
        .pipe(gulp.dest('assets/images/optimized/webp'));  // WebP 格式输出到单独目录
});

// 生成关键CSS
gulp.task('critical', () => {
    return gulp.src('*.php')
        .pipe(critical({
            base: './',
            inline: true,
            css: ['assets/css/combined.min.css'],
            dimensions: [
                {
                    height: 500,
                    width: 300
                },
                {
                    height: 720,
                    width: 1280
                }
            ],
            ignore: {
                atrule: ['@font-face']  // 忽略字体文件
            }
        }))
        .pipe(gulp.dest('dist'));
});

// 监视文件变化
gulp.task('watch', () => {
    gulp.watch('assets/scss/**/*.scss', gulp.series('styles'));
    gulp.watch('assets/js/src/**/*.js', gulp.series('scripts'));
    gulp.watch('assets/images/**/*', gulp.series('images'));
});

// 默认任务
gulp.task('default', gulp.parallel('watch'));  // 修改为 parallel 并只运行 watch

// 构建任务
gulp.task('build', gulp.series('styles', 'scripts', 'images', 'critical'));