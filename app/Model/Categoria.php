<?php 

class Categoria
{
	protected $_id;
	protected $slug;
	protected $nome;
	protected $descricao;
	protected $parent_id;

	public function __construct()
	{
		parent::__construct();
	}

	public function __set($name, $value)
	{
		$this->$name = $value;
	}

	public function __get($name)
	{
		return $this->$name;
	}

}