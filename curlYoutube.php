<?php
// comando regex para identificar o numero de visualizacoes no video: \d{1,3}.\d{1,3}.\d{1,3} visualizações
// comando regex para idetificar o link do video: watch[?]v=.{11}
class curlYoutube {
  private $curl;
  private function set_curl($search_query) {
    $this->curl = curl_init();
    curl_setopt_array($this->curl, [
      CURLOPT_URL => 'https://www.youtube.com/results?search_query='.str_replace(' ', '%', $search_query),
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_RETURNTRANSFER => true
    ]);
    return $this->curl;
  }
  
  public function exec_curl($nome) {
    $result = curl_exec($this->set_curl($nome));
    preg_match_all('/\d{1,3}.\d{1,3}.\d{1,3} visualizações/m', $result, $videos_views, PREG_SET_ORDER);
    preg_match_all('/watch[?]v=.{11}/m', $result, $links, PREG_SET_ORDER);
    $videos_views = $this->reOrder($videos_views);
    $links = $this->reOrder($links);
    $links = $this->removeDuplicateValue($links);
    $views[] = (int) str_replace('.', '', str_replace('visualizações', '', $videos_views[0]));
    $views[] = (int) str_replace('.', '', str_replace('visualizações', '', $videos_views[1]));
    $views[] = (int) str_replace('.', '', str_replace('visualizações', '', $videos_views[2]));
    $views = array_keys($this->select($views));
    $pos = $views[0];
    $video = $links[$pos];
    return 'https://www.youtube.com/'.$video;
  }
  private function select($v) {
    if ($v[0] > $v[1] && $v[2]) {
      $maior[0] = $v[0];
    } elseif ($v[1] > $v[0] && $v[2]) {
      $maior[1] = $v[1];
    } elseif ($v[2] > $v[0] && $v[1]) {
      $maior[2] = $v[2];
    }
    return $maior;
  }
  private function reOrder($vetor) {
    foreach ($vetor as $in => $val) {
      foreach ($val as $k => $v) {
        $new_vetor[] = $v;
      }
    }
    return $new_vetor;
  }
  private function removeDuplicateValue($vetor) {
    for ($i=0; $i < (count($vetor) - 1); $i++) {
      if ($vetor[$i] == $vetor[$i+1]) {
        unset($vetor[$i]);
      }
    }
    $vetor = array_values($vetor);
    return [
      $vetor[0],
      $vetor[1],
      $vetor[2]
    ];
  }
}
?>
