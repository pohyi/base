<?php
$token = $_POST['token']; //�������� ����� �� Post ����������
if (!empty($token)) //��������� ��� �������
{
$hwid = $_POST['hwid']; //�������� hwid �� Post ����������
if (!empty($hwid)) //��������� ��� ������� 
{
include_once 'Crypt/RSA_XML.php'; //���������� ������ ��� ������ � RSA
include_once 'globalsettings.php'; //���������� ������ � ������ �����������


mysql_connect($MySQL_hostname,$MySQL_username,$MySQL_password); //������ � ��������� ���������� � MySQL
mysql_select_db($MySQL_databasename); //��������� ���� ������ ��� ������
$command = "SELECT * FROM $MySQL_table WHERE `kod` = '$hwid'"; //�������� ��������
$response = mysql_query($command); //��������� �������� � �������� �����
$count = mysql_num_rows($response); //������� ���������� ���������� ������ � ������
$row=mysql_fetch_array($response); //��������������� ����� � ������

$output; //����������, � ������� ����� ��������� ����������

if ($count == 1) //���� ����� ������ ����� 1
{
$curTime = date('d.m.Y H:i:s', time()); //�������� ������� ����� (������� �� �������)
$endTime = $row['End Time']; //�������� ����� ��������� ����� ��������
$output  .='license=' . '1' . "\r\n"; //��������� � ���������� �������� license=1
$output  .='[[' . base64_encode($hwid) . ']]' . "\r\n";
$output  .='CT=' . $curTime . "\r\n"; //��������� � ���������� �������� CT=������� �����
$output  .='ET=' . $endTime . "\r\n"; //��������� � ���������� �������� ET=����� ����� ������� ��������
}
else if ($count == 0) //����� ���� ����� ������ ����� 0
{
$output  .='license=' . '0' . "\r\n"; //��������� � ���������� �������� license=0
}

$output  .= preg_replace("/ /","+",$token) . "\r\n"; //��������� � ���������� �������� ������, �.�. php �������� + �� ������, � �� ����� ������� � ������ ����� ������ " " �� "+"

$rsa = new Crypt_RSA_XML(); //�������� ��������� ������ RSA
$rsa->loadKeyfromXML($RSAprivateKey,2); //��������� ��������� ���� � XML �������
$signature = $rsa->sign($output); //����������� ���������� � ����������� 

$output .='[[' . base64_encode($signature) . ']]' . "\r\n"; //��������� � ���������� �������� ������� � [[ ]]

echo $output; //������� ���������� � �����������
}
}
?>