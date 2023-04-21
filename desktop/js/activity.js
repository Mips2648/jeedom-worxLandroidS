function addActivityLog(log) {
    let tr = '<tr>';
    tr += '<td>'
    tr += '<span class="label label-default">' + log.updated + '</span>'
    tr += '</td>'

    const dangerStatus = [0, 8, 9, 10]
    const successStatus = [1, 2, 3, 4, 5, 6, 7, 12, 30, 31, 32, 33, 34]
    if (successStatus.includes(log.status.id)) {
        label = 'label-success'
    } else if (dangerStatus.includes(log.status.id)) {
        label = 'label-danger'
    } else {
        label = 'label-warning'
    }
    tr += '<td>'
    tr += '<span class="label ' + label + '">' + log.status.description + '</span>'
    tr += '</td>'

    tr += '<td>'
    tr += '<span class="label ' + (log.error.id == 0 ? 'label-success' : 'label-danger') + '">' + log.error.description + '</span>'
    tr += '</td>'

    tr += '<td>'
    tr += log.zone.current + 1
    tr += '</td>'

    tr += '<td>'
    if (log.battery.charging) {
        tr += '<span class="label label-success"><i class="fas fa-charging-station"></i></span>'
    }

    tr += '</td>'
    tr += '</tr>';

    $('#table_activityworxLandroidS tbody').append(tr)

}

function get_activity_logs(mower_id) {

    $.ajax({
        type: "POST",
        url: "plugins/worxLandroidS/core/ajax/worxLandroidS.ajax.php",
        data: {
            action: "get_activity_logs",
            id: mower_id
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state != 'ok') {
                $('#div_worxLandroidSAlert').showAlert({ message: data.result, level: 'danger' });
                return;
            }
            $('#table_activityworxLandroidS tbody').empty()
            data.result.forEach(element => {
                addActivityLog(element)
            });

        }
    });
}

$('#sel_mower').on('change', function () {
    get_activity_logs(this.value());
});

get_activity_logs($('#sel_mower').value());
