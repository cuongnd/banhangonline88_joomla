/**
 * MaQma General JavaScript Plugin
 * Version: v1.0.0
 * License: GPL
 * http://www.imaqma.com
 */
function MaQmaJSPlugin() {

    /* Returns if it's a number or not */
    this.ValidNumber = function (number) {
        var numbers = "0123456789";

        for (var i = 0; i < number.length; i++) {
            if (numbers.indexOf(numbers.substring(i, i + 1)) == -1) {
                return false;
            }
        }

        return true;
    }

    /* Returns if a e-mail is valid or not */
    this.ValidEmail = function (email) {
        var pattern = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return pattern.test(email);
    }

    /* Only only the valid characters set */
    this.ValidChars = function (string) {
        var parsed = true;
        var validchars = "abcdefghijklmnopqrstuvwxyz0123456789@.-";

        for (var i = 0; i < string.length; i++) {
            var letter = string.charAt(i).toLowerCase();
            if (validchars.indexOf(letter) != -1)
                continue;
            parsed = false;
            break;
        }
        return parsed;
    }

    /* Publishs and Unpublishs rows using Ajax */
    this.RowPublishStatus = function (option, controller, task, id, objid) {
        $jMaQma("#" + objid).attr('src', 'components/' + option + '/assets/images/loading.gif');
        $jMaQma.ajax({
            url:'index.php?option=' + option + '&view=' + controller + '&task=' + task + '&id=' + id + '&format=raw',
            async:false,
            success:function (data) {
                $jMaQma("#" + objid).attr('src', 'components/' + option + '/assets/images/themes/' + IMQM_ICON_THEME + '/16px/' + data);
            }
        });
    }

    /* Creates a helping hand to take the attention of the user to something */
    this.AddHelpHand = function (element) {
        var div = $jMaQma('<div id="hand' + element + '"/>');
        div.addClass("helphand");
        div.html('<img src="components/com_maqmahelpdesk/images/hand_click.gif" />');
        div.appendTo("body");

        MaQmaJS.HelpHandResize();

        this.PositionHelpHand(element);
    }

    /* Adds a window resize re-position of the Helping Hand */
    this.HelpHandResize = function () {
        $jMaQma(window).resize(function () {
            clearTimeout(timer);
            clearInterval(timer);
            var timer = (function () {
                MaQmaJS.RePositionHelpHand();
            }).delay(50);
        });
    }

    /* Position the helping hand element when the window is resized */
    this.RePositionHelpHand = function () {
        $$('div.helphand').each(function (el) {
            element = el.id;
            MaQmaJS.PositionHelpHand(element.substring(4));
        });
    }

    /* Position the helping hand element */
    this.PositionHelpHand = function (element) {
        var mel = $jMaQma('#' + element).position();
        var wel = $jMaQma('#' + element).width();
        var hel = $jMaQma('#' + element).height();
        var posy = mel.top + (hel * 75 / 100);
        var posx = mel.left + (wel * 50 / 100);
        $jMaQma('#hand' + element).css({ top:posy, left:posx });
    }
}

var MaQmaJS = new MaQmaJSPlugin();
