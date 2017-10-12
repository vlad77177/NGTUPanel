<?php
/* класс проверки безопасности и прочего */
	class SecurityWorker{
		public static function CheckInsertString($str){
			return pg_escape_string($str);
		}
		/* проверка на возможность добавить данные */
		public static function CheckTheRightOfAdd(User $user,DataPattern $pattern,DBWorker $dbw){
			//если пользователь, то проверю есть ли право на добавление
			if($user->GetUserRole()==3){
				$user_groups=$dbw->GetGroupsUserIn($user->GetID());
				for($i=0;$i<count($user_groups);$i++){
					$add_right=StringConverter::GetArrayFromPostgreString($user_groups[$i]['add']);
					for($j=0;$j<count($add_right);$j++){
						$idr=StringConverter::GetArrayFromPostgreString($pattern->GetNeededRights());
						if($add_right[$j]==$idr[0])
							return true;
					}
				}
				return false;
			}
			else return true;
		}
		/* логично нужны функции на проверку чтения и удаления */
		
	}
?>