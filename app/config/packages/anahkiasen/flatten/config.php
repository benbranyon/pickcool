<?php return array(

	// Whether Flatten should be enabled in this environment
	'enabled' => env('USE_FLATTEN',false),

	// Cache checks
	//
	// Various checks that are used to see if Flatten should run
	////////////////////////////////////////////////////////////////////

	// The different pages to be ignored when caching
	// They're all regexes so go crazy
	'ignore'       => [],

	// List only specific pages to cache, useful if you have a lot of
	// pages you don't want to see cached
	// The ignored pages will still be substracted from this array
	'only'         => [
	  '\/hot$',
	  '\/new$',
	  '\/top$',
	],

	// Here you can put variables that will be taken into account when
	// checking if Flatten should run. If the sum of the array is not
	// "true", then Flatten won't start
	'blockers'     => array(),

	// Cache variables
	////////////////////////////////////////////////////////////////////

	// The default period during which a cached page should be kept (in minutes)
	// 0 means the page never gets refreshed by itself
	'lifetime'     => 0,

	// An array of string or variables to add to the salt being used
	// to differentiate pages
	'saltshaker'   => array(),
);
