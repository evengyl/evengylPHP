<?
class where
{
	public function where_processing($table = array(), $where = false)
	{
		$where_chain = " WHERE ";
		$like = false;


		if(!is_array($where))
		{
			if(!$where)
				$where_chain .= " 1 ";
			else
				$where_chain .= $table[0].".".htmlentities($where);	
		}
		else
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
					else if($row_where == "")
						$row_where = $row_where;
					else
						$row_where = $table[0].".".htmlentities($row_where)." ";
					
					$where_chain .= $row_where;
				}
			}
		}
		return $where_chain;
	}
}