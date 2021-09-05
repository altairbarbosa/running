<?php
class PurchaseClass
{
    private $id;
    private $id_usuario;
    private $total;
    private $data_comp;
    
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
     * Get the value of total
     */ 
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set the value of total
     *
     * @return  self
     */ 
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get the value of data_comp
     */ 
    public function getData_comp()
    {
        return $this->data_comp;
    }

    /**
     * Set the value of data_comp
     *
     * @return  self
     */ 
    public function setData_comp($data_comp)
    {
        $this->data_comp = $data_comp;

        return $this;
    }
}