<?php 

require_once('MongoFactory.php');

class MongoActiveRecord 
{
	/**
	*
	*	Child Model
	*	
	*	@var Object
	*/
	private $model;

	/**
	*
	*	MongoConnection
	*	
	*	@var Object
	*/
	private $mongo;

	/**
	*
	*	Reflection Class with methods and attributes of the model
	*	
	*	@var Object
	*/
	private $reflectionClass;

	/**
	*
	*	Class Name
	*	
	*	@var String
	*/
	private $className;

	/**
	*
	*	Array with de properties of the child model
	*	
	*	@var Array
	*/
	private $modelProperties = array();

	/**
	*
	*	Factory which contains the MongoDB Object
	*	
	*	@var Object
	*/

	private $factory;


	/**
	*
	*	contructor which defines child Model, reflectionClass, name of child class and 
	*	sets the MongoDB connection with the database already selected.
	*		
	*	@param Object $child
	*
	*/

	private $where = array();

	public function __construct($child){
		
		$this->model = $child;
		$this->reflectionClass = new ReflectionClass($this->model); 
		$this->className = $this->reflectionClass->name;
		$this->factory = MongoFactory::getInstance();
		$this->mongo = $this->factory->db;
	}

	public function where($where, $value){

		if(is_array($where)):
			$this->where = $where;
		else:
			$this->where = array($where => $value);
		endif;
	}

	public function get(){
		
	}

	/**
	*
	*	performs a textual search for a document through a query search. All attributes are analyzed,
	*	except the _id one.
	*	
	*	@param String $search
	*	@return return Array $modelset
	*
	*/
		
	public function search($search){		
		$regex = new MongoRegex("/$search/"); 
		
		$modelSet = $this->buildModelSet($this->mongo->{$this->className}->find(array('$or' => $this->searchInAttribute($regex) ) ));
	
		return $modelSet;
	}

	/**
	*
	*	performs a document insertion in the colletion
	*	
	*	@return Boolean
	*
	*/

	public function save(){
	
		return $this->mongo->{$this->className}->insert($this->modelToArray());
	}

	/**
	*
	*	update the document 
	*	
	*	@param Array $data, Integer $id
	*	@return return Boolean
	*
	*/
			
	public function update($data, $id){
		
		return $this->mongo->{$this->className}->update(array('_id' => new MongoId($id)), array('$set' => $this->modelToArray()));
	}

	/**
	*
	*	delete the document 
	*	
	*	@param Integer $id
	*	@return Boolean
	*
	*/
		
	public function delete($id){
		return $this->mongo->{$this->className}->remove(array('_id' => new MongoId($id)));
	}

	/**
	*
	*	performs a general search in the database
	*	
	*	@return Array $modelSet
	*/
	
	public function all(){
		$modelSet = $this->buildModelSet($this->mongo->{$this->className}->find());
		return $modelSet;
	}

	/* ==========================================================================
	protected
	========================================================================== */

	/**
	*
	*	converts the model in array
	*	
	*	@return Array $data
	*
	*/

	protected function modelToArray(){
		$data = array();

		foreach($this->getProperties() as $prop):
			($prop->name != '_id') ? $data[$prop->name] = $this->model->{$prop->name} : '';
		endforeach;

		return $data;
	}

	/**
	*
	*	return an array with model's attributes as key and 
	*	the query as data.
	*		
	*	@param String $query
	*	@return Array $searchArray
	*
	*/

	protected function searchInAttribute($query){

		$props = $this->getProperties();
		$data = array();
		$searchArray = array();

		foreach($props as $key => $prop):
			if($prop->name == '_id'):
			 	continue;
			endif;
			
			$data[$prop->name] = $query; 
			$searchArray[] = array($prop->name => $data[$prop->name]);
		endforeach;

		return $searchArray;
	}

	

	/**
	*
	* 	method which build a ModelSet with a mongo result set 
	*
	* 	@param MongoCursor $mongoResult
	* 	@return Array $modelSet
	*
	*/

	protected function buildModelSet($mongoResult){

		$modelSet = array();
		$countKeys = $this->getProperties();

		foreach($mongoResult as $match):

			$counter = 0;

			$obj = new $this->model();

			while($counter < count($countKeys)):
				$attribute = $countKeys[$counter]->name;
				$value = $match[$countKeys[$counter]->name];
				$obj->$attribute = $value;
				$counter++;
			endwhile;

			$modelSet[] = $obj;	

		endforeach;
	
		return $modelSet;
	}

	/**
	*
	*	gets the reflection properties of the model.
	*	
	*	@return Array
	*
	*/

	protected function getProperties(){
		return $this->reflectionClass->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED);
	}
		
}
