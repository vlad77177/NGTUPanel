<?php // Базовые функции парсинга
class Parser
{
    /* Метод полного грамматического разбора
    ** шаблона страницы.
    ** @param path $way относительный путь к шаблону
    ** @param array $labels массив меток и их значений
    ** @return text возвращает собранную страницу
    */
    public static function TemplateParse($way, $labels){
        // Получаем содержимое файла шаблона
        $cont = Parser::GetContent($way);
        // Функция грамматического разбора шаблона
        $cont = Parser::LabelsReplace($cont, $labels);
        // Возвращаем собранную страницу
        return $cont;
    }
 
    /* Метод получения шаблона.
    ** @param path $way относительный путь к шаблону
    ** @return text возвращает шаблон
    */
    private static function GetTemplateContent($way) {
        // Простое получение шаблона
        return Parser::GetContent($way);
    }
    
    /* Метод замены меток значениямм.
    ** @param text $cont шаблон
    ** @param array $labels массив меток и их значений
    ** @return text возвращает шаблон
    */
    private static function LabelsReplace($cont, $labels) {
        // Перебор
        foreach ( $labels as $key => $val ) {
            // Замена метки её значением.
            $cont = preg_replace('/\{'.$key.'\}/', $val, $cont);
        }
        // Возврат шаблона.
        return $cont;
    }
    
    /* Метод получения шаблона.
    ** @param path @way относительный путь к шаблону
    ** @return text возвращает шаблон
    */
    private static function GetContent($way) {
        // Использование стандартной php ф-и.
        return file_get_contents($way);
    }
    
    /* Метод вывода гипертекста.
    ** @param text $page содержимое для вывода
    ** @param bool $utf8 флаг вывода в кирилической кодировке
    */
    
    public static function PageOut($page, $utf8=false) {
        // Выводить в кирилице?
        if( $utf8 ) {
            // Да. Перобразование с cp1251 в utf8. Вывод.
            echo iconv("CP1251", "UTF-8", $page);
        } else {
            // Нет. Простой вывод.
            echo $page;
        }
    }
} ?>