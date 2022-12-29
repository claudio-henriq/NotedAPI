<?php   defined('BASEPATH') OR exit('No direct script access allowed');

class Usermodel extends MY_Model{
    
    private $table = "Users";

    
    public function __construct(){
        parent::__construct();
        $this->load->model('crud');
        $this->crud->set_table($this->table);
    }
    
    public function auth($data){
        $user = $this->crud->select(array(
            'where' => array(
                'username' => $data['user'],
                'status' => 1
            ),
        ), true);

        if(!$user['status'] || !$user['data'])
            return array('status' => false, 'data' => "Usuário não encontrado");

        $user = $user['data'];
        if(password_verify($data['password'], $user->password)){
            $user->sessionID = $this->generate_session_id($user);

            return array('status' => true, 'data' => $user);
        }else
            return array('status' => false, 'data' => "Usuário ou senha incorretos");
    }

    public function get($params, $row = FALSE){
        $options = array(
            'where' => array(),
            'select' => array('id', 'username', 'name', 'status'),
        );
        if(isset($params['search']) && $params['search']){
            $search = $params['search'];
            $options['where'][] = "(name LIKE '%{$search}%' OR username LIKE '%{$search}%')";
        }
        if(isset($params['id']) && $params['id']){
            $options['where']['id'] = $params['id'];
        }else{
            $options['where']['id'] = $this->authUser->id;
        }
        if(isset($params['status'])){
            $options['where']['status'] = $params['status'];
        }

        $users = $this->crud->select($options, $row);

        if(is_bool($row) && $row){
            $users['data']->sessionID = $this->generate_session_id($users['data']);
        }

        return $users;
    }

    public function register($data){
        $user = $this->crud->select(array(
            'where' => array(
                'username' => $data['user'],
                'status' => 1
            )
        ), true);

        if($user['status'] && $user['data'])
            return array('status' => false, 'data' => "Este nome de usuário não está disponível");
        
        $insert = array(
            'username'  => $data['user'],
            'password'  => password_hash($data['password'], PASSWORD_BCRYPT),
            'name'      => $data['name'] ?? "",
            'status'    => 1,
        );

        if($this->crud->insert($insert))
            return array('status' => true, 'data' => "Usuário cadastrado com sucesso");
        else
            return array('status' => false, 'data' => "Não foi possível cadastrar este Usuário no momento");
    }

    public function update($data){
        //valida se o user existe
        $user = $this->crud->select(array(
            'where' => array(
                'id' => $this->authUser->id,
                'status' => 1
            ),
        ), true);

        if(!$user['status'] || !$user['data'])
            return array('status' => false, 'data' => "Usuário não encontrado");

        //valida se o novo user está disponível
        if(isset($data['user']) && $data['user'] != $user['data']->username){
            $user = $this->crud->select(array(
                'where' => array(
                    'username' => $data['user'],
                    'status' => 1
                ),
            ), true);
            
            if($user['status'] && $user['data'])
                return array('status' => false, 'data' => "Este nome de usuário não está disponível");
        }

        //prepara os dados para atualizar
        $update['name']     = $data['name']?? NULL;
        $update['username'] = $data['user']?? NULL;
        $update['password'] = isset($data['password'])? password_hash($data['password'], PASSWORD_BCRYPT): NULL;
        
        $update = $this->check_null($update);

        if($this->crud->update($this->authUser->id, $update)){
            $user = $this->crud->select(array(
                'where' => array(
                    'id' => $this->authUser->id,
                    'status' => 1
                ),
            ), true);
            $user = $user['data'];
            $user->sessionID = $this->generate_session_id($user);
            return array('status' => true, 'data' => $user, 'message' => "Dados do usuário atualizados com sucesso");
        }else
            return array('status' => false, 'data' => "Não foi possível atualizar as informações deste usuário no momento");
    }

    public function delete(){
        $this->load->model('notemodel');

        $notes = $this->notemodel->delete_by_user();
        $this->crud->set_table($this->table);

        if(!$notes['status'])
            return $notes;
        
        //deleta o usuário
        if($this->crud->delete($this->authUser->id))
            return array('status' => true, 'data' => "Usuário excluído");
        else
            return array('status' => false, 'data' => "Não foi possível excluir o usuário no momento");
    }

    private function generate_session_id($user){
        unset($user->password);
        $user->loginTime = time();
        $sessionId = json_encode($user);
        return bin2hex($sessionId);
    }
}
