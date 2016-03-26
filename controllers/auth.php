<?php

class Auth extends Controller {

    function __construct() {
        parent::__construct();

    }

    public function index()
    {
    	$this->error();
    }

    public function twitter() {
    	Session::init();
        $redirect = URL.'auth/twitter';

    	if (isset($_GET['clearsessions'])) {
    		// Session::destroy();
            echo "clearsessions";
    	}

    	if (isset($_GET['connect'])) {
    		// echo "connect";

            header('Location: '.URL.'auth/twitter?login');
    	}

        // if(){
            /* Request access tokens from twitter */
            // $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
        

        if (isset($_GET['login'])) {
            // echo "login";

            $redirect = URL.'auth/twitter?callback';
            // echo WWW_APP;
            require_once( WWW_APP. 'twitterauth/init.php');

            $connection = new TwitterOAuth(TWITTER_APP_ID, TWITTER_APP_SECRET);

            $request_token = $connection->getRequestToken( $redirect );

            /* Save temporary credentials to session. */
            $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
            $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

            /* If last connection failed don't display authorization link. */
            switch ($connection->http_code) {
                case 200:
                    /* Build authorize URL and redirect user to Twitter. */

                    $url = $connection->getAuthorizeURL($token);
                    header('Location: ' . $url);
                    break;
                default:
                    /* Show notification if something went wrong. */
                    echo 'Could not connect to Twitter. Refresh the page or try again later.';
            }
        }

        if (isset($_GET['callback'])) {
            require_once( WWW_APP. 'twitterauth/init.php');

            /* If the oauth_token is old redirect to the connect page. */
            if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
                $_SESSION['oauth_status'] = 'oldtoken';
                header('Location:'.$redirect.'?clearsessions');
            }

            /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */

            $oAuthScreenName = $_SESSION['oauth_token_secret'];
            $connection = new TwitterOAuth(TWITTER_APP_ID, TWITTER_APP_SECRET, $_SESSION['oauth_token'], $oAuthScreenName);

            /* Request access tokens from twitter */
            $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);


            // $status = $connection->get('account/verify_credentials', array('screen_name'=>$oAuthScreenName, 'include_email'=>'true'));

            /* Remove no longer needed request tokens */
            unset($_SESSION['oauth_token']);
            unset($_SESSION['oauth_token_secret']);

            $this->view->data =array(
                'type' => 'twitter',
                'id' => $access_token['user_id'],
                'name' => $access_token['screen_name'],
                'email' => isset($access_token['email'])? $access_token['email']: '',
                'picture' => isset($access_token['picture'])? $access_token['picture']: ''
            );
            $this->view->render('auth/callback', true);
        }
    }

    public function google() {
        if (isset($_GET['login'])) {
            require_once WWW_APP.'googleauth/oauth_client.php';
            require_once WWW_APP.'googleauth/http.php';
            $client = new oauth_client_class;
            $redirect_uri = URL."auth/google?login";

            // set the offline access only if you need to call an API
            // when the user is not present and the token may expire
            $client->offline = FALSE;

            $client->debug = false;
            $client->debug_http = true;
            $client->redirect_uri = $redirect_uri;

            $client->client_id = GOOGLE_CLIENT_ID;
            $application_line = __LINE__;
            $client->client_secret = GOOGLE_CLIENT_SECRET;

            if (strlen($client->client_id) == 0 || strlen($client->client_secret) == 0)
                die('Please go to Google APIs console page ' .
                      'http://code.google.com/apis/console in the API access tab, ' .
                      'create a new client ID, and in the line ' . $application_line .
                      ' set the client_id to Client ID and client_secret with Client Secret. ' .
                      'The callback URL must be ' . $client->redirect_uri . ' but make sure ' .
                      'the domain is valid and can be resolved by a public DNS.');

            /* API permissions */
            $client->scope = GOOGLE_SCOPES;

            if (($success = $client->Initialize())) {

                if (($success = $client->Process())) {

                    if (strlen($client->authorization_error)) {
                        $client->error = $client->authorization_error;
                        $success = false;
                    } elseif (strlen($client->access_token)) {
                        $success = $client->CallAPI(
                          'https://www.googleapis.com/oauth2/v1/userinfo', 'GET', array(), array('FailOnAccessError' => true), $user);
                    }
                }
                
                $success = $client->Finalize($success);

            }

            if ($client->exit){

                echo 'exit'; die;
                exit;
            } 

            if ($success) {

                try {

                    // id, email, verified_email, name, picture, locale
                    $this->view->data = array(
                        'type' => 'google',
                        'id' => $user->id,
                        'email' => $user->email,
                        'verified_email' => $user->verified_email,
                        'name' => $user->name,
                        'picture' => $user->picture,
                        'locale' => $user->locale,
                    );
                    $this->view->render('auth/callback', true);
                } catch (Exception $ex) {
                    $error = $ex->getMessage();
                    // print_r($error);
                }
                
            }
            else{

                header("Location:https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=". urlencode($redirect_uri)  );
                // echo 'not success';
            }

        // end login
        }else{
            
        }
    }

}