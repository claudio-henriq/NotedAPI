<?php   defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model{
    protected $authUser;

    public function __construct(){
        parent::__construct();

        $this->authUser = $this->getAuthUser();
    }

    private function getAuthUser(){
        $session = $this->input->server('HTTP_SESSION_ID') ? $this->input->server('HTTP_SESSION_ID') : null;

        return json_decode(hex2bin($session));
    }

    protected function check_null($array = array()){
        foreach($array as $k => $v){
            if(is_null($v)){
                unset($array[$k]);
            }
        }

        return $array;
    }
}