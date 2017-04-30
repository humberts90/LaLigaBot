<?php

/*
*
*   La API de football-data es un servicio de prueba gratuito, se utilizo para experimentar un poco con el API de Telegram
*
*/
class LaLigaBot
{
    private $token;
    private $apiURL;
    private $tokenAPILiga;

    function __construct($token)
    {
        $this->setToken($token);
        $this->setApiURL('https://api.telegram.org/bot'.$this->getToken());     
    }

    function playAPI(){
        $api = file_get_contents($this->getApiURL()."/getUpdates");
        $api = json_decode($api, true);

        $UID = $this->leerID();

        if(empty($UID))
            $UID = -1;

        foreach ($api['result'] as $key => $value) {
            if(intval($value['update_id']) > $UID){
                $UID = intval($value['update_id']);            
                $id = $value['message']['chat']['id'];

                switch ($value['message']['text']) {
                    case '/tabla':
                        $this->sendMessage($this->getApiURL(),$id,$this->getTabla());
                        break;
                    case '/RMCF':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('86'));
                        break;
                    case '/BFC':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('81'));
                        break;
                    case '/Sevilla':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('559'));
                        break;
                    case '/Villareal':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('94'));
                        break;
                    case '/Malaga':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('84'));
                        break;
                    case '/Betis':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('90'));
                        break;
                    case '/Osasuna':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('79'));
                        break;
                    case '/Espanyol':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('80'));
                        break;
                    case '/RSCF':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('92'));
                        break;
                    case '/ATM':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('78'));
                        break;
                    case '/Eibar':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('278'));
                        break;
                    case '/UDP':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('275'));
                        break;
                    case '/Valencia':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('95'));
                        break;
                    case '/Leganes':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('745'));
                        break;
                    case '/RCCV':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('558'));
                        break;
                    case '/Alaves':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('263'));
                        break;
                    case '/Atletic':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('77'));
                        break;
                    case '/Gijon':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('96'));
                        break;
                    case '/Granada':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('83'));
                        break;   
                    case '/RCDC':
                        $this->sendMessage($this->getApiURL(),$id,$this->getPlantilla('560'));
                        break;                    
                    
                    default:
                        $this->sendMessage($this->getApiURL(),$id,"Comando no reconocido");
                        break;
                }
            }
        }

        $this->escribirID($UID);
    }

    function leerID(){
        $file = fopen("archivo.txt", "r");
        ob_start();
            while(!feof($file)) {
                echo fgets($file);
            }
        fclose($file);
        return intval(ob_get_contents());
    }

    function escribirID($id){
        $file = fopen("archivo.txt", "w");
        fwrite($file, $id . PHP_EOL);
        fclose($file);
    }

    function setToken($token){
        $this->token = $token;
    }

    function getToken(){
        return $this->token;
    }

    function setApiURL($apiURL){
        $this->apiURL = $apiURL;
    }

    function getApiURL(){
        return $this->apiURL;
    }

    function setTokenAPILiga($tokenAPILiga){
        $this->tokenAPILiga = $tokenAPILiga;
    }

    function getTokenAPILiga(){
        return $this->tokenAPILiga;
    }

    function sendMessage($url, $id, $msj){        
        file_get_contents($url.'/sendMessage?chat_id='.$id.'&text='.urlencode($msj));
    }

    function getTabla(){

        $uri = 'http://api.football-data.org/v1/competitions/436/leagueTable';
        $request['http']['method'] = 'GET';
        $request['http']['header'] = 'X-Auth-Token: '.$this->tokenAPILiga;

        $stream_context = stream_context_create($request);
        $response = file_get_contents($uri, false, $stream_context);
        $fixtures = json_decode($response);
        $cont = 1;

        ob_start();

        echo "P - Name - PJ - PG - PE - PP - GA - GE - Di - Pts \n\n\n";
        foreach ($fixtures->standing as $key => $value) {
            echo $cont." - ".$value->teamName." - ".$value->playedGames." - ".$value->wins." - ".$value->draws." - ".$value->losses." - ".$value->goals." - ".$value->goalsAgainst." - ".$value->goalDifference." - ".$value->points."\n\n";
            $cont = $cont + 1;
        }

        return ob_get_contents();     
    }

    function getPlantilla($equipo){

        $uri = 'http://api.football-data.org/v1/teams/'.$equipo.'/players';
        $request['http']['method'] = 'GET';
        $request['http']['header'] = 'X-Auth-Token: '.$this->tokenAPILiga;

        $stream_context = stream_context_create($request);
        $response = file_get_contents($uri, false, $stream_context);
        $fixtures = json_decode($response);

        ob_start();

        foreach ($fixtures->players as $key => $value) {
            echo "Nombre: ".$value->name."\nPosición: ".$value->position."\nFecha de Nacimiento: ".$value->dateOfBirth."\nFinalización de dontrato: ".$value->contractUntil."\nPaís: ".$value->nationality."\n\n";
        }

        return ob_get_contents();     
    }

}

$laLiga = new LaLigaBot('<ID de tu Bot>');
$laLiga->setTokenAPILiga('<ID API de football-data>');
$laLiga->playAPI();