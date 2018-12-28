<?php

	function CloudGet($busket,$prefix,$file,$localfile) {
		return false;
	}
	function CloudPut($localfile,$busket,$prefix) {
		@file_put_contents('/www/s3/oldbk/'.$prefix.'/'.basename($localfile),@file_get_contents($localfile));
	}
	function CloudDelete($busket,$prefix,$file) {
		if(file_exists('/www/s3/oldbk/'.$prefix.$file)) {
			@unlink('/www/s3/oldbk/'.$prefix.$file);
		}
	}
	function CloudSetACL($busket,$prefix,$file,$rights) {
		return true;
	}

	function CloudFlush($file) {
		return true;
	}

?>
