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

require_once __DIR__ . '/../../../core/php/core.inc.php';

function InstallComposerDependencies() {
    $pluginId = basename(realpath(__DIR__ . '/..'));
    log::add($pluginId, 'info', 'Install composer dependencies');
    $cmd = 'cd ' . __DIR__ . '/../;export COMPOSER_ALLOW_SUPERUSER=1;export COMPOSER_HOME="/tmp/composer";' . system::getCmdSudo() . 'composer install --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader;' . system::getCmdSudo() . ' chown -R www-data:www-data *';
    shell_exec($cmd);
}

function worxLandroidS_post_plugin_install() {
    InstallComposerDependencies();
}

function worxLandroidS_install() {
    $pluginId = basename(realpath(__DIR__ . '/..'));

    config::save('api', config::genKey(), $pluginId);
    config::save("api::{$pluginId}::mode", 'localhost');
    config::save("api::{$pluginId}::restricted", 1);
}

function worxLandroidS_update() {
    $pluginId = basename(realpath(__DIR__ . '/..'));

    config::save('api', config::genKey(), $pluginId);
    config::save("api::{$pluginId}::mode", 'localhost');
    config::save("api::{$pluginId}::restricted", 1);

    unlink(__DIR__ . '/packages.json');

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

        $cmdsToRecreate = ['activate_module_us', 'deactivate_module_us', 'activate_module_digital_fence_fh', 'deactivate_module_digital_fence_fh', 'activate_module_digital_fence_cut', 'deactivate_module_digital_fence_cut'];
        foreach ($cmdsToRecreate as $logicalId) {
            $cmd = $eqLogic->getCmd('action', $logicalId);
            if (is_object($cmd)) {
                $cmd->remove();
            }
        }

        $eqLogic->createCommands();
    }

    $dependencyInfo = worxLandroidS::dependancy_info();
    if (!isset($dependencyInfo['state'])) {
        message::add($pluginId, __('Veuilez vérifier les dépendances', __FILE__));
    } elseif ($dependencyInfo['state'] == 'nok') {
        try {
            $plugin = plugin::byId($pluginId);
            $plugin->dependancy_install();
        } catch (\Throwable $th) {
            message::add($pluginId, __('Cette mise à jour nécessite de réinstaller les dépendances même si elles sont marquées comme OK', __FILE__));
        }
    }
}

function worxLandroidS_remove() {
    $pluginId = basename(realpath(__DIR__ . '/..'));
    config::remove('api', $pluginId);
    config::remove("api::{$pluginId}::mode");
    config::remove("api::{$pluginId}::restricted");
}
