<?php   defined('BASEPATH') OR exit('No direct script access allowed');

class Notemodel extends MY_Model{
    
    private $table = "Note";

    
    public function __construct(){
        parent::__construct();
        $this->load->model('crud');
        $this->crud->set_table($this->table);
    }

    public function get($params, $row = FALSE){
        $options = array(
            'where' => array('id_user' => $this->authUser->id),
            'order' => array('id' => 'DESC')
        );
        if(isset($params['search']) && $params['search']){
            $search = $params['search'];
            $options['where'][] = "(title LIKE '%{$search}%' OR content LIKE '%{$search}%')";
        }
        if(isset($params['start']) && $params['start']){
            $start = $params['start'];
            $options['where'][] = "(date >= '{$start}')";
        }
        if(isset($params['end']) && $params['end']){
            $start = $params['end'];
            $options['where'][] = "(date >= '{$end}')";
        }
        if(isset($params['id']) && $params['id']){
            $options['where']['id'] = $params['id'];
        }

        $notes = $this->crud->select($options, $row);

        return $notes;
    }

    public function register($data){
        $this->load->model('usermodel');
        //valida se o user existe
        $user = $this->usermodel->get(array(
                'id' => $this->authUser->id,
                'status' => 1
            ), true);

        if(!$user['status'] || !$user['data'])
            return array('status' => false, 'data' => "Usuário não encontrado");
        $this->crud->set_table($this->table);

        $insert = array(
            'id_user'   => $this->authUser->id,
            'title'     => $data['title']?? "",
            'content'   => $data['text'] ?? "",
            'color'     => $data['color']?? "note-1",
        );

        if($this->crud->insert($insert))
            return array('status' => true, 'data' => "Nota salva");
        else
            return array('status' => false, 'data' => "Não foi possível salvar esta nota no momento");
    }

    public function update($id, $data){
        //valida se a nota existe
        $note = $this->crud->select(array(
            'where' => array(
                'id' => $id,
                'id_user' => $this->authUser->id,
            ),
        ), true);

        if(!$note['status'] || !$note['data'])
            return array('status' => false, 'data' => "Nota não encontrada");

        //prepara os dados para atualizar
        $update['title']    = $data['title']?? NULL;
        $update['content']  = $data['text'] ?? NULL;
        $update['color']    = $data['color']?? NULL;
        
        $update = $this->check_null($update);

        if($this->crud->update($id, $update))
            return array('status' => true, 'data' => "Nota atualizada");
        else
            return array('status' => false, 'data' => "Não foi possível atualizar a nota no momento");
    }

    public function delete($id){
        //valida se a nota existe
        $note = $this->crud->select(array(
            'where' => array(
                'id' => $id,
                'id_user' => $this->authUser->id,
            ),
        ), true);

        if(!$note['status'] || !$note['data'])
            return array('status' => false, 'data' => "Nota não encontrada");
        
        //deleta a nota
        if($this->crud->delete($id))
            return array('status' => true, 'data' => "Nota excluída");
        else
            return array('status' => false, 'data' => "Não foi possível excluir a nota no momento");
    }

    public function delete_by_user(){
        //valida se existe pelo menos uma nota
        $note = $this->crud->select(array(
            'where' => array(
                'id_user' => $this->authUser->id,
            ),
        ));

        if(!$note['status'] || !$note['data'])
            return array('status' => true, 'data' => "Nenhuma nota salva");
        
        if($this->crud->delete_where(array('id_user' => $this->authUser->id)))
            return array('status' => true, 'data' => "Notas excluídas");
        else
            return array('status' => false, 'data' => "Não foi possível excluir as notas do usuário no momento");
    }
}
