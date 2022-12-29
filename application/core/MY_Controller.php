<?php   defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    private $token = "bm90ZWQyMDIy";
    protected $auth_token = TRUE;
    protected $auth_user = TRUE;
    protected $allowUser = array();
    protected $allowToken = array();

    public function __construct(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Access-Control-Max-Age, token, session-id');
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            header("Access-Control-Max-Age: 86400");
            die();
        }
        parent::__construct();

        $this->connection();
    }

    private function connection(){

        foreach($this->allowUser as $method){
            $this->auth_user = $this->auth_user && ($this->router->method != $method);
        }

        foreach($this->allowToken as $method){
            $this->auth_token = $this->auth_token && ($this->router->method != $method);
        }
        
        $received_token = $this->input->server('HTTP_TOKEN') ? $this->input->server('HTTP_TOKEN') : null;
        $received_session = $this->input->server('HTTP_SESSION_ID') ? $this->input->server('HTTP_SESSION_ID') : null;

        if (($this->auth_token && $received_token != $this->token) || ($this->auth_user && !$received_session)){
            $this->output_error('Acesso negado', 401);
        }
    }

    protected function output_error($response, $code = 406){
        $this->output($response, $code);
    }

    protected function output($response, $code = 200){
        if(!is_array($response))
            $response = array('status' => $code == 200, 'data' => $response);
        
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit();
    }
}