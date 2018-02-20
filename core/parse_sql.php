<?
class parse_sql
{
	public function __construct()
	{

	}

	public function parse_var_translate($res_sql)
	{
		foreach($res_sql as $key_res => $row_obj_sql)
		{
			foreach($row_obj_sql as $key => $vallue)
			{
				if(strstr($key, '_fr'))
				{
					$tmp_fr = substr($key, 0, strpos($key , '_fr'));
					$res_sql[$key_res]->$tmp_fr = $vallue;
					unset($res_sql[$key_res]->$key);
				}
				else if(strstr($key, '_en'))
				{
					$tmp_en = substr($key, 0, strpos($key , '_en'));
					$res_sql[$key_res]->$tmp_en = $vallue;
					unset($res_sql[$key_res]->$key);
				}
				else if(strstr($key, '_nl'))
				{
					$tmp_nl = substr($key, 0, strpos($key , '_nl'));
					$res_sql[$key_res]->$tmp_nl = $vallue;
					unset($res_sql[$key_res]->$key);
				}
			}
		}
		return $res_sql;
	}
}