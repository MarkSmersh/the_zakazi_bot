<?php
include ('C:\Users\MarkSmersh\Desktop\the_zakazi_bot\methods.php');

class Converstation {

    public array $target; // вроде, лишнее
    public array $entry;
    public array $filter;
    public string $breakout;

    public function __construct(array $entry, array $filter, string $breakout) {
        $this->entry = $entry;
        $this->filter = $filter;
        $this->breakout = $breakout;
    }

    public function getFilter (array $target) {
        $filename = 'converstation';

        $json = file_get_contents($filename . '.json');
        $data = json_decode ($json, true);
        $user_id = $target['chat']['id']; //$user_id = $target['chat']['id']
        $user_message = $target['text']; // /start || n-word || coom-word || /stop;

        if (!array_key_exists ($user_id, $array)) {
            $data[$user_id] = '0';
            $json = json_encode ($data);
            file_put_contents ($filename . '.json', $json);
        }

        $user_filter = $data[$user_id]; // CONV_1 || CONV_2 || 0;

        if ($user_message == $this->entry['filter'] && $user_filter == 0){
            $func = $this->entry['func'];
            $section = $func();
            $data[$user_id] = $section;
            $json = json_encode ($data);
            file_put_contents ($filename . '.json', $json);
        }
        elseif ($user_message == $this->$breakout && $user_filter != 0){
            $section = 0;
            $data[$user_id] = $section;
            $json = json_encode ($data);
            file_put_contents ($filename . '.json', $json);
        }
    }
}

$conv_handler = new Converstation (

    $entry = array (
        'filter' => '/start', 'func' => 'start'
    ),
    
    $filter = array (
        'CONV_1' => array (
            array ('filter' => 'n-word', 'func' => 'sendAnswer'),
            array ('callback' => 'cumshot', 'func' => 'sendCallbackAnswer')
        ),
        'CONV_2' => array (
            array ('filter' => 'coom-word', 'func' => 'sendAnswer'),
            array ('callback' => 'niggershot', 'func' => 'sendCallbackAnswer')
        )
    ),

    $breakout = '/stop'

);

$conv_handler->getFilter(
    $target = array (
        "message_id" => '391',
        "from" => array (
                "id" => '562140704',
                "is_bot" => False,
                "first_name" => '0xc000007b',
                "username" => 'qekkk',
                'language_code' => 'ru'
            ),

        'chat' => array (
                'id' => '562140704',
                'first_name' => '0xc000007b',
                'username' => 'qekkk',
                'type' => 'private'
            ),

        'date' => '1630357135',
        'text' => 'cum'
    ),
);

?>

