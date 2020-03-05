<?php
/**
 * Script desenvolvido por Alan Barcellos no dia 08/12/2019 as 22:15
 */
include('curlYoutube.php');
class Youtube_dl_aux extends curlYoutube {
  private $lista;
  public $dir_musica = 'downloaded_musics';
  public $dir_videos = 'downloaded_videos';
  private $_input;

  public function __construct($lista) {
    $this->lista = $lista;
  }
  private function moverArq($opcao, $link) {
    echo "-------------------------------------------------------- \n";
    echo "[...] Downloading... (".$link.") \n\n";
    shell_exec('youtube-dl '.$opcao.' '.$link);
    echo '[+] Downloaded!'."\n";
    echo "-------------------------------------------------------- \n\n";
    $dir_atual = scandir('.');
    for ($i=0; $i < count($dir_atual); $i++) {
      if (strpos($dir_atual[$i], '.mp3') !== FALSE) {
        if (!is_dir($this->dir_musica)) {
          mkdir($this->dir_musica);
        }
        rename($dir_atual[$i], $this->dir_musica.'/'.$dir_atual[$i]);
      } elseif (strpos($dir_atual[$i], '.mp4') !== FALSE) {
        if (!is_dir($this->dir_videos)) {
          mkdir($this->dir_videos);
        }
        rename($dir_atual[$i], $this->dir_videos.'/'.$dir_atual[$i]);
      }
    }
  }
  private function Download($lista) {
    $links_musicas = file($lista, FILE_IGNORE_NEW_LINES | FILE_TEXT);
    $links_videos = file($lista, FILE_IGNORE_NEW_LINES | FILE_TEXT);
    $links_both = file($lista, FILE_IGNORE_NEW_LINES | FILE_TEXT);
    $names = file($lista, FILE_IGNORE_NEW_LINES | FILE_TEXT);
    // se for só musica
    if ($this->_input == 'm') {
      for ($i=0; $i < count($links_musicas); $i++) {
        if (strrpos($links_musicas[$i], '.v') !== FALSE) {
          $links_musicas[$i] = substr($links_musicas[$i], 0, strrpos($links_musicas[$i], '.'));
        }
        $this->moverArq('-x --audio-format mp3', $links_musicas[$i]);
      }
      //fim do bloco só musica
    } elseif ($this->_input == 'v') {
      for ($i=0; $i < count($links_videos); $i++) {
        if (strrpos($links_videos[$i], '.v') !== FALSE) {
          $links_videos[$i] = substr($links_videos[$i], 0, strrpos($links_videos[$i], '.'));
        }
        $this->moverArq('--format mp4', $links_videos[$i]);
      }
    } elseif ($this->_input == 'mv') {
      foreach ($links_both as $key => $value) {
        if (strrpos($links_both[$key], '.v') !== FALSE) {
          $link = substr($links_both[$key], 0, strrpos($links_both[$key], '.'));
          $opcao = '--format mp4';
        } else {
          $link = $links_both[$key];
          $opcao = '-x --audio-format mp3';
        }
        $this->moverArq($opcao, $link);
      }
    } elseif ($this->_input == 'n') {
      foreach ($names as $key => $value) {
        $l = $this->exec_curl($value);
        $this->moverArq('-x --audio-format mp3', $l);
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
    $this->Download($this->lista);
  }
}
$init = new Youtube_dl_aux($argv[1]);
$init->Main();
?>
