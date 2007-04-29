<?php

class table
{
	var $name;
	var $columns;
	var $hashtable;
	
	function table($name, $from_db = false)
	{
		$this->name = $name;
		$this->columns = array();
		$this->hashtable = array();
		
		if($from_db)
		{
			$desc = db_query("DESCRIBE ".$this->name);
			while($col = db_fetch($desc))
			{
				$this->add_column($col->Field, $col->Type, $col->Key, $col->Null, $col->Default, $col->Extra);
			}
		}
	}
	
	function add_column($name, $type, $key, $null, $default, $extra)
	{
		$newcol = & new table_column($name, $type, $key, $null, $default, $extra);
		$this->columns[] = &$newcol;
		$this->hashtable[$name] = &$newcol;
	}
	
	function print_all_cols()
	{
		echo "<b>Table: ".$this->name."</b><br>";
		foreach($this->columns as $col)
		{
			echo $col->name." - ".$col->generate_query()."<br>";
		}
	}
	
	// Compare this table with the supplied reference table
	function cmp(&$tbl)
	{
		$query = "ALTER TABLE ".$this->name;
		$rawquery = array();
		
		foreach($tbl->columns as $index => $foreign_col)
		{
			if(array_key_exists($foreign_col->name, $this->hashtable))
			{
				if(!$this->hashtable[$foreign_col->name]->cmp($foreign_col))
				{
					$rawquery[] = " CHANGE ".$foreign_col->name." ".$foreign_col->generate_query();
				}
			}
			else
			{
				$rawquery[] = " ADD ".$foreign_col->generate_query();
				
				if($foreign_col->key)
				{
					if(!strcasecmp($foreign_col->key, "PRI"))
						$primary_key = $foreign_col->name;
					if(!strcasecmp($foreign_col->key, "UNI"))
						$unique_key = $foreign_col->name;
				}
			}
		}
		
		foreach($this->hashtable as $index => $oldcol)
		{
			if(!array_key_exists($index, $tbl->hashtable))
			{
				$rawquery[] = " DROP ".$index;
			}
		}
		
		for($i = 0; $i < count($rawquery); $i++)
		{
			$query .= $rawquery[$i];
			
			if($i < count($rawquery)-1)
				$query .= ",";
		}
		
		if(isset($primary_key))
			$query .= ", ADD PRIMARY KEY($primary_key)";
		if(isset($unique_key))
			$query .= ", ADD UNIQUE KEY($unique_key)";
		
		if(count($rawquery))
			db_query($query);
	}
	
	// Create this table
	function create()
	{
		$query = "CREATE TABLE ".$this->name." (";
		$rawquery = array();
		$keys["pri"] = array();
		$keys["uni"] = array();
		
		foreach($this->columns as $index => $col)
		{			
			$rawquery[] = $col->generate_query();

			if($col->key)
			{
				if(!strcasecmp($col->key, "PRI"))
					$keys["pri"][] = $col->name;
				if(!strcasecmp($col->key, "UNI"))
					$keys["uni"][] = $col->name;
			}
		}
		
		for($i = 0; $i < count($rawquery); $i++)
		{
			$query .= $rawquery[$i];
			
			if($i < count($rawquery)-1)
				$query .= ",";
		}
		
		if($numkeys = count($keys["pri"]))
		{
			$query .= ", PRIMARY KEY(";
			for($i=0; $i < $numkeys; $i++)
			{
				$query .= $keys["pri"][$i];
				
				if($i < $numkeys - 1)
					$query .= ", ";
			}
			$query .= ")";
		}
		if($numkeys = count($keys["uni"]))
		{
			$query .= ", UNIQUE KEY(";
			for($i=0; $i < $numkeys; $i++)
			{
				$query .= $keys["uni"][$i];
				
				if($i < $numkeys - 1)
					$query .= ", ";
			}
			$query .= ")";
		}
		
		
		$query .= ")";
		
		echo $query;
		
		if(count($rawquery))
			db_query($query);
	}
}

class table_column
{
	var $name;
	var $type, $key;
	var $null, $default, $extra;
	var $internal_flag;
	
	function table_column($name, $type, $key, $null, $default, $extra)
	{
		$this->null = false;
		if(empty($null))
			$null = "no";
		
		$key = trim($key);
		
		$this->name = $name;
		$this->type = $type;
		$this->key = empty($key) ? null : $key;
		$this->null = strcasecmp($null, "yes") ? false : true;
		$this->default = $default;
		$this->extra = $extra;
	}
	
	// Compare this column with supplied reference column (usually from a 'describe')
	// Returns true if equal, false if unequal
	function cmp(&$col)
	{
		$changed = array();
			
		if(strcasecmp($this->type, $col->type))
		{
			$changed["type"] = $col->type;
		}
		if(strcasecmp($this->key, $col->key))
		{
			$changed["key"] = $col->key;
		}
		if($this->null =! $col->null)
		{
			$changed["null"] = $col->null;
		}
		if(strcasecmp($this->default, $col->default))
		{
			$changed["default"] = $col->default;
		}
		if(strcasecmp($this->extra, $col->extra))
		{
			$changed["extra"] = $col->extra;
		}
		
		return count($changed) ? false : true;
	}
	
	// Generates parameters query for column
	// Returns string
	function generate_query()
	{
		$this->default = trim($this->default);
		
		$query = $this->name." ".$this->type;
		
		if(null)
			$query .= " NULL";
		
		/*
		if(strtoupper($this->key) == "PRI")
			$query .= " PRIMARY KEY";
		elseif(strtoupper($this->key) == "UNI")
			$query .= " UNIQUE KEY";
		*/
		
		if(($this->default && !empty($this->default)) || (is_numeric($this->default) && $this->default == 0))
		{
			//echo $this->default."<br>";
			$query .= " DEFAULT '".$this->default."'";
		}
		
		if($this->extra && !empty($this->extra))
			$query .= " ".$this->extra;
		
		return $query;
	}
}
?>
