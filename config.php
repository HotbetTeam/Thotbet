<?php

define('PAGE_TITLE', 'Hotbet.casino');
define('PAGE_ADDRESS', '');
define('PAGE_ANCHOR_DATE', 2016);

// Always provide a TRAILING SLASH (/) AFTER A PATH
define('URL', 'http://localhost/hotbet/');

define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'hotbet');
define('DB_USER', 'root');
define('DB_PASS', '');

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('WWW_LIBS', ROOT . DS . "libs" . DS);
define('WWW_APP', WWW_LIBS. 'app'. DS);
define('WWW_DOCS', ROOT . DS . "public". DS. 'docs' . DS);
define('WWW_VIEW', ROOT . DS . 'views' . DS);
define('WWW_IMAGES', ROOT . DS . 'public' . DS. 'images' . DS );
define('WWW_IMAGES_AVATAR', WWW_IMAGES. 'avatar' . DS);
define('WWW_IMAGES_PRODUCTS', WWW_IMAGES. 'products' . DS);

define('LIBS', 'libs/');
define('DOCS', URL . 'public/docs/');
define('CSS', URL . 'public/css/');
define('JS', URL . 'public/js/');
define('IMAGES', URL . 'public/images/');
define('IMAGES_PRODUCTS', IMAGES . 'products/');
define('COOKIE_KEY', 'u_id');
define('COOKIE_KEY_PARTNER', 'p_id');
define('COOKIE_KEY_ADMIN', 'a_id');

// The sitewide hashkey, do not change this because its used for passwords!
// This is for other hash keys... Not sure yet
define('HASH_GENERAL_KEY', 'MixitUp200');

// This is for database passwords only
define('HASH_PASSWORD_KEY', 'catsFLYhigh2000miles');

// RECAPTCHA
define('RECAPTCHA_SITE_KEY', '6LfPBxMTAAAAALX9MpBvvR2sjCKZidyhU-YXYHCY');
define('RECAPTCHA_SECRET_KEY', '6LfPBxMTAAAAACav7aO-axpuFK6r_fDphq6gAs4i');

// Chat Server
define('FIREBASE_SERVER', '/hotbet/');

/**/
/* App */
/**/

// facebook
define('FACEBOOK_APP_ID', "1683578778576577");
define('FACEBOOK_APP_SECRET', "a1b95512e829e4f4eea10d7c476f3059");
define('FACEBOOK_API_VERSION', "v2.5");
define('FACEBOOK_REDIRECT_URL', URL);

// google
define('GOOGLE_CLIENT_ID', "617298172645-5hsnmpg445shr0rv94omae1ne9gvjrqe.apps.googleusercontent.com");
define('GOOGLE_CLIENT_SECRET', "8O8vtwLdy6BNu4JhbYuopHyL");
define("GOOGLE_SCOPES", 'https://www.googleapis.com/auth/userinfo.email '.
		'https://www.googleapis.com/auth/userinfo.profile' );

// twitter
define('TWITTER_APP_ID', "QI8LNsBz6lC9s7dNkzO0dynxD");
define('TWITTER_APP_SECRET', "t5jEzafyG4Vmhi1E4Nj8BPOFRmgkAMZ1lHZ6pKeaNbSGmgkIi1");
define('TWITTER_APP_OAUTH_TOKEN', "zV5Y8wAAAAAAkKy-AAABUs5KMlw");
define('TWITTER_APP_OAUTH_CALLBACK', "http://the8chiangmai.com/hotbat/twitter/tw_redirect.php?callback");