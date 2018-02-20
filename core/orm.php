<?
class orm extends _db_connect
{
	public $db_link;
	protected $_prefix_table;
	private $pool = [];
	public $sql;

	public function __construct()
	{
		if(Config::$prefix_sql != '')
			$this->_prefix_table = Config::$prefix_sql;

		$this->orm = [];
	}

	public function __get($name)
	{
		if(!array_key_exists($name, $this->orm))
		{
			$name = "model_".$name;
			$tmp = new $name($this->orm, $this->db_link);
			$this->orm[$name] = &$tmp;
			return $tmp;
		}
	}
}