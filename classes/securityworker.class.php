<?php
	class SecurityWorker{
		public static function CheckInsertString($str){
			return pg_escape_string($str);
		}
		
		public static function CheckTheRightOfAdd($user,$data_pattern,DBWorker $dbw){
			if($user[0]['user_role']==3){
				$user_groups=$dbw->GetGroupsUserIn($user[0]['id']);
				for($i=0;$i<count($user_groups);$i++){
					$add_right=StringConverter::GetArrayFromPostgreString($user_groups[$i]['add']);
					for($j=0;$j<count($add_right);$j++){
						$idr=StringConverter::GetArrayFromPostgreString($data_pattern[0]['needed_right']);
						if($add_right[$j]==$idr[0])
							return true;
					}
				}
				return false;
			}
			else return true;
		}
		
	}
?>