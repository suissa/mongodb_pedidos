<?php 

class Usuario
{
	protected $_id;
	protected $login;
	protected $email;
	protected $senha;
	protected $nome_completo;
	protected $tipo;
	protected $created_at;

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