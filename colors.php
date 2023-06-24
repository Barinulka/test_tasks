<?php 

$CMarray = [1 => 'a', 2 => 'b', 3 => 'c', 4 => 'd', 5 => 'e', 6 => 'f', 7 => 'g', 8 => 'h'];
$colors = [0 => 'Белый', 1 => 'Черный'];

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

    $result = $cell1 . ' : ' . $cell2 . "\r\n";

    // Приведение к нижнему регистру
    $cell1 = trim(strtolower($cell1));
    $cell2 = trim(strtolower($cell2));

    $cell1Arr = str_split($cell1);
    $cell2Arr = str_split($cell2);

    // проверки
    if ((count($cell1Arr) < 2 || count($cell1Arr) > 2) || (count($cell2Arr) < 2 || count($cell2Arr) > 2)) {
        return $result . "Проверьте корректность введенных данных";
    }
    if (!in_array($cell1Arr[0], $CMarray) || !in_array($cell2Arr[0], $CMarray)) {
        return $result . "Указана несуществующая клетка";
    }
    if (($cell1Arr[1] <= 0 || $cell1Arr[1] > 8) || ($cell2Arr[1] <= 0 || $cell2Arr[1] > 8)) {
        return $result . "Указана несуществующая клетка";
    }

    // полчуние цвета каждой ячейки
    $cell1Color = getCellColor($cell1Arr);
    $cell2Color = getCellColor($cell2Arr);

    $result .= $colors[$cell1Color] . ' : ' . $colors[$cell2Color] . "\r\n";

    if ($cell1Color == $cell2Color) {
        $result .= 'Цвета совпадают';
    } else {
        $result .= 'Цвета не совпадают';
    }
    
    return $result;
}

/**
 * Функция определяет цвет ячейки по сумме цифровых значений ячейки, 
 * при условии что a = 1, b = 2, c = 3 ..., h = 8 (массив CMarray)
 * если сумма четная (1) - то ячейка черная
 * если не четная (0) - то ячейка белая
 *
 * @param array $arr
 * @return bool
 */
function getCellColor(array $arr) :bool
{
    global $CMarray;

    $sum = array_search($arr[0], $CMarray) + $arr[1];
    
    return isEven($sum);
    
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

echo sameColor('a1', 'C6') . PHP_EOL;