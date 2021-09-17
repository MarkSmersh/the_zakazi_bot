<?php

class Bot {

    public string $token;

    public function __construct(string $token) {
        $this->createUrl($token);
    }

    public function createUrl(string $token) {
        $this->token = $token;
        $this->url = 'https://api.telegram.org/bot' . $token . '/';
    }

    public function __toString() {
        return $this->url;
    }

}
class Answer {

    public function __construct(string $token, string $method, array $parametrs = []) {
        if (!empty ($parametrs)) {
            $url = $token . $method . '?' . http_build_query($parametrs);
        }
        else {
            $url = $token . $method;
        }
        return json_decode (
            file_get_contents($url),
            JSON_OBJECT_AS_ARRAY
        );
    }  
}

class Update {

    public string $token;
    public int $lastupdate;

    public function __construct($token) {
        $this->token = $token;
        $this->getUpdate(); 
    }

    public function getUpdate($lastupdate = 0) {
        $url = $this->token . 'getUpdates' . '?' . http_build_query(['timeout' => '5', 'offset' => $lastupdate]);
        $this->url = $url;
        $responses = json_decode (
            file_get_contents($url),
            JSON_OBJECT_AS_ARRAY
        );

        if ($responses['ok'] == FALSE) {
            throw new Exception('ok is false');
        }

        if (!empty($responses['result'])) {

            if (key($responses['result']) == 0) {
                foreach ((array)$responses['result'][0] as $response) {
                    $lastupdate = $responses['result'][0]['update_id'];
                    $this->response   = $response;
                    $this->lastupdate = $lastupdate;
                }
            }
            elseif (key($responses['result']) == 'update_id') {
                foreach ((array)$responses['result'] as $response) {
                    $lastupdate = $responses['result']['update_id'];
                    $this->response   = $response;
                    $this->lastupdate = $lastupdate;
                }
            }
            $this->lastupdate++;
        }
        else {
            $this->response = $responses['result'];
            $this->lastupdate = 0;
        }

    }
}

class Converstation {

    public string $token; 
    public array $entry;
    public array $filter;
    public array $breakout;

    public function __construct(string $token, array $entry, array $filter, array $breakout) {
        $this->token = $token;
        $this->entry = $entry;
        $this->filter = $filter;
        $this->breakout = $breakout;
    }

    public function getFilter (array $target) {
        $filename = 'converstation';

        $json = file_get_contents($filename . '.json');
        $data = json_decode ($json, true);

        if (array_key_exists('data', $target)) {
            $callback = $target['data'];
            $user_message = null;
        }
        else {
            $user_message = $target['text'];
            $callback = null;
        }
        $user_id = $target['from']['id'];

        if (!array_key_exists ($user_id, $data)) {
            $data[$user_id] = '0';
            $json = json_encode ($data);
            file_put_contents ($filename . '.json', $json);
        }

        $user_filter = $data[$user_id];

        if ($user_message == $this->entry['filter'] && $user_filter == '0'){
            $func = $this->entry['func'];
            $section = $func($this->token, $target);
            $data[$user_id] = $section;
            $json = json_encode ($data);
            file_put_contents ($filename . '.json', $json);
        }
        elseif ($user_message == $this->breakout['filter'] && $user_filter != '0'){
            $func = $this->breakout['func'];
            $func($this->token, $target);
            $section = '0';
            $data[$user_id] = $section;
            $json = json_encode ($data);
            file_put_contents ($filename . '.json', $json);
        }
        else {
            if ($user_filter != '0') {
                $section = $this->filter[$user_filter];
                foreach ($section as $part) {
                    if (array_key_exists ('filter', $part) && $part['filter'] === $user_message) {
                        $func = $part['func'];
                        $section = $func($this->token, $target);
                        if ($section != null) {
                            $data[$user_id] = $section;
                            $json = json_encode ($data);
                            file_put_contents ($filename . '.json', $json);
                        }
                        break;
                    }
                    elseif (array_key_exists ('callback', $part) && $part['callback'] === $callback) {
                        $func = $part['func'];
                        $section = $func($this->token, $target);
                        if ($section != null) {
                            $data[$user_id] = $section;
                            $json = json_encode ($data);
                            file_put_contents ($filename . '.json', $json);
                        }
                        break;
                    }
                    elseif (array_key_exists ('filter', $part) && empty ($part['filter']) && $user_message !== null) {
                        $func = $part['func'];
                        $section = $func($this->token, $target);
                        if ($section != null) {
                            $data[$user_id] = $section;
                            $json = json_encode ($data);
                            file_put_contents ($filename . '.json', $json);
                        }
                        break;
                    }
                }
            }
        }
    }
}

/* 'main_menu' => array (
            array ('filter' => 'n-word', 'func' => 'sendAnswer'),
            array ('callback' => 'cumshot', 'func' => 'sendCallbackAnswer') */
?>