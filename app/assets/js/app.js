console.log('app.js loaded');
var current_user = {};

var app = angular.module('pickCoolApp', ['ezfb', 'ui.router', 'ng', 'angular-inview', 'ngCookies']);

app.constant('angularMomentConfig', {
  preprocess: 'unix', // optional
  timezone: 'America/Los_Angeles' // optional
});

