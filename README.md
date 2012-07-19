CodeIgniter-Akismet-Library
===========================

With the Akismet library for CodeIgniter you can easily check if messages are spam using the Akismet API. To use this library and the API you need an API key you can request at www.akismet.com

Installation
------------

Copy the files to the corresponding folder in your application folder (or use spark).

Configuration
-------------

In the akismet.php config file you can change the following configuration parameters:

	/*
	|--------------------------------------------------------------------------
	| Akismet configuration
	|--------------------------------------------------------------------------
	| This file will contain the settings for the akismet library.
	|
	| 'api_key' = your Akismet API key
	| 'blog'    = the front page or home URL of the instance making the request.
	|             LEAVE EMPTY FOR AUTOMATIC URL
	*/

	$config['api_key'] = '';
	$config['blog']    = '';
	
Note that you can leave the blog value on empty. The library will automatically use `base_url()` in this case.

Usage
-----

Before you can check for spam you need to verify your key with the Akismet API.

	$this->akismet->verify();
	
This method returns TRUE or FALSE depending on a valid key or not. When the key has been verified you can start checking spam.

	$this->akismet->check($author, $email, $content, $url = FALSE);
	
This method returns TRUE when spam is detected, FALSE otherwise.