const gulp = require('gulp');
const gulpRename = require('gulp-rename');
const gulpReplace = require('gulp-replace');
const Entities = require('html-entities').AllHtmlEntities;
const pug = require('gulp-pug');
const entities = new Entities();
let templateConfig;

try {
    templateConfig = require('./common/mail/source/config.js');
} catch (e) {
    console.log(e)
}

gulp.task('email', function () {
    if (!templateConfig) {
        return;
    }
    return gulp.series(
        'pug_templates',
        'compile_template-html',
        'compile_template-php',
        'compile_template-text',
        'copy-images'
    ).call();
});

gulp.task('pug_templates', () => {
    return gulp.src('common/mail/source/*.pug')
        .pipe(pug({
            basedir: '.',
            pretty: true

        }))
        .pipe(gulp.dest('common/mail/source'));
});

gulp.task('compile_template-html', () => {
    return generateHTML()
        .pipe(gulp.dest('common/mail'));
});

gulp.task('compile_template-text', () => {
    const htmlClean = generateHTML();
    return cleanHTML(htmlClean).pipe(gulpRename(function (path) {
        path.basename += "-text";
        path.extname = ".php";
    }))
        .pipe(gulp.dest('common/mail'));
});

gulp.task('compile_template-php', () => {
    return generatePHP().pipe(gulpRename(function (path) {
        path.basename += "-html";
        path.extname = ".php";
    })).pipe(gulp.dest('common/mail'));
});

gulp.task('copy-images', function () {
    return gulp.src(
        [
            'common/mail/source/images/**/*'
        ],
        {
            "dot": true,
            "nodir": true,
            "base": ""
        })
        .pipe(gulp.dest('htdocs/images/mail'));
});


function cleanHTML(html) {
    html = html
        .pipe(gulpReplace(/&[a-z]+;/gi, function (match, entity) {
            return entities.decode(match);
        }))
        .pipe(gulpReplace(/<[^>]*>/g, ''))
        .pipe(gulpReplace(/(\n\s*?\n)\s*\n/gi, '$1'))
        .pipe(gulpReplace(/ +/g, ' '))
        .pipe(gulpReplace(/^ +/gm, ''))
        .pipe(gulpReplace(/\n\s+/gi, '\n'))
    return html;
}


function generateHTML() {
    let html = gulp.src(['common/mail/source/*.html']);
    Object.keys(templateConfig).forEach((key) => {
        const type = templateConfig[key];
        Object.keys(type).forEach((key) => {
            const settings = type[key];
            html = html.pipe(gulpReplace(`{{${ key }}}`, function () {
                return settings.html || '';
            }));
        });
    });
    return html.pipe(gulp.dest('common/mail'));
}

function generatePHP() {
    let php = gulp.src(['common/mail/source/*.html']);
    Object.keys(templateConfig).forEach((key) => {
        const type = templateConfig[key];
        Object.keys(type).forEach((key) => {
            const settings = type[key];
            php = php.pipe(gulpReplace(`{{${ key }}}`, function () {
                return settings.php || '';
            }));
        });
    });

    return php;
}