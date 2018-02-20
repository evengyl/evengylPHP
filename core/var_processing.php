<?
class var_processing
{
	public $table = "";
	public $table_join_on = "";


	public function var_processing($var = "", $var_translate = "", $table = "", $table_join_on = "")
	{
		$array_var_translate = array();
		if(!empty($var_translate))
		{
			//on s'arrange pour transformée la chaine recu en array
			if(strstr($var_translate, ','))
				$array_var_translate = array_map('trim', explode(",", $var_translate));
			else	
				$array_var_translate[] = trim($var_translate);


			foreach($array_var_translate as $key => $row_var_translate)
			{
				//pour ça il faut que ce soit créer dans la base de données sinon ça va tout faire peter 
				if($_SESSION['lang'] == "fr")
					$array_var_translate[$key] = $row_var_translate."_fr";

				else if($_SESSION['lang'] == "en")
					$array_var_translate[$key] = $row_var_translate."_en";

				else if($_SESSION['lang'] == "nl")
					$array_var_translate[$key] = $row_var_translate."_nl";

				else
					$array_var_translate[$key] = $row_var_translate."_en";
			}
		}


		$tmp_array_var = "";
		if(isset($var) && $var != "")
			$tmp_array_var = array_map('trim', explode(",", $var));
			
		if(!empty($tmp_array_var))
			$array_all_var_listing = array_merge($array_var_translate, $tmp_array_var);
		else
			$array_all_var_listing = $array_var_translate;
		
		//processing pour le join debut
		foreach($array_all_var_listing as $row_tested)
		{
			if(strpos($row_tested, ".") === false)
				$req_join = false;
			else{
				$req_join = true;
				break;
			}
		}


		foreach($array_all_var_listing as $key => $row_all_var)
		{
			if($req_join) //si la requete est une requete avec un join tester plus haut
			{
				$table_join_on = substr($row_all_var, 0, strpos($row_all_var, '.'));
				
				if(strstr($row_all_var, ".")) // si on est sur une var qui a le '.' on doit mettre la table join
					$array_all_var_listing[$key] = $row_all_var;
				else
					$array_all_var_listing[$key] = $table.'.'.$row_all_var;
			}
			else
				$array_all_var_listing[$key] = $row_all_var;
		}

		//on peux rassembler toutes les var en un string pour continuer
		return implode($array_all_var_listing,', ')." ";
	}
}