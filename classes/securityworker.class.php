<?php
	class SecurityWorker{
		public function CheckInsertString($str){
			return pg_escape_string($str);
		}
	}
?>