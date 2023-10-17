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
    require_once __DIR__ . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - {{Accès non autorisé}}', __FILE__));
    }

    if (init('action') == 'synchronize') {
        worxLandroidS::synchronize();
        ajax::success();
    } elseif (init('action') == 'createCommands') {
        /** @var worxLandroidS */
        $eqLogic = eqLogic::byId(init('id'));
        if (!is_object($eqLogic)) {
            throw new Exception(__('worxLandroidS eqLogic non trouvé : ', __FILE__) . init('id'));
        }

        try {
            $eqLogic->createCommands();
            ajax::success();
        } catch (\Throwable $th) {
            throw new Exception(__('Erreur lors de la création des commandes: ', __FILE__) . $th->getMessage());
        }
    } elseif (init('action') == 'getActivityLogs') {
        /** @var worxLandroidS */
        $eqLogic = eqLogic::byId(init('id'));
        if (!is_object($eqLogic)) {
            throw new Exception(__('worxLandroidS eqLogic non trouvé : ', __FILE__) . init('id'));
        }
        $eqLogic->get_activity_logs();
        ajax::success();
    }

    throw new Exception(__('Aucune methode correspondante à: ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}
