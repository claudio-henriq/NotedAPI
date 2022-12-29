<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crud extends CI_Model {
    private $table = FALSE;

    public function set_table($table){
        if($table)
            $this->table = $table;
    }

    /**
     * data => dados para serem inseridos no banco
     * insert_batch
     *  se true insere um array com vários registros
     *      nesse caso data deve ser um array de data
     *  se false insere apenas um registros
     */
    public function insert($data, $insert_batch = FALSE){
        if(!$this->table)
            return FALSE;

        $this->db->trans_start();

        if($insert_batch)
            $this->db->insert_batch($this->table, $data);
        else
            $this->db->insert($this->table, $data);
        
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * id => id do registro a ser editado
     * data => dados para serem inseridos no banco
     */
    public function update($id, $data){
        if(!$this->table)
            return FALSE;

        $this->db->trans_start();

        $this->db->where(array('id' => $id));
        $this->db->update($this->table, $data);
        
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * id => id do registro a ser deletado
     */
    public function delete($id){
        if(!$this->table)
            return FALSE;

        $this->db->trans_start();

        $this->db->delete($this->table, array('id' => $id));
        
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * where => parametro ou lista de parametros para busca
     */
    public function delete_where($where){
        if(!$this->table)
            return FALSE;

        $this->db->trans_start();

        $this->db->where($where);
        $this->db->delete($this->table);
        
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * params => [select, where]
     *  select => lista de colunas selecionadas
     *  where => parametro ou lista de parametros para busca
     * row 
     *  se false retorna todos resultados
     *  se true retorna apenas o primeiro resultado
     *  se 'column_name' retorna conteudo da coluna do primeiro resultado
     */
    public function select($params = array(), $row = FALSE){
        try{
            if(!$this->table)
                return array('status' => FALSE, 'data' => "Table name is unset, use set_table to load table name");

            //Se passar nome da coluna então seleciona a coluna especificada
            $column = "*";
            if(isset($params['select']) && is_array($params['select']))
                $column = implode(",", $params['select']);
            else if(isset($params['select']))
                $column = $params['select'];

            //Passa as restrições recebidas
            $where = array();
            if(isset($params['where']) && is_array($params['where']))
                foreach($params['where'] as $key => $value){
                    if(is_int($key))
                        $this->db->where($value);
                    else
                        $this->db->where($key, $value);    
                }    
            else if(isset($params['where']))
                $this->db->where($params['where']);

            if(isset($params['order']) && is_array($params['order']))
                foreach($params['order'] as $key => $value)
                    $this->db->order_by($key, $value);

            if($row)
                if(is_bool($row))    
                    $data = $this->db->select($column)->from($this->table)->where($where)->get()->row();
                else
                    $data = $this->db->select($column)->from($this->table)->where($where)->get()->row($row);
            else
                $data = $this->db->select($column)->from($this->table)->where($where)->get()->result();

            return array('status' => true, 'data' => $data);
        }catch(Exception $e){
            var_dump($e);
            return array('status' => FALSE, 'data' => "Erro inesperado, verifique os parametros. ".$e->getMessage());
        }
    }
}