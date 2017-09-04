<?php // ������� ������� ��������
class Parser
{
    /* ����� ������� ��������������� �������
    ** ������� ��������.
    ** @param path $way ������������� ���� � �������
    ** @param array $labels ������ ����� � �� ��������
    ** @return text ���������� ��������� ��������
    */
    public static function TemplateParse($way, $labels){
        // �������� ���������� ����� �������
        $cont = Parser::GetContent($way);
        // ������� ��������������� ������� �������
        $cont = Parser::LabelsReplace($cont, $labels);
        // ���������� ��������� ��������
        return $cont;
    }
 
    /* ����� ��������� �������.
    ** @param path $way ������������� ���� � �������
    ** @return text ���������� ������
    */
    private static function GetTemplateContent($way) {
        // ������� ��������� �������
        return Parser::GetContent($way);
    }
    
    /* ����� ������ ����� ����������.
    ** @param text $cont ������
    ** @param array $labels ������ ����� � �� ��������
    ** @return text ���������� ������
    */
    private static function LabelsReplace($cont, $labels) {
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
    private static function GetContent($way) {
        // ������������� ����������� php �-�.
        return file_get_contents($way);
    }
    
    /* ����� ������ �����������.
    ** @param text $page ���������� ��� ������
    ** @param bool $utf8 ���� ������ � ������������ ���������
    */
    
    public static function PageOut($page, $utf8=false) {
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