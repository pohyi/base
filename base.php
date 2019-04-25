<?php
$token = $_POST['token']; //Получаем токен из Post параметров
if (!empty($token)) //Проверяем его наличие
{
$hwid = $_POST['hwid']; //Получаем hwid из Post параметров
if (!empty($hwid)) //Проверяем его наличие 
{
include_once 'Crypt/RSA_XML.php'; //Подключаем скрипт для работы с RSA
include_once 'globalsettings.php'; //Подключаем скрипт с нашими настройками


mysql_connect($MySQL_hostname,$MySQL_username,$MySQL_password); //Создаём и открываем соединение с MySQL
mysql_select_db($MySQL_databasename); //Указываем базу данных для работы
$command = "SELECT * FROM $MySQL_table WHERE `kod` = '$hwid'"; //Объявлем комманду
$response = mysql_query($command); //Выполняем комманду и получаем ответ
$count = mysql_num_rows($response); //Выводим количество полученных рядков с ответа
$row=mysql_fetch_array($response); //Преобразовываем рядки в массив

$output; //Переменная, в которую будем добавлять информацию

if ($count == 1) //Если число рядков равно 1
{
$curTime = date('d.m.Y H:i:s', time()); //Получаем текущее время (зависит от сервера)
$endTime = $row['End Time']; //Получаем время истечения срока лицензии
$output  .='license=' . '1' . "\r\n"; //Добавляем к переменной значение license=1
$output  .='[[' . base64_encode($hwid) . ']]' . "\r\n";
$output  .='CT=' . $curTime . "\r\n"; //Добавляем к переменной значение CT=текущее время
$output  .='ET=' . $endTime . "\r\n"; //Добавляем к переменной значение ET=время когда истечёт лицензия
}
else if ($count == 0) //Тогда если число рядков равно 0
{
$output  .='license=' . '0' . "\r\n"; //Добавляем к переменной значение license=0
}

$output  .= preg_replace("/ /","+",$token) . "\r\n"; //Добавляем к переменной значение токена, т.к. php заменяет + на пробел, я не нашёл решения и просто делаю замену " " на "+"

$rsa = new Crypt_RSA_XML(); //Объявлем экземпляр класса RSA
$rsa->loadKeyfromXML($RSAprivateKey,2); //Загружаем приватный ключ в XML формате
$signature = $rsa->sign($output); //Подписываем переменную с информацией 

$output .='[[' . base64_encode($signature) . ']]' . "\r\n"; //Добавляем к переменной значение подписи в [[ ]]

echo $output; //Выводим переменную с информацией
}
}
?>