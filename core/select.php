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

	
	public function __construct($req_sql)
	{
		if(is_object($req_sql))
		{
			// si l'objet table est un array on est en présence d'un left join
			if(is_array($req_sql->table) && is_array($req_sql->var))
				$this->construct_requete_sql = $this->select_2($req_sql);	
			else
			{/*

				//////////////////////////////////////////////////////////////////////
				////// normalement plus utiliser, faire des test //
				//////////////////////////////////////////////////////////////////////


				if(isset($req_sql->var_translate))
					$this->is_var_translate = true;


				if(!isset($req_sql->table))
					$_SESSION["error"] = "Aucune table n'a été sélectionnée pour la requète voir la requète suivante : ".$this->construct_requete_sql;

				else
				{
					$this->table = $this->_prefix_table.$req_sql->table;
					
					//part FORM et JOIN
					if(isset($req_sql->join) && !empty($req_sql->join))
					{
						if(is_array($req_sql->join))
						{
							foreach($req_sql->join as $row_join)
								$this->join[] = $row_join;
						}
						else
							$this->join = $req_sql->join;	
					}


					if(isset($req_sql->on) && !empty($req_sql->on))
					{
						if(is_array($req_sql->on))
						{
							foreach($req_sql->on as $row_on)
								$this->on[] = $row_on;
						}
						else
							$this->on = $req_sql->on;	
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
			}*/
			
		}
	}

	public function select_2($req_sql)
	{
		$construct_req = "SELECT ";
		$chain_var = "";
		$chain_var_trans = "";
		$chain_jointure = "";
		$chain_where = "";
		$chain_order = "";
		$chain_limit = "";

		$var_processing = new var_processing();
		$chain_var = $var_processing->set_var_chain($req_sql->table, $req_sql->var);
		if(isset($req_sql->var_translate))
			$chain_var_trans = $var_processing->set_var_trans_chain($req_sql->var_translate, (!empty($chain_var)?true:false), (isset($_SESSION['lang'])?$_SESSION['lang']:""));
		

		$from_processing = new parse_table_jointure();
		$chain_jointure = $from_processing->set_jointure_chain($req_sql->table, $req_sql->var);

		$where_processing = new where();
		$chain_where = $where_processing->where_processing_2($req_sql->table, (isset($req_sql->where)?$req_sql->where:""));

		$order_processing = new order_processing();
		$chain_order = $order_processing->set_order((isset($req_sql->order))?$req_sql->order:"", $req_sql->table[0]);

		$limit_processing = new limit_processing();
		$chain_limit = $limit_processing->set_limit((isset($req_sql->limit))?$req_sql->limit:"");



		$construct_req .= $chain_var.$chain_var_trans.$chain_jointure.$chain_where.$chain_order.$chain_limit;
		return $construct_req;
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