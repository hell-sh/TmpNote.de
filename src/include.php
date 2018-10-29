<?php
// Copyright (c) 2018, Hellsh

function str_rand($length = 11)
{
	$letters = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
	foreach(range("a", "z") as $letter)
	{
		array_push($letters, $letter);
	}
	$str = "";
	for($i=0;$i<$length;$i++)
	{
		$str .= $letters[array_rand($letters)];
	}
	return $str;
}

// https://github.com/hell-sh/PHP-DBAPI
class DBAPI
{
	private $connected = false;
	private $db = null;

	function query()
	{
		if(!$this->connected)
		{
			$this->db = new mysqli("localhost", "root", "", "other");
			$this->connected = true;
		}
		$arg = func_get_args();
		$res = NULL;
		if(count($arg) > 0)
		{
			if(count($arg) > 2)
			{
				if($stmt = $this->db->prepare($arg[0]))
				{
					$i = 0;
					$bind_names[] = $arg[1];
					foreach($arg as $i => $a)
					{
						if($i > 1)
						{
							$bind_name = 'bind'.$a;
							$$bind_name = $a;
							$bind_names[] = &$$bind_name;
						}
						$i++;
					}
					call_user_func_array(array($stmt,'bind_param'),$bind_names);
					$stmt->execute();
					$res = $stmt->get_result();
					if($res instanceof mysqli_result)
					{
						$fiels = json_decode(json_encode($res->fetch_fields()), true);
						$res = $res->fetch_all();
						$stmt->close();
						$nres = array();
						foreach($res as $row)
						{
							$nrow = array();
							foreach($row as $i => $val)
							{
								$nrow[$fiels[$i]["name"]] = $val;
							}
							array_push($nres, $nrow);
						}
						$res = $nres;
					} else
					{
						return $res;
					}
				}
			} elseif(count($arg) < 2)
			{
				if($query = $this->db->query($arg[0]))
				{
					if($query instanceof mysqli_result)
					{
						$res = array();
						while($r = $query->fetch_assoc())
						{
							array_push($res, $r);
						}
					} else
					{
						return $query;
					}
				}
			} else
			{
				die("DBAPI::query can't have only 2 Arguments.");
			}
		} else
		{
			die("DBAPI::query needs at least 1 Argument.");
		}
		return $res;
	}
}
$db = new DBAPI();
