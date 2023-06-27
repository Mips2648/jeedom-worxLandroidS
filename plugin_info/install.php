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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function worxLandroidS_install() {
    $pluginId = 'worxLandroidS';
    config::save('api', config::genKey(), $pluginId);
    config::save("api::{$pluginId}::mode", 'localhost');
    config::save("api::{$pluginId}::restricted", 1);
}

function worxLandroidS_update() {
    $pluginId = 'worxLandroidS';
    config::save('api', config::genKey(), $pluginId);
    config::save("api::{$pluginId}::mode", 'localhost');
    config::save("api::{$pluginId}::restricted", 1);

    $cron = cron::byClassAndFunction($pluginId, 'daemon');
    if (is_object($cron)) {
        $cron->stop();
        $cron->remove();
    }
    config::remove('initCloud', $pluginId);

    /** @var worxLandroidS */
    foreach (eqLogic::byType($pluginId) as $eqLogic) {
        $cmd = $eqLogic->getCmd('action', 'setzone');
        if (is_object($cmd)) {
            $cmd->remove();
        }
        $cmd = $eqLogic->getCmd('info', 'mower_work_time');
        if (is_object($cmd)) {
            $cmd->remove();
        }

        $eqLogic->createCommands();
    }
}

function worxLandroidS_remove() {
    $pluginId = 'worxLandroidS';
    config::remove('api', $pluginId);
    config::remove("api::{$pluginId}::mode");
    config::remove("api::{$pluginId}::restricted");
}
