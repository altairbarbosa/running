<?php
class DebtClass
{
    private $id;
    private $id_pag;
    private $id_comp;
    private $vencimento;
    private $parcela;
    private $valor;
    private $valor_pag;
    private $data_pag;

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
     * Get the value of id_pag
     */ 
    public function getId_pag()
    {
        return $this->id_pag;
    }

    /**
     * Set the value of id_pag
     *
     * @return  self
     */ 
    public function setId_pag($id_pag)
    {
        $this->id_pag = $id_pag;

        return $this;
    }

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
     * Get the value of vencimento
     */ 
    public function getVencimento()
    {
        return $this->vencimento;
    }

    /**
     * Set the value of vencimento
     *
     * @return  self
     */ 
    public function setVencimento($vencimento)
    {
        $this->vencimento = $vencimento;

        return $this;
    }

    /**
     * Get the value of parcela
     */ 
    public function getParcela()
    {
        return $this->parcela;
    }

    /**
     * Set the value of parcela
     *
     * @return  self
     */ 
    public function setParcela($parcela)
    {
        $this->parcela = $parcela;

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

    /**
     * Get the value of valor_pag
     */ 
    public function getValor_pag()
    {
        return $this->valor_pag;
    }

    /**
     * Set the value of valor_pag
     *
     * @return  self
     */ 
    public function setValor_pag($valor_pag)
    {
        $this->valor_pag = $valor_pag;

        return $this;
    }

    /**
     * Get the value of data_pag
     */ 
    public function getData_pag()
    {
        return $this->data_pag;
    }

    /**
     * Set the value of data_pag
     *
     * @return  self
     */ 
    public function setData_pag($data_pag)
    {
        $this->data_pag = $data_pag;

        return $this;
    }
}