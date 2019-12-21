<?php

require './lib/simple_html_dom.php';

class crawler {

    public $busca;
    public $config;
    public $db;

    function __construct() {

        $this->config = array();
        $this->config['host'] = 'localhost';
        $this->config['user'] = 'id12017312_dev';
        $this->config['pass'] = 'TH@y!!3r';
        $this->config['table'] = 'id12017312_buscador';

        $this->db = new DB($this->config);
        
    }
    public function seleciona($site) {

        switch ($site) {
            
            case 'reclameaqui':

                $this->reclameAqui();
                break;

            case 'playgoogle':

                    $this->playgoogle();
                    break;
            case 'applestore':
                $this->applestore();
                break;
            case 'ebit':

                $this->ebit();
                break;
                
        }
    }

    private function reclameAqui() {
        
        $url = "https://iosite.reclameaqui.com.br/raichu-io-site-v1/company/rankings/20";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        $r = json_decode(html_entity_decode($result),true);
        
        
    }
    
    private function playgoogle(){
        
        $url = 'https://play.google.com/store/search?q='.$this->busca.'&c=apps';

        
        $classificacao = array();
        
        foreach (file_get_html($url)->find('.ImZGtf .Z2nl8b .PODJt .kCSSQe .pf5lIe div[role=img]') as $key => $value) {
            
            array_push($classificacao,$value->{'aria-label'});
        }

        $empresa = array();
        
        foreach (file_get_html($url)->find('.ImZGtf .b8cIId a .WsMG1c') as $key => $value) {
            
            array_push($empresa,$value->title);
        }
        
    }
    private function applestore() {
        

        $url = "https://www.google.com/search?q=apps+Apple+store+".$this->busca;

        foreach(file_get_html($url)->find('.oqSTJd') as $classificacao) {

            $nota = $classificacao;
        }

        foreach(file_get_html($url)->find('.kCrYT',0) as $nome) {

             $empresa = explode("na Ap",explode(">",explode("<div",$nome)[3])[1])[0].'\n';
        }

        $this->db->query("INSERT IGNORE INTO empresa(nomeEmpresa,notaMedia,Categoria,site) VALUES('".$empresa."','".$nota."','','apple store')");
    }
    private function ebit(){
        
        foreach(file_get_html('https://www.ebit.com.br/magazine-luiza')->find('img.img-medal-store') as $nota) {

            $nota = $nota->title;
       }

       foreach(file_get_html('https://www.ebit.com.br/magazine-luiza')->find('img.rp-img-it') as $title){

        $empresa = $title->alt;
       }

       

    }
}



