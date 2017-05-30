(function ($) {

    $.fn.timepicker = function (options) {

        var hours = '';
        for (i = 0; i <= 23; i++)
            hours += '<a>' + (i < 10 ? '0' + i : i) + '</a>';

        var mins = '';
        for (i = 0, j = 0; i <= 11; i++, j = j + 5)
            mins += '<a>' + (j < 10 ? '0' + j : j) + '</a>';

        html = '<div id="hours" style="float:left;width:120px;">' + hours + '</div>';
        html += '<div id="mins" style="float:left;width:60px;border-left:1px solid #ddd;">' + mins + '</div>';
        html += '<a class="closepicker" onclick="jQuery(\'#timepicker\').slideUp();">&#94;</a>';

        return this.each(function () {
            $(this).click(function () {
                var obj = $(this);
                var selectedHours = null;
                var selectedMins = null;

                if (document.getElementById("timepicker") != undefined)
                    $("#timepicker").remove();

                $('<div id="timepicker">' + html + '</div>').appendTo("body")
                    .css("top", $(this).offset().top + $(this).height() + 6)
                    .css("left", $(this).offset().left)
                    .slideDown();

                if ($(obj).val() != '') {
                    selectedHours = $(obj).val().substring(0, 2);
                    selectedMins = $(obj).val().substring(3, 5);
                    $("#timepicker #hours a").each(function () {
                        if ($(this).html() == selectedHours) {
                            $(this).css("background", "#666").css("color", "#fff");
                        }
                    });
                    $("#timepicker #mins a").each(function () {
                        if ($(this).html() == selectedMins) {
                            $(this).css("background", "#666").css("color", "#fff");
                        }
                    });
                }

                $('#timepicker #hours a, #timepicker #mins a').mouseover(
                    function () {
                        $(this).css("background", "#666").css("color", "#fff");
                    }).mouseout(
                    function () {
                        $(this).css("background", "#fff").css("color", "#666");
                    }).click(function () {
                        if ($(this).parent().attr("id") == "hours") {
                            selectedHours = $(this).html();
                        } else {
                            selectedMins = $(this).html();
                        }
                        if (selectedHours != null && selectedMins != null) {
                            $(obj).val(selectedHours + ':' + selectedMins).change();
                            $("#timepicker").slideUp();
                        }
                    });
            });
        });

    };

})($jMaQma);