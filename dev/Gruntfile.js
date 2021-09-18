/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

module.exports = function( grunt ) {

    require('load-grunt-tasks')(grunt);

    grunt.initConfig( {
        composerJson: grunt.file.readJSON('./../composer.json') || {},
        banner: '/*!\n' +
                ' * @package Goomento_PageBuilder\n' +
                ' * @link https://github.com/Goomento/PageBuilder\n' +
                ' * @version <%= composerJson.version %>\n' +
                ' */\n',
        usebanner: {
            dist: {
                options: {
                    position: 'top',
                    banner: '<%= banner %>',
                },
                files: {
                    src: [
                        './../view/frontend/web/css/*.css'
                    ]
                }
            }
        },
        sass: {
            dist: {
                options: {
                    sourceMap: true
                },
                files: [ {
                    expand: true,
                    cwd: './assets/scss/source',
                    src: ['*.scss'],
                    dest:'./../view/frontend/web/css',
                    ext: '.css'
                } ]
            }
        },
        clean: {
            options: {
                force: true
            },
            css: [
                './../view/frontend/web/css'
            ],
        },
        postcss: {
            minify: {
                options: {
                    banner: '<%= banner %>',
                    processors: [
                        require( 'autoprefixer' )( {
                            browsers: 'last 10 versions'
                        } ),
                        require( 'cssnano' )( {
                            reduceIdents: false,
                            zindex: false,
                        } )
                    ]
                },
                files: [ {
                    expand: true,
                    src: [
                        './../view/frontend/web/css/*.css',
                        './../!view/frontend/web/css/*.min.css'
                    ],
                    ext: '.min.css'
                } ]
            }
        }
    });

    grunt.registerTask('build', function () {
        grunt.task.run( 'clean' );
        grunt.task.run( 'sass' );
        grunt.task.run( 'postcss:minify' );
        grunt.task.run( 'usebanner' );
    });
}
