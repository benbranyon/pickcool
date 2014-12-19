module.exports = function(grunt) {
  grunt.initConfig({
    copy: {  
      bootstrap: {
        expand: true,
        cwd: 'bower_components/bootstrap/fonts/',
        src: '**',
        dest: './app/storage/assets/fonts/',
        flatten: true,
        filter: 'isFile',
      },
      fontawesome: {
        expand: true,
        cwd: 'bower_components/fontawesome/fonts/',
        src: '**',
        dest: './app/storage/assets/fonts/',
        flatten: true,
        filter: 'isFile',
      },
    },      
    sass: {
      development: {
        options: {
          sourcemap: 'none'
        },
        files: {
          "./app/storage/grunt-tmp/assets/css/style.css":"./app/assets/css/style.scss",
        }
      }
    },
    jsvalidate: {
      options:{
        globals: {},
        esprimaOptions: {},
        verbose: false
      },
      targetName:{
        files:{
          src:[
            './app/assets/**/*.js',
          ]
        }
      }
    },
    
    concat: {
      options: {
        sourceMap: false,
      },
      js: {
        options: {
          separator: ';',
        },
        src: [
          './bower_components/jquery/dist/jquery.js',
          './bower_components/bootstrap/dist/js/bootstrap.min.js',
          './bower_components/angularjs/angular.js',
          './bower_components/angular-sanitize/angular-sanitize.js',
          './bower_components/angular-ui-router/release/angular-ui-router.js',
          './bower_components/angular-flash/dist/angular-flash.js',
          './bower_components/angular-easyfb/angular-easyfb.js',
          './bower_components/spin.js/spin.js',
          './bower_components/ladda/js/ladda.js',
          './bower_components/ladda/js/ladda.jquery.js',
          './bower_components/angular-ladda/src/angular-ladda.js',
          './app/assets/js/app.js',
          './app/assets/js/**',
        ],
        dest: './app/storage/assets/js/app.js',
        nonull: true,
      },
      js_ie_compat: {
        options: {
          separator: ';',
        },
        src: [
          './bower_components/respond/dest/respond.src.js',
          './bower_components/html5shiv/dist/html5shiv.js',
        ],
        dest: './app/storage/assets/js/ie_comapt.js',
        nonull: true,
      },
      css: {
        src: [
          './bower_components/bootstrap/dist/css/bootstrap.min.css',
          './bower_components/fontawesome/css/font-awesome.css',
          './bower_components/ladda/dist/ladda-themeless.min.css',
          './app/storage/grunt-tmp/assets/css/style.css',
        ],
        dest: './app/storage/assets/css/app.css',
        nonull: true,
      },
    },
    watch: {
      assets: {
        files: [
          './bower_components/**',
          "./app/assets/**",
        ],   
        tasks: ['init'],     //tasks to run
        options: {
          livereload: true                        //reloads the browser
        }
      },
    },
    run: {
      options: {
        // Task-specific options go here.
      },
      artisan: {
        cmd: './artisan',
        args: [
          'cache:views:clear',
        ]
      }
    }
  });
    

  // Plugin loading
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-run');
  grunt.loadNpmTasks('grunt-jsvalidate');
  

  // Task definition
  grunt.registerTask('init', ['jsvalidate', 'sass', 'concat', 'copy', 'run',]);
  grunt.registerTask('default', ['watch']);

};