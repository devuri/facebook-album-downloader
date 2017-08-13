<?php

namespace FacebookAlbumDownloader\Helper;

class FacebookClient
{
    protected $appID = '<APP-ID>';
    protected $appSecret = '<APP-SECRET>';
    protected $graphVersion = 'v2.10';
    protected $redirectedUri = '<REDIRECTED-URI>';
    
    protected $fb;
    protected $helper;

    /**
     *   Construct an easy to use Facebook API client.
     */
    public function __construct()
    {
        $this->fb    = new \Facebook\Facebook([
                            'app_id' => $this->appID,
                            'app_secret' => $this->appSecret,
                            'default_graph_version' => $this->graphVersion,
                        ]);
        $this->helper = $this->fb->getRedirectLoginHelper();
    }

    public function authenticate()
    {
        try {
            if (isset($_SESSION['FACEBOOK_ACCESS_TOKEN'])) {
                $accessToken = $_SESSION['FACEBOOK_ACCESS_TOKEN'];
            } else {
                $accessToken = $this->helper->getAccessToken();
            }
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        if (isset($accessToken)) {
            if (isset($_SESSION['FACEBOOK_ACCESS_TOKEN'])) {
                $this->fb->setDefaultAccessToken($_SESSION['FACEBOOK_ACCESS_TOKEN']);
            } else {
                // getting short-lived access token
                $_SESSION['FACEBOOK_ACCESS_TOKEN'] = (string) $accessToken;

                // OAuth 2.0 client handler
                $oAuth2Client = $this->fb->getOAuth2Client();

                // Exchanges a short-lived access token for a long-lived one
                $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['FACEBOOK_ACCESS_TOKEN']);

                $_SESSION['FACEBOOK_ACCESS_TOKEN'] = (string) $longLivedAccessToken;

                // setting default access token to be used in script
                $fb->setDefaultAccessToken($_SESSION['FACEBOOK_ACCESS_TOKEN']);
            }
            return true;
        } else {
            return false;
        }
    }

    public function getAuthUrl()
    {
        return $this->helper->getLoginUrl($this->redirectedUri);
    }

    public function getUserDetails()
    {
        try {
            $profile_request = $this->fb->get('/me?fields=name,first_name,last_name,email');
            return  $profile_request->getGraphNode()->asArray();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        return false;
    }

    public function getAlbums()
    {
        try {
            $response = $this->fb->get('/me/albums?fields=count,name,cover_photo{id}');  
            return $response->getGraphEdge()->asArray();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          echo 'Graph returned an error: ' . $e->getMessage();
          exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
          exit;
        }
        return false;
    }

    public function getLowestResolutionImage($imageId){
        try {
            $response = $this->fb->get('/'.$imageId.'?fields=images');
            return end($response->getGraphNode()->asArray()['images'])['source'];
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          echo 'Graph returned an error: ' . $e->getMessage();
          exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
          exit;
        }
        return false;
    }

    public function getAlbum($albumId)
    {
        try {
            $response = $this->fb->get('/'.$albumId.'?fields=name,photos{images,name,created_time}');  
            return $response->getGraphNode()->asArray();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          echo 'Graph returned an error: ' . $e->getMessage();
          exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
          exit;
        }
        return false;
    }


    public function getUsername($profileID)
    {
        $login_email = "<LOGIN-EMAIL>";
        $login_pass = "<LOGIN-PASSWORD>";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.facebook.com/login.php');
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'email='.urlencode($login_email).'&pass='.urlencode($login_pass));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__."/cookies.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__."/cookies.txt");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) 
                        AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36");
        curl_setopt($ch, CURLOPT_REFERER, "http://www.facebook.com");
        $page = curl_exec($ch) or die(curl_error($ch));
        curl_setopt($ch, CURLOPT_URL, 'http://www.facebook.com/'.$profileID);
        $page = curl_exec($ch) or die(curl_error($ch));
        $effetive_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $username_url = explode('/', $effetive_url);
        curl_close($ch);
        $username = $username_url[count($username_url) - 1];
        if (substr($username, 0, 11)=='profile.php') {
            return $profileID;
        }
        return $username;
    }
}
