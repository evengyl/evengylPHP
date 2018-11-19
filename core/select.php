<?

class select extends all_query
{
	public $construct_requete_sql = "";
	public $is_var_translate = false;

	
	public function __construct($req_sql)
	{
		if(is_object($req_sql))
		{
			// si l'objet table est un array on est en présence d'un left join
			if(is_array($req_sql->table) && is_array($req_sql->var))
				$this->construct_requete_sql = $this->select_($req_sql);	
		}
	}

	public function select_($req_sql)
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
		$chain_where = $where_processing->where_processing($req_sql->table, (isset($req_sql->where)?$req_sql->where:""));

		$order_processing = new order_processing();
		$chain_order = $order_processing->set_order((isset($req_sql->order))?$req_sql->order:"", $req_sql->table[0]);

		$limit_processing = new limit_processing();
		$chain_limit = $limit_processing->set_limit((isset($req_sql->limit))?$req_sql->limit:"");



		$construct_req .= $chain_var.$chain_var_trans.$chain_jointure.$chain_where.$chain_order.$chain_limit;
		return $construct_req;
	}
}