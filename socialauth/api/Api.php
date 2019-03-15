<?php

class Api {
    protected $urlGoogle;

    public function __construct()
    {
        $config = parse_ini_file("config.ini", true);

        $this->urlGoogle = $config['google']['redirect_uri'];
    }
}