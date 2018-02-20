<?php
##########################################
#	Createur : Evengyl
#	Date de creation : 29-09-2014
#	Version : 1.2
#	Date de modif : 20-09-2016
##########################################

class all_query extends _db_connect
{
	public $db_link;
	protected $_prefix_table;

	public function __construct()
	{
		if(Config::$prefix_sql != '')
			$this->_prefix_table = Config::$prefix_sql;
	}


	public function select($req_sql, $return_sql_prepare = 0)
	{
		$parse_sql = new parse_sql();
		$select =  new select($req_sql);
		$construct_requete_sql = $select->get_construct_requete_sql();
		$is_var_translate = $select->get_if_var_translate();

		$i = 0;

		while($row = parent::fetch_object($construct_requete_sql))
		{
			$res_fx[$i] = $row;
			$i++;
		}

		if($return_sql_prepare)
			$res_fx[] = $construct_requete_sql;
		else
			unset($construct_requete_sql); //vide le buffer de memoire $req_sql pour liberer de la place 	
		
		if(!isset($res_fx))
			return '';
		else 
		{
			if($is_var_translate)//il n'ira enlever les _fr _nl _en que si i ly a des var trnaslate
				return $parse_sql->parse_var_translate($res_fx); //va enlever les _fr _en _nl des var recu pour plus de facilité dans les templates
			else
				return $res_fx;
		}
			
	}


	public function insert_into($req_sql) // opérationnel et fonctionnel , reste à tester sur la validation
	{
		$this->set_db_link();

		$req_sql->table = $this->_prefix_table.$req_sql->table;	

		$columns = "";
		$values = "";

		foreach($req_sql->ctx as $nom_colonne => $valeur)
		{			
			$valeur = mysqli_real_escape_string($this->db_link, $valeur);

			if($nom_colonne == "id")
				$valeur = intval($valeur);

			$columns = $columns.', '.$nom_colonne;
			$values = $values.', "'.$valeur.'"';			
		}

		$columns = substr($columns,2);

		$values = substr($values,2);
		$req_sql = "INSERT INTO ".$req_sql->table." (".$columns.") VALUES (".$values.")";
		parent::query($req_sql);
		unset($req_sql);

	}


	public function update($req_sql)
	{
		$set_all = "";

		$this->set_db_link();

		$req_sql->table = $this->_prefix_table.$req_sql->table;	
		

		foreach($req_sql->ctx as $key => $values)
		{
			$values = mysqli_real_escape_string($this->db_link, $values);

			if($key == "id")
				$values = intval($values);

			$set_all .= ', '.$key.' = "'.$values.'"';				
		}
	
		$set_all = substr($set_all,2);

		if(isset($req_sql->where))
		{				
			if($req_sql->where == "" OR $req_sql->where == " ")
				$req_sql = 'UPDATE '.$req_sql->table.' SET '.$set_all;
			else
				$req_sql = 'UPDATE '.$req_sql->table.' SET '.$set_all.' WHERE '.$req_sql->where;	
		}

		$requete_win_lost = parent::query_update($req_sql);
		if($requete_win_lost > 0)
			return $erreur = 'modification bien appliquée';
		else
		{
			return false;
		}
		unset($req_sql);
        return $erreur = true;

	}


	public function delete_row($table, $where)
	{
		$req_sql->table = $this->_prefix_table.$req_sql->table;	
		

		$req_sql = "DELETE FROM ".$table." WHERE ".$where;
		parent::query($req_sql);
	}

	public function delete($obj)
	{
		$construct_req = "";

		$obj->table = $this->_prefix_table.$obj->table;	
		

		if(is_object($obj))
		{
			if(isset($obj->where) && $obj->where != "")
			{
				$construct_req = "DELETE FROM ".$obj->table ." WHERE ". $obj->where ."";	
				parent::query($construct_req);
			}
			else
				return 0;
		}
		else
			return 0;
	}




	public function other_query($req_sql)
	{
		if(isset($req_sql))
		{
			$i = 0;
			while($row = parent::fetch_object($req_sql))
			{
				$res_fx[$i] = $row;
				$i++;
			}
            if(isset($res_fx))
            {
                if(empty($res_fx))
                {
                    return $res_fx = NULL;
                }
                else
                {
                    return $res_fx;
                }
            }
            else
            {
                return $res_fx = NULL;
            }
		}
		else
		{
			return false;
		}
	}

	public function set_db_link()
	{
		$this->db_link = parent::get_db_link();
	}

    public function query_simple($query)
    {
        parent::query($query);
    }


	public function generate_form_unpdate($table_needed, $id)
	{
		if(Config::$prefix_sql != '')
		{
			if(strpos($table_needed, Config::$prefix_sql) === false)
				$table_needed = Config::$prefix_sql.$table_needed;
		}
			

		$req_simply = new stdClass();
		$req_simply->table = $table_needed;
		$req_simply->var = "*";
		$req_simply->where = "id = '". $id ."'";
		$req_simply = $this->select($req_simply);
		
		ob_start();?>
		    <div class='contenu_compte'>
		        <div class="row">
		            <div class="col-lg-12">
		                <form class="form-inline" style="margin:auto;" role="form" action="" method="POST">
		                    <br><?php
		                    foreach($req_simply[0] as $key => $value)
		                    {?>
		                            <div class="form-group <?
			                            if($key == 'id')
			                                echo  'has-error';
			                            else
		    	                            echo 'has-success';
	        	                    	?>" style="margin-right:30px;">

		                                <div class="input-group">
		                                    <div style="width: 200px;" class="input-group-addon"><?php echo $key ?></div>
		                                    <input style='width:450px;' type="text" <?php echo ($key == 'id')? 'disabled id="disabledInput"':'id="inputSuccess1"'; ?>
	                                            id="disabledInput" value="<?= $value ?>"
		                                        class="form-control" name="<?php echo $key ?>">

		                                </div>

		                            </div>
		                            <br><?
		                    }?>
		                    <input type="hidden" name="id" value="<?= $id ?>">
		                    <button style="width: 650px; margin-top:15px;" type="submit" class="btn btn-default">Submit</button>
		                </form>
		            </div>
		        </div>
		    </div><?
	    $content = ob_get_clean();
	    return $content;
	}


	public function generate_form_insert_into($table_needed, $option = array("TYPE" => null, "CHAMPS" => array()))
	{
		if(Config::$prefix_sql != '')
		{
			if(strpos($table_needed, Config::$prefix_sql) === false)
				$table_needed = Config::$prefix_sql.$table_needed;	
		}
		

		$req_simply = new stdClass();
		$req_simply->table = $table_needed;
		$req_simply->var = "*";
		$req_simply = $this->select($req_simply);?>
		
	    <div class='contenu_compte'>
	        <div class="row">
	            <div class="col-lg-12">
	                <form class="" style="margin:auto;" role="form" action="" method="POST">
	                    <br><?php
	                    foreach($req_simply[0] as $key => $value)
	                    {
                    		if(in_array($key, $option['CHAMPS']) && $option['TYPE'] != null)
							{
								if($option['TYPE'] == "select")
								{
									$for_select = new stdClass();
									$for_select->table = Config::$prefix_sql.$table_needed;
									$for_select->var = "marque";
									$for_select->distinct = true;
									$for_select = $this->select($for_select);?>
									
									<div class="input-group">
										<div style="width: 200px;" class="input-group-addon"><?php echo $key ?></div>
										<select name="<?= $key; ?>" style="width:450px;" class="form-control "><?
											foreach($for_select as $row_select)
											{?>
												<option value="<?= $row_select->{$option['CHAMPS'][array_search($key, $option['CHAMPS'])]} ?>"><?= $row_select->{$option['CHAMPS'][array_search($key, $option['CHAMPS'])]} ?></option><?
											}?>
										</select>
									</div><br><?
								}
							}
							else
							{?>
								<div class="form-group <?
		                            if($key == 'id')
		                                echo  'has-error';
		                            else
	    	                            echo 'has-success';
        	                    	?>" style="margin-right:30px;">

	                                <div class="input-group">
	                                    <div style="width: 200px;" class="input-group-addon"><?php echo $key ?></div>
	                                    <input style='width:450px;' type="text"
	                                        <?php echo ($key == 'id')? 'disabled id="disabledInput"':'id="inputSuccess1"'; ?>
	                                        <?php echo ($key == 'id_category')? 'disabled id="disabledInput" placeholder="'.$value_2.'"':'id="inputSuccess1"'; ?>
	                                           class="form-control" name="<?php echo $key ?>">

	                                </div>
	                            </div><?
							}
	                    }?>
	                    <button style="width: 650px; margin-top:15px;" type="submit" class="btn btn-default">Submit</button>
	                </form>
	            </div>
	        </div>
	    </div><?
	}
}

?>