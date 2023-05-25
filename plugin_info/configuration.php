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
include_file('core', 'authentification', 'php');
if (!isConnect()) {
	include_file('desktop', '404', 'php');
	die();
}
?>

<form class="form-horizontal">
	<fieldset>
		<div class="form-group">
			<label class="col-sm-4 control-label">{{Adresse email}}</label>
			<div class="col-sm-3">
				<input class="configKey form-control" data-l1key="email" type="text" placeholder="{{adresse email cloud worx}}">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">{{Mot de passe}}</label>
			<div class="col-sm-3">
				<div class="input-group">
					<input class="configKey form-control roundedLeft inputPassword" data-l1key="passwd" type="text" placeholder="{{saisir le mot de passe}}">
					<span class="input-group-btn">
						<a class="btn btn-default form-control bt_showPass roundedRight"><i class="fas fa-eye"></i></a>
					</span>
				</div>
			</div>
		</div>
	</fieldset>
</form>