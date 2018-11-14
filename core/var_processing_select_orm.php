<?
class var_processing_select_orm  
{
	private $array_var_modele;
	
	public function __construct(&$var, &$modele)
	{
		$this->set_var_modele($var, $modele);
	}
	public function set_var_modele(&$var, &$modele)
	{
		foreach($var as $key_var => $value_var)
		{
			$this->array_var_modele[$value_var] = $modele->$value_var;
		}
	}

	public function get_var_modele()
	{
		return $this->array_var_modele;
	}
}
