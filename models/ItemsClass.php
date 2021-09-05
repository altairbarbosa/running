<?php
class ItemsClass
{
    private $id_comp;
    private $id_prodserv;
    private $quantidade;
    private $valor;

    /**
     * Get the value of id_comp
     */ 
    public function getId_comp()
    {
        return $this->id_comp;
    }

    /**
     * Set the value of id_comp
     *
     * @return  self
     */ 
    public function setId_comp($id_comp)
    {
        $this->id_comp = $id_comp;

        return $this;
    }

    /**
     * Get the value of id_prodserv
     */ 
    public function getId_prodserv()
    {
        return $this->id_prodserv;
    }

    /**
     * Set the value of id_prodserv
     *
     * @return  self
     */ 
    public function setId_prodserv($id_prodserv)
    {
        $this->id_prodserv = $id_prodserv;

        return $this;
    }

    /**
     * Get the value of quantidade
     */ 
    public function getQuantidade()
    {
        return $this->quantidade;
    }

    /**
     * Set the value of quantidade
     *
     * @return  self
     */ 
    public function setQuantidade($quantidade)
    {
        $this->quantidade = $quantidade;

        return $this;
    }

    /**
     * Get the value of valor
     */ 
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set the value of valor
     *
     * @return  self
     */ 
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }
}