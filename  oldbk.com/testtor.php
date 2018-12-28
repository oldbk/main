	<?
	
	function test_tor_ip($ip)
	{
	 $tor_file='/www/oldbk.com/tor/data';
	   if (file_exists($tor_file))
		{
		$lines=file($tor_file);
			foreach ($lines as $line_num => $line) 
			{
				if ($ip==trim($line))
					{
					return true;
					}
			}
		}
		else
		{
		return false;
		}
	return false;
	}
	?>