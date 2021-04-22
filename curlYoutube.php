<?php
// comando regex para identificar o numero de visualizacoes no video: \d{1,3}.\d{1,3}.\d{1,3} visualizações
// comando regex para idetificar o link do video: watch[?]v=.{11}

class curlYoutube {
  public $curl;

  private function set_curl($search_query) {
    $this->curl = curl_init();
    $cookieFile = tempnam(sys_get_temp_dir(), 'curlYt');

    curl_setopt_array($this->curl, [
      CURLOPT_URL => 'https://www.youtube.com/results?search_query='.urlencode($search_query),
      CURLOPT_HTTPHEADER => [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.183 Safari/537.36',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'Cache-control: no-cache',
        'Pragma: no-cache'
      ],
      CURLOPT_COOKIEJAR => $cookieFile,
      CURLOPT_COOKIEFILE => $cookieFile,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_RETURNTRANSFER => true
    ]);
    return $this->curl;
  }

  public function curlClose() {
    if (is_resource($this->curl))
      curl_close($this->curl);
  }
  
  public function exec_curl($nome) {
    $result = curl_exec($this->set_curl($nome));
    return $result;
  }
  public function getLinkVideo($strResult) {
    preg_match_all('/watch[?]v=.{11}/', $strResult, $links, PREG_SET_ORDER);
    return 'https://www.youtube.com/'.$links[0][0];
  }
  public function getTitleVideo($link) {
    return shell_exec('youtube-dl -e '.$link);
  }
}
?>