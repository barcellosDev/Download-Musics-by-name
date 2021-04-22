<?php
define('DS', DIRECTORY_SEPARATOR);
define('dirM', 'downloaded_musics');
define('dirV', 'downloaded_videos');

$file = 'treat_errors'.DS.'musicNames.txt';

if (file_exists($file)) {
	$musicNames = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES | FILE_TEXT);

	if (!isEmptyDir(dirM)) {
		$musics = removeDots(scandir(dirM));
		$result = hasDifferent($musics, $musicNames, 'music');

		if (gettype($result) == 'array') {
			writeResultsToFile($result, 'musicas');
			echo "[-] Algumas musicas não foram baixadas, elas estão salvas num arquivo pra você. \n";
			echo "[!] Arquivo: listaMUSICAS.txt\n";
		} else {
			echo $result."\n";
		}
	}
	if (!isEmptyDir(dirV)) {
		$videos = removeDots(scandir(dirV));
		$result = hasDifferent($videos, $musicNames, 'videos');

		if (gettype($result) == 'array') {
			writeResultsToFile($result, 'videos');
			echo "[-] Algumas videos não foram baixadas, elas estão salvas num arquivo pra você. \n";
			echo "[!] Arquivo: listaVIDEOS.txt\n";
		} else {
			echo $result."\n";
		}
	}
} else {
	echo "[-] Tem certeza que iniciou o programa?";
}

function writeResultsToFile($result, $type)
{
	$f = fopen('lista'.strtoupper($type).'.txt', 'w');
	foreach ($result as $key => $value) {
		fwrite($f, $value."\n");
	}
	fclose($f);
}

function removeDots($array)
{
	if ($array[0] == '.' && $array[1] == '..') {
		unset($array[0]);
		unset($array[1]);
	}
	return array_values($array);
}

function getId($fileNames)
{
	$len = strlen($fileNames);
	$proibidos = ['(', ')', ' ', '[', ']'];

	for ($i=($len-1); $i > 0; $i--) {
		if (!in_array($fileNames[$i], $proibidos)) {
			if ($fileNames[$i] === '-')
				$pos = $i;
		} else {
			break;
		}
	}

	$start = ++$pos;
	$end = strrpos($fileNames, '.') - $pos;
	$id = substr($fileNames, $start, $end);

	if (strlen($id) > 11)
		$id = substr($id, strpos($id, '-')+1);

	return $id;
}

function hasDifferent($arrFiles, $musicNames)
{
	foreach ($arrFiles as $key => $value) {
		$filesId[] = getId($value);
	}
	foreach ($musicNames as $key => $id) {
		if (!in_array($id, $filesId)) {
			$linksToDown[] = 'https://www.youtube.com/watch?v='.$id;
		}
	}
	return (isset($linksToDown) && count($linksToDown) > 0) ? $linksToDown : 'Nenhum erro foi encontrado!';
}

function isEmptyDir($dir)
{
	$dirToScan = scandir($dir);
	$empty = null;

	if (count($dirToScan) > 2) {
		$empty = false;
	} else {
		$empty = true;
	}
	return $empty;
}
?>