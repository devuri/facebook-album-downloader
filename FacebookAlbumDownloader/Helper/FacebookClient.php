<?php

namespace FacebookAlbumDownloader\Helper;

class FacebookClient{
    
    protected $appID = '466264270413471';
    protected $appSecret = '40b370381f93c23bd1bce9b34c65065d';
    protected $graphVersion = 'v2.10';
    protected $redirectedUri = 'http://dharmin.com/facebook-album/';
    
    protected $fb;
    protected $helper;

    /**
     *   Construct an easy to use Facebook API client.
     */
    public function __construct(){
        $this->fb    = new \Facebook\Facebook([
                            'app_id' => $this->appID,
                            'app_secret' => $this->appSecret,
                            'default_graph_version' => $this->graphVersion,
                        ]);
        $this->helper = $this->fb->getRedirectLoginHelper();

    }

    public function authenticate(){
        try {
            if (isset($_SESSION['FACEBOOK_ACCESS_TOKEN'])) {
                $_SESSION['FACEBOOK_ACCESS_TOKEN'] = $this->helper->getAccessToken();
            }else {
                $accessToken = $this->helper->getAccessToken();
            }
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if(isset($accessToken)){
            $this->fb->setDefaultAccessToken($accessToken);
            return true;
        }else{
            return false;
        }
    }

    public function getAuthUrl(){
        return $this->helper->getLoginUrl($this->redirectedUri);
    }

    public function getBasicDetails(){
        try {
            $profile_request = $this->fb->get('/me?fields=name,first_name,last_name,email');
            return  $profile_request->getGraphNode()->asArray();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        return false;
    }

    public function getUsername($profileID){
        $url = "http://www.facebook.com/".$profileID;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__."/cookies.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__."/cookies.txt");
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36');
        $html = curl_exec($ch);
        $effetive_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        $username_url = explode('/', $effetive_url);
        return $username_url[count($username_url) - 1];
    }
}