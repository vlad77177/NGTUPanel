<?php // ������� ������� ��������
class Parser
{
    /* ����� ������� ��������������� �������
    ** ������� ��������.
    ** @param path $way ������������� ���� � �������
    ** @param array $labels ������ ����� � �� ��������
    ** @return text ���������� ��������� ��������
    */
    public function TemplateParse($way, $labels){
        // �������� ���������� ����� �������
        $cont = $this->GetContent($way);
        // ������� ��������������� ������� �������
        $cont = $this->LabelsReplace($cont, $labels);
        // ���������� ��������� ��������
        return $cont;
    }
 
    /* ����� ��������� �������.
    ** @param path $way ������������� ���� � �������
    ** @return text ���������� ������
    */
    public function GetTemplateContent($way) {
        // ������� ��������� �������
        return $this->GetContent($way);
    }
    
    /* ����� ������ ����� ����������.
    ** @param text $cont ������
    ** @param array $labels ������ ����� � �� ��������
    ** @return text ���������� ������
    */
    public function LabelsReplace($cont, $labels) {
        // �������
        foreach ( $labels as $key => $val ) {
            // ������ ����� � ���������.
            $cont = preg_replace('/\{'.$key.'\}/', $val, $cont);
        }
        // ������� �������.
        return $cont;
    }
    
    /* ����� ��������� �������.
    ** @param path @way ������������� ���� � �������
    ** @return text ���������� ������
    */
    public function GetContent($way) {
        // ������������� ����������� php �-�.
        return file_get_contents($way);
    }
    
    /* ����� ������ �����������.
    ** @param text $page ���������� ��� ������
    ** @param bool $utf8 ���� ������ � ������������ ���������
    */
    
    public function PageOut($page, $utf8=false) {
        // �������� � ��������?
        if( $utf8 ) {
            // ��. �������������� � cp1251 � utf8. �����.
            echo iconv("CP1251", "UTF-8", $page);
        } else {
            // ���. ������� �����.
            echo $page;
        }
    }
} ?>