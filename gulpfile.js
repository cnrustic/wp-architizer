const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const cleanCSS = require('gulp-clean-css');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const critical = require('critical').stream;

// CSS任务
gulp.task('styles', async () => {
    const autoprefixer = (await import('gulp-autoprefixer')).default;
    
    return gulp.src('assets/scss/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(cleanCSS())
        .pipe(concat('combined.min.css'))
        .pipe(gulp.dest('assets/css'));
});

// JavaScript任务
gulp.task('scripts', () => {
    return gulp.src([
        'assets/js/src/header.js',
        'assets/js/src/utils.js',
        'assets/js/src/performance-monitor.js',
        'assets/js/src/interactions.js',
        'assets/js/src/image-preview.js',
        'assets/js/src/social-share.js',
        'assets/js/src/home.js'
    ])
    .pipe(concat('combined.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest('assets/js'));
});

// 图片优化任务
gulp.task('images', async () => {
    const imagemin = (await import('gulp-imagemin')).default;
    const webp = (await import('gulp-webp')).default;
    
    return gulp.src('assets/images/**/*')
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
        .pipe(gulp.dest('assets/images/optimized'))
        .pipe(webp())
        .pipe(gulp.dest('assets/images/optimized/webp'));
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
                atrule: ['@font-face']
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
gulp.task('default', gulp.parallel('watch'));

// 构建任务
gulp.task('build', gulp.series('styles', 'scripts', 'images', 'critical'));