<?php
	class StringConverter{
		public static function ConvertTo1251($str){
			return iconv('utf-8','windows-1251',$str);
		}
		public static function ConvertToUTF8($str){
			return iconv('windows-1251','utf-8',$str);
		}
		public static function ConvertToPostgreString($arr){
			$str='{';
			for($i=0;$i<count($arr);$i++){
				$str=$str.$arr[$i];
				if($i!=count($arr)-1)
					$str=$str.',';
			}
			$str=$str.'}';
			return $str;
		}
		
		//‘ункци€ превращ€ет строку типа {1,2,3} в массив соответствующих чисел
		
		public static function GetArrayFromPostgreString($pstr){
			$a=array();
			$a_c=0;
			for($i=0;$i<strlen($pstr);){
				if($pstr[$i]!='{' and $pstr[$i]!='}' and $pstr[$i]!=',' and $pstr[$i]!='"'){
					$number=$pstr[$i];
					$j=1;
					while(true){
						if($pstr[$i+$j]!='{' and $pstr[$i+$j]!='}' and $pstr[$i+$j]!=',' and $pstr[$i+$j]!='"'){
							$number=$number.$pstr[$i+$j];
							$j=$j+1;
						}
						else
							break;
					}
					$a[$a_c]=$number;
					$a_c=$a_c+1;
					$i=$i+$j;
				}
				else $i=$i+1;
			}
			return $a;
		}
		
		public static function GetArrayFromAccArray($a,$name){
			$arr=array();
			for($i=0;$i<count($a);$i++){
				$arr[$i]=$a[$i][$name];
			}
			return $arr;
		}
	}
?>