<?php 

$CMarray = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
$colors = [0 => 'Черный', 1 => 'Белый'];

/**
 * Принимает названия 2-ух ячеек
 * находит цвета каждой ячейки и сравнивает их
 *
 * @param string $cell1
 * @param string $cell2
 * @return string
 */
function sameColor(string $cell1, string $cell2) :string 
{
    global $CMarray, $colors;

    $result = '';

    // Приведение к нижнему регистру
    $cell1 = trim(strtolower($cell1));
    $cell2 = trim(strtolower($cell2));

    $cell1Arr = str_split($cell1);
    $cell2Arr = str_split($cell2);

    // проверки
    if ((count($cell1Arr) < 2 || count($cell1Arr) > 2) || (count($cell2Arr) < 2 || count($cell2Arr) > 2)) {
        return "Проверьте корректность введенных данных";
    }
    if (!in_array($cell1Arr[0], $CMarray) || !in_array($cell2Arr[0], $CMarray)) {
        return "Указана несуществующая клетка";
    }
    if (($cell1Arr[1] <= 0 || $cell1Arr[1] > 8) || ($cell2Arr[1] <= 0 || $cell2Arr[1] > 8)) {
        return "Указана несуществующая клетка";
    }

    $cell1Color = getCellColor($cell1Arr);
    $cell2Color = getCellColor($cell2Arr);

    if ($cell1Color == $cell2Color) {
        $result =  $cell1 . ' : ' . $cell2 . "\r\n" . $colors[$cell1Color] . ' : ' . $colors[$cell2Color] . "\r\n" . 'Цвета совпадают';
    } else {
        $result =  $cell1 . ' : ' . $cell2 . "\r\n" . $colors[$cell1Color] . ' : ' . $colors[$cell2Color] . "\r\n" . 'Цвета не совпадают';
    }
    
    return $result;
}

/**
 * Функция определяет цвет ячейки
 * 0 - черный
 * 1 - белый
 *
 * @param array $arr
 * @return integer
 */
function getCellColor(array $arr) :int
{
    global $CMarray;

    $col = isEven(array_search($arr[0], $CMarray) + 1);
    $row = isEven($arr[1]);
    
    if ($col == $row) {
        return 0;
    } else {
        return 1;
    }

}

/**
 * Функция определяет четное или не четное число
 *
 * @param integer $num
 * @return boolean
 */
function isEven (int $num) :bool 
{
    if ($num % 2 == 0) {
        return true; 
    } else {
        return false;
    }
}

echo sameColor('a1', 'c6') . PHP_EOL;