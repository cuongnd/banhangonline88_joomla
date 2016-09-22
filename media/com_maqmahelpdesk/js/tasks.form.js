function GetLabourTimeTasks() {
	starttime = $jMaQma("#AddTask #taskstart").val();
	endtime = $jMaQma("#AddTask #taskend").val();
	breaktime = $jMaQma("#AddTask #taskbreak").val();
	if (starttime == endtime) return;

	var hours_starttime = starttime.substring(0, 2);
	var mins_starttime = starttime.substring(3, 5);
	var hours_endtime = endtime.substring(0, 2);
	var mins_endtime = endtime.substring(3, 5);
	var hours_breaktime = breaktime.substring(0, 2);
	var mins_breaktime = breaktime.substring(3, 5);

	var starttime_decim = (hours_starttime * 1) + (mins_starttime / 60);
	var endtime_decim = (hours_endtime * 1) + (mins_endtime / 60);
	var breaks_decim = (hours_breaktime * 1) + (mins_breaktime / 60);
	var replytime_decim = (endtime_decim - starttime_decim) - (breaks_decim);

	if ((replytime_decim < 0) && starttime != endtime && breaks_decim == 0) {
		$jMaQma("#AddTask #taskend").val($jMaQma("#AddTask #taskstart").val());
	}

	if (replytime_decim > 0) {
		var replytime_hours = Math.floor(replytime_decim);
		var replytime_mins = (replytime_decim - replytime_hours) * 60;
	} else {
		var replytime_hours = Math.ceil(replytime_decim);
		var replytime_mins = ( (replytime_decim * (-1)) - (replytime_hours * (-1)) ) * 60;
	}

	if (replytime_decim < 0 && replytime_hours == 0) {
		var neg_hhmm = "-";
	} else {
		var neg_hhmm = "";
	}

	replytime_mins = Math.round(replytime_mins * Math.pow(10, 0)) / Math.pow(10, 0);

	if (replytime_mins == "0") {
		replytime_mins = "00";
	} else if (replytime_mins < 10) {
		replytime_mins = "0" + replytime_mins;
	}

	var replytime_hhmm = neg_hhmm + replytime_hours + ':' + replytime_mins;

	$jMaQma("#AddTask #tasktime").val(replytime_hhmm);

	if (replytime_decim < 0) {
		alert(MQM_NEGATIVE_LABOUR);
		$jMaQma("#AddTask #taskbreak").val("00:00");
		GetLabourTime();
	}
}

function TaskCreateSubmit() {
	document.addTask.submit();
}

$jMaQma(document).ready(function () {
	$jMaQma(".timepicker").timepicker();
});

function deleteTask() {
	window.location = MQM_URL_DELETE;
}

function cancelTask() {
	window.location = MQM_URL_CANCEL;
}