<?

class select_orm
{
	public $var;
	public $where;
	public $order_by;
	public $array_var_modele;


	public function __construct($req_sql, $modele_used)
	{
		$this->req_sql = $req_sql;
		$this->modele_used = $modele_used;
		$this->select_processing();

		//var processing
		$var_processing = new var_processing_select_orm($this->var, $this->modele_used);
		$this->array_var_modele = $var_processing->get_var_modele();

		//where processing


		//order by processing

		affiche_pre($this->array_var_modele);
	}

	public function select_processing()
	{
		$this->var = trim(substr($this->req_sql, 0, strpos($this->req_sql, 'where')));

		$this->req_sql = str_replace([$this->var, "where"], "", $this->req_sql);

		$this->where = trim(substr($this->req_sql, 0, strpos($this->req_sql, 'order')));

		$this->order_by = trim(str_replace([$this->where, "order by"], "", $this->req_sql));

		$this->var = array_map('trim', explode(",", $this->var));
	}
}
