<?
if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}

$number_images = 8; // ����� �������� ��� ������, max 8
$first = 1; // �� ��������� ������ �������� - 1
$random = mt_rand($first, $number_images);

//target=\"_blank\" 
if ($random==1)(
$pic="�� ��������� <a border=\"0\" title=\"����� �� ����!!!!!!\" href=\"my.php\">������</a> ����� �������� \"��� ����������\" ��������� �� ������ ���� � �������������..."
);

if ($random==2)(
$pic="��� �������� ������� � ���� \"�������\" (������) ������ �� ����� ���������, ������� ������� ���� ��� ������� �� ���!"
);

if ($random==3)(
$pic="��� �������� �������, ���� ���� ����� ������ ��������� � ���������, �� �����: �������� � ������ ��� �������� ���������� �����. ������� ������: ����������� ���� � ��������"
);

if ($random==4)(
$pic="����� ����, ��� �� ������ �������, �� ��� ��� ���� ��� �� ��������� ������������� - �� �� ������ ��� ��������� (����������)"
);

if ($random==5)(
$pic="�� ������ ��� �����������, �� ��������� 1 �����, ����� �� ����� �������� �� ������ ��� ������ (�����������)"
);

if ($random==6)(
$pic="TAGS - ��� ����� ����, ������� ������������� �������. ���� ������� ��� ����, ����� ����������� ������������ ��������. ��� �������� �������� �� ������ ������� ��������� ���� ����� �������. ����� ���������� ���� ����� ����������� <a border=\"0\" title=\"�������� ���������\" href=\"browse.php\">���</a> - ���� \"������ �����\""
);

if ($random==7)(
$pic="������ ������� ����� �� ������ �� ��� �����, �� � �� ��������."
);

if ($random==8)(
$pic="ViKa - ��� �� �� (���������� ������), ����������� ���, ��������� ��� ������� � ����."
);


$content.= "<center>$pic</center>";


$blocktitle = "� ������ ��???";
?>