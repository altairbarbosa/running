<?php
class TrainingViewClass
{
    private $id_treino;
    private $id_exercicio;
    private $ordem;
    private $peso;
    private $serie;
    private $repeticao;
    private $descricao;

    /**
     * Get the value of id_treino
     */ 
    public function getId_treino()
    {
        return $this->id_treino;
    }

    /**
     * Set the value of id_treino
     *
     * @return  self
     */ 
    public function setId_treino($id_treino)
    {
        $this->id_treino = $id_treino;

        return $this;
    }

    /**
     * Get the value of id_exercicio
     */ 
    public function getId_exercicio()
    {
        return $this->id_exercicio;
    }

    /**
     * Set the value of id_exercicio
     *
     * @return  self
     */ 
    public function setId_exercicio($id_exercicio)
    {
        $this->id_exercicio = $id_exercicio;

        return $this;
    }

    /**
     * Get the value of ordem
     */ 
    public function getOrdem()
    {
        return $this->ordem;
    }

    /**
     * Set the value of ordem
     *
     * @return  self
     */ 
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;

        return $this;
    }

    /**
     * Get the value of peso
     */ 
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set the value of peso
     *
     * @return  self
     */ 
    public function setPeso($peso)
    {
        $this->peso = $peso;

        return $this;
    }

    /**
     * Get the value of serie
     */ 
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Set the value of serie
     *
     * @return  self
     */ 
    public function setSerie($serie)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get the value of repeticao
     */ 
    public function getRepeticao()
    {
        return $this->repeticao;
    }

    /**
     * Set the value of repeticao
     *
     * @return  self
     */ 
    public function setRepeticao($repeticao)
    {
        $this->repeticao = $repeticao;

        return $this;
    }

    /**
     * Get the value of descricao
     */ 
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set the value of descricao
     *
     * @return  self
     */ 
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }
}
?>