
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
$("#bt_addworxLandroidSAction").on('click', function (event) {
    addCmdToTable({ type: 'action' })
    modifyWithoutSave = true
});

$("#bt_addworxLandroidSInfo").on('click', function (event) {
    addCmdToTable({ type: 'info' })
    modifyWithoutSave = true
});

$('#bt_healthworxLandroidS').on('click', function () {
    $('#md_modal').dialog({ title: "{{Santé worxLandroidS}}" });
    $('#md_modal').load('index.php?v=d&plugin=worxLandroidS&modal=health').dialog('open');
});

$('.pluginAction[data-action=openLocation]').on('click', function () {
    window.open($(this).attr("data-location"), "_blank", null);
});

$("#table_cmd").sortable({ axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true });

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = { configuration: {} };
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }

    let tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">'
    tr += '<td class="hidden-xs">'
    tr += '<span class="cmdAttr" data-l1key="id"></span>'
    tr += '</td>'

    tr += '<td>'
    tr += '<div class="input-group">'
    tr += '<input class="cmdAttr form-control input-sm roundedLeft" data-l1key="name" placeholder="{{Nom de la commande}}">'
    tr += '<span class="input-group-btn"><a class="cmdAction btn btn-sm btn-default" data-l1key="chooseIcon" title="{{Choisir une icône}}"><i class="fas fa-icons"></i></a></span>'
    tr += '<span class="cmdAttr input-group-addon roundedRight" data-l1key="display" data-l2key="icon" style="font-size:19px;padding:0 5px 0 0!important;"></span>'
    tr += '</div>'
    tr += '<select class="cmdAttr form-control input-sm" data-l1key="value" style="display:none;margin-top:5px;" title="{{Commande info liée}}">'
    tr += '<option value="">{{Aucune}}</option>'
    tr += '</select>'
    tr += '</td>'

    tr += '<td>'
    tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>'
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>'
    tr += '</td>'

    tr += '<td>'
    tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>{{Afficher}}</label> '
    tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" checked/>{{Historiser}}</label> '
    tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label> '
    tr += '<div style="margin-top:7px;">'
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
    tr += '</div>'
    tr += '</td>'

    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="htmlstate"></span>';
    tr += '</td>';

    tr += '<td>'
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> '
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>'
    }
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove" title="{{Supprimer la commande}}"></i></td>'
    tr += '</tr>';

    $('#table_cmd tbody').append(tr)

    const $tr = $('#table_cmd tbody tr').last()
    jeedom.eqLogic.buildSelectCmd({
        id: $('.eqLogicAttr[data-l1key=id]').value(),
        filter: { type: 'info' },
        error: function (error) {
            $('#div_alert').showAlert({ message: error.message, level: 'danger' })
        },
        success: function (result) {
            $tr.find('.cmdAttr[data-l1key=value]').append(result)
            $tr.setValues(_cmd, '.cmdAttr')
            jeedom.cmd.changeType($tr, init(_cmd.subType))
        }
    })
}

// Called by the plugin core to inform about the inclusion of an equipment
$('body').off('worxLandroidS::includeEqpt').on('worxLandroidS::includeEqpt', function (_event, _options) {
    if (modifyWithoutSave) {
        $('#div_newEqptMsg').showAlert({ message: '{{Un équipement vient d\'être inclu. Veuillez réactualiser la page}}', level: 'warning' });
    }
    else {
        $('#div_newEqptMsg').showAlert({ message: '{{Un équipement vient d\'être inclu. La page va se réactualiser.}}', level: 'warning' });
        // Reload the page after a delay to let the user read the message
        setTimeout(function () {
            if (_options == '') {
                window.location.reload();
            } else {
                window.location.href = 'index.php?v=d&p=worxLandroidS&m=worxLandroidS&id=' + _options;
            }
        }, 2000);
    }
});

$('#bt_syncworxLandroidS').on('click', function () {
    $.ajax({
        type: "POST",
        url: "plugins/worxLandroidS/core/ajax/worxLandroidS.ajax.php",
        data: {
            action: "synchronize",
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({ message: data.result, level: 'danger' });
                return;
            }
            $('#div_alert').showAlert({ message: '{{Synchronisation réussie.}}', level: 'success' });
            setTimeout(function () {
                window.location.replace("index.php?v=d&m=worxLandroidS&p=worxLandroidS");
            }, 3000);
        }
    });
});

$('#bt_createCommands').on('click', function () {
    $.ajax({
        type: "POST",
        url: "plugins/worxLandroidS/core/ajax/worxLandroidS.ajax.php",
        data: {
            action: "createCommands",
            id: $('.eqLogicAttr[data-l1key=id]').value()
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({ message: data.result, level: 'danger' });
                return;
            }
            $('#div_alert').showAlert({ message: '{{Opération réalisée avec succès}}', level: 'success' });
            $('.eqLogicDisplayCard[data-eqLogic_id=' + $('.eqLogicAttr[data-l1key=id]').value() + ']').click();
        }
    });
});

function updatePlanning(cmdId, refreshId) {
    var result = '{"sc":{"d":[';

    for (let i = 0; i < 7; i++) {
        result += '["' + document.getElementById('startTime' + i).value;
        result += '",' + document.getElementById('duration' + i).value;
        result += ',';
        result += document.getElementById('edge' + i).checked ? 1 : 0;

        result += ']';
        if (i < 6) result += ',';

    }
    result += ']}}';
    //alert(result);

    jeedom.cmd.execute({ id: cmdId, value: { message: result } });
};

function updateAreas(cmdId, refreshId) {
    var result = '{"mz":[';
    var resultv = '"mzv":[';
    var dist = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    var valeur = 0;

    for (let i = 0; i < 4; i++) {
        result += document.getElementById('area' + i).value;
        result += i == 3 ? '' : ',';

        for (let j = 0; j < 10; j++) {

            valeur = document.getElementById('dist' + i + j).checked == true ? i : dist[j];
            dist[j] = valeur;
        }
    }

    for (let j = 0; j < 10; j++) {
        resultv += dist[j];
        resultv += j == 9 ? '' : ',';
    }
    result += '],';
    resultv += ']}';
    result += resultv;
    //alert(result);

    jeedom.cmd.execute({ id: cmdId, value: { message: result } });
}
