<?php
//����������� ����� ��� ����������� item, 
abstract class ItemBuilder{
	
	protected $intable;//��� �������
	protected $colnames;//������ � ���������� ��������
	protected $values;//������ ��������
	
	function GetTable(){
		return $this->intable;
	}
	function GetColumns(){
		return $this->colnames;
	}
	function GetValues(){
		return $this->values;
	}
}
?>