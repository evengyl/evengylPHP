<?

class select extends all_query
{
	public $construct_requete_sql = "";
	public $table = "";
	public $on = "";
	public $var = "";
	public $where = "";
	public $from = "";
	public $join = "";
	public $order = "";
	public $is_var_translate = false;

	/*
	IDEALISATION D'UNE REQUETE COMPLEXE
	AVEC LES 3 TABLE DE TEST

	$sql = new stdClass();
	$sql->table = ["test_pays", "test_famille", "test_enfant"];
	$sql->var = [
					"test_pays" => ["id", "pays"], 
					"test_famille" => ["id", "id_pays", "name", "coord", "mail"], 
					"test_enfant" => ["id", id_famille", "enfant_name_1", "enfant_name_2", "enfant_name_2"]];
	$sql->where = "pays = belgique";

	-le where pays et lié a la premiere table entrée a savoir
	-pour le nombre de left join et on, il suffit de compter le nombre de table dans table et faire -1, on aura deux left join ici 

	en sql elle marcherai si 

	SELECT 
		test_pays.pays, 
		test_famille.id_pays, test_famille.name, test_famille.coord, test_famille.mail, 
		test_enfant.id_famille, test_enfant.enfant_name_1, test_enfant.enfant_name_2, test_enfant.enfant_name_3

	FROM 
		test_pays
	LEFT JOIN
		test_famille
	ON 
		test_pays.id = test_famille.id_pays

	LEFT JOIN
		test_enfant
	ON
		test_famille.id = test_enfant.id_famille 

	WHERE
		test_pays.pays = 'belgique'


	*/

	public function __construct($req_sql)
	{
		if(is_object($req_sql))
		{
			if(isset($req_sql->var_translate) && !empty($req_sql->var_translate))
				$this->is_var_translate = true;

			if(isset($req_sql->table) && !empty($req_sql->table))
				$this->table = $this->_prefix_table.$req_sql->table;	
			else
				$_SESSION["error"] = "Aucune table n'a été sélectionnée pour la requète voir la requète suivante : ".$this->construct_requete_sql;


			//part FORM et JOIN
			if(isset($req_sql->join) && !empty($req_sql->join))
			{
				if(is_array($req_sql->join))
				{
					foreach($req_sql->join as $row_join)
					{
						$this->join[] = $row_join;
					}	
				}
				else{
					$this->join = $req_sql->join;	
				}
			}


			if(isset($req_sql->on) && !empty($req_sql->on))
			{
				if(is_array($req_sql->on))
				{
					foreach($req_sql->on as $row_on)
					{
						$this->on[] = $row_on;
					}	
				}
				else{
					$this->on = $req_sql->on;	
				}
			}
				


			$this->construct_requete_sql .= "SELECT ";


			//partie gestion des var et var translate
			$var_processing = new var_processing();
			$this->construct_requete_sql .= $var_processing->var_processing(isset($req_sql->var)?$req_sql->var:"", isset($req_sql->var_translate)?$req_sql->var_translate:"", $this->table, $this->on);



			//part FORM et JOIN
			$this->from_processing();
			$this->construct_requete_sql .= $this->from;



			//Part where process
			$where = new where();
			$this->construct_requete_sql .= $where->where_processing(isset($req_sql->where)?$req_sql->where:"", $this->table, $this->on);



			//part Order BY
			$this->order_by_processing($req_sql);
			

			//Part Limit
			if(isset($req_sql->limit) && $req_sql->limit != "")
				$this->construct_requete_sql .= " LIMIT ".$req_sql->limit." ";	
			else
				$this->construct_requete_sql .= "";	
		}
	}


	private function order_by_processing($req_sql)
	{
		if(isset($req_sql->order) && $req_sql->order != "")
			$this->order = $req_sql->order;

		if(empty($this->order))
			$this->construct_requete_sql .= " ORDER BY ".((!empty($this->on))?$this->table.".":"")."id ASC";	
		else
			$this->construct_requete_sql .= " ORDER BY ".((!empty($this->on))?$this->table:"")." ".$this->order." ";	
			
	}

	private function from_processing()
	{
		$this->from .= "FROM ".$this->table." ";
		if($this->join != "")
		{
			if(is_array($this->join))
			{
				foreach($this->join as $key_join => $row_join)
				{
					$this->from .= "LEFT JOIN ".$row_join." ON ".$this->on[$key_join]." ";
				}
			}
			else
				$this->from .= "LEFT JOIN ".$this->join." ON ".$this->on." ";
		}
	}

	

		
	public function get_construct_requete_sql()
	{
		return $this->construct_requete_sql;
	}

	public function get_if_var_translate()
	{
		return $this->is_var_translate;
	}
}