<?php



class _db_connect extends Config
{
	private $db_link;	
	private $is_connected = false;
	private $last_res_sql = null;
	private $last_req_sql = null;
	


	public function __construct()
	{
		$this->connect();
	}

    public function get_connect_data()
    {
        return array(parent::$hote, parent::$user, parent::$Mpass, parent::$base);
    }
	
	public function connect()
	{

		$this->db_link  = mysqli_connect(parent::$hote, parent::$user, parent::$Mpass, parent::$base)or die('erreur');
		$this->is_connected = true;
		mysqli_set_charset($this->db_link, 'utf8');
		
	}

	//cette fonction va permettre de remplacer dans toute les boucle de fetch, par mysqli_fetch_object
	//elle recois la requete envoyer par l'appelant  
	public function fetch_object($req_sql)  // elle recois la requ�te sql sous forme de string
	{	
		if($this->is_connected == false) // v�rifie si la connection � la DB est �tablie si pas , elle le fait
			$this->connect(); //appel la fonction

		if(is_null($this->last_req_sql) || is_null($this->last_res_sql) || $req_sql != $this->last_req_sql)
		{
			$this->last_req_sql = $req_sql; // enregistre une copie temporaire de la requete
			parent::set_list_req_sql($req_sql);
			$this->last_res_sql = mysqli_query($this->db_link, $req_sql)or die('Probleme de requete = '. $req_sql);// enregistre une copie temporaire de la reponse requete
			
			if(!$this->last_res_sql && $_SERVER['HTTP_HOST'] == "localhost")
			{
            	affiche_pre(mysqli_error($this->db_link));
        	}
		}// si les valeurs sont null ou diff�rente , enregistre les variable correctement
		$res = mysqli_fetch_object($this->last_res_sql);  //enregistre les lignes de la requ�te sur un object
		if (is_null($res))
		 // fetch va vider l'objet envoyer donc on v�rifie si le resultat est null , 
		//si c'est le cas on vide le buffer et remet la variable a null pour une prochaine requ�te
		{
			mysqli_free_result($this->last_res_sql);
			$this->last_res_sql = null;
		}
		
		
		return $res; // renvoi un tableau d'objet
	}


	public function query($req_sql) //not for return somethings
	{
		parent::set_list_req_sql($req_sql);
		$this->connect();
		$time_request_before = date("U");
		$res_sql = mysqli_query($this->db_link, $req_sql)or die(mysqli_error($this->db_link));
		$nb_link_affected = $this->db_link->affected_rows;
		$time_request_after = date("U");

		$_SESSION["time_exec_sql"] = (double)$time_request_after - $time_request_before;

		return $res_sql;
	}

	public function query_update($req_sql) //not for return somethings
	{
		parent::set_list_req_sql($req_sql);
		$this->connect();
		$time_request_before = date("U");
		$res_sql = mysqli_query($this->db_link, $req_sql)or die(mysqli_error($this->db_link));
		$nb_link_affected = $this->db_link->affected_rows;
		$time_request_after = date("U");

		$_SESSION["time_exec_sql"] = (double)$time_request_after - $time_request_before;

		return $res_sql;
	}


    public function escape_sql($var)
    {
        $this->connect();
        return mysqli_real_escape_string($this->db_link, $var);
    }

	public function get_last_insert_id()
	{
		return mysqli_insert_id($this->db_link);
	}


	public function get_db_link()
	{
		if($this->is_connected == false) // v�rifie si la connection � la DB est �tablie si pas , elle le fait
			$this->connect(); //appel la fonction
		return $this->db_link;
	}


	public function escape_string($txt)
	{
		if($this->is_connected == false)
			$this->connect();
		return mysqli_real_escape_string($this->db_link, $txt);
	}


    public function get_db_name()
    {
        return $this->base;
    }
}

?>
