
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
$('#bt_activityworxLandroidS').on('click', function () {
    $('#md_modal').dialog({ title: "{{Rapport d'activité}}" });
    $('#md_modal').load('index.php?v=d&plugin=worxLandroidS&modal=activity').dialog('open');
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

function printSchedulesPanel(schedule) {
    //schedules panel
    $('#table_schedules tbody').empty();
    let i = 0;
    Object.entries(schedule['primary']).forEach(element => {
        let tr = '<tr>';
        tr += '<td>' + translateWeekday(element[0]) + '</td>';
        tr += '<td><input id="primary_start' + i + '" class="form-control" type="time" value="' + element[1].start + '"></td>';
        tr += '<td><input id="primary_end' + i + '" class="form-control" type="time" value="' + element[1].end + '"></td>';
        tr += '<td>' + element[1].duration + ' min</td>';
        tr += '<td><input id="edge' + i + '" class="form-control" type="checkbox" ' + (element[1].boundary ? 'checked' : '') + '></td>';
        tr += '</tr>'
        $('#table_schedules tbody').append(tr);
        if (schedule['secondary']) {
            let tr = '<tr>';
            tr += '<td></td>';
            tr += '<td><input id="secondary_start' + i + '" class="form-control" type="time" value="' + schedule['secondary'][element[0]].start + '"></td>';
            tr += '<td><input id="secondary_end' + i + '" class="form-control" type="time" value="' + schedule['secondary'][element[0]].end + '"></td>';
            tr += '<td>' + schedule['secondary'][element[0]].duration + ' min</td>';
            tr += '<td></td>';
            tr += '</tr>'
            $('#table_schedules tbody').append(tr);
        }
        i++;
    });

    // general info panel
    if (schedule['active']) {
        labelClass = 'label-success';
        text = '<i class="fas fa-check"> Actif</i>'
        $("#div_schedulesPanel :input").prop('disabled', false);
        $("#div_schedulesPanel a").show()
    } else {
        labelClass = 'label-danger';
        text = '<i class="fas fa-times"> Inactif</i>'
        $("#div_schedulesPanel :input").prop('disabled', true);
        $("#div_schedulesPanel a").hide()
    }
    $('#schedule').html('<label class="label ' + labelClass + '">' + text + '</label>')

    //TODO: add save feature, for now disabling input
    $("#div_schedulesPanel a").hide()
    $("#div_schedulesPanel :input").prop('disabled', true);
}

function printZonePanel(zone) {
    $('#table_zone tbody').empty();
    const counts = {};
    zone.indicies.forEach(function (x) { counts[x] = (counts[x] || 0) + 1; });
    Object.entries(zone['starting_point']).forEach(element => {
        let tr = '<tr>';
        tr += '<td>Zone ' + (parseInt(element[0]) + 1) + '</td>';
        tr += '<td>' + element[1] + ' m</td>';
        tr += '<td>'
        tr += '<input ' + (element[1] == 0 ? 'disabled' : '') + ' id="zone_percent' + element[0] + '" type="range" min="0" max="100" step="10" value="' + (counts[element[0]] ?? 0) * 10 + '" class="slider" oninput="this.parentElement.nextElementSibling.firstChild.value = this.value+\'%\'">'
        tr += '</td>';
        tr += '<td>'
        tr += '<output>' + (counts[element[0]] ?? 0) * 10 + '%</output>'
        tr += '</td>';
        tr += '</tr>'
        $('#table_zone tbody').append(tr);
    });

    //TODO: add save feature, for now disabling input
    $("#div_zonePanel a").hide()
    $("#div_zonePanel :input").prop('disabled', true);
}

function printAutoSchedulePanel(auto_schedule) {
    const MAP_soil_type = {
        'clay': 'Argile',
        'silt': 'Limon',
        'sand': 'Sable',
        'ignore': 'Inconnu'
    };

    const MAP_grass_type = {
        'mixed_species': 'Espèces mixtes'
    };

    const settings = auto_schedule['settings'];

    $('#grass_type').text(MAP_grass_type[settings['grass_type']] ?? settings['grass_type']);
    $('#soil_type').text(MAP_soil_type[settings['soil_type']] ?? settings['soil_type']);
    $('#irrigation').html(settings['irrigation'] ? '<i class="fas fa-check"> Actif</i>' : '<i class="fas fa-times"> Inactif</i>');
    $('#nutrition').html(settings['nutrition'] ?? '<i class="fas fa-times"> Inactif</i>');

    $('#table_exclusions tbody').empty();

    for (let day = 1; day <= 7; day++) {
        let data = settings['exclusion_scheduler']['days'][day % 7]

        if (data.exclude_day) {
            let tr = '<tr>';
            tr += '<td>' + translateWeekday(day) + '</td>';
            tr += '<td><input class="form-control" type="time" value="--:--"></td>';
            tr += '<td><input class="form-control" type="time" value="--:--"></td>';
            tr += '<td></td>';
            tr += '<td><input class="form-control" type="checkbox" ' + (data.exclude_day ? 'checked' : '') + '></td>';
            tr += '</tr>'
            $('#table_exclusions tbody').append(tr);
        } else {
            let dayNamePrinted = false
            data.slots.forEach(slot => {
                let tr = '<tr>';
                if (!dayNamePrinted) {
                    tr += '<td>' + translateWeekday(day) + '</td>';
                    dayNamePrinted = true
                } else {
                    tr += '<td></td>';
                }
                tr += '<td><input class="form-control" type="time" value="' + slot.start_time + '"></td>';
                tr += '<td><input class="form-control" type="time" value="' + slot.end_time + '"></td>';
                tr += '<td>' + slot.duration + ' min</td>';
                tr += '<td><input class="form-control" type="checkbox" ' + (data.exclude_day ? 'checked' : '') + '></td>';
                tr += '</tr>'
                $('#table_exclusions tbody').append(tr);
            });
        }
    }

    if (auto_schedule['enabled']) {
        labelClass = 'label-success';
        text = '<i class="fas fa-check"> Actif</i>'
    } else {
        labelClass = 'label-danger';
        text = '<i class="fas fa-times"> Inactif</i>'
    }
    $('#auto_schedule').html('<label class="label ' + labelClass + '">' + text + '</label>')

    //TODO: add save feature, for now disabling input
    $("#div_autoschedulesPanel a").hide()
    $("#div_autoschedulesPanel :input").prop('disabled', true);

}

function printEqLogic(_eqLogic) {
    const schedule = _eqLogic.configuration.schedules;

    if (_eqLogic.configuration.rain_delay_active === 1) {
        labelClass = 'label-success';
        text = '<i class="fas fa-check"> Actif</i>'
    } else {
        labelClass = 'label-danger';
        text = '<i class="fas fa-times"> Inactif</i>'
    }
    $('#rain_delay').html('<label class="label ' + labelClass + '">' + text + '</label>')

    printSchedulesPanel(schedule);
    printZonePanel(JSON.parse(_eqLogic.configuration.zone));
    printAutoSchedulePanel(schedule['auto_schedule']);

}

function translateWeekday(weekday) {
    let weekdayIndex
    if (typeof weekday === "string") {
        const WEEKDAYS = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']
        weekdayIndex = WEEKDAYS.indexOf(weekday.toLowerCase())
        if (weekdayIndex < 0) throw new Error(`Unknown weekday "${weekday}"`)
    } else {
        weekdayIndex = weekday
    }

    const dummyDate = new Date(2001, 0, weekdayIndex)
    const locale = new Intl.DateTimeFormat().resolvedOptions().locale

    return capitalizeFirstLetter(dummyDate.toLocaleDateString(locale, { weekday: 'long' }))
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

$('#bt_saveZone').on('click', function () {

    let ranges = document.querySelectorAll('input[type=range]');
    let tot = 0;
    ranges.forEach(element => {
        tot += parseInt(element.value);
    });

    if (tot > 100) {
        $.fn.showAlert({ message: '{{Le total de la répartition pour toute les zones ne peut pas dépasser 100%}}', level: 'danger' });
        return;
    }

    $.ajax({
        type: "POST",
        url: "plugins/worxLandroidS/core/ajax/worxLandroidS.ajax.php",
        data: {
            action: "saveZone",
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

$('#bt_saveSchedules').on('click', function () {
    $.ajax({
        type: "POST",
        url: "plugins/worxLandroidS/core/ajax/worxLandroidS.ajax.php",
        data: {
            action: "saveSchedules",
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