# AgriLife Today
[![Codeship Status for AgriLife/agrilife-today](https://app.codeship.com/projects/e79b2410-2ee2-0137-03f5-66eddf10bdad/status?branch=master)](https://app.codeship.com/projects/331732)

This WordPress theme provides functionality and visual styles for the AgriLife Today website.

## WordPress Requirements

1. Genesis theme
2. Jetpack by WordPress.com plugin, for the Related Posts feature
3. PHP 5.6+, tested with PHP 7.2

## Installation

1. [Download the latest release](https://github.com/agrilife/agrilife-today/releases/latest)
2. Upload the theme to your site

## Features
1. Related Posts

## Development Installation

1. Copy this repo to the desired location.
2. In your terminal, navigate to the plugin location 'cd /path/to/the/plugin'
3. Run "npm start" to configure your local copy of the repo, install dependencies, and build files

## Development Notes

When you stage changes to this repository and initiate a commit, they must pass PHP and Sass linting tasks before they will complete the commit step. Sass rules can be found and modified in the .sass-lint.yml file.

Release tasks in Gruntfile.coffee can only be used by the repository's owners.

## Development Tasks

1. Run "grunt develop" to compile the css when developing the plugin.
2. Run "grunt watch" to automatically compile the css after saving a *.scss file.
3. Run "grunt" to compile the css when publishing the plugin.
4. Run "npm run checkwp" to check PHP files against WordPress coding standards.
5. Run "npm run sassdev" to compile sass for a development environment if you don't wish to use Grunt.
6. Run "npm run sasspkg" to compile sass for a production environment if you don't wish to use Grunt.

## Development Requirements

* Node: http://nodejs.org/
* NPM: https://npmjs.org/
* Ruby: http://www.ruby-lang.org/en/, version >= 2.0.0p648
* Ruby Gems: http://rubygems.org/
* Ruby Sass: version >= 3.4.22

## To do
### Global
Reduce width of content areas to that of the single page template for genesis layout of full-width only
Automatically remove leading spaces from post excerpts

### Home
Add 2 lines of description to centered aside posts on home page

### Single Page
Fix related post issue on single page, example: https://agtodaydev.wpengine.com/2019/10/11/new-texas-am-dual-purpose-cotton-variety-can-be-used-for-food-fiber/
Missing events calendar

### Category archive pages
Make story title font match home page story title font attributes

### Search page
Make the search box look better
