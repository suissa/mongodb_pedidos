<?php
	
class MongoFactory implements DBFactory
{

	private static $connection;

	protected $db;

	public function __construct(){

	}
	public function makeConnection(){
		if(self::$connection == null):
			self::$connection = new Mongo( 'mongodb://test:test@ds029187.mongolab.com:29187/' );

			$this->setDB( 'pedidos' );
		endif;

		return self::$connection;
		
	}

	private function setDB( $db ){
		$this->db = self::$connection->selectDB( $db );
	}	
}