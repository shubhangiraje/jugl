var gulp = require('gulp');
var plugins = require('gulp-load-plugins')();
var merge = require('merge-stream');

var app_files=[
    'app/app.js',
    'app/**/*.js'
];

gulp.task('appjs', function () {
    return gulp.src(app_files)
        //.pipe(plugins.plumber(function(error) {plugins.util.colors.red(error.message);this.emit('end');plugins.runSequence('appjs');}))
        .pipe(plugins.jshint())
        .pipe(plugins.jshint.reporter('default'))
        .pipe(plugins.sourcemaps.init())
        .pipe(plugins.concat('app.js'))
        .pipe(plugins.sourcemaps.write('maps'))
        .pipe(gulp.dest('static/build'));
});

gulp.task('smiles',function() {
    return  gulp.src('scss/*.scss')
        .pipe(plugins.compass({
            config_file: './compass-config.rb',
            project: __dirname,
            css: 'static/css',
            sass: 'scss',
            image: 'static/images'
        }))
        .pipe(gulp.dest('static/build'));
});

gulp.task('appjsmin', function () {
    return gulp.src(app_files)
        .pipe(plugins.sourcemaps.init())
        .pipe(plugins.concat('app.js'))
        .pipe(plugins.ngAnnotate())
        .pipe(plugins.uglify())
        .pipe(plugins.sourcemaps.write('maps'))
        .pipe(gulp.dest('static/build'));
});

gulp.task('pot', function () {
    return gulp.src(['app/**/*.js','../views/app-view/*.php'])
        .pipe(plugins.angularGettext.extract('web2.pot', {
            // options to pass to angular-gettext-tools...
        }))
        .pipe(gulp.dest('po/'));
});

gulp.task('po', function () {
    return gulp.src('po/*.po')
        .pipe(plugins.angularGettext.compile({
            // options to pass to angular-gettext-tools...
        }))
        .pipe(plugins.concat('translations.js'))
        .pipe(gulp.dest('static/build/'));
});

gulp.task('i18n',function() {
    return gulp.src([
        'bower_components/angular-i18n/angular-locale_de.js',
        'bower_components/angular-i18n/angular-locale_ru.js',
        'bower_components/angular-i18n/angular-locale_en.js'
    ])
        .pipe(gulp.dest('static/build/'));
});


gulp.task('bower_components', function () {
    return merge(gulp.src([
            'bower_components/angular/angular.js',
            'bower_components/angular-ui-router/release/angular-ui-router.js',
            'bower_components/angular-once/once.js',
            'bower_components/es5-shim/es5-shim.js',
            'bower_components/es5-shim/es5-sham.js',
            'bower_components/angular-file-upload/dist/angular-file-upload.min.js',
            'bower_components/angular-touch/angular-touch.js',
            'bower_components/socket.io-client/socket.io.js',
            'bower_components/iCheck/icheck.js',
            'bower_components/fancybox/source/jquery.fancybox.js',
            'bower_components/bootstrap-select/dist/js/bootstrap-select.js',
            'bower_components/angular-bootstrap-select/src/angular-bootstrap-select.js',
            'bower_components/jscrollpane/script/jquery.mousewheel.js',
            'bower_components/jscrollpane/script/jquery.jscrollpane.js',
            'bower_components/angular-scroll-pane/dist/angular-jscrollpane.js',
            'bower_components/angular-gettext/dist/angular-gettext.js',
            'bower_components/angular-sanitize/angular-sanitize.js',
            'bower_components/angular-cookies/angular-cookies.js',
            'bower_components/angular-animate/angular-animate.js',
            'bower_components/angular-dynamic-locale/dist/tmhDynamicLocale.js',
            'bower_components/re-tree/re-tree.js',
            'bower_components/ng-device-detector/ng-device-detector.js',
            'bower_components/ngstorage/ngStorage.js',
            'bower_components/ngstorage/ngStorage.js',
            'static/js/angular-facebook-pixel.js'
        ])
        .pipe(plugins.sourcemaps.init({loadMaps: true}))
        .pipe(plugins.concat('bower_components.js'))
        .pipe(plugins.ngAnnotate())
        .pipe(plugins.uglify())
        .pipe(plugins.sourcemaps.write('maps'))
        .pipe(gulp.dest('static/build'))
    ,
        gulp.src([
            'bower_components/fancybox/source/*.gif',
            'bower_components/fancybox/source/*.png',
            'bower_components/fancybox/source/*.css',
        ])
        .pipe(gulp.dest('static/build/fancybox'))
    );
});

gulp.task('deploy', ['appjs','po'], function () {
});

gulp.task('default', ['bower_components','deploy', 'i18n'], function () {
    gulp.watch([
        'app/**/*.js'
    ], ['appjs']);
});
