<?php
/**
 * Script desenvolvido por Alan Barcellos no dia 08/12/2019 as 22:15
 */
require_once 'curlYoutube.php';

class Youtube_dl_aux extends curlYoutube {
  private $lista;
  public $dir_musica = 'downloaded_musics';
  public $dir_videos = 'downloaded_videos';
  private $_input;
  private $f;

  function __construct($lista) {
    $this->lista = (isset($lista)) ? $lista : null;

    if ($this->lista != null) {
      $this->f = fopen('treat_errors'.DIRECTORY_SEPARATOR.'musicNames.txt', 'w');

      if (!is_dir($this->dir_musica)) {
        mkdir($this->dir_musica);
      }

      if (!is_dir($this->dir_videos)) {
        mkdir($this->dir_videos);
      }
    } else {
      echo "]: Por favor selecione uma lista para ler";
      exit();
    }
  }

  function __destruct() {
    $this->curlClose();
    
    if ($this->lista != null) {
      fclose($this->f); // Closing fopen handle
      pclose(popen('start cmd /c php treat_errors'.DIRECTORY_SEPARATOR.'treatErrors.php ^& pause', 'r'));
    }
  }

  private function Download($opcao, $link, $videoTitle, $videoNotepad) {
    echo "[...] Downloading... (".$link.") => ".$videoTitle." \n";
    echo "[!] Titulo original escrito: ".$videoNotepad." \n\n";

    shell_exec('youtube-dl '.$opcao.' '.$link);
    echo '[+] Downloaded!'."\n";
    echo "-------------------------------------------------------- \n\n";
  }

  private function moverArq() {
    $dir_atual = scandir('.');

    for ($i=0; $i < count($dir_atual); $i++) {
      if (strpos($dir_atual[$i], '.mp3') !== FALSE) {
          rename($dir_atual[$i], $this->dir_musica.DIRECTORY_SEPARATOR.$dir_atual[$i]);

      } elseif (strpos($dir_atual[$i], '.mp4') !== FALSE) {
          rename($dir_atual[$i], $this->dir_videos.DIRECTORY_SEPARATOR.$dir_atual[$i]);
      }
    }
  }

  private function downloadSelectedVideos($lista) {
    $listOfNames = file($lista, FILE_IGNORE_NEW_LINES | FILE_TEXT | FILE_SKIP_EMPTY_LINES);

    switch ($this->_input) {
      case 'm':
        $this->call_moverArqFunc($listOfNames, '-x --audio-format mp3');
        break;

      case 'v':
        $this->call_moverArqFunc($listOfNames, '--format mp4');
        break;

      case 'mv':
        $this->call_moverArqFunc_mv($listOfNames);
        break;

      case 'n':
        $this->call_moverArqFunc($listOfNames, '-x --audio-format mp3', 'n');
        break;

      default:
        echo 'Bye ;)';
        exit();
        break;
    }
  }

  private function call_moverArqFunc_mv($list) {
    foreach ($list as $value) {
      if (strrpos($value, '.v') !== FALSE) {
        $link = substr($value, 0, strrpos($value, '.v'));
        $opcao = '--format mp4';
      } else {
        $link = $value;
        $opcao = '-x --audio-format mp3';
      }

      $fileID = $this->getId($link);
      $this->Download($opcao, $link, $this->getTitleVideo($link), $value);
      fwrite($this->f, $fileID."\n");
      $this->moverArq();
    }
  }

  private function getId($link) {
    $start = strpos($link, 'v=');
    $res = substr($link, $start + 2);
    return $res;
  }

  private function call_moverArqFunc($list, $opcao, $choose = '') {
    foreach ($list as $value) {
      if ($choose == 'n') {
        $linkVideo = $this->getLinkVideo($this->exec_curl($value));
        $titleVideo = $this->getTitleVideo($linkVideo);
        $fileID = $this->getId($linkVideo);

        $this->Download($opcao, $linkVideo, $titleVideo, $value);
        fwrite($this->f, $fileID."\n");
        $this->moverArq();
      } else {
        $titleVideo = $this->getTitleVideo($value);
        $fileID = $this->getId($value);

        $this->Download($opcao, $value, $titleVideo, $value);
        fwrite($this->f, $fileID."\n");
        $this->moverArq();
      }
    }
  }

  public function Main() {
    echo "Você deseja baixar as musicas por meio de nomes('n') ou links('l') ?: ";
    $this->_input = trim(fgets(STDIN));

    if ($this->_input != 'n') {
      echo "Voce quer baixar apenas musicas (Digite 'm') apenas videos (Digite 'v') ou os dois (Digite 'mv'): ";
      $this->_input = trim(fgets(STDIN));
    }
    $this->downloadSelectedVideos($this->lista);
  }
}
$init = @new Youtube_dl_aux($argv[1]);
$init->Main();
?>
