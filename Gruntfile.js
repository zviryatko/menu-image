'use strict';
module.exports = function(grunt){

  grunt.initConfig({
    wp_readme_to_markdown: {
      your_target: {
        files: {
          'README.md': 'readme.txt'
        },
        options: {
          screenshot_url: "https://ps.w.org/{plugin}/assets/{screenshot}.png",
        },
      },
    },
  });

  grunt.loadNpmTasks('grunt-wp-readme-to-markdown');

  grunt.registerTask('default', ['wp_readme_to_markdown']);

}
