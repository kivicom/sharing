<?php

class Google extends Api
{
    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $params;
    private $url;
    private $tokenInfo;

    public function __construct()
    {
        parent::__construct();

        $config = parse_ini_file("config.ini", true);
        $this->client_id = $config['google']['client_id'];
        $this->client_secret = $config['google']['client_secret'];
        $this->redirect_uri = $this->urlGoogle;
        $this->url = $config['google']['url'];

        $this->params = array(
            'redirect_uri'  => $this->redirect_uri,
            'response_type' => 'code',
            'client_id'     => $this->client_id,
            'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile'
        );
    }

    public function getLink()
    {
        return $this->url . '?' . urldecode(http_build_query($this->params));
    }

    public function auth()
    {
        if (isset($_GET['code'])) {
            $result = false;

            $params = array(
                'client_id'     => $this->client_id,
                'client_secret' => $this->client_secret,
                'redirect_uri'  => $this->redirect_uri,
                'grant_type'    => 'authorization_code',
                'code'          => $_GET['code']
            );

            $url = 'https://accounts.google.com/o/oauth2/token';

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($curl);

            curl_close($curl);
            $tokenInfo = json_decode($result, true);

            if (isset($tokenInfo['access_token'])) {
                $params['access_token'] = $tokenInfo['access_token'];

                $this->tokenInfo = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo' . '?' . urldecode(http_build_query($params))), true);
            }
        }
    }

    public function getTokenInfo()
    {
        return $this->tokenInfo;
    }
}