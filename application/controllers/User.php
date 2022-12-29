<?php   (defined('BASEPATH')) or exit('No direct script access allowed');

class User extends MY_Controller {
    public function __construct(){
        $this->allowUser = array('auth', 'register');
        parent::__construct();
        $this->auth_user = TRUE;
        $this->load->model('usermodel');
    }

    public function index(){
        $get = $this->input->get();

        $response = $this->usermodel->get($get, true);

        $this->output($response);
    }

    public function auth(){
        $post = $this->input->post();

        if(!isset($post['user']))
            $this->output_error("O campo de usuário é obrigatório");
        if(!isset($post['password']))
            $this->output_error("O campo de senha é obrigatório");
        
        $response = $this->usermodel->auth($post);

        $this->output($response);
    }

    public function delete_account(){
        $response = $this->usermodel->delete();

        $this->output($response);
    }

    public function register(){
        $post = $this->input->post();

        if(!isset($post['user']))
            $this->output_error("O campo de usuário é obrigatório");
        if(!isset($post['password']))
            $this->output_error("O campo de senha é obrigatório");
        
        $response = $this->usermodel->register($post);

        $this->output($response);
    }

    public function update(){
        $post = $this->input->post();

        $response = $this->usermodel->update($post);

        $this->output($response);
    }
}