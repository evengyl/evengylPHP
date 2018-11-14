<?php 

Class modele_sql   
{
	public $modele_used;

	//part select_orm
	public $var;
	public $where;
	public $order_by;

	public function __construct($class)
	{
		$this->modele_used = $class;
	}

	public function select($req_sql)
	{
		//attention que le var processing et where proeceessing doit aussi etre utilisé par write donc il faut standardiser les deux 
		$select = new select_orm($req_sql, new $this->modele_used);

	}
}
