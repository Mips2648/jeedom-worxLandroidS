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

if (!isConnect('admin')) {
	throw new Exception('401 Unauthorized');
}

include_file('desktop', 'activity', 'js', 'worxLandroidS');
?>
<div id='div_worxLandroidSAlert' style="display: none;"></div>
<span class='pull-right'>
	<select id="sel_mower" class="form-control">
		<?php
		/** @var eqLogic[] */
		$eqLogics = worxLandroidS::byType('worxLandroidS', true);
		foreach ($eqLogics as $eqLogic) {
			echo '<option value="' . $eqLogic->getId() . '">' . $eqLogic->getName() . '</option>';
		}
		?>
	</select>
</span>

<table class="table table-condensed tablesorter" id="table_activityworxLandroidS">
	<thead>
		<tr>
			<th>{{Date/heure}}</th>
			<th>{{Statut}}</th>
			<th>{{Erreur}}</th>
			<th>{{Zone}}</th>
			<th>{{Chargement}}</th>
		</tr>
	</thead>
	<tbody>

	</tbody>
</table>