<?php
class TrainingClass
{
    private $id;
    private $id_usuario;
    private $nome;
    private $data_inicio;
    private $data_fim;

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id_usuario
     */ 
    public function getId_usuario()
    {
        return $this->id_usuario;
    }

    /**
     * Set the value of id_usuario
     *
     * @return  self
     */ 
    public function setId_usuario($id_usuario)
    {
        $this->id_usuario = $id_usuario;

        return $this;
    }

    /**
     * Get the value of nome
     */ 
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     *
     * @return  self
     */ 
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get the value of data_inicio
     */ 
    public function getData_inicio()
    {
        return $this->data_inicio;
    }

    /**
     * Set the value of data_inicio
     *
     * @return  self
     */ 
    public function setData_inicio($data_inicio)
    {
        $this->data_inicio = $data_inicio;

        return $this;
    }

    /**
     * Get the value of data_fim
     */ 
    public function getData_fim()
    {
        return $this->data_fim;
    }

    /**
     * Set the value of data_fim
     *
     * @return  self
     */ 
    public function setData_fim($data_fim)
    {
        $this->data_fim = $data_fim;

        return $this;
    }
}
?>