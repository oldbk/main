<?php
class User implements ArrayAccess {
    private $data = array();
	
    private function __construct($row) {
	    $this->data = $row;
	}

    public static function getUser($id) {
	    $query = mysql_query(sprintf("SELECT * FROM users WHERE id = %d", $id));
		if(($row = mysql_fetch_assoc($query)) !== false) {
		    return new User($row);
		} else {
		    return false;
		}
	}
	
	public function isAdmin() {
	    return $this->data['align'] > 2 && $this->data['align'] < 3;
	}
	
	public function isAuth() {
	    return !empty($this->data);
	}
	
	public function drawLogin() {
	$clan = $this->data['klan'] != "" ? '<img title="'.$this->data['klan'].'" src="https://i.oldbk.com/i/klan/'.$this->data['klan'].'.gif">' : "";
		
return <<<EOF

<img src="https://i.oldbk.com/i/align_{$this->data['align']}.gif">{$clan}<b>{$this->data['login']}</b> [{$this->data['level']}] <a href="inf.php?{$this->data['id']}" target="_blank"><img src="https://i.oldbk.com/i/inf.gif" width="12" height="11" alt="Инф. о {$this->data['login']}"></a>

EOF;
	} 
	
	public function getOut() {
	    print 'exit...';
		exit;
	}
		
	/*******************************************/
	public function offsetExists( $offset ) {
        return isset( $this->data[$offset]);
    }

    public function offsetSet( $offset, $value) {
        $this->data[$offset] = $value;
    }

    public function offsetGet( $offset ) {
        return $this->data[$offset];
    }

    public function offsetUnset( $offset ) {
        unset( $this->data[$offset]);
    }
	/*******************************************/
}
?>