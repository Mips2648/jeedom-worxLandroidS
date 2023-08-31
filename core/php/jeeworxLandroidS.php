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
try {
    require_once dirname(__FILE__) . "/../../../../core/php/core.inc.php";

    if (!jeedom::apiAccess(init('apikey'), 'worxLandroidS')) {
        echo __('Vous n\'etes pas autorisé à effectuer cette action', __FILE__);
        die();
    }

    if (init('test') != '') {
        echo 'OK';
        log::add('worxLandroidS', 'debug', 'test from daemon');
        die();
    }
    $result = json_decode(file_get_contents("php://input"), true);
    if (!is_array($result)) {
        die();
    } elseif (isset($result['devices'])) {
        log::add('worxLandroidS', 'debug', 'devices received:' . json_encode($result['devices']));
        worxLandroidS::create_or_update_devices($result['devices']);
    } elseif (isset($result['uuid'])) {
        /** @var worxLandroidS */
        $eqLogic = eqLogic::byLogicalId($result['uuid'], 'worxLandroidS');
        if (!is_object($eqLogic)) {
            log::add('worxLandroidS', 'error', __('worxLandroidS eqLogic non trouvé : ', __FILE__) . $result['uuid']);
        } else {
            log::add('worxLandroidS', 'debug', "new message for '{$result['uuid']}': " . json_encode($result['data']));
            $eqLogic->on_message($result['data']);
        }
    } elseif (isset($result['activity_logs'])) {
        /** @var worxLandroidS */
        $eqLogic = eqLogic::byLogicalId($result['activity_logs'], 'worxLandroidS');
        if (!is_object($eqLogic)) {
            log::add('worxLandroidS', 'error', __('worxLandroidS eqLogic non trouvé : ', __FILE__) . $result['uuid']);
        } else {
            log::add('worxLandroidS', 'debug', "activity_logs for '{$result['activity_logs']}': " . json_encode($result['data']));
            worxLandroidS::on_activity_logs($result['data']);
        }
    } else {
        log::add('worxLandroidS', 'debug', 'unknown message: ' . json_encode($result));
    }

    echo 'OK';
} catch (Exception $e) {
    log::add('worxLandroidS', 'error', displayException($e));
}
