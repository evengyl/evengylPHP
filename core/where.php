<?
class where
{
	public $table = "";
	public $table_join_on = "";


	public function where_processing($where, $table, $table_join_on)
	{
		$symbol = "";
		$where_first_element = "";
		$where_second_element = "";
		$comparateur = "";

		$string_where = "WHERE ";
		if(empty($where))
		{
			$string_where .= " 1";
		}
		else
		{
			if(is_array($where))
			{
				foreach($where as $key_where => $row_where)
				{
					if(is_string($key_where)) //si on lui donne une clé dans le tableau du where
					{
						$where_first_element = $key_where;

						if(!empty($table_join_on))
							$where_first_element = $table.'.'.$where_first_element;		

						if(is_array($row_where)) // au cas ou on lui a donner un paramettre qui est un tableau comme id = (1,2,3,4)
						{
							$where_second_element = "(".implode($row_where, ',').")";

							if(end($where) == "NOT IN")
								$symbol = " NOT IN ";
							else if(end($where) == "IN")
								$symbol = " IN ";
							else if(end($where) == "OR")
								$symbol = " OR ";
							else if(end($where) == "AND")
								$symbol = " AND ";
								
						}
						else
						{
							if(is_int($row_where))
								$where_second_element = $row_where;

							else if(is_string($row_where))
								$where_second_element = '"'.$row_where.'"';

							$symbol = " = "; 
						}
					}
					else if(is_int($key_where))
					{
						if(is_string($row_where))
						{
							if($row_where == "!=" || $row_where == "<" || $row_where == "<=" || $row_where == ">" || $row_where == ">=")
							{
								$symbol = " ".$row_where." ";
							}//on a déjà tester les IN et NOT IN, alors il fuat tester les autre comparateur
						}
					}
				}
			}
			else //si on a envoyer qu'un chaine pas de tab
			{
				$where_first_element = $where;

				if(!empty($table_join_on))
					$where_first_element = $table_join_on.'.'.$where_first_element;
			}
		}
		return $string_where.$where_first_element.$symbol.$where_second_element;
	}

	public function where_processing_2($table = array(), $where = array())
	{
		$where_chain = " WHERE ";
		$like = false;

		if(empty($where[0]) || $where[0] == '1')
			$where_chain .= " 1 ";
		else
		{
			if(is_array($where))
			{
				foreach($where as $key => $row_where)
				{
					if($row_where == "OR" || $row_where == "AND")
						$where_chain .= htmlentities($row_where)." ";

					else if($row_where == "LIKE")
					{
						$where_chain .= " LIKE ";
						$like = true;
					}
					else if($row_where == "NOT LIKE")
					{
						$where_chain .= " NOT LIKE ";
						$like = true;
					}

					else if($row_where == "IN")
						$where_chain .= htmlentities($row_where)." ";

					else if($row_where == "NOT IN")
						$where_chain .= htmlentities($row_where)." ";

					else
					{
						if($like)
							$row_where = "'%".htmlentities($row_where)."%' ";
						else
							$row_where = $table[0].".".htmlentities($row_where)." ";
						
						$where_chain .= $row_where;
					}
				}
			}
			else
				$where_chain .= $table[0].".".htmlentities($where);	
		}
		return $where_chain;
	}
}