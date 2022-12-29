<?php   (defined('BASEPATH')) or exit('No direct script access allowed');

class Note extends MY_Controller {
    public function __construct(){
        parent::__construct();
        
        $this->load->model('notemodel');
    }

    public function index(){
        $get = $this->input->get();

        $response = $this->notemodel->get($get);

        $this->output($response);
    }

    public function delete($id){
        $response = $this->notemodel->delete($id);

        $this->output($response);
    }

    public function register(){
        $post = $this->input->post();

        if((isset($post['title']) && $post['title'] == "") && (isset($post['text']) && $post['text'] == ""))
            $this->output("Nota vazia");

        $response = $this->notemodel->register($post);

        $this->output($response);
    }

    public function edit($id){
        $post = $this->input->post();

        $response = $this->notemodel->update($id, $post);

        $this->output($response);
    }
}