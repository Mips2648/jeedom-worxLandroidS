<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/
require_once __DIR__  . '/../../../../core/php/core.inc.php';
require_once dirname(__FILE__) . '/../../vendor/autoload.php';

class worxLandroidS extends eqLogic {
    use MipsEqLogicTrait;

    public static $_encryptConfigKey = array('email', 'passwd');

    public static $_widgetPossibility = array('custom' => array(
        'visibility' => true,
        'displayName' => true,
        'displayObjectName' => true,
        'optionalParameters' => false,
        'background-color' => true,
        'text-color' => true,
        'border' => true,
        'border-radius' => true,
        'background-opacity' => true
    ));

    protected static function getSocketPort() {
        return config::byKey('socketport', __CLASS__, 55073);
    }


    //         } else {
    //             log::add(__CLASS__, 'info', 'Connexion OK');
    //             // get users parameters
    //             $url       = "https://api.worxlandroid.com/api/v2/users/me";
    //             $api_token = $json['access_token'];
    //             $token     = $json['api_token'];

    //             $content = "application/json";
    //             $ch      = curl_init($url);
    //             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             curl_setopt($ch, CURLOPT_HEADER, FALSE);
    //             curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //                 "Content-Type: application/json",
    //                 'Authorization: Bearer ' . $api_token
    //             ));

    //             $result_users = curl_exec($ch);
    //             log::add(__CLASS__, 'info', 'Connexion result :' . $result_users);
    //             $json_users = json_decode($result_users, true);

    //             // get certificate
    //             $url = "https://api.worxlandroid.com:443/api/v2/users/certificate";
    //             //$api_token = $json['api_token'];
    //             //$token = $json['api_token'];

    //             $content = "application/json";
    //             $ch      = curl_init($url);
    //             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //                 'mqtt_endpoint:' . $json_users['mqtt_endpoint'],
    //                 "Content-Type: application/json",
    //                 'Authorization: Bearer ' . $api_token
    //             ));

    //             $result = curl_exec($ch);
    //             log::add(__CLASS__, 'info', 'Connexion result :' . $result);

    //             $json2 = json_decode($result, true);


    //             if (is_null($json2)) {
    //             } else {
    //                 $pkcs12 = base64_decode($json2['pkcs12']);
    //                 openssl_pkcs12_read($pkcs12, $certs, "");
    //                 file_put_contents($CERTFILE, $certs['cert']);
    //                 file_put_contents($PKEYFILE, $certs['pkey']);

    //                 // get product item (mac address)
    //                 $url = "https://api.worxlandroid.com:443/api/v2/product-items";

    //                 $content = "application/json";
    //                 $ch      = curl_init($url);
    //                 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    //                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //                     'Authorization: Bearer ' . $api_token
    //                 ));

    //                 $result = curl_exec($ch);
    //                 log::add(__CLASS__, 'info', 'get product-items:' . $result);

    //                 $json3 = json_decode($result, true);
    //                 config::save('api_token', $api_token, __CLASS__); //$json_users['id'],__CLASS__);

    //                 config::save('mqtt_client_id', 'android-uuid/v1', __CLASS__); //$json_users['id'],__CLASS__);
    //                 config::save('mqtt_endpoint', $json_users['mqtt_endpoint'], __CLASS__);

    //                 if (is_null($json3)) {
    //                 } else {
    //                     // get boards => id => code
    //                     $url = "https://api.worxlandroid.com:443/api/v2/boards";
    //                     curl_setopt($ch, CURLOPT_URL, $url);

    //                     $boards_result = curl_exec($ch);
    //                     log::add(__CLASS__, 'info', 'get boards:' . $boards_result);
    //                     $boards = json_decode($boards_result, true);

    //                     // get products => product_id => board_id
    //                     $url = "https://api.worxlandroid.com:443/api/v2/products";
    //                     curl_setopt($ch, CURLOPT_URL, $url);
    //                     $products = json_decode(curl_exec($ch), true);
    //                     foreach ($json3 as $key => $product) {
    //                         $typetondeuse     = 'DB510';
    //                         $found_key        = array_search($product['product_id'], array_column($products, 'id'));
    //                         $board_id         = $products[$found_key]['board_id'];
    //                         $mowerDescription = $products[$found_key]['code'];
    //                         log::add(__CLASS__, 'info', 'board_id: ' . $board_id . ' / product id:' . $product['product_id']);
    //                         $found_key    = array_search($board_id, array_column($boards, 'id'));
    //                         // $typetondeuse = $boards[$found_key]['code'];
    //                         $typetondeuse = reset(explode('/', $product['mqtt_topics']['command_in'], 2));
    //                         log::add(__CLASS__, 'info', 'typetondeuse:' . $typetondeuse);
    //                         $doubleSchedule = $boards[$found_key]['features']['scheduler_two_slots'];

    //                         log::add(__CLASS__, 'info', 'mac_address ' . $product['mac_address'] . $typetondeuse);
    //                         // create Equipement if not already created
    //                         $elogic = self::byLogicalId($product['mac_address'], __CLASS__);
    //                         if (!is_object($elogic)) {

    //                             $elogic_prev = self::byLogicalId($typetondeuse . '/' . $product['mac_address'] . '/commandOut', __CLASS__);
    //                             if (is_object($elogic_prev)) {
    //                                 // created equipement in previous plugin release*
    //                                 message::add(__CLASS__, 'Veuillez supprimer la tondeuse ajoutée précédemment: ' . $elogic_prev->getName(), null, null);
    //                                 log::add(__CLASS__, 'info', 'Suppress existing first : mac_address ' . $product['mac_address'] . $typetondeuse . $product['product_id']);
    //                             } else {

    //                                 log::add(__CLASS__, 'info', 'mac_address ' . $product['mac_address'] . $typetondeuse . $product['product_id']);
    //                                 worxLandroidS::create_equipement($product, $typetondeuse, $mowerDescription, $doubleSchedule);
    //                                 // message par défault pour éviter code 500 à la première initialisation
    //                                 // message par défault pour éviter code 500 à la première initialisation
    //                                 $default_message = file_get_contents($default_message_file, true);
    //                                 $topic = $product['product_id'] . '/' . $product['mac_address'] . '/dummy';
    //                                 $message = json_decode($default_message, true);
    //                                 $message->topic = $topic; //$product['product_id'].'/'.$product['mac_address'].'/dummy';
    //                                 log::add(__CLASS__, 'info', 'default msg:' . $message->payload . '/topic:' . $message->topic);
    //                                 worxLandroidS::message($message);
    //                             }
    //                         }
    //                     }
    //                     config::save('initCloud', 0, __CLASS__);
    //                 }
    //             }
    //         }
    //     }
    // }

    public static function deamon_info() {
        $return = array();
        $return['log'] = __CLASS__;
        $return['state'] = 'nok';
        $pid_file = jeedom::getTmpFolder(__CLASS__) . '/daemon.pid';
        if (file_exists($pid_file)) {
            if (@posix_getsid(trim(file_get_contents($pid_file)))) {
                $return['state'] = 'ok';
            } else {
                shell_exec(system::getCmdSudo() . 'rm -rf ' . $pid_file . ' 2>&1 > /dev/null');
            }
        }

        $return['launchable'] = 'ok';
        $email = config::byKey('email', __CLASS__);
        $pswd = config::byKey('passwd', __CLASS__);
        if ($email == '') {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('L\'adresse email n\'est pas configuré', __FILE__);
        } elseif ($pswd == '') {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('Le mot de passe n\'est pas configuré', __FILE__);
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('L\'adresse email n\'est pas valide', __FILE__);
        }
        return $return;
    }

    public static function deamon_start() {
        self::deamon_stop();
        $deamon_info = self::deamon_info();
        if ($deamon_info['launchable'] != 'ok') {
            throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
        }

        $path = realpath(dirname(__FILE__) . '/../../resources');
        $cmd = "/usr/bin/python3 {$path}/worxLandroidSd.py";
        $cmd .= ' --loglevel ' . log::convertLogLevel(log::getLogLevel(__CLASS__));
        $cmd .= ' --socketport ' . self::getSocketPort();
        $cmd .= ' --callback ' . network::getNetworkAccess('internal', 'proto:127.0.0.1:port:comp') . '/plugins/worxLandroidS/core/php/jeeworxLandroidS.php';
        $cmd .= ' --apikey ' . jeedom::getApiKey(__CLASS__);
        $cmd .= ' --pid ' . jeedom::getTmpFolder(__CLASS__) . '/daemon.pid';
        $cmd .= ' --email "' . trim(str_replace('"', '\"', config::byKey('email', __CLASS__))) . '"';
        $cmd .= ' --pswd "' . trim(str_replace('"', '\"', config::byKey('passwd', __CLASS__))) . '"';
        log::add(__CLASS__, 'info', 'Lancement démon');
        $result = exec(system::getCmdSudo() . $cmd . ' >> ' . log::getPathToLog(__CLASS__ . '_daemon') . ' 2>&1 &');
        $i = 0;
        while ($i < 10) {
            $deamon_info = self::deamon_info();
            if ($deamon_info['state'] == 'ok') {
                break;
            }
            sleep(1);
            $i++;
        }
        if ($i >= 10) {
            log::add(__CLASS__, 'error', __('Impossible de lancer le démon', __FILE__), 'unableStartDeamon');
            return false;
        }
        message::removeAll(__CLASS__, 'unableStartDeamon');

        return true;
    }

    public static function deamon_stop() {
        try {
            $params = [
                'action' => 'stop'
            ];
            self::sendToDaemon($params);
            sleep(1);
        } catch (\Throwable $th) {
            //throw $th;
        }

        $pid_file = jeedom::getTmpFolder(__CLASS__) . '/daemon.pid';
        if (file_exists($pid_file)) {
            $pid = intval(trim(file_get_contents($pid_file)));
            system::kill($pid);
        }
        sleep(1);
        system::kill('worxLandroidSd.py');
        // system::fuserk(config::byKey('socketport', __CLASS__));
        sleep(1);
    }

    public static function create_or_update_devices($devices) {
        foreach ($devices as $device) {
            /** @var worxLandroidS */
            $eqLogic = self::byLogicalId($device['uuid'], __CLASS__);
            if (!is_object($eqLogic)) {
                $eqLogic = new self();
                $eqLogic->setEqType_name(__CLASS__);
                $eqLogic->setLogicalId($device['uuid']);
                $eqLogic->setName($device['name']);
                $eqLogic->setConfiguration('serial_number', $device['serial_number']);
                $eqLogic->setConfiguration('purchased_at', $device['purchased_at']);
                $eqLogic->setConfiguration('warranty_expires_at', $device['warranty']['expires_at']);
                $eqLogic->setConfiguration('registered_at', $device['registered_at']);
                $eqLogic->setConfiguration('mac_address', implode(":", str_split($device['mac_address'], 2)));

                event::add('jeedom::alert', array(
                    'level' => 'success',
                    'page' => 'worxLandroidS',
                    'message' => __('Nouvelle tondeuse ajoutée:', __FILE__) . $eqLogic->getName(),
                ));
            }
            $eqLogic->setConfiguration('firmware_version', $device['firmware']['version']);
            $eqLogic->setConfiguration('product_code', $device['product']['code']);
            $eqLogic->setConfiguration('product_description', $device['product']['description']);
            $eqLogic->setConfiguration('product_year', $device['product']['year']);
            $eqLogic->setConfiguration('product_cutting_width', $device['product']['cutting_width'] . ' mm');
            $eqLogic->setConfiguration('lawn_perimeter', $device['lawn']['perimeter'] . ' m');
            $eqLogic->setConfiguration('lawn_size', $device['lawn']['size'] . ' m²');
            $eqLogic->setConfiguration('accessories', $device['accessories']);
            $eqLogic->save();
            $eqLogic->on_message($device);
        }
    }

    public function createCommands() {
        $this->createCommandsFromConfigFile(__DIR__ . '/../config/commands.json', 'common');
        $this->createCommandsFromConfigFile(__DIR__ . '/../config/commands.json', 'ots');
        $this->createCommandsFromConfigFile(__DIR__ . '/../config/commands.json', 'partymode');
        $this->createCommandsFromConfigFile(__DIR__ . '/../config/commands.json', 'schedules');
        $this->createCommandsFromConfigFile(__DIR__ . '/../config/commands.json', 'zone');

        $accessories = $this->getConfiguration('accessories');
        if (is_array($accessories)) {
            $modules_cmds = self::getCommandsFileContent(__DIR__ . '/../config/modules.json');
            foreach ($accessories as $key => $value) {
                if (!$value)
                    continue;
                if (array_key_exists($key, $modules_cmds)) {
                    $this->createCommandsFromConfig($modules_cmds[$key]);
                }
            }
        }
    }

    public function postInsert() {
        $this->createCommands();
    }

    public static function synchronize() {
        self::sendToDaemon(['action' => 'synchronize']);
    }

    public function set_schedule() {
        $primary = [
            ["11:00", 150, 1],
            ["11:00", 150, 0],
            ["00:00", 0, 0],
            ["11:00", 150, 1],
            ["11:00", 135, 0],
            ["11:00", 135, 0],
            ["11:00", 135, 0]
        ];
        self::sendToDaemon([
            'action' => 'set_schedule',
            'serial_number' => $this->getConfiguration('serial_number'),
            'args' => [$primary]
        ]);
    }

    public function on_message($data) {
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $this->checkAndUpdateCmd($key, $value);
            }
        }

        $this->checkAndUpdateCmd('battery_temperature', $data['battery']['temperature']);
        $this->checkAndUpdateCmd('battery_voltage', $data['battery']['voltage']);
        $this->checkAndUpdateCmd('battery_percent', $data['battery']['percent']);
        $this->checkAndUpdateCmd('battery_charging', $data['battery']['charging']);
        $this->checkAndUpdateCmd('battery_cycles_total', $data['battery']['cycles']['total']);

        $this->checkAndUpdateCmd('blades_total_on', $data['blades']['total_on']);
        $this->checkAndUpdateCmd('blades_current_on', $data['blades']['current_on']);

        $this->checkAndUpdateCmd('orientation_pitch', $data['orientation']['pitch']);
        $this->checkAndUpdateCmd('orientation_roll', $data['orientation']['roll']);
        $this->checkAndUpdateCmd('orientation_yaw', $data['orientation']['yaw']);

        $this->checkAndUpdateCmd('rainsensor_delay', $data['rainsensor']['delay']);
        $this->checkAndUpdateCmd('rainsensor_triggered', $data['rainsensor']['triggered']);
        $this->checkAndUpdateCmd('rainsensor_remaining', $data['rainsensor']['remaining']);

        $this->checkAndUpdateCmd('zone_starting_point_0', $data['zone']['starting_point'][0]);
        $this->checkAndUpdateCmd('zone_starting_point_1', $data['zone']['starting_point'][1]);
        $this->checkAndUpdateCmd('zone_starting_point_2', $data['zone']['starting_point'][2]);
        $this->checkAndUpdateCmd('zone_starting_point_3', $data['zone']['starting_point'][3]);
        $this->checkAndUpdateCmd('zone_next_start', $data['zone']['current']);
        $this->checkAndUpdateCmd('zone_current', $data['zone']['current'] + 1);

        $this->checkAndUpdateCmd('modules_ultrasonic', $data['active_modules']['ultrasonic']);
        $this->checkAndUpdateCmd('modules_digital_fence_fh', $data['active_modules']['digital_fence_fh']);
        $this->checkAndUpdateCmd('modules_digital_fence_cut', $data['active_modules']['digital_fence_cut']);
        $this->checkAndUpdateCmd('modules_headlight', $data['active_modules']['headlight']);
        $this->checkAndUpdateCmd('modules_cellular', $data['active_modules']['cellular']);

        $this->checkAndUpdateCmd('schedules_active', $data['schedules']['active']);
        $this->checkAndUpdateCmd('schedules_daily_progress', $data['schedules']['daily_progress']);

        $this->checkAndUpdateCmd('statistics_distance', $data['statistics']['distance']);
        $this->checkAndUpdateCmd('statistics_worktime_total', $data['statistics']['worktime_total']);

        $this->checkAndUpdateCmd('status_id', $data['status']['id']);
        $this->checkAndUpdateCmd('status_description', self::getStatusDescription($data['status']['id']));

        $this->checkAndUpdateCmd('error_id', $data['error']['id']);
        $this->checkAndUpdateCmd('error_description', self::getErrorDescription($data['error']['id']));

        if (array_key_exists('gps', $data)) {
            $this->checkAndUpdateCmd('gps_latitude', $data['gps']['latitude']);
            $this->checkAndUpdateCmd('gps_longitude', $data['gps']['longitude']);
        }

        if ($this->getConfiguration('automaticWidget', 0) == 1) {
            $this->refreshWidget();
        }

        //rain delay
        $rain_delay_active = $this->getConfiguration('rain_delay_active');
        if ($data['rainsensor']['delay'] == 0 & $rain_delay_active != 0) {
            $this->setConfiguration('rain_delay_active', 0);
            $this->save(true);
            log::add(__CLASS__, 'info', "rain delay inactive for {$this->getName()}");
        } elseif ($data['rainsensor']['delay'] > 0 & $rain_delay_active != 1) {
            $this->setConfiguration('rain_delay_active', 1);
            $this->save(true);
            log::add(__CLASS__, 'info', "rain delay active for {$this->getName()}");
        }

        //schedule
        $old_schedules = $this->getConfiguration('schedules');
        if ($old_schedules != $data['schedules']) {
            $this->setConfiguration('schedules', $data['schedules']);
            $this->save(true);
            log::add(__CLASS__, 'info', "Schedules updated for {$this->getName()}");
        }

        //zone
        $new_zone = json_encode([
            "indicies" => $data['zone']['indicies'],
            "starting_point" => $data['zone']['starting_point']
        ]);
        $old_zone = $this->getConfiguration('zone');
        if ($old_zone != $new_zone) {
            $this->setConfiguration('zone', $new_zone);
            $this->save(true);
            log::add(__CLASS__, 'info', "Zone updated for {$this->getName()}");
        }
    }

    public static function on_activity_logs($data) {
        foreach ($data as &$v) {
            unset($v['_payload']);
            $v['status']['description'] = self::getStatusDescription($v['status']['id']);
            $v['error']['description'] = self::getErrorDescription($v['error']['id']);
        }

        event::add('worxLandroidS::activity_logs', array($data));
    }

    public function get_activity_logs() {
        worxLandroidS::sendToDaemon([
            'action' => 'get_activity_logs',
            'serial_number' => $this->getConfiguration('serial_number')
        ]);
    }

    public static function message($message) {
        log::add(__CLASS__, 'debug', 'Message ' . $message->payload . ' sur ' . $message->topic);
        //json message
        $nodeid     = $message->topic;
        $value      = $message->payload;
        $json2_data = json_decode($value);

        $split_topic = explode('/', $nodeid);
        $mac_address = $split_topic[1];

        /** @var worxLandroidS */
        $elogic = eqlogic::byLogicalId($mac_address, __CLASS__, false);

        /*
          cfg->lg language: string;
          cfg->dt dateTime: moment.Moment;
          dat->mac macAddress: string;
          dat->fw firmware: string;
          dat->rsi wifiQuality: number;
          active: boolean;
          cfg->rd rainDelay: number;
          timeExtension: number;
          cfg->sn serialNumber: string;
          dat->st->wt totalTime: number;
          dat->st->d totalDistance: number;
          dat->st->b totalBladeTime: number;
          dat->bt->nr batteryChargeCycle: number;
          dat->bt->c batteryCharging: boolean;
          dat->bt->v batteryVoltage: number;
          dat->bt->t batteryTemperature: number;
          dat->bt->p batteryLevel: number;
          dat->le errorCode: number;
          errorDescription: string;
          dat->ls statusCode: number;
          statusDescription: string;
          schedule: TimePeriod[];
        */

        // $elogic->newInfo('langue', $json2_data->cfg->lg, 'string', 0, '');

        // $elogic->newInfo('zonesList', $json2_data->dat->mz, 'string', 0, '');
        // //area
        // $elogic->newInfo('areaList', $json2_data->cfg->mz[0] . '|' . $json2_data->cfg->mz[1] . '|' . $json2_data->cfg->mz[2] . '|' . $json2_data->cfg->mz[3], 'string', 1, '');
        // $elogic->newInfo(
        //     'areaListDist',
        //     $json2_data->cfg->mzv[0] . '|' . $json2_data->cfg->mzv[1] . '|' . $json2_data->cfg->mzv[2] . '|' . $json2_data->cfg->mzv[3] . '|' .
        //         $json2_data->cfg->mzv[4] . '|' . $json2_data->cfg->mzv[5] . '|' . $json2_data->cfg->mzv[6] . '|' . $json2_data->cfg->mzv[7] . '|' .
        //         $json2_data->cfg->mzv[8] . '|' . $json2_data->cfg->mzv[9],
        //     'string',
        //     1,
        //     ''
        // );

        // if (array_key_exists('conn', $json2_data->dat)) { // for mower with 4G modules
        //     $elogic->newInfo('connexion', $json2_data->dat->conn, 'string', 1, '');
        //     $elogic->newInfo('GPSLatitude', $json2_data->dat->modules->{'4G'}->gps->coo[0], 'string', 1, '');
        //     $elogic->newInfo('GPSLongitude', $json2_data->dat->modules->{'4G'}->gps->coo[1], 'string', 1, '');
        // } else {
        //     $elogic->newInfo('connexion', ' ', 'string', 0, '');
        //     $elogic->newInfo('GPSLatitude', ' ', 'string', 0, '');
        //     $elogic->newInfo('GPSLongitude', ' ', 'string', 0, '');
        // }

        //  date début + durée + bordure
        // $completePlanning = '';
        // for ($i = 0; $i < 7; $i++) {
        //     $completePlanning .= '';
        //     $elogic->newInfo('Planning_startTime_' . $i, $json2_data->cfg->sc->d[$i][0], 'string', 1, '');
        //     $elogic->newInfo('Planning_duration_' . $i, $json2_data->cfg->sc->d[$i][1], 'string', 1, '');
        //     $elogic->newInfo('Planning_cutEdge_' . $i, $json2_data->cfg->sc->d[$i][2], 'string', 1, '');
        //     $completePlanning .= $json2_data->cfg->sc->d[$i][0] . ',' . $json2_data->cfg->sc->d[$i][1] . ',' . $json2_data->cfg->sc->d[$i][2] . '|';
        //     $elogic->newInfo('completePlanning', $completePlanning, 'string', 1, '');
        // }
        // // scheduler double
        // if ($elogic->getConfiguration('doubleSchedule', '') != '') {
        //     for ($i = 0; $i < 7; $i++) {
        //         $completePlanning .= '';
        //         $elogic->newInfo('Planning_startTime2_' . $i, $json2_data->cfg->sc->dd[$i][0], 'string', 1, '');
        //         $elogic->newInfo('Planning_duration2_' . $i, $json2_data->cfg->sc->dd[$i][1], 'string', 1, '');
        //         $elogic->newInfo('Planning_cutEdge2_' . $i, $json2_data->cfg->sc->dd[$i][2], 'string', 1, '');
        //         $completePlanning .= $json2_data->cfg->sc->dd[$i][0] . ',' . $json2_data->cfg->sc->dd[$i][1] . ',' . $json2_data->cfg->sc->dd[$i][2] . '|';
        //         $elogic->newInfo('completePlanning', $completePlanning, 'string', 1, '');
        //     }
        // }


        // mise a jour des infos virtuelles séparées par des virgules
        $cmd = worxLandroidSCmd::byEqLogicIdCmdName($elogic->getId(), 'virtualInfo');
        $name = $cmd->getConfiguration('request', '');
        $cmdlist = explode(',', $name);
        $value = '';
        foreach ($cmdlist as $cmdname) {
            if (strstr($cmdname, '#')) {
                $value .= ',' . strval(jeedom::evaluateExpression($cmdname)); // name = command number
            } else {
                $cmdlogic = worxLandroidSCmd::byEqLogicIdCmdName($elogic->getId(), $cmdname);
                if (empty($value)) {
                    $value = $cmdlogic->getConfiguration('topic', '');
                } else {
                    $value .= ',' . $cmdlogic->getConfiguration('topic', '');
                }
            }
        }

        $cmd->setConfiguration('topic', $value);
        $cmd->save();
        $elogic->checkAndUpdateCmd($cmd, $value);

        $elogic->save();
        $elogic->refreshWidget();
    }


    public static function getErrorDescription($code) {
        $desc = [
            -1 => __('Inconnu', __FILE__),
            0 => __('Aucune erreur', __FILE__),
            1 => __('Bloquée', __FILE__),
            2 => __('Soulevée', __FILE__),
            3 => __('Câble non trouvé', __FILE__),
            4 => __('En dehors des limites', __FILE__),
            5 => __('Délai pluie', __FILE__),
            6 => "close door to mow",
            7 => "close door to go home",
            8 => __('Moteur lames bloqué', __FILE__),
            9 => __('Moteur roues bloqué', __FILE__),
            10 => __('Timeout après blocage', __FILE__),
            11 => __('Renversée', __FILE__),
            12 => __('Batterie faible', __FILE__),
            13 => __('Câble inversé', __FILE__),
            14 => __('Erreur charge batterie', __FILE__),
            15 => __('Délai recherche base dépassé', __FILE__),
            16 => __('Verrouillée', __FILE__),
            17 => "battery temperature error",
            18 => "dummy model",
            19 => "battery trunk open timeout",
            20 => "wire sync",
            21 => "msg num",
        ];
        return $desc[$code];
    }

    public static function getStatusDescription($code) {
        $desc = [
            -1 => __('Inconnu', __FILE__),
            0 => __("Inactive", __FILE__),
            1 => __("Sur la base", __FILE__),
            2 => __("Séquence de démarrage", __FILE__),
            3 => __("Quitte la base", __FILE__),
            4 => __("Suit le câble", __FILE__),
            5 => __("Recherche de la base", __FILE__),
            6 => __("Recherche du câble", __FILE__),
            7 => __("En cours de tonte", __FILE__),
            8 => __("Soulevée", __FILE__),
            9 => __("Bloquée", __FILE__),
            10 => __("Lames bloquées", __FILE__),
            11 => 'Debug',
            12 => __("Contrôle à distance", __FILE__),
            30 => __("Retour à la base", __FILE__),
            31 => __("Création de zones", __FILE__),
            32 => __("Coupe la bordure", __FILE__),
            33 => __("Départ vers zone de tonte", __FILE__),
            34 => __("Pause", __FILE__),

        ];
        return $desc[$code];
    }

    public static function getSavedDaySchedule($_id, $i) {
        $cmdlogic = worxLandroidSCmd::byEqLogicIdCmdName($_id, 'Planning_startTime_' . $i);
        $day[0]   = $cmdlogic->getConfiguration('savedValue', '10:00');

        $cmdlogic = worxLandroidSCmd::byEqLogicIdCmdName($_id, 'Planning_duration_' . $i);
        $day[1]   = intval($cmdlogic->getConfiguration('savedValue', 420));
        $cmdlogic = worxLandroidSCmd::byEqLogicIdCmdName($_id, 'Planning_cutEdge_' . $i);
        $day[2]   = intval($cmdlogic->getConfiguration('topic', 0));

        return $day;
    }
    public static function getSchedule($_id) {
        $schedule = array();

        $day = array();
        for ($i = 0; $i < 7; $i++) {

            $cmdlogic = worxLandroidSCmd::byEqLogicIdCmdName($_id, 'Planning_startTime_' . $i);
            $day[0]   = $cmdlogic->getConfiguration('topic', '10:00');
            $cmdlogic = worxLandroidSCmd::byEqLogicIdCmdName($_id, 'Planning_duration_' . $i);
            $day[1]   = intval($cmdlogic->getConfiguration('topic', 420));
            $cmdlogic = worxLandroidSCmd::byEqLogicIdCmdName($_id, 'Planning_cutEdge_' . $i);
            $day[2]   = intval($cmdlogic->getConfiguration('topic', 0));

            $schedule[$i] = $day;
        }
        return $schedule;
    }

    public static function setDaySchedule($_id, $daynumber, $daySchedule) {
        $schedule                     = array();
        // $elogic = self::byLogicalId($nodeid, __CLASS__);
        $schedule                     = worxLandroidS::getSchedule($_id);
        // $daySchedule[2]               = $schedule[intval($daynumber)][2];
        $schedule[intval($daynumber)] = $daySchedule;
        $_message                     = '{"sc":' . json_encode(array(
            'd' => $schedule
        )) . "}";
        log::add(__CLASS__, 'debug', '$current schedule: ' . $_message);
        return $_message;
        //  worxLandroidS::setSchedule($eqlogic, $schedule);
    }

    public static function publishMosquitto($_id, $_subject, $_message, $_retain) {
        // save schedule if setting to 0 - and retrieve from saved value (new values must be set from smartphone
        $cmd = worxLandroidSCmd::byId($_id);
        log::add(__CLASS__, 'debug', 'Publication du message ' . $cmd->getName() . ' ' . $_message);
        $eqlogicid = $cmd->getEqLogic_id();
        $eqlogic   = $cmd->getEqLogic();

        if (substr_compare($cmd->getName(), 'off', 0, 3) == 0) {
            log::add(__CLASS__, 'debug', 'Envoi du message OFF: ' . $_message);
            if ($cmd->getName() == 'off_today') {
                $_message = 'off_' . date('w');
            }

            $sched    = array(
                '00:00',
                0,
                0
            );
            $_message = self::setDaySchedule($eqlogicid, substr($_message, 4, 1), $sched); //  $this->saveConfiguration('savedValue',
        }
        if (substr_compare($cmd->getName(), 'on', 0, 2) == 0) {
            log::add(__CLASS__, 'debug', 'Envoi du message On: ' . $_message);
            if ($cmd->getName() == 'on_today') {
                $_message = 'on_' . date('w');
            }

            $sched = self::getSavedDaySchedule($eqlogicid, substr($_message, 3, 1));

            $_message = self::setDaySchedule($eqlogicid, substr($_message, 3, 1), $sched); //  $this->saveConfiguration('savedValue',
        }

        if ($cmd->getName() == 'user_message') {
            $_message = trim($_message, '|');
        }

        if ($cmd->getName() == 'set_schedule') {
            $req = explode(";", $_message); // format = numéro jour;heure:minute;durée en minutes;0 ou 1 pour la bordure
            $sched = array(
                $req[1],
                intval($req[2]),
                intval($req[3])
            );
            $_message = self::setDaySchedule($eqlogicid, intval($req[0]), $sched);
        }
    }

    public function toHtml($_version = 'dashboard') {
        if ($this->getConfiguration('automaticWidget', 0) == 0) {
            return parent::toHtml($_version);
        }

        $jour            = array(
            "Dimanche",
            "Lundi",
            "Mardi",
            "Mercredi",
            "Jeudi",
            "Vendredi",
            "Samedi"
        );
        $replace         = $this->preToHtml($_version);
        if (!is_array($replace)) {
            return $replace;
        }
        $version                 = jeedom::versionAlias($_version);
        $today                   = date('w');
        //if ($version != 'mobile' || $this->getConfiguration('fullMobileDisplay', 0) == 1) {
        $worxStatus_template     = getTemplate('core', $version, 'worxStatus', __CLASS__);
        $replace['#daySetup#'] = '';
        // for ($i = 0; $i <= 6; $i++) {
        //     $replaceDay                    = array();
        //     $replaceDay['#day#']           = $jour[$i];
        //     $replaceDay['#daynum#']           = $i;
        //     $startTime                     = $this->getCmd(null, 'Planning_startTime_' . $i);
        //     $cutEdge                       = $this->getCmd(null, 'Planning_cutEdge_' . $i);
        //     $duration                      = $this->getCmd(null, 'Planning_duration_' . $i);
        //     $replaceDay['#startTime#']     = is_object($startTime) ? $startTime->execCmd() : '';
        //     $replaceDay['#duration#']      = is_object($duration) ? $duration->execCmd() : '';
        //     $cmdS                          = $this->getCmd('action', 'on_' . $i);
        //     $replaceDay['#on_daynum_id#']  = $cmdS->getId();
        //     $cmdE                          = $this->getCmd('action', 'off_' . $i);
        //     $replaceDay['#off_daynum_id#'] = $cmdE->getId();

        //     //$replaceDay['#on_id#'] = $this->getCmd('action', 'on_1');
        //     //$replaceDay['#off_id#'] = $this->getCmd('action', 'off_1');
        //     // transforme au format objet DateTime
        //     if ($replaceDay['#duration#'] == 0) {
        //         $replaceDay['#checkedDaynum#'] = '';
        //     } else {
        //         $replaceDay['#checkedDaynum#'] = 'checked';
        //     }

        //     $initDate = DateTime::createFromFormat('H:i', $replaceDay['#startTime#']);
        //     if ($initDate !== false && $replaceDay['#duration#'] != '') {
        //         $initDate->add(new DateInterval("PT" . $replaceDay['#duration#'] . "M"));
        //         $replaceDay['#endTime#'] = $initDate->format("H:i");
        //     } else {
        //         $replaceDay['#endTime#'] = '00:00';
        //     }

        //     $replaceDay['#cutEdge#'] = is_object($cutEdge) ? $cutEdge->execCmd() : '';
        //     $replaceDay['#cutEdgeInfo#'] = $replaceDay['#cutEdge#'];
        //     if ($replaceDay['#cutEdge#'] == '1') {
        //         $replaceDay['#cutEdgeIcon#'] = 'fa nature-grass';
        //         $replaceDay['#cutEdge#'] = 'Bord.';
        //     } else {
        //         $replaceDay['#cutEdgeIcon#'] = 'fa fa-ban';
        //         $replaceDay['#cutEdge#'] = '.';
        //     }


        //     if ($startTime->getIsVisible()) {
        //         $replaceDay['#day_status_visible#'] = '';
        //     } else {
        //         $replaceDay['#day_status_visible#'] = 'display:none';
        //     }

        //     //$replaceDay['#icone#'] = is_object($condition) ? self::getIconFromCondition($condition->execCmd()) : '';
        //     //$replaceDay['#conditionid#'] = is_object($condition) ? $condition->getId() : '';
        //     $replace['#daySetup#'] .= template_replace($replaceDay, $worxStatus_template);

        //     if ($today == $i) {
        //         $replace['#todayStartTime#']      = is_object($startTime) ? $startTime->execCmd() : '';
        //         $replace['#todayDuration#']       = is_object($duration) ? $duration->execCmd() : '';
        //         $replace['#today_on_daynum_id#']  = $cmdS->getId();
        //         $replace['#today_off_daynum_id#'] = $cmdE->getId();
        //         if ($initDate !== false && $replaceDay['#duration#'] != '') {
        //             $replace['#todayEndTime#'] = $initDate->format("H:i");
        //         } else {
        //             $replace['#todayEndTime#'] = '00:00';
        //         }

        //         if ($replace['#cutEdge#'] == '1') {
        //             $replace['#cutEdge#'] = 'Bord.';
        //         }
        //         $replace['#today#'] = $jour[$i];
        //     }
        // }
        //}

        $theme = jeedom::getThemeConfig();

        if (strstr($theme['current_desktop_theme'], 'Light')) {
            $replace['#theme#']  = "light";
            $replace['#backgroundColor#']  = "";
        } else {
            $replace['#theme#']  = "dark";
            $replace['#backgroundColor#']  = "background-color:black;opacity:0.8";
        }

        $cmd_html = '';
        foreach ($this->getCmd('info') as $cmd) {
            $replace['#' . $cmd->getLogicalId() . '_id#']      = $cmd->getId();
            $replace['#' . $cmd->getLogicalId() . '#']         = $cmd->execCmd();
            $replace['#' . $cmd->getLogicalId() . '_collect#'] = $cmd->getCollectDate();
            if ($cmd->getLogicalId() == 'encours') {
                $replace['#batteryLevel#'] = $cmd->getDisplay('icon');
            }

            if ($cmd->getIsVisible()) {
                $replace['#' . $cmd->getLogicalId() . '_visible#'] = '';
            } else {
                $replace['#' . $cmd->getLogicalId() . '_visible#'] = 'display:none';
            }
            if ($cmd->getIsHistorized() == 1) {
                $replace['#' . $cmd->getLogicalId() . '_history#'] = 'history cursor';
            } else {
                $replace['#' . $cmd->getLogicalId() . '_history#'] = '';
            }

            // if ($cmd->getLogicalId() == 'virtualInfo') {
            //     if ($cmd->getTemplate('dashboard', '') == '') {
            //         $cmd->setTemplate('dashboard', 'badge');
            //     }
            //     if (substr_compare($cmd->getName(), 'Planning', 0, 8) != 0) {
            //         $cmd_html .= $cmd->toHtml($_version, '');
            //     }
            // }

        }
        $cmdaction_html = '';
        foreach ($this->getCmd('action') as $cmd) {
            $replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
            if ($cmd->getIsVisible()) {
                $replace['#' . $cmd->getLogicalId() . '_visible#'] = '';
            } else {
                $replace['#' . $cmd->getLogicalId() . '_visible#'] = 'display:none';
            }

            $addCmds = ['activateschedules', 'deactivateschedules', 'set_mowing_zone', 'setpartymode', 'unsetpartymode', 'activate_module_us', 'deactivate_module_us', 'activate_module_digital_fence_fh', 'deactivate_module_digital_fence_fh', 'activate_module_digital_fence_cut', 'deactivate_module_digital_fence_cut'];

            if ($cmd->getIsVisible() and (in_array($cmd->getLogicalId(), $addCmds))) {
                $cmdaction_html .= $cmd->toHtml($_version, '');
            }
        }
        $replace['#cmdaction#'] = $cmdaction_html;

        $batteryLevelCmd = $this->getCmd(null, 'battery_percent');
        $batteryLevel = is_object($batteryLevelCmd) ? $batteryLevelCmd->execCmd() : 0;
        if ($batteryLevel > 90)  $replace['#batteryIMG#']  = "batterie_full.png";
        elseif ($batteryLevel > 75) $replace['#batteryIMG#']  = "batterie_high.png";
        elseif ($batteryLevel > 50) $replace['#batteryIMG#']  = 'batterie_medium.png';
        elseif ($batteryLevel > 25) $replace['#batteryIMG#']  = 'batterie_low.png';
        else $replace['#batteryIMG#']  = 'batterie_highlow.png';

        $wifiQualityCmd = $this->getCmd(null, 'rssi');
        $wifiQuality = is_object($wifiQualityCmd) ? $wifiQualityCmd->execCmd() : -100;
        if ($wifiQuality <= -90) $replace['#wifiIconClass#']  = 'jeedom2-fdp1-signal0';
        elseif ($wifiQuality <= -80) $replace['#wifiIconClass#']  = 'jeedom2-fdp1-signal1';
        elseif ($wifiQuality <= -70) $replace['#wifiIconClass#']  = 'jeedom2-fdp1-signal2';
        elseif ($wifiQuality <= -67) $replace['#wifiIconClass#']  = 'jeedom2-fdp1-signal3';
        elseif ($wifiQuality <= -50) $replace['#wifiIconClass#']  = 'jeedom2-fdp1-signal4';
        else $replace['#wifiIconClass#']  = 'jeedom2-fdp1-signal5';

        $rainCmdHtml = '';
        $rainCmdId = ['rainsensor_delay', 'setraindelay'];
        foreach ($rainCmdId as $c) {
            /** @var cmd */
            $rainCmd = $this->getCmd(null, $c);
            if (is_object($rainCmd) && $rainCmd->getIsVisible()) {
                $rainCmdHtml .= $rainCmd->toHtml($_version, '');
            }
        }
        $replace['#raindelay#'] =  $rainCmdHtml;

        $bladesCmd = $this->getCmd(null, 'blades_current_on');
        $blades = is_object($bladesCmd) ? $bladesCmd->execCmd() : 0;
        $replace['#blades_current_on#'] = round($blades / 60);

        $replace['#bladesDurationColor#'] = 'green';
        if ($replace['#blades_current_on#'] > $this->getConfiguration('maxBladesDuration')) {
            $replace['#bladesDurationColor#'] = 'orange';
        }

        $replace['#errorColor#'] = 'darkgreen';
        $replace['#error_title#'] = $replace['#error_description#'];
        if ($replace['#error_id#'] == 5) {
            $replace['#errorColor#'] = 'lightblue';
            $replace['#error_title#'] = $replace['#error_title#'] . ' (' . $replace['#rainsensor_remaining#'] . ' min)';
        } elseif ($replace['#error_id#'] != 0) {
            $replace['#errorColor#'] = 'orange';
        }
        switch ($replace['#error_id#']) {
                // affichage icone pluie
            case '0':
                $replace['#errorIcon#'] = 'jeedomapp-sun';
                break;
            case '1':
                $replace['#errorIcon#'] = 'fas fa-exclamation-circle icon_red';
                break;
            case '2':
                $replace['#errorIcon#'] = 'fas fa-arrow-up';
                break;
            case '4':
                $replace['#errorIcon#'] = 'fas nature-wood6';
                break;
            case '5':
                $replace['#errorIcon#'] = 'meteo-pluie';
                break;
            case '8':
                $replace['#errorIcon#'] = 'jeedom-ventilo';
                break;
            case '9':
                $replace['#errorIcon#'] = 'fas fa-ban';
                break;
            case '12':
                $replace['#errorIcon#'] = 'jeedom-batterie0';
                break;

            default:
                $replace['#errorIcon#'] = 'fas fa-exclamation-circle icon_red';
                break;
        }

        $code = $replace['#status_id#'];
        if ($code < 5 or $code == 10 or $code == 9 or $code == 34) {
            $replace['#moving#'] = 'display:none';
        }

        // nouveau template
        $replaceImg['#worxImg#'] = '';
        $replaceImg['#theme#'] = $replace['#theme#'];
        $worxImg_template     = getTemplate('core', $version, strval($code), __CLASS__);
        $replace['#worxImg#'] .= template_replace($replaceImg, $worxImg_template);
        // fin nouveau template
        if ($cmd->getLogicalId() == 'virtualInfo') {
            $replace['#widget#'] = $cmd_html; // FIXME $cmd_html assigned to #widget# & #cmd# ?
        }
        $replace['#cmd#'] = $cmd_html; // FIXME $cmd_html assigned to #widget# & #cmd# ?

        // if ($automaticWidget == true) {
        return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'worxMain', __CLASS__)));
        // } else {
        //     return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'worxMainOwn', __CLASS__)));
        // }
    }
}

class worxLandroidSCmd extends cmd {

    public function execute($_options = array()) {
        $eqLogic = $this->getEqLogic();

        $params = [
            'action' => $this->getConfiguration('action', '') ?: $this->getLogicalId(),
            'serial_number' => $eqLogic->getConfiguration('serial_number')
        ];

        switch ($this->getLogicalId()) {
            case 'setraindelay':
                $params['args'] = [$_options['slider']];
                break;
            case 'lock':
            case 'setpartymode':
            case 'activateschedules':
                $params['args'] = [true];
                break;
            case 'unlock':
            case 'unsetpartymode':
            case 'deactivateschedules':
                $params['args'] = [false];
                break;
            case 'activate_module_us':
            case 'deactivate_module_us':
            case 'activate_module_digital_fence_fh':
            case 'deactivate_module_digital_fence_fh':
            case 'activate_module_digital_fence_cut':
            case 'deactivate_module_digital_fence_cut':
            case 'activate_module_hl':
            case 'deactivate_module_hl':
                $params['args'] = [$this->getConfiguration('module_name'), $this->getConfiguration('module_key'), boolval($this->getConfiguration('module_value'))];
                break;
            case 'cutedge':
                $params['args'] = [true, 0];
                break;
            case 'onetimeschedule':
                $params['args'] = [false, $_options['message']];
                break;
            case 'set_mowing_zone':
                $params['args'] = [$_options['select']];
                break;
            case 'set_zones_starting_point':
                $zones = explode(',', $_options['message']);
                $points = [0, 0, 0, 0];
                $i = 0;
                foreach ($zones as $zone) {
                    if (!is_numeric($zone) || $zone < 0) throw new Exception("Zone must be numeric");
                    if ($zone == 0) break;
                    $points[$i++] = intval($zone);
                    if ($i > 3) break;
                }
                $params['args'] = [$points];
                break;
            case 'set_zones_vector':
                $zones = explode(',', $_options['message']);
                $vectors = [0, 0, 0, 0];
                $i = 0;
                foreach ($zones as $zone) {
                    if (!is_numeric($zone) || $zone < 0 || $zone > 100) throw new Exception("Zone must be numeric");
                    $vectors[$i++] = intval($zone);
                    if ($i > 3) break;
                }
                $params['args'] = [$vectors];
                break;
            case 'set_schedule':
                $primary = [
                    ["11:00", 150, 1],
                    ["11:00", 150, 0],
                    ["00:00", 0, 0],
                    ["11:00", 150, 1],
                    ["11:00", 135, 0],
                    ["11:00", 135, 0],
                    ["11:00", 135, 0]
                ];
                $secondary = [
                    ["11:00", 150, 1],
                    ["11:00", 150, 0],
                    ["00:00", 0, 0],
                    ["11:00", 150, 1],
                    ["11:00", 135, 0],
                    ["11:00", 135, 0],
                    ["11:00", 135, 0]
                ];
                $params['args'] = [$primary, $secondary];

                break;
            default:
                # code...
                break;
        }

        worxLandroidS::sendToDaemon($params);

        // if ($this->getLogicalId() == 'newBlades') {
        //     $elogic = $this->getEqLogic();
        //     $cmdin = worxLandroidSCmd::byEqLogicIdCmdName($elogic->getId(), 'totalBladeTime');
        //     $value = $cmdin->execCmd();
        //     $elogic->newInfo('lastBladesChangeTime', $value, 'numeric', 0);
        //     return true;
        // } else {

        //     switch ($this->getType()) {
        //         case 'action':
        //             $request = $this->getConfiguration('request', '1');
        //             $topic   = $this->getConfiguration('topic');
        //             switch ($this->getSubType()) {
        //                 case 'slider':
        //                     $request = str_replace('#slider#', $_options['slider'], $request);
        //                     break;
        //                 case 'color':
        //                     $request = str_replace('#color#', $_options['color'], $request);
        //                     break;
        //                 case 'message':
        //                     $request = str_replace('#title#', $_options['title'], $request);
        //                     $request = str_replace('#message#', $_options['message'], $request);
        //                     break;
        //             }

        //             $request = str_replace('\\', '', jeedom::evaluateExpression($request));
        //             if ($this->getName() == 'set_schedule') {
        //                 $request = $_options['message'];
        //             }
        //             $request = cmd::cmdToValue($request);
        //             // save schedule if setting to 0 - and retrieve from saved value (new values must be set from smartphone

        //             $eqlogic = $this->getEqLogic();
        //             log::add(__CLASS__, 'debug', 'Eqlogicname: ' . $eqlogic->getName());
        //             worxLandroidS::publishMosquitto($this->getId(), $topic, $request, $this->getConfiguration('retain', '0'));
        //     }
        // }
    }
}
