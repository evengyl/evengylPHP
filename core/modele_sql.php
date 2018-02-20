<?php 

Class modele_sql
{
	private $orm;
	private $db_link;
	public function __construct(&$orm, &$db_link)
	{
		$this->orm = &$orm;
		$this->db_link = &$db_link;
	}

	public function select()
	{
		affiche_pre($this->orm);
	}

	public function write($req_sql)
	{
		affiche_pre($req_sql);
	}

}
