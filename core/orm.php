<?
class orm extends _db_connect
{
	public $db_link;
	protected $_prefix_table;
	private $pool = [];
	public $sql;
	public $orm;
	private $base_app_dir;

	public function __construct($base_dir)
	{
		//on set le dossier courant de l'app
		$this->base_app_dir = $base_dir;

		if(Config::$prefix_sql != '')
			$this->_prefix_table = Config::$prefix_sql;

		$this->orm = [];
	}

	public function __get($modele)
	{
		//si le modèle appeler n'existe pas dans le tableau de l'orm en gros la pool
		if(!array_key_exists($modele, $this->orm))
		{
			$modele = "modele_".$modele;


			if(file_exists($this->base_app_dir."/app/modele/modele_object/".$modele.".php"))
			{
				$this->orm[$modele] = new $modele($this->orm, $this->db_link);

				return $this->orm[$modele];
			}
			else
			{
				$_SESSION['error'] = "The modele object ".$modele." not exist in folder modele";
			}
			
		}
	}
}