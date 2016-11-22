jQuery.jchat = jchatAlias;
function jchatAlias($) {
    var jsonLiveSite = jchat_livesite + "index.php?option=com_jchat&format=json";
    var htmlLiveSite = jchat_livesite + "index.php?option=com_jchat&format=raw";
    var t = {};
    var s = {};
    var T = "";
    var r = 0;
    var o = 0;
    var J = true;
    var U;
    var X = 2000;
    var K = 2000;
    var F = 10000;
    var openedChatboxes = {};
    var nofluxChatBoxes = {};
    var minimizedIndexes = {};
    var emptyResponse = 20;
    var restartInterval = 1;
    var h = 1;
    var R = 0;
    var n = 0;
    var l = 1;
    var vars = null;
    var targetElement = "body";
    var chatTitle = "";
    var privateChatTitle = "";
    var publicChatTitle = "";
    var chatStatus = null;
    var forceBuddylistRefresh = 0;
    var wallMessagesAlertInterval = null;
    var popupTooltipDependency = null;
    var donotdisturb = 0;
    var audio = 1;
    var wallaudio = 1;
    var vibrate = 1;
    var notification = 0;
    var minimizedAllChatboxes = 0;
    var sound = false;
    var avatarUploadEnabled = true;
    var avatarEnabled = true;
    var attachmentsEnabled = true;
    var videoLength = 9;
    var tabLength = 14;
    var headLength = 30;
    var chatLength = 20;
    var buddyLength = 18;
    var chatRoomLength = 30;
    var chatTitleLength = 26;
    var buddyList = {};
    var publicChatStateClosed = false;
    var buddylistMaximized = false;
    var firstCall = true;
    var showSuggestionTooltip = false;
    var popupPositions = new Object();
    var popupDimensions = new Object();
    var positionmentChatboxes = "middle";
    var asyncSendMessage = "before";
    var popupState = new Object();
    var popupMaximizedState = new Object();
    var defaultPopupHeight = 211;
    var wallStartHeight = 0;
    var wallStartState = true;
    var isContentEditable = null;
    var longname = null;
    var shortname = null;
    var my_username;
    var my_email;
    var my_avatar;
    var fromPlaceholder;
    var writeElement = '<div contentEditable="true" class="jchat_textarea"></div>';
    var writeElementWall = '<div contentEditable="true" class="jchat_textarea jchat_textarea_wall"></div>';
    var valFunction = "html";
    var guestEnabledMode = 0;
    var usersBanning = 0;
    var usersBanningMode = "private";
    var timeoutID = null;
    var isTabFocused = true;
    var originalPageTitle = $("title").text();
    var microSplacementKonstant = 2;
    var doubleChatboxesDirection = false;
    var defaultSuggestionText = jchat_fallback_suggestion;
    var chatFormLink = null;
    var excludeOnMobile = false;
    var guestPrefix = "Guest";
    var notificationsAutoEnable = false;
    var addChatrooms = false;
    var deleteChatrooms = false;
    var wallHistoryDelayAutoload = false;
    var wallHistoryDelayAutoloadMap = {};
    var privateMessagingCounters = {};
    var originalSidebarWidth = 260;
    var wordsBanning = false;
    var wordsBanned = "shit,fuck,cock,asshole";
    var wordsBannedReplacement = "banned";
    var webrtcEnabled = true;
    var webrtcDirector = null;
    var conferenceDirector = null;
    var recorderDirector = null;
    var webrtcRingingTone = "skype";
    var iceServers = null;
    var timeoutStartCall = 20;
    var timeoutEndCall = 20;
    var webrtcFallbackEnabled = true;
    var hideWebcamWhenDisabled = 1;
    var showWebRTCStats = true;
    var showWebRTCVUMeter = true;
    var serverLoadReduction = false;
    var defaultMicVolume = 0.8;
    var defaultAudioVolume = 0.8;
    var videochatAutoMaximize = 1024;
    var autoQualityBandwidthMgmt = false;
    var recordingEnabled = false;
    var ticketsForm = false;
    var ticketsFormAlwaysVisible = false;
    var autoOpenRandomAgentBox = false;
    var autoOpenAgentboxDefaultMessage = "";
    var isChatAdmin = false;
    var affectPublicChat = true;
    var currentJoinedChatRoom = null;
    var roomsAvailable = new Array();
    var myChatRoomUsers = new Array();
    var showChatroomsUsersDetails = true;
    var chatRoomLatestMessages = true;
    var autoClearConversation = true;
    var userAccessLevels = {};
    var joomlaAccessLevels = {};
    var currentScopedUsers = {};
    var defaultChatroom = null;
    var skypeEnabled = true;
    var groupChat = 1;
    var buddyListVisible = true;
    var groupChatMode = "chatroom";
    var chatboxesOpenMode = false;
    var showSendButton = 2;
    var chatTemplate = "default.css";
    var chatTemplateTooltip = "std";
    var chatTemplateTooltipVariant = "horiz";
    var resizableChatboxes = true;
    var resizableSidebar = true;
    var searchFieldEnabled = true;
    var showMyUsername = true;
    var messagesHistory = true;
    var renderingMode = "auto";
    var baloonPosition = "top";
    var separateWidgets = false;
    var privateChatEnabled = true;
    var typingEnabled = true;
    var maximizeButton = 1024;
    var autoClosePopups = true;
    var geolocationEnabled = false;
    var geolocationService = "geoip";
    var gMapReference = {};
    var debugEnabled = false;
    var thirdPartyIntegration = null;
    var PMIntegration = false;
    var facebookLoginActive = false;
    var googleLoginActive = false;
    var twitterLoginActive = false;
    var languageTranslationEnabled = false;
    var popupLanguages = new Object();
    var defaultLanguage = "en";
    var defaultTranslateToLanguage = "en";
    var languageFallbacks = {sourcelang: defaultLanguage, targetlang: defaultTranslateToLanguage};
    var translateSelfMessages = true;
    var isGuest = true;
    var hasSuperUser = false;
    var allowGuestAvatarupload = true;
    var allowGuestFileupload = true;
    var allowGuestSkypeBridge = true;
    var allowGuestOverrideName = true;
    var allowGuestBanning = true;
    var allowGuestBuddylist = true;
    var allowMediaObjects = true;
    var allowChatroomsCreation = false;
    var allowChatroomsDeletion = false;
    var userPermissions = {
        allow_videochat: null,
        allow_media_recorder: null,
        allow_media_recorder_save: null,
        moderation_groups: null
    };
    var enableModeration = false;
    $("<div/>").attr("id", "jchat_base").attr("dir", "ltr").appendTo($(jchatTargetElement));
    function injectFloatingMsg(text, userDetails) {
        if (userDetails) {
            text = sprintf(text, userDetails)
        }
        $("div#jchat_msg").remove();
        $("<div/>").attr("id", "jchat_msg").prependTo("body").append('<div id="jchat_msgtext">' + text + "</div>").css("margin-top", 0).hide().fadeIn(500).delay(2500).fadeOut(500, function () {
            $(this).remove()
        })
    }

    function triggerGenericPopover(selector, element, text) {
        var maximizedClass = "";
        var parentPopupContainer = $(element).parents(".jchat_tabpopup.jchat_tabopen");
        if ($(parentPopupContainer).hasClass("maximized") && !selector.match(/jchat_contact/i)) {
            maximizedClass = " maximized"
        }
        var isMobile = jchatHasTouch();
        if ((isMobile || maximizedClass) && selector.match(/jchat_trigger_send_tooltip/i)) {
            return
        }
        var leftKonstant = 0;
        var topKonstant = 0;
        var customClass = "";
        var isAValidPopup = $(element).parents("div[id^=jchat_user_]");
        if (isAValidPopup.length && !$(element).hasClass("jchat_webrtc_disabled")) {
            popupTooltipDependency = isAValidPopup
        }
        if (!text) {
            text = $(element).data("text")
        }
        if (!text) {
            return
        }
        if (selector.match(/jchat_room/i)) {
            leftKonstant = 25;
            text = text.replace(/\r?\n/ig, "<br/>")
        }
        if (selector.match(/webrtc/i) && webrtcDirector.callee && !($(element).prev(".jchat_webrtctooltip")).length) {
            if ($(element).hasClass("jchat_webrtc_disabled")) {
                return
            }
            text = jchat_trigger_webrtc_ringing;
            customClass = " jchat_tooltip_webrtc"
        }
        $(selector).remove();
        $("body").append('<div id="' + selector.replace("#", "") + '" class="jchat_generic_tooltip' + maximizedClass + customClass + '"><div class="jchat_tooltip_content">' + text + "</div></div>");
        var ab = $(element).offset();
        var Y = $(element).width();
        var elementWidth = $(selector).width();
        if (maximizedClass) {
            elementWidth = 24;
            if (selector.match(/jchat_skype/i)) {
                leftKonstant = 175;
                topKonstant = -75
            }
        }
        $(selector).css("left", (ab.left + Y) - $(window).scrollLeft() - elementWidth - leftKonstant + 8);
        $(selector).css("top", (ab.top - parseInt($(selector).height()) - topKonstant - 6) - $(window).scrollTop() + "px");
        $(selector).fadeIn(200);
        if (isMobile) {
            setTimeout(function () {
                $(selector).fadeOut(200)
            }, 1000)
        }
    }

    function bindTooltipEvents(selectors, identifier, completeObject) {
        $(selectors, "#jchat_user_" + identifier + "_popup").toggle(function (event) {
            if (!!$('.jchat_tabcontent div[class$="tooltip"]', "#jchat_user_" + identifier + "_popup").length) {
                $('.jchat_tabcontent div[class$="tooltip"]', "#jchat_user_" + identifier + "_popup").filter(function (index) {
                    return $(this).css("display") !== "none"
                }).next().trigger("click")
            }
            var targetElementClass = $(this).attr("class").split(" ").shift();
            switch (targetElementClass) {
                case"jchat_trigger_history":
                    historyTooltip(this, "#jchat_user_" + identifier + "_popup", identifier, completeObject.loggedid);
                    break;
                case"jchat_trigger_geolocation":
                    geolocationTooltip(this, "#jchat_user_" + identifier + "_popup", identifier, completeObject);
                    break
            }
            $(this).addClass("toggle_on")
        }, function (event) {
            var targetElementClass = $(this).attr("class").split(" ").shift();
            switch (targetElementClass) {
                case"jchat_trigger_history":
                    $(".jchat_historytooltip", "#jchat_user_" + identifier + "_popup").remove();
                    break;
                case"jchat_trigger_geolocation":
                    $(".jchat_geolocationtooltip", "#jchat_user_" + identifier + "_popup").hide();
                    break
            }
            $(this).removeClass("toggle_on")
        })
    }

    function wallTooltipUsers(element) {
        var concatenatedUsers = null;
        currentScopedUsers = {};
        switch (groupChatMode) {
            case"global":
                concatenatedUsers = jchat_groupchat_allusers;
                currentScopedUsers[0] = jchat_groupchat_allusers;
                break;
            case"chatroom":
                concatenatedUsers = jchat_groupchat_nousers;
                currentScopedUsers[0] = jchat_groupchat_nousers;
                if (myChatRoomUsers.length || currentJoinedChatRoom) {
                    concatenatedUsers = my_username + ", ";
                    currentScopedUsers[0] = my_username;
                    for (var i = 0; i < myChatRoomUsers.length; i++) {
                        concatenatedUsers += myChatRoomUsers[i].name + ", ";
                        currentScopedUsers[myChatRoomUsers[i].sessionid] = myChatRoomUsers[i].name;
                        if (((i + 1) % 3) == 0 && (i + 1) < myChatRoomUsers.length) {
                            concatenatedUsers += "<br/>"
                        }
                    }
                    concatenatedUsers = concatenatedUsers.slice(0, -2)
                } else {
                    concatenatedUsers = my_username + ", ";
                    currentScopedUsers[0] = my_username;
                    if (!$.isEmptyObject(buddyList)) {
                        var counter = 0;
                        $.each(buddyList, function (index, user) {
                            if (!user.hasroomid) {
                                concatenatedUsers += user.name + ", ";
                                currentScopedUsers[index] = user.name;
                                if (((counter + 1) % 3) == 0) {
                                    concatenatedUsers += "<br/>"
                                }
                                counter++
                            }
                        })
                    }
                    concatenatedUsers = concatenatedUsers.replace(/<br\/>$/gi, "");
                    concatenatedUsers = concatenatedUsers.slice(0, -2)
                }
                break;
            case"invite":
                var usersOfGroupChat = $("div[id^=jchat_userlist] span.jchat_contact[data-contact=1]");
                if (usersOfGroupChat.length) {
                    concatenatedUsers = my_username + "<br/>";
                    currentScopedUsers[0] = my_username;
                    for (var i = 0; i < usersOfGroupChat.length; i++) {
                        var userContactName = $(usersOfGroupChat[i]).attr("data-name");
                        var userContactSessionid = $(usersOfGroupChat[i]).attr("data-userid");
                        concatenatedUsers += userContactName + "<br/>";
                        currentScopedUsers[userContactSessionid] = userContactName
                    }
                } else {
                    concatenatedUsers = jchat_groupchat_nousers;
                    currentScopedUsers[0] = jchat_groupchat_nousers
                }
                break
        }
        var maximizedClass = "";
        var parentPopupContainer = $(element).parents(".jchat_tabpopup.jchat_tabopen");
        if ($(parentPopupContainer).hasClass("maximized")) {
            maximizedClass = " maximized"
        }
        $("#jchat_users_informations_tooltip").remove();
        $("body").append('<div id="jchat_users_informations_tooltip" class="jchat_generic_tooltip' + maximizedClass + '"><div class="jchat_tooltip_content">' + concatenatedUsers + "</div></div>");
        var ab = $(element).offset();
        var Y = $(element).width();
        var elementWidth = $("#jchat_users_informations_tooltip").width();
        if (maximizedClass) {
            elementWidth = 24
        }
        $("#jchat_users_informations_tooltip").css({
            left: (ab.left + Y) - $(window).scrollLeft() - elementWidth + 12,
            top: (ab.top - parseInt($("#jchat_users_informations_tooltip").height()) - 5) - $(window).scrollTop() + "px"
        });
        $("#jchat_users_informations_tooltip").fadeIn(200)
    }

    function hoverSidebarTooltips(element, text) {
        if ($("#jchat_tooltip").length > 0) {
            $("#jchat_tooltip .jchat_tooltip_content").html(text)
        } else {
            $(jchatTargetElement).append('<div id="jchat_tooltip"><div class="jchat_tooltip_content">' + text + "</div></div>")
        }
        var ab = $(element).offset();
        var Y = $(element).width();
        var mixed = $("#jchat_tooltip").width();
        $("#jchat_tooltip").css({
            top: ab.top - 28 - $(window).scrollTop(),
            left: (ab.left + Y) - $(window).scrollLeft() - mixed + 12
        });
        $("#jchat_tooltip").fadeIn(200);
        if (avatarUploadEnabled !== true || (isGuest && !allowGuestAvatarupload)) {
            $("#jchat_avatar").remove()
        }
        if (!skypeEnabled || (isGuest && !allowGuestSkypeBridge)) {
            $("div.jchat_skype").nextAll().remove().end().remove()
        }
        if (!isGuest || !allowGuestOverrideName) {
            $("div.jchat_override_name").next().remove().end().remove()
        }
    }

    function avatarTooltip(element, userName, messageTime) {
        var selfMessageClass = "";
        var parentImgContainer = $(element).parents("div.jchat_chatboxmessage");
        if (!parentImgContainer.hasClass("selfmessage")) {
            selfMessageClass = "selfmessage"
        }
        if (typeof(userName) === "undefined") {
            userName = ""
        }
        $(element).parent().append('<div id="jchat_avatartooltip" class="' + selfMessageClass + '"><div class="jchat_tooltip_content"></div></div>');
        var formattedTime = "";
        if (messageTime !== undefined) {
            formattedTime = '<br/><span class="jchat_time_details">' + messageTime + "</span>"
        }
        $("#jchat_avatartooltip .jchat_tooltip_content").html(userName + formattedTime);
        var ab = $(element).offset();
        var Y = $(element).width();
        var windowScroll = jchatGetPageScroll();
        var tooltipWidth = $("#jchat_avatartooltip").width();
        if (selfMessageClass) {
            tooltipWidth = -35
        }
        $("#jchat_avatartooltip").css({
            top: ab.top - parseInt(windowScroll[1]),
            left: (ab.left + Y) - $(window).scrollLeft() - tooltipWidth - 35,
            "z-index": 10002
        });
        $("#jchat_avatartooltip").fadeIn(200)
    }

    function avatarUploadTooltip(element) {
        $(element).before('<div class="jchat_avatar_upload_tooltip"></div>').before('<div class="jchat_overlay"></div>');
        $("div.jchat_overlay").fadeIn(300);
        $(".jchat_avatar_upload_tooltip").append('<div class="jchat_tooltip_header jchat_tooltip_avatar_upload_header">' + jchat_manage_avatars + "</div>");
        $(".jchat_avatar_upload_tooltip").click(function () {
            $(element).trigger("click");
            $("div.jchat_overlay").remove()
        });
        $(".jchat_avatar_upload_tooltip").append("<img/>").children("img").attr("src", jchat_livesite + "components/com_jchat/images/loading.gif").css({
            position: "absolute",
            margin: "25% 38%",
            width: "64px"
        });
        $(".jchat_avatar_upload_tooltip").append('<iframe id="avatarUpload_iframe" scrolling="no" src="' + htmlLiveSite + '&task=avatar.display"></iframe>');
        $("#avatarUpload_iframe").on("load", function () {
            setTimeout(function () {
                $(".jchat_avatar_upload_tooltip img").remove()
            }, 1)
        });
        $(".jchat_avatar_upload_tooltip").show().css("z-index", 10002)
    }

    function chatroomAdderTooltip(element) {
        $(element).parents("#jchat_roomstooltip").append('<div class="jchat_chatroom_adder_tooltip"></div><div class="jchat_overlay"></div>');
        $("div.jchat_overlay").fadeIn(300);
        $(".jchat_chatroom_adder_tooltip").append('<div class="jchat_tooltip_header jchat_tooltip_chatroom_adder_header">' + jchat_addnew_chatroom + "</div>");
        $(".jchat_tooltip_chatroom_adder_header").click(function () {
            $("div.jchat_overlay, div.jchat_chatroom_adder_tooltip").remove()
        });
        $(".jchat_chatroom_adder_tooltip").append('<div id="chatroom_adder"></div>');
        var formContainer = $("<form/>").attr("name", "chatroomform").attr("id", "chatroomform").attr("class", "ajaxform");
        $("#chatroom_adder").append(formContainer);
        var validationLabel = $("<label/>").text(jchat_lamform_required).attr("class", "jchat_label_validate");
        var nameLabel = $("<label/>").text(jchat_chatroomform_name).attr("class", "jchat_label_title");
        var nameInput = $("<input/>").attr("name", "chatroom_name").attr("data-validation", "required");
        $(formContainer).append(nameLabel).append($(validationLabel.clone(true))).append(nameInput);
        var descriptionLabel = $("<label/>").text(jchat_chatroomform_description).attr("class", "jchat_label_title");
        var descriptionArea = $("<textarea/>").attr("name", "chatroom_description");
        $(formContainer).append(descriptionLabel).append(descriptionArea);
        var accessLabel = $("<label/>").text(jchat_chatroomform_accesslevel).attr("class", "jchat_label_title");
        var accessArea = $("<select/>").attr({id: "chatroom_access", name: "chatroom_access"});
        $.each(joomlaAccessLevels, function (index, level) {
            if ($.inArray(parseInt(level.value), userAccessLevels) == -1) {
                return true
            }
            var selected = parseInt(level.value) == 1 ? 'selected="selected"' : "";
            $(accessArea).append("<option " + selected + ' value="' + level.value + '">' + level.text + "</option>")
        });
        $(formContainer).append(accessLabel).append($(validationLabel.clone(true))).append(accessArea);
        var submitDivButton = $("<div/>").text(jchat_chatroomform_submit).attr("class", "jchat_submit_chatroom_form");
        $(formContainer).append(submitDivButton);
        $(submitDivButton).on("click", function (event) {
            var chatRoomName = $("input[name=chatroom_name]", formContainer).val();
            var chatRoomDescription = $("textarea[name=chatroom_description]", formContainer).val();
            var chatRoomAccessLevel = $("#chatroom_access", formContainer).val();
            var validForm = jchatValidateForm("#chatroomform *[data-validation^=required]");
            if (!validForm) {
                return
            }
            $("#chatroom_adder").append("<img/>").children("img").attr("src", jchat_livesite + "components/com_jchat/images/loading.gif").css({
                position: "absolute",
                top: "10%",
                left: "38%",
                width: "64px"
            }).addClass("waiter");
            var ajaxparams = {
                newroom_name: chatRoomName,
                newroom_description: chatRoomDescription,
                newroom_access: chatRoomAccessLevel,
                task: "stream.saveEntity"
            };
            $.ajax({
                type: "post",
                url: jsonLiveSite,
                dataType: "json",
                context: this,
                data: ajaxparams,
            }).done(function (response) {
                if (response.storing.status) {
                    injectFloatingMsg(jchat_success_storing_chatroom);
                    var formItem = $(formContainer).get(0);
                    $.each(formItem, function (k, elem) {
                        $(elem).val("")
                    });
                    $("div.jchat_noroom").remove();
                    var chatroomSnippet = $("<div/>").attr("class", "jchat_room");
                    var chatroomDeleteSnippet = (deleteChatrooms && allowChatroomsDeletion) ? '<span data-roomid="' + response.storing.chatroomid + '" class="jchat_roomdelete"></span>' : "";
                    chatRoomName = chatRoomName.length < chatRoomLength ? chatRoomName : chatRoomName.substr(0, chatRoomLength) + "...";
                    chatroomSnippet.html('<div class="jchat_roomleft"><div class="jchat_roomname" data-text="' + chatRoomDescription + '">' + chatRoomName + chatroomDeleteSnippet + '</div><div class="jchat_roomcount" data-roomcountid="' + response.storing.chatroomid + '">' + jchat_chatroom_users + '0</div></div><div class="jchat_roomright"><div class="jchat_roomjoin" data-roomid="' + response.storing.chatroomid + '" data-name="' + chatRoomName + '">' + jchat_chatroom_join + "</div></div>");
                    $("div.jchat_roomslist").append(chatroomSnippet).scrollTop($("div.jchat_roomslist")[0].scrollHeight);
                    $("div.jchat_roomjoin").on("click", chatRoomsJoiner);
                    $("span.jchat_roomdelete[data-roomid=" + response.storing.chatroomid + "]").on("click", chatRoomsDeleter);
                    $(".jchat_tooltip_chatroom_adder_header").trigger("click")
                } else {
                    injectFloatingMsg(response.storing.details)
                }
            }).always(function () {
                $("#chatroom_adder img.waiter").remove()
            })
        })
    }

    function emoticonsTooltip(element, context, identifier, wall) {
        var dataWall = "";
        var dataUserID = "";
        if (wall) {
            dataWall = ' data-wall="1" '
        } else {
            dataUserID = ' data-userid="' + identifier + '" '
        }
        var maximized = false;
        var parentPopupContainer = $(element).parents(".jchat_tabpopup.jchat_tabopen");
        if ($(parentPopupContainer).hasClass("maximized") || $(window).width() <= 360) {
            maximized = true
        }
        $(element).before("<div" + dataUserID + dataWall + ' class="jchat_emoticonstooltip"></div>');
        var emoticonsIcons = new Array();
        var indexCounter = 0;
        $.each(window.jchat_emoticons, function (index, value) {
            if ((index > 0) && (index % 10) == 0) {
                indexCounter++
            }
            if (emoticonsIcons[indexCounter] === undefined) {
                emoticonsIcons[indexCounter] = ""
            }
            emoticonsIcons[indexCounter] += '<img class="jchat_emoticons" title="' + value.keycode + '" src="' + jchat_livesite + value.path + '"/>'
        });
        $(".jchat_tooltip_header", context).click(function () {
            $(element).trigger("click")
        });
        $.each(emoticonsIcons, function (k, elements) {
            $(".jchat_emoticonstooltip", context).append('<div class="jchat_tooltip_content">' + elements + "</div>")
        });
        var ab = $(element).offset();
        var triggerWidth = $(element).innerWidth();
        var windowScroll = jchatGetPageScroll();
        var elementWidth = 216;
        if (maximized) {
            elementWidth = triggerWidth
        }
        $(".jchat_emoticonstooltip", context).css("top", ab.top - parseInt(windowScroll[1]) - 134).css("left", ab.left - $(window).scrollLeft() - elementWidth + triggerWidth).css("display", "block").css("z-index", 10002);
        $(".jchat_emoticonstooltip img", context).click(function (event) {
            var range, selection;
            var targetContentEditable = $(this).parent().parent().prevAll(".jchat_tabcontentinput").children("div");
            var nativeElement = $(targetContentEditable).get(0);
            if ($.support.leadingWhitespace && isContentEditable) {
                var clonedEmoticon = $(this).clone();
                $("br:last-child", targetContentEditable).remove();
                $(targetContentEditable).append(clonedEmoticon)
            } else {
                if (isContentEditable) {
                    var fromTitle = " " + $(this).attr("title");
                    $(targetContentEditable).append(fromTitle)
                } else {
                    var fromTitle = " " + $(this).attr("title");
                    targetContentEditable = $(this).parent().parent().prev().children("textarea");
                    $(targetContentEditable).val($(targetContentEditable).val() + fromTitle)
                }
            }
            if (document.createRange) {
                range = document.createRange();
                range.selectNodeContents(nativeElement);
                range.collapse(false);
                selection = window.getSelection();
                selection.removeAllRanges();
                if (!jchatHasTouch()) {
                    selection.addRange(range)
                }
            } else {
                if (document.selection) {
                    range = document.body.createTextRange();
                    range.moveToElementText(nativeElement);
                    range.collapse(false);
                    if (!jchatHasTouch()) {
                        range.select()
                    }
                }
            }
            if (!jchatHasTouch()) {
                targetContentEditable.focus()
            }
            if (jchatHasTouch() || autoClosePopups) {
                $(element).trigger("click")
            }
        });
        if (!jchatHasTouch()) {
            $("div.jchat_textarea").focus()
        }
    }

    function chatroomUsersInformationsTooltip(element, context, identifier, wall) {
        var ab = $(element).offset();
        var triggerWidth = $(element).width();
        var windowScroll = jchatGetPageScroll();
        var maximized = false;
        var parentPopupContainer = $(element).parents(".jchat_tabpopup.jchat_tabopen");
        if ($(parentPopupContainer).hasClass("maximized") || $(window).width() <= 360) {
            maximized = true
        }
        $(element).before('<div class="jchat_chatroom_usersinfo_tooltip"><span class="jchat_tab">' + jchat_users_inchatroom + '</span><div id="jchat_chatroom_userslist"><ul></ul></div></div>');
        var banButton = "";
        var banModeratorButton = "";
        var targetULElement = $("#jchat_chatroom_userslist ul");
        var validBanStatus = !!(usersBanning && (!isGuest || (isGuest && allowGuestBanning)) && usersBanningMode == "private_public");
        $.each(currentScopedUsers, function (identifier, username) {
            if (parseInt(identifier) != 0 && validBanStatus) {
                var currentBanStatus = !!parseInt($("div.jchat_userlist span.jchat_banning[data-userid=" + identifier + "]").data("banned"));
                var currentBanClass = currentBanStatus ? " banned" : "";
                banButton = '<span class="jchat_banning' + currentBanClass + '" data-userid="' + identifier + '"></span>'
            }
            if (parseInt(identifier) != 0 && enableModeration && userPermissions.moderation_groups) {
                banModeratorButton = '<span class="jchat_ban_moderator" data-userid="' + identifier + '"></span>'
            }
            var trimmedUsername = username.length < chatLength ? username : username.substr(0, chatLength) + "...";
            targetULElement.append('<li data-userid="' + identifier + '">' + trimmedUsername + banModeratorButton + banButton + "</li>");
            if (identifier) {
                $("#jchat_chatroom_userslist li").on("click", function (jqEvent) {
                    var userID = $(this).data("userid");
                    $("#jchat_userlist_" + userID).trigger("click")
                })
            }
        });
        $("#jchat_chatroom_userslist span.jchat_banning").on("click", function (jqEvent) {
            $(this).toggleClass("banned");
            var buddyListIdentifier = $(this).data("userid");
            $("span.jchat_banning[data-userid=" + buddyListIdentifier + "]").trigger("dblclick", [this]);
            return false
        });
        $("#jchat_chatroom_userslist span.jchat_ban_moderator").on("click", function (jqEvent) {
            $(this).toggleClass("banned");
            var buddyListIdentifier = $(this).data("userid");
            $("span.jchat_ban_moderator[data-userid=" + buddyListIdentifier + "]").trigger("dblclick");
            setTimeout(function (target) {
                $(target).parent("li").slideUp(250)
            }, 500, this);
            return false
        });
        var elementWidth = $(".jchat_chatroom_usersinfo_tooltip", context).outerWidth();
        if (maximized) {
            elementWidth = triggerWidth
        }
        $(".jchat_chatroom_usersinfo_tooltip", context).css("top", ab.top - parseInt(windowScroll[1]) - 152).css("left", ab.left - $(window).scrollLeft() - elementWidth + triggerWidth).css("display", "block").css("z-index", 10002)
    }

    function addChatRoomsUsersDetails(room, isMyJoinedRoom) {
        var maxOtherUsersPerLine = 3;
        if (renderingMode == "module") {
            maxOtherUsersPerLine = 2
        }
        if (showChatroomsUsersDetails) {
            var targetRoomName = $("div [data-roomcountid=" + room.id + "]");
            var concatenatedUsersInThisRoom = "";
            if (isMyJoinedRoom) {
                concatenatedUsersInThisRoom += my_username + ", "
            }
            if (room.users) {
                $.each(room.users, function (index, user) {
                    concatenatedUsersInThisRoom += user + ", ";
                    if (((index + 1) % maxOtherUsersPerLine) == 0 && (index + 1) < room.users.length) {
                        concatenatedUsersInThisRoom += "<br/>"
                    }
                })
            }
            targetRoomName.data("text", concatenatedUsersInThisRoom.slice(0, -2))
        }
    }

    function roomsTooltip(element, context, wall) {
        $(element).before('<div id="jchat_roomstooltip" class="jchat_roomstooltip"><span id="jchat_roomsdragger" class="jchat_tab"><span class="jchat_rooms_title">' + jchat_available_rooms + '</span></span><div class="jchat_roomslist"></div></div>');
        if (addChatrooms && allowChatroomsCreation) {
            $("#jchat_roomstooltip span.jchat_tab").append('<div class="jchat_chatrooms_adder"></div>')
        }
        $("#jchat_roomstooltip span.jchat_tab").append('<div class="jchat_closebox_bottom"></div>');
        $("#jchat_roomstooltip span.jchat_tab .jchat_closebox_bottom").on("click", function () {
            $(".jchat_trigger_room", "#jchat_wall_popup").trigger("click")
        });
        $("#jchat_roomstooltip div.jchat_chatrooms_adder").on("click", function () {
            chatroomAdderTooltip(this)
        });
        if (jchatHasTouch()) {
            $("#jchat_roomstooltip span.jchat_tab").css("background-position", "97% -851px")
        }
        if (!$.isArray(roomsAvailable)) {
            $.each(roomsAvailable, function (k, room) {
                var isMyJoinedRoom = !!(room.id == currentJoinedChatRoom);
                var deleteChatroomButton = "";
                if (deleteChatrooms && allowChatroomsDeletion) {
                    deleteChatroomButton = room.numusers == 0 ? '<span data-roomid="' + room.id + '" class="jchat_roomdelete"></span>' : ""
                }
                var roomStructure = $("<div/>").attr("class", "jchat_room");
                room.name = room.name.length < chatRoomLength ? room.name : room.name.substr(0, chatRoomLength) + "...";
                roomStructure.html('<div class="jchat_roomleft"><div class="jchat_roomname" data-text="' + room.description + '">' + room.name + deleteChatroomButton + '</div><div class="jchat_roomcount" data-roomcountid="' + room.id + '">' + jchat_chatroom_users + room.numusers + '</div></div><div class="jchat_roomright"><div class="jchat_roomjoin" data-roomid="' + room.id + '" data-name="' + room.name + '">' + jchat_chatroom_join + "</div></div>");
                $(".jchat_roomslist", context).append(roomStructure);
                if (isMyJoinedRoom) {
                    $("div[data-roomid=" + currentJoinedChatRoom + "]").addClass("jchat_joined").data("joined", true).text(jchat_chatroom_joined)
                }
                addChatRoomsUsersDetails(room, isMyJoinedRoom)
            });
            $("div.jchat_roomjoin").on("click", chatRoomsJoiner);
            $("span.jchat_roomdelete").on("click", chatRoomsDeleter)
        } else {
            var roomStructure = $("<div/>").attr("class", "jchat_room jchat_noroom");
            roomStructure.html('<div class="jchat_roomname">' + jchat_noavailable_rooms + "</div>");
            $(".jchat_roomslist", context).append(roomStructure)
        }
        var maximized = false;
        var parentPopupContainer = $(element).parents(".jchat_tabpopup.jchat_tabopen");
        if ($(parentPopupContainer).hasClass("maximized") || $(window).width() <= 360) {
            maximized = true
        }
        var ab = $(element).offset();
        var triggerWidth = $(element).innerWidth();
        var windowScroll = jchatGetPageScroll();
        var elementWidth = $(".jchat_roomstooltip", context).width() + 2;
        if (maximized) {
            elementWidth = triggerWidth
        }
        $(".jchat_roomstooltip", context).css("top", ab.top - parseInt(windowScroll[1]) - 232).css("left", ab.left - $(window).scrollLeft() - elementWidth + triggerWidth).css("display", "block").css("z-index", 10002);
        $("#jchat_roomstooltip").draggable({
            handle: "#jchat_roomsdragger", start: function (event, ui) {
                $(this).addClass("dragging")
            }, stop: function (event, ui) {
                $(this).removeClass("dragging")
            }
        });
        jchatInitTouchEvents($("#jchat_roomsdragger").get(0))
    }

    function chatRoomsJoiner(event) {
        var joinedRoomId = $(this).data("roomid");
        var currentState = $(this).data("joined");
        if (!currentState) {
            $(".jchat_roomjoin").removeClass("jchat_joined").data("joined", false).text(jchat_chatroom_join);
            $(".jchat_userstabtitle", "#jchat_wall_popup").next(".jchat_userstabsubtitle").remove();
            $(this).addClass("jchat_joined").data("joined", true);
            $(this).text(jchat_chatroom_joined);
            var chatRoomName = $(this).data("name");
            currentJoinedChatRoom = joinedRoomId;
            var subTitle = $("<div/>").addClass("jchat_userstabsubtitle").html('<span class="jchat_roomtitle">' + jchat_chatroom + "</span> " + chatRoomName);
            $(".jchat_userstabtitle", "#jchat_wall_popup").after(subTitle);
            $("#jchat_wall_popup .jchat_userstabsubtitle").width($("#jchat_wall_popup").width() - 7);
            $("span.jchat_roomdelete[data-roomid=" + joinedRoomId + "]").remove()
        } else {
            $(this).removeClass("jchat_joined").data("joined", false);
            $(this).text(jchat_chatroom_join);
            $(".jchat_userstabtitle", "#jchat_wall_popup").next(".jchat_userstabsubtitle").remove();
            currentJoinedChatRoom = null;
            joinedRoomId = null
        }
        if (autoClearConversation) {
            $("div.jchat_chatboxmessage", "#jchat_wall_popup").remove()
        }
        if (jchatHasTouch() || autoClosePopups) {
            setTimeout(function () {
                $(".jchat_trigger_room", "#jchat_wall_popup").trigger("click")
            }, 500)
        }
        $.post(jsonLiveSite, {roomid: joinedRoomId, task: "stream.saveEntity"}, function (response) {
            if (joinedRoomId && chatRoomLatestMessages) {
                restartUpdateSession("buddylist", "1")
            }
            if (wallHistoryDelayAutoload) {
                $("#jchat_wall_popup div.jchat_trigger_history_wall").trigger("click", [true]);
                window.sessionStorage.removeItem("wall_autoload_history");
                if (!joinedRoomId) {
                    window.sessionStorage.setItem("wall_autoload_history", 1)
                }
            }
            if (!response.storing.status) {
                showDebugMsgs(response.storing.details)
            }
        })
    }

    function chatRoomAutoJoiner(defaultRoomID) {
        var chatRoomName = null;
        $.each(roomsAvailable, function (k, room) {
            if (defaultRoomID == room.id) {
                chatRoomName = room.name;
                return false
            }
        });
        if (!chatRoomName) {
            return
        }
        var subTitle = $("<div/>").addClass("jchat_userstabsubtitle").html('<span class="jchat_roomtitle">' + jchat_chatroom + "</span> " + chatRoomName);
        $(".jchat_userstabtitle", "#jchat_wall_popup").after(subTitle);
        $("#jchat_wall_popup .jchat_userstabsubtitle").width($("#jchat_wall_popup").width() - 7);
        if (autoClearConversation) {
            $("div.jchat_chatboxmessage", "#jchat_wall_popup").remove()
        }
        currentJoinedChatRoom = defaultRoomID;
        setTimeout(function () {
            $.post(jsonLiveSite, {
                roomid: defaultRoomID,
                silentJoin: true,
                task: "stream.saveEntity"
            }, function (response) {
                if (chatRoomLatestMessages) {
                    restartUpdateSession("buddylist", "1")
                }
                if (wallHistoryDelayAutoload) {
                    $("#jchat_wall_popup div.jchat_trigger_history_wall").trigger("click", [true]);
                    window.sessionStorage.removeItem("wall_autoload_history")
                }
                if (!response.storing.status) {
                    showDebugMsgs(response.storing.details)
                }
            })
        }, 500)
    }

    function chatRoomsDeleter() {
        var chatRoomID = parseInt($(this).data("roomid"));
        $.post(jsonLiveSite, {todeleteroomid: chatRoomID, task: "stream.saveEntity"}, function (response) {
            if (response.storing.status) {
                $("div[data-roomid=" + chatRoomID + "]").parents("div.jchat_room").slideUp().promise().done(function () {
                    $(this).remove();
                    if (!$("div.jchat_roomslist div.jchat_room").length) {
                        var roomStructure = $("<div/>").attr("class", "jchat_room jchat_noroom");
                        roomStructure.html('<div class="jchat_roomname">' + jchat_noavailable_rooms + "</div>");
                        $("div.jchat_roomslist").append(roomStructure)
                    }
                });
                injectFloatingMsg(jchat_success_deleting_chatroom)
            } else {
                showDebugMsgs(response.storing.details)
            }
        })
    }

    function uploadFileTooltip(element, context, to, tologged) {
        var maximized = false;
        var parentPopupContainer = $(element).parents(".jchat_tabpopup.jchat_tabopen");
        if ($(parentPopupContainer).hasClass("maximized") || $(window).width() <= 360) {
            maximized = true
        }
        $(element).before('<div data-userid="' + to + '" class="jchat_fileuploadtooltip"></div>');
        $(".jchat_fileuploadtooltip", context).append("<img/>").children("img").attr("src", jchat_livesite + "components/com_jchat/images/loading.gif").css({
            position: "absolute",
            margin: "10px 46%",
            width: "32px"
        });
        $(".jchat_fileuploadtooltip", context).append('<iframe id="fileupload_iframe"  scrolling="no" src="' + htmlLiveSite + "&task=attachments.display&to=" + to + "&tologged=" + tologged + '"></iframe>');
        $("#fileupload_iframe", context).on("load", function () {
            setTimeout(function () {
                $(".jchat_fileuploadtooltip img", context).remove()
            }, 1)
        });
        var ab = $(element).offset();
        var triggerWidth = $(element).innerWidth();
        var windowScroll = jchatGetPageScroll();
        var elementWidth = $(".jchat_fileuploadtooltip", context).width() + 2;
        if (maximized) {
            elementWidth = triggerWidth
        }
        $(".jchat_fileuploadtooltip", context).css("top", ab.top - parseInt(windowScroll[1]) - 59).css("left", ab.left - $(window).scrollLeft() - elementWidth + triggerWidth).css("display", "block").css("z-index", 10002);
        if (!jchatHasTouch()) {
            $("div.jchat_textarea").focus()
        }
    }

    function webrtcTooltip(element, context, otherPeerID, otherPeerName, thisPeerName, otherPeerFullName) {
        if ($(element).hasClass("jchat_webrtc_disabled")) {
            return
        }
        $(".jchat_trigger_webrtc", context).trigger("mouseout");
        var topHeightKonstant = 300;
        var maximized = false;
        var parentPopupContainer = $(element).parents(".jchat_tabpopup.jchat_tabopen");
        if ($(parentPopupContainer).hasClass("maximized")) {
            maximized = true
        }
        var classRecording = "";
        if (recordingEnabled && userPermissions.allow_media_recorder) {
            var classRecording = " jchat_large_recording";
            topHeightKonstant += 50
        }
        $(element).before('<div dir="ltr" data-peerid="' + otherPeerID + '" class="jchat_webrtctooltip' + classRecording + '"><span id="jchat_webrtcdragger" class="jchat_tab">' + jchat_webrtc_videochat + '<div class="jchat_closebox_bottom"></div><span id="jchat_maximize_webrtc" class="jchat_maximize_webrtc"></span></span></div>');
        $(".jchat_webrtctooltip span.jchat_tab .jchat_closebox_bottom").on("click", function () {
            $(".jchat_trigger_webrtc", "#jchat_user_" + otherPeerID + "_popup").trigger("click")
        });
        if (jchatHasTouch()) {
            $(".jchat_webrtctooltip  span.jchat_tab").css("background-position", "90% -851px")
        }
        var webRTCSupported = webrtcDirector.initializeVideo($(".jchat_webrtctooltip", context), otherPeerID, otherPeerName, thisPeerName);
        if (webRTCSupported && webrtcDirector.callee) {
            webrtcDirector.setCalleeRingingButton()
        }
        if (webRTCSupported && recorderDirector && recordingEnabled && userPermissions.allow_media_recorder) {
            recorderDirector.initializeVideo($(".jchat_webrtctooltip", context), otherPeerFullName, my_username)
        }
        var ab = $(element).offset();
        var triggerWidth = $(element).innerWidth();
        var windowScroll = jchatGetPageScroll();
        var elementWidth = $(".jchat_webrtctooltip", context).width() + 12;
        if (maximized) {
            elementWidth = triggerWidth
        }
        $(".jchat_webrtctooltip", context).css("top", ab.top - parseInt(windowScroll[1]) - topHeightKonstant).css("left", ab.left - $(window).scrollLeft() - elementWidth + triggerWidth).css("display", "block").css("z-index", 10002);
        refreshStream();
        $("div[data-peerid=" + otherPeerID + "] .jchat_maximize_webrtc").on("hover", function () {
            $(this).toggleClass("jchat_maximizebox_bottomhover")
        });
        $("div[data-peerid=" + otherPeerID + "] .jchat_maximize_webrtc").toggle(function (jqEvent) {
            var container = $(this).parents("div[data-peerid=" + otherPeerID + "]");
            var viewportHeight = (window.innerHeight ? window.innerHeight : $(window).height()) - 25;
            $(container).animate({width: "100%", height: viewportHeight, top: "26px", left: 0}, {
                duration: 300,
                easing: "swing",
                start: function () {
                    $(this).addClass("fullscreen")
                },
                progress: function (jqEvent) {
                    $("#jchat_localvideo_placeholder, #jchat_remotevideo_placeholder").height($("#jchat_localvideo").height())
                },
                complete: function () {
                    $("#jchat_webrtcdragger").css({width: "100%"})
                }
            });
            $(this).addClass("maximized");
            var setDimensions = function () {
                var viewportHeight = (window.innerHeight ? window.innerHeight : $(window).height()) - 42;
                $(".jchat_webrtctooltip", "#jchat_user_" + otherPeerID + "_popup").height(viewportHeight)
            };
            $(window).on("resize.jchat", function (jqEvent) {
                setDimensions()
            });
            jqEvent.stopPropagation();
            return false
        }, function (jqEvent) {
            var container = $(this).parents("div[data-peerid=" + otherPeerID + "]");
            $(container).animate({
                width: "520px",
                height: topHeightKonstant + "px",
                top: "50%",
                left: "50%",
                "margin-left": "-260px",
                "margin-top": "-150px"
            }, {
                duration: 300, easing: "swing", start: function () {
                    $("#jchat_webrtcdragger").hide();
                    container.removeClass("fullscreen")
                }, progress: function (jqEvent) {
                    $("#jchat_localvideo_placeholder, #jchat_remotevideo_placeholder").height($("#jchat_localvideo").height())
                }, complete: function () {
                    $("#jchat_webrtcdragger").css({width: "520px"});
                    $("#jchat_webrtcdragger").show();
                    $(this).css("margin", 0);
                    popupsReposition(true)
                }
            });
            $(this).removeClass("maximized");
            $("#jchat_localvideo_placeholder, #jchat_remotevideo_placeholder").height($("#jchat_localvideo").height());
            $(window).off("resize.jchat");
            jqEvent.stopPropagation();
            return false
        });
        $(".jchat_webrtctooltip", "#jchat_user_" + otherPeerID + "_popup").draggable({
            handle: "#jchat_webrtcdragger",
            start: function (event, ui) {
                $(this).addClass("dragging")
            },
            stop: function (event, ui) {
                $(this).removeClass("dragging")
            }
        });
        if (jchatDetectMobileDevice(videochatAutoMaximize)) {
            $(".jchat_webrtctooltip #jchat_maximize_webrtc", "#jchat_user_" + otherPeerID + "_popup").trigger("click")
        }
        if (resizableChatboxes && !jchatHasTouch()) {
            $(".jchat_webrtctooltip", "#jchat_user_" + otherPeerID + "_popup").resizable({
                handles: "se",
                minHeight: 300,
                minWidth: 520,
                aspectRatio: true,
                start: function (event, ui) {
                },
                stop: function (event, ui) {
                    var currentWidth = parseInt($(this).innerWidth() + 2);
                    var currentHeight = parseInt($(this).innerHeight() + 2);
                    if (currentWidth == 520 && currentHeight == 300) {
                        $(this).removeClass("fullscreen")
                    }
                },
                resize: function (event, ui) {
                    var currentWidth = parseInt($(this).innerWidth() + 2);
                    var currentHeight = parseInt($(this).innerHeight() + 2);
                    if (currentWidth > 520 && currentHeight > 300) {
                        $(this).addClass("fullscreen")
                    }
                    $("#jchat_localvideo_placeholder, #jchat_remotevideo_placeholder").height($("#jchat_localvideo").height());
                    $("#jchat_webrtcdragger", "#jchat_user_" + otherPeerID + "_popup").innerWidth(currentWidth - 2)
                }
            })
        }
        jchatInitTouchEvents($("#jchat_webrtcdragger").get(0))
    }

    function infoGuestTooltip(element, context, identifier) {
        $(element).before('<div data-userid="' + identifier + '" class="jchat_infoguesttooltip"></div>');
        var infoCache = new Array();
        $.ajax({
            url: jsonLiveSite,
            data: {guestsession: identifier, task: "stream.showEntity"},
            type: "get",
            cache: false,
            dataType: "json",
            success: function (response) {
                if (response.status) {
                    $.each(response.details, function (title, info) {
                        title = title.charAt(0).toUpperCase() + title.slice(1) + ":";
                        info = info ? info : "-", $(".jchat_infoguesttooltip").append('<div class="jchat_infoguest_title">' + title + "</div>");
                        $(".jchat_infoguesttooltip").append('<div class="jchat_infoguest_value">' + info + "</div>")
                    })
                } else {
                    showDebugMsgs(response.details);
                    return false
                }
            }
        });
        var maximized = false;
        var parentPopupContainer = $(element).parents(".jchat_tabpopup.jchat_tabopen");
        if ($(parentPopupContainer).hasClass("maximized")) {
            maximized = true
        }
        var ab = $(element).offset();
        var triggerWidth = $(element).innerWidth();
        var windowScroll = jchatGetPageScroll();
        var elementWidth = $(".jchat_infoguesttooltip", context).width() + 2;
        if (maximized) {
            elementWidth = triggerWidth
        }
        $(".jchat_infoguesttooltip", context).css("top", ab.top - parseInt(windowScroll[1]) - 201).css("left", ab.left - $(window).scrollLeft() - elementWidth + triggerWidth).css("display", "block").css("z-index", 10002)
    }

    function historyTooltip(element, context, identifier, logged_identifier) {
        $(element).before('<div data-userid="' + identifier + '" data-loggedid="' + logged_identifier + '" class="jchat_historytooltip"></div>');
        var periodsAvailable = new Array("1d", "1w", "1m", "3m", "6m", "1y");
        $(".jchat_historytooltip", "#jchat_user_" + identifier + "_popup").append('<div id="jchat_select_period">' + jchat_select_period + "</div>");
        $.each(periodsAvailable, function (index, period) {
            $(".jchat_historytooltip", "#jchat_user_" + identifier + "_popup").append('<div data-value="' + period + '" class="jchat_single_period">' + eval("jchat_period_" + period) + "</div>")
        });
        $("div.jchat_single_period").on("click", function (jqEvent) {
            var loggedID = $(this).parent().data("loggedid");
            var period = $(this).data("value");
            var minMessageID = null;
            var messagesParent = $(this).parents("div.jchat_tabcontent.messagelist");
            var minMessageElementId = messagesParent.find("div.jchat_tabcontenttext.private div.jchat_chatboxmessage:first-child").attr("id");
            if (minMessageElementId) {
                minMessageID = minMessageElementId.split("_")[2]
            }
            var conversationSessionUserId = $(this).parent().data("userid");
            fetchHistoryMessages(loggedID, conversationSessionUserId, period, minMessageID);
            if (jchatHasTouch() || autoClosePopups) {
                $(element).trigger("click")
            }
        });
        var ab = $(element).offset();
        var Y = $(element).width();
        var windowScroll = jchatGetPageScroll();
        var mixed = $(".jchat_historytooltip", context).width();
        $(".jchat_historytooltip", context).css("top", ab.top - parseInt(windowScroll[1]) - 161).css("left", ab.left - $(window).scrollLeft() - mixed + 18).css("display", "block").css("z-index", 10002)
    }

    function geolocationTooltip(element, context, identifier, completeObject) {
        var ab = $(element).offset();
        var triggerWidth = $(element).width();
        var windowScroll = jchatGetPageScroll();
        var geolocationServices = {
            geoip: "https://geoip.nekudo.com/api/",
            ipinfo: "http://ipinfo.io/",
            freegeoip: "https://freegeoip.net/json/"
        };
        var geolocationServiceRestEndpoint = geolocationServices[geolocationService];
        var maximized = false;
        var parentPopupContainer = $(element).parents(".jchat_tabpopup.jchat_tabopen");
        if ($(parentPopupContainer).hasClass("maximized")) {
            maximized = true
        }
        if (!gMapReference[identifier]) {
            $(element).before('<div data-userid="' + identifier + '" class="jchat_geolocationtooltip"><div class="jchat_gmap" id="jchat_gmap_' + identifier + '"></div></div>');
            $.get(geolocationServiceRestEndpoint + completeObject.geoip, function (response) {
                if (typeof(response.location) !== "undefined") {
                    var lats = response.location.latitude;
                    var lngs = response.location.longitude
                } else {
                    if (typeof(response.loc) !== "undefined") {
                        var lats = response.loc.split(",")[0];
                        var lngs = response.loc.split(",")[1]
                    } else {
                        if (typeof(response.latitude) !== "undefined" && typeof(response.longitude) !== "undefined") {
                            var lats = response.latitude;
                            var lngs = response.longitude
                        } else {
                            var lats = 40.73061;
                            var lngs = -73.935242
                        }
                    }
                }
                gMapReference[identifier] = new GMaps({
                    div: "#jchat_gmap_" + identifier,
                    lat: lats,
                    lng: lngs,
                    zoom: 5,
                    zoomControl: true,
                    zoomControlOpt: {style: "SMALL", position: "TOP_LEFT"},
                    panControl: true,
                    streetViewControl: true,
                    mapTypeControl: true,
                    overviewMapControl: true
                });
                gMapReference[identifier].setCenter(lats, lngs);
                gMapReference[identifier].addMarker({lat: lats, lng: lngs})
            }, "jsonp")
        }
        var elementWidth = $(".jchat_geolocationtooltip", context).outerWidth();
        if (maximized) {
            elementWidth = triggerWidth
        }
        $(".jchat_geolocationtooltip", context).css("top", ab.top - parseInt(windowScroll[1]) - 302).css("left", ab.left - $(window).scrollLeft() - elementWidth + triggerWidth).css("display", "block").css("z-index", 10002)
    }

    function suggestionTooltip(alwaysVisible) {
        if ($("#jchat_default_suggestion_tooltip").length > 0) {
            $("#jchat_default_suggestion_tooltip .jchat_tooltip_content").html(defaultSuggestionText)
        } else {
            $("body").append('<div id="jchat_default_suggestion_tooltip" dir="ltr"><div id="jchat_tooltip_close"></div><div class="jchat_tooltip_content">' + defaultSuggestionText + '</div><div class="jchat_tooltip_pointer"></div></div>');
            if (chatTemplateTooltip == "arm") {
                $("#jchat_default_suggestion_tooltip").addClass("jchat_arm")
            }
            if (chatTemplateTooltipVariant == "skrilled") {
                $("#jchat_default_suggestion_tooltip, #jchat_default_suggestion_tooltip .jchat_tooltip_content, #jchat_tooltip_close").addClass("jchat_skrilled")
            }
        }
        var targetElement = $("#jchat_userstab");
        if (renderingMode == "module" && baloonPosition == "bottom" && !separateWidgets) {
            targetElement = $("#jchat_target")
        }
        var ab = targetElement.offset();
        var Y = targetElement.width() + 30;
        var windowScroll = jchatGetPageScroll();
        setTimeout(function () {
            ab = targetElement.offset();
            Y = targetElement.width() + 30;
            windowScroll = jchatGetPageScroll()
        }, 0);
        setTimeout(function () {
            if (!$("#jchat_base").length) {
                return
            }
            if ($("#jchat_base:visible").length) {
                $("#jchat_default_suggestion_tooltip").css("top", ab.top - parseInt(windowScroll[1]) - parseInt($("#jchat_default_suggestion_tooltip").height()) - 30).css("left", ab.left - $(window).scrollLeft() + 18)
            }
            if ((!$.jStorage.get("default_suggestion") && !($("#jchat_userstab").hasClass("jchat_tabclick"))) || alwaysVisible) {
                $("#jchat_default_suggestion_tooltip").show()
            }
        }, 500);
        if (alwaysVisible) {
            $("#jchat_default_suggestion_tooltip").css({
                left: "auto",
                top: "auto",
                bottom: "20px",
                right: "10px"
            }).attr("data-link", chatFormLink);
            $(document).off("click", "#jchat_default_suggestion_tooltip");
            $(document).on("click", "#jchat_default_suggestion_tooltip", function (jqEvent) {
                window.location.href = $(this).data("link")
            })
        }
    }

    function sendMessage(Z, textareaElement, userid, loggedid, notOpen) {
        if (Z.keyCode == 13 && Z.shiftKey == 0) {
            var message = eval("$(textareaElement)." + valFunction + "()");
            message = message.replace(/^\s+|\s+$/g, "");
            message = html_entity_decode(message, "ENT_QUOTE");
            message = message.replace(/<a\b[^>]*>|<\/a>/gi, "");
            var linksArray = message.match(/([^">=]http|[^">]https|^http|^https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/gi);
            if (linksArray !== null && linksArray.length > 0) {
                $.each(linksArray, function (index, link) {
                    message = message.replace(link, '<a target="_blank" href="' + link.trim() + '">' + link + "</a>")
                })
            }
            if (Z.which != "videomessage") {
                message = jchatStripTags(message, allowMediaObjects, true);
                message = jchatDetectImages(message);
                message = jchatDetectVideos(message);
                message = $.jchatEmoticons(message)
            }
            if (wordsBanning && wordsBanned && Z.which != "videomessage") {
                var words = wordsBanned.split(",");
                var cleanedWords = new Array();
                $.each(words, function (index, word) {
                    if ($.inArray(word, ["ass", "src", "title"]) >= 0) {
                        word = word + " "
                    }
                    cleanedWords.push(word)
                });
                message = message.replace(new RegExp(cleanedWords.join("|"), "g"), "*" + wordsBannedReplacement + "*")
            }
            eval("$(textareaElement)." + valFunction + '("")');
            if (!$(textareaElement).hasClass("jchat_noresize")) {
                $(textareaElement).css("height", "22px");
                $(textareaElement).css("overflow-y", "hidden")
            }
            var thisPopupHeight = popupDimensions[userid] ? popupDimensions[userid].height + 11 : defaultPopupHeight;
            if (resizableChatboxes && !jchatHasTouch()) {
                $("#jchat_user_" + userid + "_popup").resizable("enable")
            }
            $("#jchat_user_" + userid + "_popup .jchat_tabcontenttext").css("height", parseInt(thisPopupHeight - 11) + "px");
            if (!jchatHasTouch()) {
                $(textareaElement).focus()
            } else {
                $(textareaElement).blur();
                $("body").focus()
            }
            if (message != "" && message != "<br>") {
                $("span.jchat_messaging.msg_seen, div.jchat_agent_dummy_message").remove();
                $("span.msg_seen", "#jchat_user_" + userid + "_popup").remove();
                var postObject = {to: userid, tologged: loggedid, message: message, task: "stream.saveEntity"};
                var languageTranslationObject = {};
                if (languageTranslationEnabled) {
                    if (typeof(popupLanguages[userid]) !== "undefined") {
                        languageTranslationObject.sourcelang = popupLanguages[userid].sourcelang || defaultLanguage;
                        languageTranslationObject.targetlang = popupLanguages[userid].targetlang || defaultTranslateToLanguage;
                        languageTranslationObject.lang_switch_enabled = popupLanguages[userid].lang_switch_enabled || 0;
                        postObject = $.extend(postObject, languageTranslationObject);
                        if (translateSelfMessages && postObject.lang_switch_enabled && postObject.sourcelang != postObject.targetlang) {
                            message = '<img class="clessidra" src="' + jchat_livesite + 'components/com_jchat/images/default/loading.gif"/>' + message
                        }
                    }
                }
                if (asyncSendMessage == "before" && !notOpen) {
                    $(".jchat_chatboxmessage.dummy_typing", "#jchat_user_" + userid + "_popup").remove();
                    $("#jchat_userlist_" + userid).trigger("addmessage", [message, "1", "1", "temp", {time: ""}]);
                    if (userid) {
                        $("#jchat_user_" + userid + "_popup .jchat_tabcontenttext").scrollTop($("#jchat_user_" + userid + "_popup .jchat_tabcontenttext")[0].scrollHeight)
                    }
                }
                $.post(jsonLiveSite, postObject, function (response) {
                    if (response.storing.status) {
                        if (typeof(response.storing.translatedmessage) !== "undefined") {
                            message = response.storing.translatedmessage
                        }
                        $(".jchat_chatboxmessage.dummy_typing", "#jchat_user_" + userid + "_popup").remove();
                        $("#jchat_message_temp", "#jchat_user_" + userid + "_popup").remove();
                        var idmessaggio = response.storing.details.id;
                        var insertionTime = response.storing.details.time;
                        if (idmessaggio && !notOpen) {
                            $("#jchat_userlist_" + userid).trigger("addmessage", [message, "1", "1", idmessaggio, {time: insertionTime}]);
                            if (userid) {
                                $("#jchat_user_" + userid + "_popup .jchat_tabcontenttext").scrollTop($("#jchat_user_" + userid + "_popup .jchat_tabcontenttext")[0].scrollHeight)
                            }
                        }
                        sendAppendPrivateMessage(loggedid, idmessaggio, message, true)
                    } else {
                        showDebugMsgs(response.storing.details);
                        return false
                    }
                    refreshStream()
                }, "json");
                if (typingEnabled && timeoutID) {
                    clearTimeout(timeoutID);
                    timeoutID = null;
                    s.typing = 0;
                    s.typing_to = null;
                    b("updatesession", "1")
                }
            }
            return false
        } else {
            if (typingEnabled) {
                s.typing = 1;
                s.typing_to = userid;
                b("updatesession", "1");
                if (timeoutID) {
                    clearTimeout(timeoutID)
                }
                timeoutID = setTimeout(function () {
                    clearTimeout(timeoutID);
                    timeoutID = null;
                    s.typing = 0;
                    s.typing_to = null;
                    b("updatesession", "1")
                }, K / 2)
            }
        }
    }

    function sendWallMessage(Z, Y) {
        if (Z.keyCode == 13 && Z.shiftKey == 0) {
            var message = eval("$(Y)." + valFunction + "()");
            message = message.replace(/^\s+|\s+$/g, "");
            message = html_entity_decode(message, "ENT_QUOTE");
            message = message.replace(/<a\b[^>]*>|<\/a>/gi, "");
            var linksArray = message.match(/([^">=]http|[^">]https|^http|^https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/gi);
            if (linksArray !== null && linksArray.length > 0) {
                $.each(linksArray, function (index, link) {
                    message = message.replace(link, '<a target="_blank" href="' + link.trim() + '">' + link + "</a>")
                })
            }
            message = jchatStripTags(message, allowMediaObjects, true);
            message = jchatDetectImages(message);
            message = jchatDetectVideos(message);
            message = $.jchatEmoticons(message);
            if (wordsBanning && wordsBanned) {
                var words = wordsBanned.split(",");
                var cleanedWords = new Array();
                $.each(words, function (index, word) {
                    if ($.inArray(word, ["ass", "src", "title"]) >= 0) {
                        word = word + " "
                    }
                    cleanedWords.push(word)
                });
                message = message.replace(new RegExp(cleanedWords.join("|"), "g"), "*" + wordsBannedReplacement + "*")
            }
            var testomessaggio = message.replace(/&/gi, "&amp;");
            eval("$(Y)." + valFunction + '("")');
            $(Y).css("height", "22px");
            $(Y).css("overflow-y", "hidden");
            if (!jchatHasTouch()) {
                $(Y).focus()
            } else {
                $(Y).blur();
                $("body").focus()
            }
            popupsReposition();
            if (message != "" && message != "<br>") {
                if (asyncSendMessage == "before") {
                    if (my_avatar !== undefined && my_avatar !== null && my_avatar !== "") {
                        var tempPlaceholder = '<img width="32px" height="32px" src="' + my_avatar + '" />'
                    } else {
                        var tempPlaceholder = "<strong>" + my_username + "</strong>"
                    }
                    $("#jchat_wall_popup div.jchat_tabcontenttext").append('<div class="jchat_chatboxmessage selfmessage" id="jchat_message_publictemp"><span class="jchat_chatboxmessagefrom">' + tempPlaceholder + '</span><span class="jchat_chatboxmessagecontent selfmessage">' + testomessaggio + "</span></div>")
                }
                $.post(jsonLiveSite, {to: "wall", message: message, task: "stream.saveEntity"}, function (response) {
                    if (response.storing.status) {
                        $("#jchat_message_publictemp", "#jchat_wall_popup").remove();
                        var idmessaggio = response.storing.details.id;
                        var insertionTime = response.storing.details.time;
                        if (idmessaggio) {
                            if (my_avatar !== undefined && my_avatar !== null && my_avatar !== "") {
                                fromPlaceholder = '<img alt="' + jchat_you + my_username + '" data-time="' + insertionTime + '" width="32px" height="32px" src="' + my_avatar + '" />'
                            } else {
                                fromPlaceholder = '<strong data-time="' + insertionTime + '">' + my_username + "</strong>"
                            }
                            if ($("#jchat_message_" + idmessaggio).length > 0) {
                            } else {
                                $("#jchat_wall_popup div.jchat_tabcontenttext").append('<div class="jchat_chatboxmessage selfmessage" id="jchat_message_' + idmessaggio + '"><span class="jchat_chatboxmessagefrom">' + fromPlaceholder + '</span><span class="jchat_chatboxmessagecontent selfmessage">' + testomessaggio + "</span></div>");
                                $("span.jchat_chatboxmessagefrom img, span.jchat_chatboxmessagefrom strong", "#jchat_wall_popup").mouseover(function (event) {
                                    avatarTooltip(this, $(this).attr("alt"), $(this).data("time"))
                                }).mouseout(function (event) {
                                    $("#jchat_avatartooltip").remove()
                                })
                            }
                            $("#jchat_wall_popup div.jchat_tabcontenttext").scrollTop($("#jchat_wall_popup div.jchat_tabcontenttext")[0].scrollHeight)
                        }
                    } else {
                        showDebugMsgs(response.storing.details);
                        return false
                    }
                    refreshStream()
                }, "json")
            }
            return false
        }
    }

    function sendAppendPrivateMessage(userid, messageid, message, isSelf, messageObject) {
        var previewMessage = jchatStripTags(message, false, true, true);
        if (previewMessage.length > 25) {
            previewMessage = previewMessage.substr(0, 22) + "..."
        }
        if (previewMessage) {
            $("li.jchat_userbox[data-userid=" + userid + "] div.jchat_lastmessage").html('<span class="jchat_lastmessage_icon"></span>' + previewMessage)
        }
        $(".jchat_chatboxmessage.dummy_typing", "#jchat_usersmessages").remove();
        if (!$("li.jchat_userbox[data-userid=" + userid + "]").hasClass("jchat_active")) {
            return
        }
        var messageClass = "";
        if (isSelf) {
            var userName = jchat_my_username;
            var userAvatar = jchat_my_avatar;
            messageClass = " selfmessage"
        } else {
            var userName = messageObject.fromuser;
            var userAvatar = messageObject.avatar
        }
        var messageDate = new Date();
        var messageSnippet = '<div class="jchat_chatboxmessage' + messageClass + '" data-messageid="' + messageid + '"><span class="jchat_chatboxmessagefrom"><img alt="' + userName + '" width="32px" height="32px" src="' + userAvatar + '"></span><div class="jchat_chatboxmessageinfo"><div class="jchat_chatboxmessagefromname">' + userName + '</div><span class="jchat_chatboxmessagecontent' + messageClass + '">' + message + '</span></div><div class="jchat_chatboxmessagedate' + messageClass + '"><div>' + messageDate.toLocaleString() + "</div></div></div>";
        $("#jchat_usersmessages").append(messageSnippet);
        $("#jchat_usersmessages").scrollTop($("#jchat_usersmessages")[0].scrollHeight);
        $("#jchat_loadolder_messages").show();
        $("div.jchat_messaging_info").remove();
        var isUserOnline = $("div.jchat_userlist[data-loggedid=" + userid + "]").length;
        if (thirdPartyIntegration == "jomsocial" && PMIntegration && !isUserOnline) {
            var messageText = jchatStripTags(message, false, true, true);
            var jomSocialMessageTitle = messageText.length < chatTitleLength ? messageText : messageText.substr(0, chatTitleLength) + "...";
            var postVars = {
                option: "community",
                no_html: 1,
                task: "azrul_ajax",
                func: "inbox,ajaxSend",
                arg2: '[["subject","' + jomSocialMessageTitle + '"],["body","' + messageText + '"],["to",' + userid + "]]"
            };
            postVars[jchat_form_token] = 1;
            $.ajax({url: jchat_livesite, data: postVars, type: "post"})
        }
        if (thirdPartyIntegration == "easysocial" && PMIntegration && !isUserOnline) {
            var messageText = jchatStripTags(message, false, true, true);
            var postVars = {
                tmpl: "component",
                format: "ajax",
                no_html: 1,
                "uid[]": userid,
                message: messageText,
                option: "com_easysocial",
                namespace: "site/controllers/conversations/store"
            };
            postVars[jchat_form_token] = 1;
            $.ajax({url: jchat_livesite, data: postVars, type: "post"})
        }
    }

    function sendTicketMessage(formObject) {
        var formName = $("input[name=lam_name]", formObject).val();
        var formEmail = $("input[name=lam_email]", formObject).val();
        var formMessage = $("textarea[name=lam_message]", formObject).val();
        var validForm = jchatValidateForm("#lamform *[data-validation^=required]", "#lamform input[data-validation*=email]");
        if (!validForm) {
            return
        }
        $("#jchat_userstab_popup").append("<img/>").children("img").attr("src", jchat_livesite + "components/com_jchat/images/loading.gif").css({
            position: "absolute",
            top: "10%",
            left: "38%",
            width: "64px"
        }).addClass("waiter");
        var ajaxparams = {name: formName, email: formEmail, message: formMessage};
        var defer = $.ajax({
            type: "post",
            url: "index.php?option=com_jchat&task=ticket.saveEntity&format=raw",
            dataType: "json",
            context: this,
            data: ajaxparams,
            success: function (response) {
                if (response.storing.status) {
                    injectFloatingMsg(jchat_success_send_lamessage);
                    var formItem = $(formObject).get(0);
                    $.each(formItem, function (k, elem) {
                        $(elem).val("")
                    })
                } else {
                    injectFloatingMsg(jchat_error_send_lamessage + response.storing.details)
                }
            }
        });
        defer.always(function () {
            $("#jchat_userstab_popup img.waiter").remove()
        })
    }

    function deleteConversation(conversationFromId, isWall) {
        $.post(jsonLiveSite, {from: conversationFromId, task: "stream.saveEntity"}, function (response) {
            if (!isWall) {
                $("div.jchat_chatboxmessage, span.msg_seen", "#jchat_user_" + conversationFromId + "_popup").remove()
            } else {
                $("div.jchat_chatboxmessage", "#jchat_wall_popup").remove()
            }
            if (!response.storing.status) {
                showDebugMsgs(response.storing.details)
            }
        })
    }

    function getLanguageToolbar(popupIdentifier, disable) {
        var languagesInfo = jchatBuildLanguagesList();
        var languagesList = languagesInfo.list;
        var languagesPath = languagesInfo.arrayLanguagesPath;
        var popupNamespace = "#jchat_user_" + popupIdentifier + "_popup";
        if (disable) {
            $(document).off(".language", popupNamespace + " span.jchat_source_language," + popupNamespace + " span.jchat_target_language");
            $(document).off(".language", popupNamespace + " ul.jchat_languages_list li");
            $(document).off(".language", popupNamespace + " input[id^=jchat_lang_switch]");
            $(document).off(".language", popupNamespace + " span.jchat_translate_arrow");
            return null
        }
        $(document).on("click.language", popupNamespace + " span.jchat_source_language," + popupNamespace + " span.jchat_target_language", function (jqEvent) {
            var dataRole = $(this).data("role");
            var assignedClass = "jchat_" + $(this).data("role");
            var hasSameClass = $("ul.jchat_languages_list", "#jchat_user_" + popupIdentifier + "_popup").hasClass(assignedClass);
            var isOpened = $("ul.jchat_languages_list", "#jchat_user_" + popupIdentifier + "_popup").hasClass("jchat_tabopen");
            $("ul.jchat_languages_list, span.jchat_languages_list_pointer", "#jchat_user_" + popupIdentifier + "_popup").toggleClass("jchat_tabopen").removeClass("jchat_sourcelang jchat_targetlang").addClass(assignedClass).data("role", dataRole);
            if (isOpened && !hasSameClass) {
                $(this).trigger("click")
            }
            if (!isOpened) {
                var selectedSourceOrTargetLang = languageFallbacks[dataRole];
                if (typeof(popupLanguages[popupIdentifier]) !== "undefined") {
                    selectedSourceOrTargetLang = popupLanguages[popupIdentifier][dataRole] || languageFallbacks[dataRole]
                }
                if (selectedSourceOrTargetLang) {
                    var liElementFound = $("ul.jchat_languages_list li[data-isocode=" + selectedSourceOrTargetLang + "]", "#jchat_user_" + popupIdentifier + "_popup");
                    $("ul.jchat_languages_list li", "#jchat_user_" + popupIdentifier + "_popup").removeClass("jchat_lang_selected");
                    liElementFound.addClass("jchat_lang_selected");
                    var containerScrollingArea = $("ul.jchat_languages_list", "#jchat_user_" + popupIdentifier + "_popup");
                    var liElementFoundOffset = parseInt(liElementFound.position().top) + containerScrollingArea.scrollTop();
                    containerScrollingArea.animate({scrollTop: liElementFoundOffset}, 50)
                }
            }
        });
        $(document).on("click.language", popupNamespace + " ul.jchat_languages_list li", function (jqEvent) {
            var selectedIsoCode = $(this).data("isocode");
            var languageStyle = "background-image: url(" + jchat_livesite + languagesPath[selectedIsoCode]["path"] + selectedIsoCode + ".gif)";
            var parentDataRole = $(this).parent("ul.jchat_languages_list").data("role");
            $("div.jchat_toolbarlanguage > span[data-role=" + parentDataRole + "]", "#jchat_user_" + popupIdentifier + "_popup").attr("style", languageStyle);
            $("div.jchat_toolbarlanguage > span[data-role=" + parentDataRole + "]", "#jchat_user_" + popupIdentifier + "_popup").attr("data-isocode", selectedIsoCode);
            $("ul.jchat_languages_list li", "#jchat_user_" + popupIdentifier + "_popup").removeClass("jchat_lang_selected");
            $(this).addClass("jchat_lang_selected");
            if (typeof(popupLanguages[popupIdentifier]) === "undefined") {
                popupLanguages[popupIdentifier] = {}
            }
            popupLanguages[popupIdentifier][parentDataRole] = selectedIsoCode;
            sessionStorage.setItem("popupLanguages", JSON.stringify(popupLanguages))
        });
        $(document).on("change.language", popupNamespace + " input[id^=jchat_lang_switch]", function (jqEvent) {
            if (typeof(popupLanguages[popupIdentifier]) === "undefined") {
                popupLanguages[popupIdentifier] = {}
            }
            popupLanguages[popupIdentifier]["lang_switch_enabled"] = ~~(!popupLanguages[popupIdentifier]["lang_switch_enabled"]);
            sessionStorage.setItem("popupLanguages", JSON.stringify(popupLanguages))
        });
        $(document).on("click.language", popupNamespace + " span.jchat_translate_arrow", function (jqEvent) {
            if (typeof(popupLanguages[popupIdentifier]) !== "undefined") {
                var leftSourceLang = popupLanguages[popupIdentifier].sourcelang || defaultLanguage;
                var rightTargetLang = popupLanguages[popupIdentifier].targetlang || defaultTranslateToLanguage;
                if (leftSourceLang != rightTargetLang) {
                    popupLanguages[popupIdentifier].sourcelang = rightTargetLang;
                    popupLanguages[popupIdentifier].targetlang = leftSourceLang;
                    var sourceLangStyle = "background-image: url(" + jchat_livesite + languagesPath[leftSourceLang]["path"] + leftSourceLang + ".gif)";
                    $("div.jchat_toolbarlanguage span[data-role=targetlang]", "#jchat_user_" + popupIdentifier + "_popup").attr("style", sourceLangStyle);
                    $("div.jchat_toolbarlanguage span[data-role=targetlang]", "#jchat_user_" + popupIdentifier + "_popup").attr("data-isocode", leftSourceLang);
                    var targetLangStyle = "background-image: url(" + jchat_livesite + languagesPath[rightTargetLang]["path"] + rightTargetLang + ".gif)";
                    $("div.jchat_toolbarlanguage span[data-role=sourcelang]", "#jchat_user_" + popupIdentifier + "_popup").attr("style", targetLangStyle);
                    $("div.jchat_toolbarlanguage span[data-role=sourcelang]", "#jchat_user_" + popupIdentifier + "_popup").attr("data-isocode", rightTargetLang);
                    sessionStorage.setItem("popupLanguages", JSON.stringify(popupLanguages))
                }
            }
        });
        $(document).on("click.language", "body > *:not(ul.jchat_languages_list)", function (jqEvent) {
            if (!$(jqEvent.target).hasClass("jchat_languages_list") && !$(jqEvent.target).parents("div.jchat_toolbarlanguage").length) {
                $("ul.jchat_languages_list, span.jchat_languages_list_pointer", "#jchat_user_" + popupIdentifier + "_popup").removeClass("jchat_tabopen")
            }
        });
        var storedSourceLang = ' style="background-image: url(' + jchat_livesite + languagesPath[defaultLanguage]["path"] + defaultLanguage + '.gif)"';
        var storedTargetLang = ' style="background-image: url(' + jchat_livesite + languagesPath[defaultTranslateToLanguage]["path"] + defaultTranslateToLanguage + '.gif)"';
        var storedSourceIsocode = ' data-isocode="' + defaultLanguage + '"';
        var storedTargetIsocode = ' data-isocode="' + defaultTranslateToLanguage + '"';
        var enabledSwitcher = "";
        if (typeof(popupLanguages[popupIdentifier]) !== "undefined") {
            if (popupLanguages[popupIdentifier].sourcelang) {
                storedSourceLang = ' style="background-image: url(' + jchat_livesite + languagesPath[popupLanguages[popupIdentifier]["sourcelang"]]["path"] + popupLanguages[popupIdentifier]["sourcelang"] + '.gif)"';
                storedSourceIsocode = ' data-isocode="' + popupLanguages[popupIdentifier]["sourcelang"] + '"'
            }
            if (popupLanguages[popupIdentifier].targetlang) {
                storedTargetLang = ' style="background-image: url(' + jchat_livesite + languagesPath[popupLanguages[popupIdentifier]["targetlang"]]["path"] + popupLanguages[popupIdentifier]["targetlang"] + '.gif)"';
                storedTargetIsocode = ' data-isocode="' + popupLanguages[popupIdentifier]["targetlang"] + '"'
            }
            if (popupLanguages[popupIdentifier].lang_switch_enabled) {
                var enabledSwitcher = popupLanguages[popupIdentifier]["lang_switch_enabled"] ? ' checked="checked"' : ""
            }
        }
        var toolbarHtmlFragment = '<div class="jchat_toolbarlanguage"><span class="jchat_langlabel">From</span><span data-role="sourcelang"' + storedSourceIsocode + ' class="jchat_source_language"' + storedSourceLang + '></span><span class="jchat_translate_arrow"></span><span class="jchat_langlabel">To</span><span data-role="targetlang"' + storedTargetIsocode + ' class="jchat_target_language"' + storedTargetLang + '></span><div class="jchat_onoffswitchlang jchat_lang_switcher"><input type="checkbox" name="jchat_onoffswitch" class="jchat_onoffswitch-checkbox" id="jchat_lang_switch_' + popupIdentifier + '"' + enabledSwitcher + '><label class="jchat_onoffswitch-label" for="jchat_lang_switch_' + popupIdentifier + '"><span class="jchat_onoffswitch-inner"></span><span class="jchat_onoffswitch-switch"></span></label></div>' + languagesList + "</div>";
        return toolbarHtmlFragment
    }

    function setLanguageSessionvars() {
        var postLanguageSessionVars = {};
        var mapPopupsOnScreen = new Array();
        $.each(openedChatboxes, function (sessionid, assignedUserInfo) {
            var sourceLanguage = $("span[data-role=sourcelang]", "#jchat_user_" + sessionid + "_popup").attr("data-isocode") || defaultLanguage;
            var targetLanguage = $("span[data-role=targetlang]", "#jchat_user_" + sessionid + "_popup").attr("data-isocode") || defaultTranslateToLanguage;
            var translatorStatus = !!$("input[name=jchat_onoffswitch]", "#jchat_user_" + sessionid + "_popup").prop("checked") || 0;
            postLanguageSessionVars[sessionid] = {
                sourcelanguage: sourceLanguage,
                targetlanguage: targetLanguage,
                translatorstatus: translatorStatus
            };
            mapPopupsOnScreen[sessionid] = true
        });
        $.each(popupLanguages, function (sessionid, assignedLanguageInfo) {
            if (!mapPopupsOnScreen[sessionid] && typeof(popupLanguages[sessionid]) !== "undefined") {
                var sourceLanguage = popupLanguages[sessionid].sourcelang || defaultLanguage;
                var targetLanguage = popupLanguages[sessionid].targetlang || defaultTranslateToLanguage;
                var translatorStatus = parseInt(popupLanguages[sessionid].lang_switch_enabled) || 0;
                postLanguageSessionVars[sessionid] = {
                    sourcelanguage: sourceLanguage,
                    targetlanguage: targetLanguage,
                    translatorstatus: translatorStatus
                }
            }
        });
        s.langvars = JSON.stringify(postLanguageSessionVars)
    }

    function popup(ab, mixed, ac) {
        var Z = mixed.clientHeight;
        var Y = 94;
        if (Y > Z) {
            Z = Math.max(mixed.scrollHeight, Z);
            if (Y) {
                Z = Math.min(Y, Z)
            }
            if (Z > mixed.clientHeight) {
                $(mixed).css("height", Z + 4 + "px");
                var thisPopupHeight = popupDimensions[ac] ? popupDimensions[ac].height + 11 : defaultPopupHeight;
                $("#jchat_user_" + ac + "_popup .jchat_tabcontenttext").css("height", parseInt(thisPopupHeight - Z + 6) + "px");
                if (resizableChatboxes && !jchatHasTouch()) {
                    $("#jchat_user_" + ac + "_popup").resizable("disable")
                }
            }
        } else {
            $(mixed).css("overflow-y", "auto")
        }
        $("#jchat_user_" + ac + "_popup .jchat_tabcontenttext").scrollTop($("#jchat_user_" + ac + "_popup .jchat_tabcontenttext")[0].scrollHeight)
    }

    function popupWall(ab, mixed, wallStartHeight) {
        var Z = mixed.clientHeight;
        var Y = 94;
        if (Y > Z) {
            Z = Math.max(mixed.scrollHeight, Z);
            if (Y) {
                Z = Math.min(Y, Z)
            }
            if (Z > mixed.clientHeight) {
                $(mixed).css("height", Z + 4 + "px");
                $("#jchat_wall_popup div.jchat_tabcontenttext").height(wallStartHeight - ($(".jchat_textarea_wall").height() - 22))
            }
        } else {
            $(mixed).css("overflow-y", "auto")
        }
        $("#jchat_wall_popup div.jchat_tabcontenttext").scrollTop($("#jchat_wall_popup div.jchat_tabcontenttext")[0].scrollHeight)
    }

    function statusClassOp() {
        $("#jchat_optionsbutton_popup .offline").css("text-decoration", "none");
        $("#jchat_optionsbutton_popup .available").css("text-decoration", "none");
        $("#jchat_userstab_icon").removeClass("jchat_user_available2");
        $("#jchat_userstab_icon").removeClass("jchat_user_offline")
    }

    function postStatus(Y) {
        $.post(jsonLiveSite, {status: Y, task: "stream.saveEntity"}, function (response) {
            if (!response.storing.status) {
                showDebugMsgs(response.storing.details)
            }
        })
    }

    function postGenericStatus(statusVarName, statusVarValue) {
        var postDataObject = {};
        postDataObject[statusVarName] = statusVarValue;
        postDataObject.task = "stream.saveEntity";
        $.post(jsonLiveSite, postDataObject, function (response) {
            if (!response.storing.status) {
                showDebugMsgs(response.storing.details)
            }
        })
    }

    function turnChatOffline(Y) {
        o = 1;
        statusClassOp();
        $("#jchat_userstab_icon").addClass("jchat_user_offline");
        $("#jchat_userstab_text").html(chatTitle);
        if (Y != 1) {
            postGenericStatus("user_status", "offline")
        }
        $("#jchat_wall_popup").removeClass("jchat_tabopen");
        $("#jchat_userstab_popup").removeClass("jchat_tabopen");
        $("#jchat_userstab").removeClass("jchat_tabclick").removeClass("jchat_userstabclick");
        restartUpdateSession("buddylist", "0")
    }

    function refreshActiveChatBoxes() {
        var Y = "";
        openedChatboxes = {};
        $("span[id^=jchat_user_]").each(function (index, elem) {
            var mixed = $(this).data("id");
            var Z = 0;
            if ($("#jchat_user_" + mixed + " .jchat_tabalert").length > 0) {
                Z = parseInt($("#jchat_user_" + mixed + " .jchat_tabalert").html())
            }
            Y += mixed + "|" + Z + ",";
            openedChatboxes[mixed] = index
        });
        Y = Y.slice(0, -1);
        restartUpdateSession("activeChatboxes", Y)
    }

    function createPrivateMessagesPopup(ae, ab, Y, ad, statusAwayInfo, completeObject) {
        if ($("#jchat_user_" + ae).length > 0) {
            if (!$("#jchat_user_" + ae).hasClass("jchat_tabclick")) {
                $(".jchat_tabalert").css("display", "none");
                var mixed = 800;
                if (e("initialize") == 1 || e("updatesession") == 1) {
                    mixed = 0
                }
                $("#jchat_user_" + ae).trigger("fetchMessages")
            }
            return
        }
        if (ab.length > tabLength) {
            shortname = ab.substr(0, tabLength) + "..."
        } else {
            shortname = ab
        }
        if (ab.length > headLength) {
            longname = ab.substr(0, headLength) + "..."
        } else {
            longname = ab
        }
        var popupDimension = {width: 230, height: 200};
        if (typeof popupDimensions[ae] === "object") {
            popupDimension.width = popupDimensions[ae].width + "px";
            popupDimension.height = popupDimensions[ae].height + "px"
        }
        var popupPosition = {};
        var marginPosition = {};
        var overridePosition = false;
        var isThereEnoughSpace = false;
        if (positionmentChatboxes == "bottom") {
            var noFluxSubtract = 0;
            $.each(nofluxChatBoxes, function (i, noFluxIndex) {
                if (openedChatboxes[ae] > noFluxIndex) {
                    noFluxSubtract++
                }
            });
            var rightDisplacement = (openedChatboxes[ae] - noFluxSubtract) * parseInt(popupDimension.width);
            var sidebarWidth = 0;
            if (renderingMode != "module" || (renderingMode == "module" && separateWidgets)) {
                sidebarWidth = $("#jchat_userstab_popup").width()
            }
            var windowWidth = $(window).width();
            var maxRightDisplacement = parseInt(sidebarWidth + rightDisplacement) + parseInt(popupDimension.width);
            isThereEnoughSpace = !(maxRightDisplacement > windowWidth);
            if (!isThereEnoughSpace) {
                nofluxChatBoxes[ae] = openedChatboxes[ae];
                $.jStorage.set("nofluxchatboxes", nofluxChatBoxes)
            } else {
                if (!firstCall || (isThereEnoughSpace && nofluxChatBoxes[ae] && !popupPositions[ae])) {
                    try {
                        delete nofluxChatBoxes[ae];
                        delete minimizedIndexes[ae];
                        $.jStorage.set("nofluxchatboxes", nofluxChatBoxes);
                        $.jStorage.set("minimizedIndexes", minimizedIndexes)
                    } catch (e) {
                    }
                }
            }
        }
        if (positionmentChatboxes == "middle" || !isThereEnoughSpace) {
            var lengthOfArray = Object.keys(openedChatboxes).length - $(".jchat_bottomed").length;
            var microSplacement = parseInt(lengthOfArray) / microSplacementKonstant;
            popupPosition = {left: (50 + microSplacement) + "%", top: (50 + microSplacement) + "%"};
            marginPosition = {left: "-115px", top: "-167px"}
        } else {
            popupPosition = {right: parseInt(sidebarWidth + rightDisplacement) + "px", bottom: 0}
        }
        if (typeof popupPositions[ae] === "object") {
            popupPosition.left = popupPositions[ae].x + "px";
            popupPosition.top = popupPositions[ae].y + "px";
            marginPosition.left = 0;
            marginPosition.top = 0;
            overridePosition = true;
            if (positionmentChatboxes == "bottom") {
                nofluxChatBoxes[ae] = openedChatboxes[ae];
                $.jStorage.set("nofluxchatboxes", nofluxChatBoxes)
            }
        }
        var infoTrigger = "";
        if (completeObject.isguest && guestEnabledMode == 2) {
            infoTrigger = '<div class="jchat_trigger_infoguest"></div>'
        }
        var historyTrigger = "";
        if (!completeObject.isguest && !isGuest && messagesHistory) {
            historyTrigger = '<div class="jchat_trigger_history"></div>'
        }
        var geolocationTrigger = "";
        if (completeObject.geoip && geolocationEnabled) {
            geolocationTrigger = '<div class="jchat_trigger_geolocation"></div>'
        }
        var webrtcTrigger = "";
        if (typeof(JChatWebrtc) !== "undefined" && webrtcEnabled && hasWebRTCSupport() && userPermissions.allow_videochat && typeof(JChatConference) === "undefined") {
            var stateClassWebrtc = "";
            if ($(".jchat_webrtctooltip").length) {
                stateClassWebrtc = " jchat_webrtc_disabled"
            }
            webrtcTrigger = '<div class="jchat_trigger_webrtc' + stateClassWebrtc + '"></div>'
        }
        var sendTrigger = "";
        if (showSendButton == 1 || (showSendButton == 2 && jchatHasTouch())) {
            sendTrigger = '<div data-id="' + ae + '" class="jchat_trigger_send"></div>';
            $(document).on("click", "div.jchat_trigger_send[data-id=" + ae + "]", function (jqEvent) {
                var customEvent = $.Event("keydown");
                customEvent.keyCode = 13;
                customEvent.shiftKey = false;
                var targetIdentifier = $(this).data("id");
                $("#jchat_user_" + targetIdentifier + "_popup .jchat_textarea").trigger(customEvent)
            })
        }
        var toolbarLanguageHTML = "";
        if (languageTranslationEnabled) {
            toolbarLanguageHTML = getLanguageToolbar(ae, false)
        }
        $("<div/>").attr("id", "jchat_user_" + ae + "_popup").addClass("jchat_tabpopup jchat_tabopen").attr("dir", "ltr").html(toolbarLanguageHTML + '<div class="jchat_tabcontent messagelist"><div class="jchat_tabcontenttext private"></div><div class="jchat_tabcontentinput">' + writeElement + "</div>" + webrtcTrigger + '<div class="jchat_trigger_emoticon"></div><div class="jchat_trigger_fileupload"></div><div class="jchat_trigger_export"></div><div class="jchat_trigger_delete"></div><div class="jchat_trigger_refresh"></div>' + infoTrigger + historyTrigger + geolocationTrigger + sendTrigger + "</div>").appendTo($("body"));
        if ((infoTrigger || historyTrigger) && geolocationTrigger && sendTrigger) {
            $("div[class^=jchat_trigger]").addClass("jchat_trigger_smallspaced")
        }
        if (!attachmentsEnabled || (isGuest && !allowGuestFileupload)) {
            $(".jchat_trigger_fileupload").remove()
        }
        if (completeObject.imbanned) {
            $("#jchat_user_" + ae + "_popup .jchat_tabcontentinput").addClass("jchat_textarea_banned").html('<span class="banned_msg">' + jchat_banneduser + "</span>");
            $(".jchat_trigger_webrtc, .jchat_trigger_fileupload, .jchat_trigger_emoticon", "#jchat_user_" + ae + "_popup").hide();
            if ($("#jchat_privatemessaging_textarea[data-loggedid=" + completeObject.loggedid + "]").length) {
                $("#jchat_privatemessaging_textarea, div.jchat_userslist_ctrls").hide();
                $("div.jchat_fullcolumn_input span.banned_msg").remove();
                $("div.jchat_fullcolumn_input").append('<span class="banned_msg">' + jchat_banneduser + "</span>")
            }
        }
        if (positionmentChatboxes == "middle" || overridePosition || !isThereEnoughSpace) {
            $("#jchat_user_" + ae + "_popup").css({
                left: popupPosition.left,
                top: popupPosition.top,
                "margin-left": marginPosition.left,
                "margin-top": marginPosition.top
            })
        } else {
            $("#jchat_user_" + ae + "_popup").css({
                right: popupPosition.right,
                bottom: popupPosition.bottom
            }).addClass("jchat_bottomed")
        }
        $("#jchat_user_" + ae + "_popup").css({width: popupDimension.width});
        $("#jchat_user_" + ae + "_popup .jchat_tabcontenttext").css({height: popupDimension.height});
        $("<div/>").attr("id", "jchat_user_" + ae + "_popup");
        $("#jchat_user_" + ae + "_popup .jchat_textarea").focusin(function () {
            T = ae
        });
        $("div.jchat_trigger_send[data-id=" + ae + "]").hover(function () {
            T = ae
        });
        $(document).on("keydown", "#jchat_user_" + ae + "_popup .jchat_textarea", function (jqEvent) {
            return sendMessage(jqEvent, this, ae, completeObject.loggedid)
        });
        $(document).on("keyup", "#jchat_user_" + ae + "_popup .jchat_textarea", function (jqEvent) {
            return popup(jqEvent, this, ae)
        });
        $(".jchat_trigger_emoticon", "#jchat_user_" + ae + "_popup").toggle(function (event) {
            var openedPopups = $('.jchat_tabcontent div[class*="tooltip"]', "#jchat_user_" + ae + "_popup").filter(function (index) {
                return $(this).css("display") !== "none"
            });
            if (!!openedPopups.length) {
                openedPopups.next().trigger("click")
            }
            emoticonsTooltip(this, "#jchat_user_" + ae + "_popup", ae);
            $(this).addClass("toggle_on")
        }, function (event) {
            $(".jchat_emoticonstooltip", "#jchat_user_" + ae + "_popup").remove();
            $(this).removeClass("toggle_on")
        });
        $(".jchat_trigger_fileupload", "#jchat_user_" + ae + "_popup").toggle(function (event) {
            var openedPopups = $('.jchat_tabcontent div[class*="tooltip"]', "#jchat_user_" + ae + "_popup").filter(function (index) {
                return $(this).css("display") !== "none"
            });
            if (!!openedPopups.length) {
                openedPopups.next().trigger("click")
            }
            uploadFileTooltip(this, "#jchat_user_" + ae + "_popup", ae, completeObject.loggedid);
            $(this).addClass("toggle_on")
        }, function (event) {
            $(".jchat_fileuploadtooltip", "#jchat_user_" + ae + "_popup").remove();
            $(this).removeClass("toggle_on")
        });
        $(".jchat_trigger_webrtc", "#jchat_user_" + ae + "_popup").toggle(function (event) {
            var activeConnection = false;
            if ($("#jchat_end_call", "#jchat_user_" + ae + "_popup").length) {
                activeConnection = !($("#jchat_end_call", "#jchat_user_" + ae + "_popup").hasClass("jchat_disabled"))
            }
            if ($(this).hasClass("jchat_webrtc_disabled") || activeConnection || $(".jchat_webrtctooltip", "#jchat_user_" + ae + "_popup").length) {
                $(this).trigger("click");
                webrtcDirector.showInnerMessage(jchat_endcall_totoggle_popups);
                return
            }
            var openedPopups = $('.jchat_tabcontent div[class*="tooltip"]', "#jchat_user_" + ae + "_popup").filter(function (index) {
                return $(this).css("display") !== "none"
            });
            if (!!openedPopups.length) {
                openedPopups.next().trigger("click")
            }
            var videochatShortName = ab.length > videoLength ? ab.substr(0, videoLength) + "..." : ab;
            var videoMyShortName = my_username.length > videoLength ? my_username.substr(0, videoLength) + "..." : my_username;
            webrtcTooltip(this, "#jchat_user_" + ae + "_popup", ae, videochatShortName, videoMyShortName, ab);
            $(this).addClass("toggle_on");
            $("div[id!=jchat_user_" + ae + "_popup].jchat_tabpopup.jchat_tabopen .jchat_trigger_webrtc").addClass("jchat_webrtc_disabled")
        }, function (event) {
            var activeConnection = false;
            if ($("#jchat_end_call", "#jchat_user_" + ae + "_popup").length) {
                activeConnection = !($("#jchat_end_call", "#jchat_user_" + ae + "_popup").hasClass("jchat_disabled"))
            }
            if ($(this).hasClass("jchat_webrtc_disabled") || activeConnection) {
                webrtcDirector.showInnerMessage(jchat_endcall_totoggle_popups);
                return
            }
            $(".jchat_webrtctooltip", "#jchat_user_" + ae + "_popup").remove();
            $(this).removeClass("toggle_on");
            webrtcDirector.flushMedias();
            $("div[id!=jchat_user_" + ae + "_popup].jchat_tabpopup.jchat_tabopen .jchat_trigger_webrtc").removeClass("jchat_webrtc_disabled")
        });
        $(".jchat_trigger_infoguest", "#jchat_user_" + ae + "_popup").toggle(function (event) {
            var openedPopups = $('.jchat_tabcontent div[class*="tooltip"]', "#jchat_user_" + ae + "_popup").filter(function (index) {
                return $(this).css("display") !== "none"
            });
            if (!!openedPopups.length) {
                openedPopups.next().trigger("click")
            }
            infoGuestTooltip(this, "#jchat_user_" + ae + "_popup", ae);
            $(this).addClass("toggle_on")
        }, function (event) {
            $(".jchat_infoguesttooltip", "#jchat_user_" + ae + "_popup").remove();
            $(this).removeClass("toggle_on")
        });
        bindTooltipEvents(".jchat_trigger_history, .jchat_trigger_geolocation", ae, completeObject);
        $(".jchat_trigger_export", "#jchat_user_" + ae + "_popup").append("<a/>");
        $(".jchat_trigger_export a", "#jchat_user_" + ae + "_popup").attr("href", htmlLiveSite + "&task=export.display&chatid=" + ae);
        $(".jchat_trigger_delete", "#jchat_user_" + ae + "_popup").on("click", function () {
            deleteConversation(ae)
        });
        $("div.jchat_trigger_refresh", "#jchat_user_" + ae + "_popup").click(function (event) {
            var closureShortName = ab.length > tabLength ? ab.substr(0, tabLength) + "..." : ab;
            $("div.jchat_tab_shortname", "#jchat_user_" + ae + "_popup").text(closureShortName);
            return fetchMessages(ae)
        });
        $("div.jchat_trigger_refresh", "#jchat_user_" + ae + "_popup").mousedown(function () {
            $(this).addClass("jchat_refresh_mousedown")
        }).mouseup(function () {
            $(this).removeClass("jchat_refresh_mousedown")
        });
        $("#jchat_user_" + ae + "_popup .jchat_tabtitle").append('<div class="jchat_closebox"></div><br clear="all"/>');
        $("#jchat_user_" + ae + "_popup .jchat_tabtitle .jchat_closebox").mouseenter(function () {
            $(this).addClass("jchat_chatboxmouseoverclose");
            $("#jchat_user_" + ae + "_popup .jchat_tabtitle").removeClass("jchat_chatboxtabtitlemouseover")
        });
        $("#jchat_user_" + ae + "_popup .jchat_tabtitle .jchat_closebox").mouseleave(function () {
            $(this).removeClass("jchat_chatboxmouseoverclose");
            $("#jchat_user_" + ae + "_popup .jchat_tabtitle").addClass("jchat_chatboxtabtitlemouseover")
        });
        $("#jchat_user_" + ae + "_popup .jchat_tabtitle .jchat_closebox").click(function () {
            $("#jchat_user_" + ae + "_popup").remove();
            $("#jchat_user_" + ae).remove();
            if (T == ae) {
                T = "";
                restartUpdateSession("openChatboxId", "")
            }
            popupsReposition();
            refreshActiveChatBoxes()
        });
        $("#jchat_user_" + ae + "_popup .jchat_tabtitle").click(function () {
            $("#jchat_user_" + ae).trigger("fetchMessages")
        });
        $("#jchat_user_" + ae + "_popup .jchat_tabtitle").mouseenter(function () {
            $(this).addClass("jchat_chatboxtabtitlemouseover")
        });
        $("#jchat_user_" + ae + "_popup .jchat_tabtitle").mouseleave(function () {
            $(this).removeClass("jchat_chatboxtabtitlemouseover")
        });
        shortname = completeObject.profilelink !== null ? '<a href="' + completeObject.profilelink + '">' + shortname + "</a>" : shortname;
        $("<span/>").attr("id", "jchat_user_" + ae).attr("data-id", ae).addClass("jchat_tab jchat_tabclick jchat_usertabclick").html('<div class="jchat_closebox_bottom_status jchat_' + Y + '">' + statusAwayInfo + '</div><div class="jchat_tab_shortname">' + shortname + "</div>").prependTo($("#jchat_user_" + ae + "_popup"));
        $("#jchat_user_" + ae).attr("data-loggedid", completeObject.loggedid);
        var maximizeButtonHtml = '<div class="jchat_maximizebox_bottom"></div>';
        if (!maximizeButton || (maximizeButton > 1 && !jchatDetectMobileDevice(maximizeButton))) {
            maximizeButtonHtml = ""
        }
        $("#jchat_user_" + ae).append('<div class="jchat_closebox_bottom"></div>' + maximizeButtonHtml + '<div class="jchat_minimizebox_bottom"></div>');
        $("#jchat_user_" + ae + " .jchat_closebox_bottom").mouseenter(function () {
            $(this).addClass("jchat_closebox_bottomhover")
        });
        $("#jchat_user_" + ae + " .jchat_closebox_bottom").mouseleave(function () {
            $(this).removeClass("jchat_closebox_bottomhover")
        });
        $("#jchat_user_" + ae + " .jchat_closebox_bottom").click(function () {
            var hasWebrtcTooltip = !!$(this).parent().next().children(".jchat_webrtctooltip").length;
            if (hasWebrtcTooltip) {
                if (!($("#jchat_end_call", "#jchat_user_" + ae + "_popup").hasClass("jchat_disabled"))) {
                    webrtcDirector.showInnerMessage(jchat_endcall_before_close);
                    return false
                }
                $("div[id!=jchat_user_" + ae + "_popup].jchat_tabpopup.jchat_tabopen .jchat_trigger_webrtc").removeClass("jchat_webrtc_disabled")
            }
            if ($("#jchat_user_" + ae + "_popup").hasClass("maximized")) {
                popupDimensions[ae] = {width: 230, height: 200}
            }
            if (positionmentChatboxes == "bottom") {
                if (!(ae in nofluxChatBoxes)) {
                    var leftChatBoxes = $("#jchat_user_" + ae + "_popup").nextAll("div[id^=jchat_user_].jchat_bottomed");
                    if (leftChatBoxes.length) {
                        $.each(leftChatBoxes, function (index, chatbox) {
                            var $chatbox = $(chatbox);
                            var currentDisplacement = parseInt($chatbox.css("right"));
                            $chatbox.animate({right: (currentDisplacement - parseInt(popupDimension.width)) + "px"}, 300)
                        })
                    }
                }
            }
            try {
                delete openedChatboxes[ae];
                if (nofluxChatBoxes[ae]) {
                    delete nofluxChatBoxes[ae];
                    $.jStorage.set("nofluxchatboxes", nofluxChatBoxes)
                }
                if (minimizedAllChatboxes && minimizedIndexes[ae]) {
                    var offsetValue = minimizedIndexes[ae];
                    $.each(minimizedIndexes, function (index, value) {
                        if (value > offsetValue) {
                            minimizedIndexes[index]--;
                            if (popupPositions[index]) {
                                var updatedTop = popupPositions[index].y + 26;
                                $("#jchat_user_" + index + "_popup").animate({top: updatedTop + "px"}, 300);
                                popupPositions[index].y = updatedTop
                            }
                        }
                    });
                    delete minimizedIndexes[ae];
                    delete popupPositions[ae];
                    delete popupDimensions[ae];
                    delete popupState[ae];
                    delete popupMaximizedState[ae];
                    $.jStorage.set("minimizedIndexes", minimizedIndexes);
                    $.jStorage.set("popupPositions", popupPositions);
                    $.jStorage.set("popupState", popupState);
                    $.jStorage.set("popupMaximizedState", popupMaximizedState);
                    $.jStorage.set("popupDimensions", popupDimensions)
                }
            } catch (e) {
            }
            delete gMapReference[ae];
            if (languageTranslationEnabled) {
                getLanguageToolbar(ae, true)
            }
            $("#jchat_user_" + ae + "_popup").remove();
            $("#jchat_user_" + ae).remove();
            if (T == ae) {
                T = "";
                restartUpdateSession("openChatboxId", "")
            }
            popupsReposition();
            refreshActiveChatBoxes()
        });
        $("#jchat_user_" + ae + " .jchat_minimizebox_bottom").on("hover", function () {
            $(this).toggleClass("jchat_minimizebox_bottomhover")
        });
        $("#jchat_user_" + ae + " .jchat_maximizebox_bottom").on("hover", function () {
            $(this).toggleClass("jchat_maximizebox_bottomhover")
        });
        $("#jchat_user_" + ae + " .jchat_minimizebox_bottom").click(function (jqEvent, caller) {
            var windowWidth = $(window).width();
            var windowHeight = $(window).height();
            var counterBottomedChatboxes = $(".jchat_bottomed").length - 1;
            var counterNotBottomedChatBoxes = $("div[id^=jchat_user_]:not(.jchat_bottomed)").length - 1;
            var sidebarWidth = 0;
            if (renderingMode == "auto" || (renderingMode == "module" && separateWidgets)) {
                sidebarWidth = $("#jchat_userstab_popup").width()
            }
            if ($("#jchat_userstab_popup").hasClass("maximized")) {
                sidebarWidth = $.jStorage.get("sidebarWidth", false) || (renderingMode == "auto" ? 260 : originalSidebarWidth)
            }
            $(this).toggleClass("minimized");
            var $currentPopup = $("#jchat_user_" + ae + "_popup");
            var currentMinimizedState = !$(this).data("minimized");
            $(this).data("minimized", currentMinimizedState);
            popupState[ae] = currentMinimizedState;
            $.jStorage.set("popupState", popupState);
            if (currentMinimizedState) {
                $currentPopup.toggleClass("minimized");
                $("#jchat_user_" + ae + "_popup .ui-resizable-handle").toggle()
            }
            $("#jchat_user_" + ae + "_popup .jchat_tabcontent.messagelist").slideToggle(300, "swing", function () {
                $("#jchat_user_" + ae + "_popup .jchat_tabcontenttext").scrollTop($("#jchat_user_" + ae + "_popup .jchat_tabcontenttext")[0].scrollHeight);
                if (!currentMinimizedState) {
                    $currentPopup.toggleClass("minimized");
                    $("#jchat_user_" + ae + "_popup .ui-resizable-handle").toggle();
                    if (minimizedAllChatboxes) {
                        if (!$currentPopup.hasClass("jchat_bottomed") && !caller) {
                            var lengthOfArray = minimizedIndexes[ae];
                            var microSplacement = parseInt(lengthOfArray) / microSplacementKonstant;
                            var popupPosition = {left: (50 + microSplacement) + "%", top: (50 + microSplacement) + "%"};
                            var marginPosition = {left: "-115px", top: "-167px"};
                            $currentPopup.animate({
                                left: popupPosition.left,
                                top: popupPosition.top,
                                height: "auto",
                                "margin-left": marginPosition.left,
                                "margin-top": marginPosition.top
                            }, 500);
                            try {
                                delete (popupPositions[ae]);
                                $.jStorage.set("popupPositions", popupPositions)
                            } catch (e) {
                            }
                        } else {
                            var $firstBottomedChatbox = $($("div.jchat_bottomed").get(0));
                            if ($firstBottomedChatbox.attr("id") == $currentPopup.attr("id") && !caller) {
                                var additionalHeight = $firstBottomedChatbox.height();
                                $("div[id^=jchat_user_]:not(.jchat_bottomed).minimized").each(function (index, elem) {
                                    var identifier = $("span[id^=jchat_user_]", elem).data("id");
                                    var currentUpperPopupPosition = popupPositions[identifier].y;
                                    var newPosition = popupPositions[identifier].y = currentUpperPopupPosition - additionalHeight + 26;
                                    $(elem).animate({top: newPosition}, 300)
                                })
                            }
                            $.jStorage.set("popupPositions", popupPositions)
                        }
                    }
                } else {
                    if (minimizedAllChatboxes) {
                        var firstBottomedHeight = $($("div.jchat_bottomed").get(0)).height() || 0;
                        if (!minimizedIndexes[ae] && !caller && !$currentPopup.hasClass("jchat_bottomed")) {
                            var maxIndex = jchatDetectMaxValue(minimizedIndexes);
                            minimizedIndexes[ae] = maxIndex + 1;
                            $.jStorage.set("minimizedIndexes", minimizedIndexes)
                        }
                        if (!$currentPopup.hasClass("jchat_bottomed") && !caller) {
                            var calculatedLeft = (windowWidth - sidebarWidth - 230);
                            var calculatedTop = (windowHeight - firstBottomedHeight - (minimizedIndexes[ae] * 26));
                            $currentPopup.animate({
                                left: calculatedLeft + "px",
                                top: calculatedTop + "px",
                                "margin-left": 0,
                                "margin-top": 0,
                                bottom: "auto",
                                right: "auto",
                                width: "230px",
                                height: "293px"
                            }, 500);
                            popupPositions[ae] = {x: calculatedLeft, y: calculatedTop};
                            $.jStorage.set("popupPositions", popupPositions);
                            try {
                                delete (popupDimensions[ae]);
                                $.jStorage.set("popupDimensions", popupDimensions);
                                $("#jchat_user_" + ae + "_popup .jchat_tabcontenttext").removeAttr("style")
                            } catch (e) {
                            }
                        } else {
                            var $firstBottomedChatbox = $($("div.jchat_bottomed").get(0));
                            if ($firstBottomedChatbox.attr("id") == $currentPopup.attr("id") && !caller) {
                                var additionalHeight = 293;
                                $("div[id^=jchat_user_]:not(.jchat_bottomed).minimized").each(function (index, elem) {
                                    var identifier = $("span[id^=jchat_user_]", elem).data("id");
                                    var currentUpperPopupPosition = popupPositions[identifier].y;
                                    var newPosition = popupPositions[identifier].y = currentUpperPopupPosition + additionalHeight - 26;
                                    $(elem).animate({top: newPosition}, 300)
                                });
                                $.jStorage.set("popupPositions", popupPositions)
                            }
                        }
                    }
                }
            })
        });
        $("#jchat_user_" + ae + " .jchat_maximizebox_bottom").click(function () {
            $(this).toggleClass("maximized");
            var currentMaximizedState = !$(this).data("maximized");
            $(this).data("maximized", currentMaximizedState);
            var languageToolbarHeight = $("div.jchat_toolbarlanguage", "#jchat_user_" + ae + "_popup").height() || 0;
            if (currentMaximizedState) {
                $("#jchat_user_" + ae + "_popup").toggleClass("maximized");
                $("#jchat_user_" + ae + "_popup .ui-resizable-handle").toggle();
                $("#jchat_user_" + ae + "_popup").css({
                    top: 0,
                    left: 0,
                    "margin-left": 0,
                    "margin-top": 0,
                    width: "100%",
                    height: "auto"
                });
                var setDimensions = function () {
                    var viewportWidth = (window.innerWidth ? window.innerWidth : $(window).width()) - 28;
                    var viewportHeight = (window.innerHeight ? window.innerHeight : $(window).height()) - 92 - languageToolbarHeight;
                    $(".jchat_tabcontenttext.private", "#jchat_user_" + ae + "_popup").width(viewportWidth);
                    $(".jchat_tabcontenttext.private", "#jchat_user_" + ae + "_popup").height(viewportHeight);
                    popupDimensions[ae] = {width: viewportWidth, height: viewportHeight}
                };
                setDimensions();
                $(window).on("resize.jchat", function (jqEvent) {
                    setDimensions()
                });
                popupsReposition(true);
                $("#jchat_user_" + ae + "_popup").draggable("disable");
                $("div[id^=jchat_user_]").removeClass("maximized_hover");
                $("div[id^=jchat_user_]").each(function (index, elem) {
                    var thisID = $(elem).attr("id");
                    var hasMaximizedClass = $(elem).hasClass("maximized");
                    if (hasMaximizedClass && thisID != "jchat_user_" + ae + "_popup") {
                        $(".jchat_maximizebox_bottom", elem).trigger("click")
                    }
                });
                popupMaximizedState[ae] = currentMaximizedState;
                $.jStorage.set("popupMaximizedState", popupMaximizedState)
            } else {
                $("#jchat_user_" + ae + "_popup").toggleClass("maximized");
                $("#jchat_user_" + ae + "_popup .ui-resizable-handle").toggle();
                var originalTopPosition = "50%";
                var originalLeftPosition = "50%";
                var originalWidth = 230;
                var originalHeight = 200 - languageToolbarHeight;
                var originalMarginLeft = marginPosition.left;
                var originalMarginTop = marginPosition.top;
                popupDimensions[ae] = {width: originalWidth, height: originalHeight};
                if (typeof popupPositions[ae] === "object") {
                    originalTopPosition = popupPositions[ae].y;
                    originalLeftPosition = popupPositions[ae].x;
                    originalMarginLeft = 0;
                    originalMarginTop = 0
                }
                if ($("#jchat_user_" + ae + "_popup").hasClass("jchat_bottomed")) {
                    $("#jchat_user_" + ae + "_popup").css({
                        top: "auto",
                        left: "auto",
                        "margin-left": "auto",
                        "margin-top": "auto",
                        width: originalWidth,
                        height: "auto"
                    })
                } else {
                    $("#jchat_user_" + ae + "_popup").css({
                        top: originalTopPosition,
                        left: originalLeftPosition,
                        "margin-left": originalMarginLeft,
                        "margin-top": originalMarginTop,
                        width: originalWidth,
                        height: "293px"
                    })
                }
                $(".jchat_tabcontenttext.private", "#jchat_user_" + ae + "_popup").innerWidth(originalWidth - 4);
                $(".jchat_tabcontenttext.private", "#jchat_user_" + ae + "_popup").height(originalHeight);
                $(window).off("resize.jchat");
                popupsReposition();
                $("#jchat_user_" + ae + "_popup").draggable("enable");
                $("div[id^=jchat_user_]").removeClass("maximized_hover");
                delete popupMaximizedState[ae];
                $.jStorage.set("popupMaximizedState", popupMaximizedState)
            }
            $("#jchat_user_" + ae + "_popup .jchat_tabcontenttext").scrollTop($("#jchat_user_" + ae + "_popup .jchat_tabcontenttext")[0].scrollHeight)
        });
        if (popupState[ae] === true) {
            $("#jchat_user_" + ae + " .jchat_minimizebox_bottom").addClass("minimized").data("minimized", popupState[ae]);
            $("#jchat_user_" + ae + "_popup .jchat_tabcontent.messagelist").toggle()
        }
        $("#jchat_user_" + ae).mouseenter(function () {
            $(this).addClass("jchat_tabmouseover");
            $("#jchat_user_" + ae + " div").addClass("jchat_tabmouseovertext")
        });
        $("#jchat_user_" + ae).mouseleave(function () {
            $(this).removeClass("jchat_tabmouseover");
            $("#jchat_user_" + ae + " div").removeClass("jchat_tabmouseovertext")
        });
        $("#jchat_user_" + ae).on("fetchMessages", function () {
            if ($("#jchat_user_" + ae + " .jchat_tabalert").length > 0) {
                $("#jchat_user_" + ae + " .jchat_tabalert").remove();
                refreshActiveChatBoxes()
            }
            if ($("div.jchat_avatar_upload_tooltip").length > 0) {
                $("#jchat_avatar").trigger("click")
            }
            if ($("div.jchat_emoticonstooltip", "#jchat_wall_popup").length > 0) {
                $("div.jchat_trigger_emoticon", "#jchat_wall_popup").trigger("click")
            }
            $(this).addClass("jchat_tabclick").addClass("jchat_usertabclick");
            restartUpdateSession("openChatboxId", ae);
            T = ae;
            fetchMessages(ae);
            $("#jchat_user_" + ae + "_popup .jchat_tabcontenttext").scrollTop($("#jchat_user_" + ae + "_popup .jchat_tabcontenttext")[0].scrollHeight);
            if (!firstCall && !jchatHasTouch()) {
                $("#jchat_user_" + ae + "_popup .jchat_textarea").focus()
            }
        });
        $("#jchat_user_" + ae).trigger("fetchMessages");
        if (!isTabFocused) {
            $("title").text(ab + jchat_newmessage_tab)
        }
        refreshActiveChatBoxes();
        $("#jchat_user_" + ae + "_popup").draggable({
            handle: "#jchat_user_" + ae, drag: function (event, ui) {
                var isMaximized = $("#jchat_wall_popup").hasClass("maximized") || $(this).hasClass("maximized");
                popupsReposition(isMaximized, this)
            }, start: function (event, ui) {
                $(this).addClass("dragging");
                $(this).css({bottom: "auto", right: "auto"});
                if (positionmentChatboxes == "bottom" && $("#jchat_user_" + ae + "_popup").hasClass("jchat_bottomed")) {
                    var leftChatBoxes = $("#jchat_user_" + ae + "_popup.jchat_bottomed").removeClass("jchat_bottomed").nextAll("div[id^=jchat_user_].jchat_bottomed");
                    if (leftChatBoxes.length) {
                        $.each(leftChatBoxes, function (index, chatbox) {
                            var $chatbox = $(chatbox);
                            var currentDisplacement = parseInt($chatbox.css("right"));
                            $chatbox.animate({right: (currentDisplacement - parseInt(popupDimension.width)) + "px"}, 300)
                        })
                    }
                    nofluxChatBoxes[ae] = openedChatboxes[ae];
                    $.jStorage.set("nofluxchatboxes", nofluxChatBoxes)
                }
            }, stop: function (event, ui) {
                popupPositions[ae] = {
                    x: ui.helper.offset().left - $(window).scrollLeft(),
                    y: ui.helper.offset().top - $(window).scrollTop()
                };
                $.jStorage.set("popupPositions", popupPositions);
                $(this).removeClass("dragging")
            }
        });
        if (resizableChatboxes && !jchatHasTouch()) {
            $("#jchat_user_" + ae + "_popup").resizable({
                handles: "se",
                alsoResize: "#jchat_user_" + ae + "_popup .jchat_tabcontenttext.private",
                minHeight: 293,
                minWidth: 230,
                start: function (event, ui) {
                },
                stop: function (event, ui) {
                    var parentContainerOfText = $(ui.element).get(0);
                    var targetElem = $(".jchat_tabcontenttext", parentContainerOfText).get(0);
                    var defaultChatboxHeight = $(".jchat_tabcontenttext", parentContainerOfText).get(0).clientHeight - 10;
                    popupDimensions[ae] = {width: ui.size.width, height: defaultChatboxHeight};
                    $.jStorage.set("popupDimensions", popupDimensions)
                },
                resize: function (event, ui) {
                    popupsReposition();
                    var parentContainerOfText = $(ui.element).get(0);
                    if (ui.originalSize.height > ui.size.height) {
                        $(".jchat_tabcontenttext", parentContainerOfText).scrollTop($(".jchat_tabcontenttext", parentContainerOfText)[0].scrollHeight)
                    }
                    if ($(".jchat_tabcontenttext iframe", parentContainerOfText).height() < 150) {
                        $(".jchat_tabcontenttext iframe", parentContainerOfText).height(150)
                    }
                }
            })
        }
        if (popupMaximizedState[ae] === true) {
            $("#jchat_user_" + ae + " .jchat_maximizebox_bottom").trigger("click")
        }
        makeSidebarResizable();
        if (popupState[ae] === true) {
            $("#jchat_user_" + ae + "_popup .ui-resizable-handle").toggle();
            $("#jchat_user_" + ae + "_popup").toggleClass("minimized")
        }
        if ($.browser.msie && $.browser.version <= 8) {
        } else {
            jchatInitTouchEvents($("#jchat_user_" + ae).get(0));
            if (resizableChatboxes && !jchatHasTouch()) {
                jchatInitTouchEvents($("#jchat_user_" + ae + "_popup div.ui-resizable-handle").get(0))
            }
        }
        if (jchatHasTouch()) {
            $("#jchat_user_" + ae).css("background-position", "80% -850px");
            $("div.ui-resizable-handle").css({width: "36px", height: "36px", "background-position": "22px -1402px"});
            $("div.jchat_tabcontent").css("border-bottom", "none")
        }
    }

    function fetchMessages(Y) {
        $.ajax({
            async: true,
            url: jsonLiveSite,
            data: {chatbox: Y, task: "stream.display"},
            type: "post",
            cache: false,
            dataType: "json",
            success: function (ab) {
                if (ab) {
                    $("#jchat_user_" + Y + "_popup .jchat_tabcontenttext").html("");
                    var Z = "";
                    $.each(ab, function (ac, ad) {
                        if (ac == "messages") {
                            $.each(ad, function (af, ae) {
                                if (ae.id > n) {
                                    n = ae.id
                                }
                                var selfClass = "";
                                if (ae.self == 1) {
                                    if (my_avatar !== undefined && my_avatar !== null && my_avatar !== "" && avatarEnabled === true) {
                                        fromPlaceholder = '<img alt="' + jchat_you + my_username + '" data-time="' + ae.time + '" width="32px" height="32px" src="' + my_avatar + '" />'
                                    } else {
                                        fromPlaceholder = '<strong data-time="' + ae.time + '">' + my_username + "</strong>"
                                    }
                                    selfClass = " selfmessage"
                                } else {
                                    var userName = $("#jchat_userlist_" + Y).triggerHandler("getname");
                                    var userAvatar = $("#jchat_userlist_" + Y).triggerHandler("getavatar");
                                    if (userAvatar === undefined || userAvatar === null || userAvatar === "") {
                                        userAvatar = ae.avatar
                                    }
                                    if (userName === undefined || userName === null || userName === "") {
                                        if (ae.fromuser.length > chatLength) {
                                            userName = ae.fromuser.substr(0, chatLength) + "..."
                                        } else {
                                            userName = ae.fromuser
                                        }
                                    }
                                    if (userAvatar !== undefined && userAvatar !== null && userAvatar !== "" && avatarEnabled === true) {
                                        fromPlaceholder = '<img alt="' + userName + '" data-time="' + ae.time + '" width="32px" height="32px" src="' + userAvatar + '" />'
                                    } else {
                                        fromPlaceholder = '<strong data-time="' + ae.time + '">' + userName + "</strong>"
                                    }
                                }
                                if (typeof ae.profilelink !== "undefined" && ae.profilelink !== "" && ae.profilelink !== null) {
                                    fromPlaceholder = '<a href="' + ae.profilelink + '">' + fromPlaceholder + "</a>"
                                }
                                if (ae.type == "file") {
                                    ae.message = jchatTrasformMsgFile(ae.message, ae.id, ae.self, ae.from, ae.status, false, ae)
                                } else {
                                    ae.message = jchatStripTags(ae.message, allowMediaObjects);
                                    try {
                                        ae.message = ae.message.replace(/&/gi, "&amp;")
                                    } catch (e) {
                                    }
                                }
                                if (ae.message) {
                                    Z += ('<div class="jchat_chatboxmessage' + selfClass + '" id="jchat_message_' + ae.id + '"><span class="jchat_chatboxmessagefrom">' + fromPlaceholder + '</span><span class="jchat_chatboxmessagecontent' + selfClass + '">' + ae.message + "</span></div>")
                                }
                            })
                        }
                    });
                    $("#jchat_user_" + Y + "_popup .jchat_tabcontenttext").append(Z);
                    $("#jchat_user_" + Y + "_popup .jchat_tabcontenttext").scrollTop($("#jchat_user_" + Y + "_popup .jchat_tabcontenttext")[0].scrollHeight)
                }
                $("span.jchat_chatboxmessagefrom img, span.jchat_chatboxmessagefrom strong").mouseover(function (event) {
                    avatarTooltip(this, $(this).attr("alt"), $(this).data("time"))
                }).mouseout(function (event) {
                    $("#jchat_avatartooltip").remove()
                })
            }
        })
    }

    function fetchWallMessages(historyMessages, noScrollToTop) {
        $("#jchat_wall_popup").append("<img/>").children("img").attr("src", jchat_livesite + "components/com_jchat/images/loading.gif").css({
            position: "absolute",
            top: "10%",
            left: "38%",
            width: "64px"
        }).addClass("waiter");
        $.ajax({
            async: true,
            url: jsonLiveSite,
            data: {wall: true, task: "stream.display", download_history: historyMessages},
            type: "post",
            cache: false,
            dataType: "json",
            success: function (ab) {
                if (ab && typeof(ab.wallmessages) !== "undefined") {
                    $("#jchat_wall_popup div.jchat_tabcontenttext").html("");
                    var Z = "";
                    $.each(ab.wallmessages, function (af, ae) {
                        var selfClass = "";
                        var userNameWidthClass = "";
                        var msgWidthClass = "";
                        var hasAvatar = false;
                        if (ae.self == 1) {
                            if (my_avatar !== undefined && my_avatar !== null && my_avatar !== "") {
                                fromPlaceholder = '<img alt="' + jchat_you + my_username + '" data-time="' + ae.time + '" width="32px" height="32px" src="' + my_avatar + '" />'
                            } else {
                                fromPlaceholder = '<strong data-time="' + ae.time + '">' + my_username + "</strong>"
                            }
                            selfClass = " selfmessage"
                        } else {
                            var userName = $("#jchat_userlist_" + ae.fromuserid).triggerHandler("getname");
                            var userAvatar = $("#jchat_userlist_" + ae.fromuserid).triggerHandler("getavatar");
                            if (userAvatar === undefined || userAvatar === null || userAvatar === "") {
                                userAvatar = ae.avatar
                            }
                            if (userName === undefined || userName === null || userName === "") {
                                if (ae.fromuser.length > chatLength) {
                                    userName = ae.fromuser.substr(0, chatLength) + "..."
                                } else {
                                    userName = ae.fromuser
                                }
                            }
                            var hasAvatar = !!(userAvatar !== undefined && userAvatar !== null && userAvatar !== "" && !userAvatar.match(/default_other.png/) && avatarEnabled === true);
                            if (!hasAvatar && userName.length >= (chatLength - 13)) {
                                userNameWidthClass = " jchat_username_large";
                                msgWidthClass = " jchat_message_small"
                            }
                            if (userAvatar !== undefined && userAvatar !== null && userAvatar !== "" && !userAvatar.match(/default_other.png/) && avatarEnabled === true) {
                                fromPlaceholder = '<img alt="' + userName + '" data-time="' + ae.time + '" width="32px" height="32px" src="' + userAvatar + '" />'
                            } else {
                                fromPlaceholder = '<strong data-time="' + ae.time + '">' + userName + "</strong>"
                            }
                        }
                        if (typeof ae.profilelink !== "undefined" && ae.profilelink !== "" && ae.profilelink !== null) {
                            fromPlaceholder = '<a href="' + ae.profilelink + '">' + fromPlaceholder + "</a>"
                        }
                        ae.message = jchatStripTags(ae.message, allowMediaObjects);
                        ae.message = ae.message.replace(/&/gi, "&amp;");
                        Z += ('<div class="jchat_chatboxmessage' + selfClass + '" id="jchat_message_' + ae.id + '"><span class="jchat_chatboxmessagefrom' + userNameWidthClass + '">' + fromPlaceholder + '</span><span class="jchat_chatboxmessagecontent' + selfClass + msgWidthClass + '">' + ae.message + "</span></div>")
                    });
                    $("#jchat_wall_popup div.jchat_tabcontenttext").append(Z);
                    if (historyMessages && !noScrollToTop) {
                        $("#jchat_wall_popup div.jchat_tabcontenttext").animate({scrollTop: 0}, 300, "swing")
                    } else {
                        $("#jchat_wall_popup div.jchat_tabcontenttext").scrollTop($("#jchat_wall_popup div.jchat_tabcontenttext")[0].scrollHeight)
                    }
                }
                $("span.jchat_chatboxmessagefrom img, span.jchat_chatboxmessagefrom strong", "#jchat_wall_popup").mouseover(function (event) {
                    avatarTooltip(this, $(this).attr("alt"), $(this).data("time"))
                }).mouseout(function (event) {
                    $("#jchat_avatartooltip").remove()
                });
                $("#jchat_wall_popup img.waiter").remove()
            }
        })
    }

    function fetchHistoryMessages(loggedid, userid, messagesPeriod, minMessageId) {
        $("#jchat_user_" + userid + "_popup").append("<img/>").children("img").attr("src", jchat_livesite + "components/com_jchat/images/loading.gif").css({
            position: "absolute",
            top: "10%",
            left: "38%",
            width: "64px"
        }).addClass("waiter");
        var historyFetchPromise = $.Deferred(function (defer) {
            $.ajax({
                type: "post",
                url: jsonLiveSite,
                dataType: "json",
                cache: false,
                context: this,
                data: {
                    task: "stream.showHistory",
                    from_loggedid: loggedid,
                    from_userid: userid,
                    msgs_period: messagesPeriod,
                    min_message_id: minMessageId
                }
            }).done(function (data, textStatus, jqXHR) {
                if (!data.status) {
                    defer.reject(data.details, textStatus);
                    return false
                }
                if (!data.messages.length) {
                    defer.reject(jchat_nomessages_available, textStatus);
                    return false
                }
                if (data.status && data.messages.length) {
                    defer.resolve(data.messages)
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                var genericStatus = textStatus[0].toUpperCase() + textStatus.slice(1);
                defer.reject("-" + genericStatus + "- " + errorThrown)
            }).always(function () {
                $("#jchat_user_" + userid + "_popup img.waiter").remove()
            })
        }).promise();
        historyFetchPromise.then(function (messages) {
            var messagesToPrepend = "";
            $.each(messages, function (index, message) {
                var selfClass = "";
                if (message.self == 1) {
                    if (my_avatar !== undefined && my_avatar !== null && my_avatar !== "" && avatarEnabled === true) {
                        fromPlaceholder = '<img alt="' + jchat_you + my_username + '" data-time="' + message.time + '" width="32px" height="32px" src="' + my_avatar + '" />'
                    } else {
                        fromPlaceholder = '<strong data-time="' + message.time + '">' + my_username + "</strong>"
                    }
                    selfClass = " selfmessage"
                } else {
                    var userName = $("#jchat_userlist_" + userid).triggerHandler("getname");
                    var userAvatar = $("#jchat_userlist_" + userid).triggerHandler("getavatar");
                    if (userName === undefined || userName === null || userName === "") {
                        if (message.fromusername.length > chatLength) {
                            userName = message.fromusername.substr(0, chatLength) + "..."
                        } else {
                            userName = message.fromusername
                        }
                    }
                    if (userAvatar !== undefined && userAvatar !== null && userAvatar !== "" && avatarEnabled === true) {
                        fromPlaceholder = '<img alt="' + userName + '" data-time="' + message.time + '" width="32px" height="32px" src="' + userAvatar + '" />'
                    } else {
                        fromPlaceholder = '<strong data-time="' + message.time + '">' + userName + "</strong>"
                    }
                }
                if (typeof message.profilelink !== "undefined" && message.profilelink !== "" && message.profilelink !== null) {
                    fromPlaceholder = '<a href="' + message.profilelink + '">' + fromPlaceholder + "</a>"
                }
                if (message.type == "file") {
                    message.message = jchatTrasformMsgFile(message.message, message.id, message.self, message.from, message.status, false, message)
                } else {
                    message.message = jchatStripTags(message.message, allowMediaObjects);
                    message.message = message.message.replace(/&/gi, "&amp;")
                }
                messagesToPrepend += ('<div class="jchat_chatboxmessage' + selfClass + '" id="jchat_message_' + message.id + '"><span class="jchat_chatboxmessagefrom">' + fromPlaceholder + '</span><span class="jchat_chatboxmessagecontent' + selfClass + '">' + message.message + "</span></div>")
            });
            $("#jchat_user_" + userid + "_popup .jchat_tabcontenttext").prepend(messagesToPrepend);
            $("#jchat_user_" + userid + "_popup .jchat_tabcontenttext").animate({scrollTop: 0}, 300, "swing");
            $("span.jchat_chatboxmessagefrom img, span.jchat_chatboxmessagefrom strong").mouseover(function (event) {
                avatarTooltip(this, $(this).attr("alt"), $(this).data("time"))
            }).mouseout(function (event) {
                $("#jchat_avatartooltip").remove()
            })
        }, function (errorText, error) {
            injectFloatingMsg(errorText)
        })
    }

    function listaUtenti(ab, Z, status, mixed, completeObject, contactExit) {
        if (mixed == "") {
            mixed = jchat_defaultstatus
        }
        var statusAwayInfo = "";
        if (status.indexOf("|") > 0) {
            var statusInfoArray = status.split("|");
            status = statusInfoArray[0];
            var statusAwayTime = statusInfoArray[1];
            statusAwayInfo = '<span class="jchat_timeinfo">' + statusAwayTime + "</span>"
        } else {
            if (status === "available" && !completeObject.lastmessagetime) {
                status = "neveractive"
            }
        }
        var exists = !!($("#jchat_userlist_" + ab).length);
        var wasOwned = $("#jchat_userlist_" + ab + " span.jchat_contact").attr("data-owned");
        var wasContact = $("#jchat_userlist_" + ab + " span.jchat_contact").attr("data-contact");
        var isInitialize = e("initialize") == "0" ? false : true;
        var storedConferenceButtonsState = null;
        var storedBandwidth = null;
        if ($("#jchat_userlist_" + ab).length > 0) {
            $("#jchat_user_" + ab + " .jchat_closebox_bottom_status").removeClass("jchat_available");
            $("#jchat_user_" + ab + " .jchat_closebox_bottom_status").removeClass("jchat_busy");
            $("#jchat_user_" + ab + " .jchat_closebox_bottom_status").removeClass("jchat_away");
            $("#jchat_user_" + ab + " .jchat_closebox_bottom_status").removeClass("jchat_offline");
            $("#jchat_user_" + ab + " .jchat_closebox_bottom_status").removeClass("jchat_disconnected");
            $("#jchat_user_" + ab + " .jchat_closebox_bottom_status").addClass("jchat_" + status);
            $("#jchat_user_" + ab + " .jchat_closebox_bottom_status").html(statusAwayInfo);
            if (completeObject.imbanned) {
                $("#jchat_user_" + ab + "_popup .jchat_tabcontentinput").addClass("jchat_textarea_banned").html('<span class="banned_msg">' + jchat_banneduser + "</span>");
                $(".jchat_trigger_webrtc, .jchat_trigger_fileupload, .jchat_trigger_emoticon", "#jchat_user_" + ab + "_popup").hide();
                if ($("#jchat_privatemessaging_textarea[data-loggedid=" + completeObject.loggedid + "]").length) {
                    $("#jchat_privatemessaging_textarea, div.jchat_userslist_ctrls").hide();
                    $("div.jchat_fullcolumn_input span.banned_msg").remove();
                    $("div.jchat_fullcolumn_input").append('<span class="banned_msg">' + jchat_banneduser + "</span>")
                }
            } else {
                if ($("#jchat_user_" + ab + "_popup .jchat_tabcontentinput").hasClass("jchat_textarea_banned")) {
                    $("#jchat_user_" + ab + "_popup .jchat_tabcontentinput").removeClass("jchat_textarea_banned").html(writeElement);
                    $(".jchat_trigger_webrtc, .jchat_trigger_fileupload, .jchat_trigger_emoticon", "#jchat_user_" + ab + "_popup").show()
                }
                if ($("#jchat_privatemessaging_textarea[data-loggedid=" + completeObject.loggedid + "]").length) {
                    $("#jchat_privatemessaging_textarea, div.jchat_userslist_ctrls").show();
                    $("div.jchat_fullcolumn_input span.banned_msg").remove()
                }
            }
            if (!completeObject.isguest && !isGuest && !$("#jchat_user_" + ab + "_popup .jchat_trigger_history").length && messagesHistory) {
                $('<div class="jchat_trigger_history"></div>').appendTo("#jchat_user_" + ab + "_popup div.messagelist").on("mouseover", function (event) {
                    triggerGenericPopover("#jchat_trigger_history_tooltip", this, jchat_trigger_history)
                }).on("mouseout", function (event) {
                    $("#jchat_trigger_history_tooltip").css("display", "none")
                });
                bindTooltipEvents(".jchat_trigger_history", ab, completeObject)
            }
            $("#jchat_userlist_" + ab).remove();
            if (typeof(JChatConference) !== "undefined") {
                storedConferenceButtonsState = $("div.jchat_conference_btns", "#jchat_conference_userslist li[data-sessionid=" + ab + "]").html();
                storedBandwidth = $("div.jchat_peer_bandwidth", "#jchat_conference_userslist li[data-sessionid=" + ab + "]").html();
                $("#jchat_conference_userslist li[data-sessionid=" + ab + "]").remove()
            }
        }
        if ($("#jchat_private_messaging").length) {
            if (statusAwayInfo) {
                $("span.jchat_usersbox_status[data-userid=" + completeObject.loggedid + "]").append(statusAwayInfo)
            } else {
                $("span.jchat_usersbox_status[data-userid=" + completeObject.loggedid + "]").addClass("jchat_online")
            }
        }
        if (Z.length > tabLength) {
            shortname = Z.substr(0, tabLength) + "..."
        } else {
            shortname = Z
        }
        if ($("div.jchat_tab_shortname a", "#jchat_user_" + ab + "_popup").length) {
            $("div.jchat_tab_shortname a", "#jchat_user_" + ab + "_popup").text(shortname)
        } else {
            $("div.jchat_tab_shortname", "#jchat_user_" + ab + "_popup").text(shortname)
        }
        if (Z.length > buddyLength) {
            longname = Z.substr(0, buddyLength) + "..."
        } else {
            longname = Z
        }
        var userAvatar = "";
        if (typeof completeObject.avatar !== "undefined" && completeObject.avatar !== "") {
            if (completeObject.avatar !== null && avatarEnabled === true) {
                userAvatar = '<img width="32px" height="32px"  src="' + completeObject.avatar + '" />'
            }
        }
        var userSnippet = $("<div/>").attr("id", "jchat_userlist_" + ab).attr("data-loggedid", completeObject.loggedid).attr("data-sessionid", ab).addClass("jchat_userlist").html('<span class="jchat_userscontentname">' + userAvatar + '<span class="jchat_banning"></span><span class="jchat_ban_moderator"></span>' + longname + '</span><span class="jchat_contact"></span>' + statusAwayInfo + '<span class="jchat_userscontentdot jchat_' + status + '"></span>').data("id", ab);
        if ((longname.indexOf(guestPrefix)) === 0) {
            userSnippet.appendTo($("#jchat_userstab_popup .jchat_tabcontent .jchat_userscontent .jchat_userslist_" + status))
        } else {
            userSnippet.prependTo($("#jchat_userstab_popup .jchat_tabcontent .jchat_userscontent .jchat_userslist_" + status))
        }
        if (typeof(JChatConference) !== "undefined" && (completeObject.loggedid || jchat_conference_access_guest) && userPermissions.allow_videochat) {
            var conferenceButtons = storedConferenceButtonsState ? '<div class="jchat_conference_btns">' + storedConferenceButtonsState + "</div>" : '<div class="jchat_conference_btns"><div class="jchat_end_decline_call jchat_disabled"></div><div class="jchat_start_accept_call"></div></div>';
            var bandWidthInfo = storedBandwidth ? '<div class="jchat_peer_bandwidth">' + storedBandwidth + "</div>" : '<div class="jchat_peer_bandwidth"><span>0.0 mbits/s</span></div>';
            var conferenceUserSnippet = '<li class="jchat_userbox" data-sessionid="' + ab + '" data-username="' + longname + '"><div class="jchat_usertab_container"><span class="jchat_usersbox_name">' + userAvatar + '<span class="jchat_usersbox_textname">' + longname + "</span>" + statusAwayInfo + '<span class="jchat_userscontentdot jchat_' + status + '"></span></span>' + bandWidthInfo + conferenceButtons + "</div></li>";
            var usersListSearch = $.trim($("#jchat_leftusers_search").val());
            if (usersListSearch) {
                var re = new RegExp(usersListSearch, "gi");
                if (longname.match(re)) {
                    $(conferenceUserSnippet).prependTo("#jchat_conference_userslist")
                }
            } else {
                $(conferenceUserSnippet).prependTo("#jchat_conference_userslist")
            }
        }
        if (privateChatEnabled) {
            $("#jchat_userlist_" + ab).mouseover(function () {
                $(this).addClass("jchat_userlist_hover")
            });
            $("#jchat_userlist_" + ab).mouseout(function () {
                $(this).removeClass("jchat_userlist_hover")
            });
            $("#jchat_userlist_" + ab).on("click dblclick", function (jqEvent, caller) {
                if (!caller) {
                    var lengthOfArray = Object.keys(openedChatboxes).length;
                    openedChatboxes[ab] = lengthOfArray
                }
                createPrivateMessagesPopup(ab, Z, status, mixed, statusAwayInfo, completeObject)
            })
        } else {
            $("#jchat_userlist_" + ab).css({cursor: "auto"})
        }
        $("span.jchat_contact", "#jchat_userlist_" + ab).attr("data-userid", ab);
        $("span.jchat_banning", "#jchat_userlist_" + ab).attr("data-userid", ab);
        $("span.jchat_ban_moderator", "#jchat_userlist_" + ab).attr("data-userid", ab).attr("data-loggedid", completeObject.loggedid);
        if (completeObject.iscontact && completeObject.isowner) {
            $("span.jchat_contact", "#jchat_userlist_" + ab).attr("data-contact", 1).attr("data-name", completeObject.name);
            $("span.jchat_contact", "#jchat_userlist_" + ab).addClass("active").attr("data-text", jchat_remove);
            if (!wasContact && !isInitialize && exists) {
                injectFloatingMsg(jchat_groupchat_request_accepted_owner, completeObject.name);
                JChatNotifications.playSentFile()
            }
        } else {
            if (completeObject.iscontact || completeObject.isowner) {
                $("span.jchat_contact", "#jchat_userlist_" + ab).attr("data-owned", 1);
                $("span.jchat_contact", "#jchat_userlist_" + ab).addClass("owned").attr("data-text", jchat_pending);
                if (!wasOwned && !isInitialize && exists) {
                    injectFloatingMsg(jchat_groupchat_request_received, completeObject.name);
                    JChatNotifications.playSentFile()
                }
            } else {
                $("span.jchat_contact", "#jchat_userlist_" + ab).attr("data-contact", 0);
                $("span.jchat_contact", "#jchat_userlist_" + ab).addClass("noactive").attr("data-text", jchat_invite)
            }
        }
        $("#jchat_userlist_" + ab).on("click dblclick", "span.jchat_contact", function (event) {
            event.stopPropagation();
            var userid = $(this).attr("data-userid");
            var activeContact = $(this).attr("data-contact");
            var activeOwned = $(this).attr("data-owned");
            var isClient = (completeObject.iscontact && !completeObject.isowner) ? true : false;
            var isOwner = (completeObject.isowner && !completeObject.iscontact) ? true : false;
            if (isClient) {
                $(this).removeClass("owned").addClass("noactive").attr("data-contact", 0).attr("data-owned", 0);
                updateGroupChatUsers("groupchat.deleteEntity", userid);
                injectFloatingMsg(jchat_groupchat_request_removed, completeObject.name)
            } else {
                if (isOwner) {
                    if (activeContact) {
                        $(this).removeClass("owned").addClass("noactive").attr("data-contact", 0).attr("data-owned", 0);
                        updateGroupChatUsers("groupchat.deleteEntity", userid);
                        injectFloatingMsg(jchat_groupchat_request_removed, completeObject.name)
                    } else {
                        if (activeOwned) {
                            $(this).removeClass("noactive").addClass("active").attr("data-owned", 0).attr("data-contact", 1).attr("data-name", completeObject.name);
                            updateGroupChatUsers("groupchat.saveEntity", userid);
                            injectFloatingMsg(jchat_groupchat_request_accepted)
                        }
                    }
                } else {
                    if (activeContact != 1 && activeOwned != 1) {
                        $(this).removeClass("noactive").addClass("owned").attr("data-owned", 1);
                        updateGroupChatUsers("groupchat.saveEntity", userid);
                        injectFloatingMsg(jchat_groupchat_request_sent, completeObject.name)
                    } else {
                        $(this).removeClass("active").addClass("noactive").attr("data-contact", 0).attr("data-owned", 0);
                        updateGroupChatUsers("groupchat.deleteEntity", userid);
                        injectFloatingMsg(jchat_groupchat_request_removed, completeObject.name)
                    }
                }
            }
        });
        if (completeObject.isbanned) {
            $("span.jchat_banning", "#jchat_userlist_" + ab).data("banned", 1).addClass("banned");
            $("#jchat_chatroom_userslist span.jchat_banning[data-userid=" + ab + "]").data("banned", 1).addClass("banned")
        }
        $("#jchat_userlist_" + ab).on("click dblclick", "span.jchat_banning", function (event, triggerElement) {
            event.stopPropagation();
            var bannedUserSessionId = $(this).data("userid");
            var bannedCurrentState = !$(this).data("banned");
            var bannedObject = {};
            bannedObject.userSessionId = bannedUserSessionId;
            bannedObject.currentState = bannedCurrentState;
            postGenericStatus("bannedinfo", JSON.stringify(bannedObject));
            var currentRefreshedState = bannedCurrentState ? 1 : 0;
            $(this).toggleClass("banned").data("banned", currentRefreshedState);
            if (!triggerElement) {
                $("#jchat_chatroom_userslist span.jchat_banning[data-userid=" + bannedUserSessionId + "]").toggleClass("banned")
            }
        });
        $("#jchat_userlist_" + ab).on("click dblclick", "span.jchat_ban_moderator", function (event) {
            event.stopPropagation();
            $(this).toggleClass("banned");
            var bannedObject = {};
            bannedObject.userSessionId = $(this).data("userid");
            bannedObject.userId = $(this).data("loggedid");
            postGenericStatus("bannedmoderatorinfo", JSON.stringify(bannedObject));
            setTimeout(function (target) {
                $(target).parents("div.jchat_userlist").slideUp(250)
            }, 500, this)
        });
        $("#jchat_userlist_" + ab).on("addmessage", function (event, testomessaggio, af, ae, idmessaggio, messageObject, lastMessageReached, isPrivateMessagingOpened) {
            var lengthOfArray = Object.keys(openedChatboxes).length;
            openedChatboxes[ab] = lengthOfArray;
            if (!isPrivateMessagingOpened && !($("#jchat_user_" + ab + "_popup").length)) {
                if (typeof(jchat_auto_open_msgspopup) !== "undefined") {
                    if (jchat_auto_open_msgspopup == 0) {
                        var privateMessagingListitem = $("#jchat_userslist li.jchat_userbox[data-userid=" + completeObject.loggedid + "]", "#jchat_private_messaging");
                        var currentCounting = privateMessagingCounters[completeObject.loggedid] || 0;
                        currentCounting++;
                        var privateMessagingNotifier = '<span class="jchat_newmessages_notifier">' + currentCounting + "</span>";
                        $("span.jchat_usersbox_name", privateMessagingListitem).next(".jchat_newmessages_notifier").remove().end().after(privateMessagingNotifier);
                        privateMessagingCounters[completeObject.loggedid] = currentCounting;
                        $.jStorage.set("privateMessagingCounters", privateMessagingCounters, {TTL: (3600 * 1000)})
                    } else {
                        createPrivateMessagesPopup(ab, Z, status, mixed, statusAwayInfo, completeObject)
                    }
                } else {
                    createPrivateMessagesPopup(ab, Z, status, mixed, statusAwayInfo, completeObject)
                }
            }
            if (!messageObject.id && T == ab && af == 1) {
                var selfClass = "";
                if (af == 1) {
                    if (my_avatar !== undefined && my_avatar !== null && my_avatar !== "" && avatarEnabled === true) {
                        fromPlaceholder = '<img alt="' + jchat_you + my_username + '" data-time="' + messageObject.time + '" width="32px" height="32px" src="' + my_avatar + '" />'
                    } else {
                        fromPlaceholder = '<strong data-time="' + messageObject.time + '">' + my_username + "</strong>"
                    }
                    selfClass = " selfmessage"
                }
                testomessaggio = jchatStripTags(testomessaggio, allowMediaObjects);
                testomessaggio = testomessaggio.replace(/&/gi, "&amp;");
                if ($("#jchat_message_" + idmessaggio).length > 0) {
                } else {
                    $("#jchat_user_" + ab + "_popup .jchat_tabcontenttext").append('<div class="jchat_chatboxmessage' + selfClass + '" id="jchat_message_' + idmessaggio + '"><span class="jchat_chatboxmessagefrom">' + fromPlaceholder + '</span><span class="jchat_chatboxmessagecontent' + selfClass + '">' + testomessaggio + "</span></div>");
                    $("span.jchat_chatboxmessagefrom img, span.jchat_chatboxmessagefrom strong").mouseover(function (event) {
                        avatarTooltip(this, $(this).attr("alt"), $(this).data("time"))
                    }).mouseout(function (event) {
                        $("#jchat_avatartooltip").remove()
                    })
                }
            }
            if (T != ab && ae == 0) {
                tabAlert(ab, 1, 1, messageObject, lastMessageReached)
            } else {
                if (audio == 1 && af == 0 && ae == 0 && messageObject.type != "file") {
                    JChatNotifications.playMessageAlert(messageObject)
                } else {
                    if (audio == 1 && af == 0 && ae == 0 && messageObject.type == "file") {
                        JChatNotifications.playSentFile(messageObject)
                    }
                }
            }
            if (ae == 0) {
                var target = $("#jchat_user_" + messageObject.from + "_popup .jchat_textarea");
                if (target.length && !$(target).is(":focus") && !$(target).data("isblinking")) {
                    $(target).blink();
                    $(target).data("isblinking", true)
                }
            }
        });
        $("#jchat_userlist_" + ab).on("getname", function (ac) {
            if (Z.length > chatLength) {
                return Z.substr(0, chatLength) + "..."
            } else {
                return Z
            }
        });
        $("#jchat_userlist_" + ab).on("getavatar", function (ac) {
            return completeObject.avatar
        });
        if (completeObject.skypeid) {
            injectSkypeControls(completeObject)
        }
    }

    function tabAlert(mixed, Y, Z, messageObject, lastMessageReached) {
        if (audio == 1 && messageObject.type != "file") {
            JChatNotifications.playMessageAlert(messageObject)
        } else {
            if (audio == 1 && messageObject.type == "file") {
                JChatNotifications.playSentFile(messageObject)
            }
        }
    }

    function initializeDom() {
        $("<span/>").attr("id", "jchat_userstab").addClass("jchat_tab").css("display", "none").html('<span id="jchat_userstab_icon"></span><span id="jchat_userstab_text" style="float:left">' + chatTitle + "</span>").appendTo($("#jchat_base"));
        $("<span/>").attr("id", "jchat_closesidebarbutton").addClass("jchat_tab").addClass("jchat_exitimages").appendTo($("#jchat_userstab"));
        $("<span/>").attr("id", "jchat_optionsbutton").addClass("jchat_tab").addClass("jchat_optionsimages").appendTo($("#jchat_userstab"));
        $("<span/>").attr("id", "jchat_maximizebutton").attr("data-translation", "jchat_maximizebutton").addClass("jchat_tab").addClass("jchat_maximizeimages").appendTo($("#jchat_userstab"));
        $("<div/>").attr("id", "jchat_userstab_popup").attr("dir", "ltr").addClass("jchat_tabpopup").css("display", "none").html('<div class="jchat_userstabtitle"><span class="jchat_privatechattitle">' + jchat_privatechat + '</span><input id="jchat_users_search" type="text" value="' + jchat_search + '"/><div id="jchat_myusername"></div></div><div class="jchat_tabcontent userslist"><div class="jchat_userscontent"><div class="jchat_userslist_available"></div><div class="jchat_userslist_away"></div><div class="jchat_userslist_neveractive"></div><div class="jchat_userslist_busy"></div></div>').appendTo($(jchatTargetElement));
        $("#jchat_userstab").mouseover(function () {
            $(this).addClass("jchat_tabmouseover")
        });
        $("#jchat_userstab").mouseout(function () {
            $(this).removeClass("jchat_tabmouseover")
        });
        $(document).on("click", "#jchat_userstab", function (event) {
            if (o == 1) {
                o = 0;
                $("#jchat_userstab_text").html(chatTitle);
                ajaxReceive();
                $("#jchat_optionsbutton_popup .available").click()
            }
            s.options = 0;
            if (renderingMode == "module" && separateWidgets) {
            } else {
                $("#jchat_wall_popup").toggleClass("jchat_tabopen")
            }
            if ($(this).hasClass("jchat_tabclick")) {
                restartUpdateSession("buddylist", "0");
                if (!$.jStorage.get("default_suggestion")) {
                    $("#jchat_default_suggestion_tooltip").show()
                }
            } else {
                restartUpdateSession("buddylist", "1");
                $("#jchat_default_suggestion_tooltip").hide();
                makeSidebarResizable();
                if ($("#jchat_wall_popup div.jchat_tabcontenttext").length) {
                    $("#jchat_wall_popup div.jchat_tabcontenttext").scrollTop($("#jchat_wall_popup div.jchat_tabcontenttext")[0].scrollHeight)
                }
            }
            $("#jchat_userstab_popup").css("right", "0").css("bottom", "0");
            $(this).toggleClass("jchat_tabclick").toggleClass("jchat_userstabclick");
            if (buddyListVisible) {
                if (!allowGuestBuddylist && isGuest) {
                } else {
                    $("#jchat_userstab_popup").toggleClass("jchat_tabopen")
                }
            }
            $("#jchat_newpublic_msg").remove();
            clearInterval(wallMessagesAlertInterval)
        });
        $(document).on("click", "#jchat_default_suggestion_tooltip", function () {
            $("#jchat_userstab").click()
        });
        var defaultGenericTooltips = ["jchat_trigger_emoticon", "jchat_trigger_fileupload", "jchat_trigger_export", "jchat_trigger_delete", "jchat_trigger_refresh", "jchat_trigger_skypesave", "jchat_trigger_skypedelete", "jchat_trigger_override_name", "jchat_trigger_infoguest", "jchat_trigger_room", "jchat_trigger_webrtc", "jchat_roomname", "jchat_roomcount", "jchat_banning", "jchat_ban_moderator", "jchat_trigger_history", "jchat_trigger_history_wall", "jchat_skype", "jchat_quality_cam", "jchat_onoffswitch", "jchat_trigger_messaging_emoticon", "jchat_trigger_messaging_fileupload", "jchat_trigger_messaging_export", "jchat_trigger_messaging_delete", "jchat_trigger_messaging_openbox", "jchat_trigger_send", "jchat_trigger_geolocation", "jchat_source_language", "jchat_target_language", "jchat_lang_switcher", "jchat_translate_arrow", "jchat_busy", "jchat_start_accept_call", "jchat_end_decline_call", "jchat_start_recording", "jchat_pause_recording", "jchat_stop_recording", "jchat_view_recording", "jchat_download_recording", "jchat_send_recording", "jchat_upload_recording"];
        $.each(defaultGenericTooltips, function (k, elem) {
            $(document).on("mouseover", "." + elem, function (event) {
                triggerGenericPopover("#" + elem + "_tooltip", this, eval(elem))
            }).on("mouseout", "." + elem, function (event) {
                $("#" + elem + "_tooltip").css("display", "none")
            })
        });
        $(document).on("mouseover", ".jchat_contact", function (event) {
            triggerGenericPopover("#jchat_contact_tooltip", this)
        }).on("mouseout", ".jchat_contact", function (event) {
            $("#jchat_contact_tooltip").css("display", "none")
        });
        $("#jchat_users_search").focusin(function () {
            $(this).val("")
        }).focusout(function () {
            $(this).val(jchat_search);
            forceBuddylistRefresh = 0
        }).keyup(function () {
            restartUpdateSession("buddylist", "1");
            forceBuddylistRefresh = 1
        });
        $("head").append('<style>.jchat_tab_shortname a:hover::before{ content:"' + jchat_userprofile_link + '" }</style>');
        $(window).focus(function () {
            isTabFocused = true;
            $("title").text(originalPageTitle)
        });
        $(window).blur(function () {
            isTabFocused = false
        });
        $(document).on("click", "#jchat_tooltip_close", function (jqEvent) {
            $("#jchat_default_suggestion_tooltip").remove();
            jqEvent.stopPropagation();
            $.jStorage.set("default_suggestion", 1, {TTL: (3600 * 1000)})
        });
        if ($.jStorage.get("buddylistMaximized", false)) {
            $("#jchat_maximizebutton").trigger("click", [true])
        }
    }

    function initializeOptionsDiv() {
        $("<div/>").attr("id", "jchat_optionsbutton_popup").attr("dir", "ltr").addClass("jchat_tabpopup").css("display", "none").html('<div class="jchat_tabcontent"><span style="float:left" class="jchat_user_available"></span><span class="jchat_optionsstatus available">' + jchat_available + '</span><br clear="all"/><span class="jchat_optionsstatus2 jchat_user_offline"></span><span class="jchat_optionsstatus offline">' + jchat_statooffline + '</span><br clear="all"/><span class="jchat_optionsstatus2 jchat_user_donotdisturb"></span><span class="jchat_optionsstatus jchat_donotdisturb">' + jchat_statodonotdisturb + '</span><br clear="all"/><span class="jchat_optionsstatus2 jchat_reset_chatboxes"></span><span class="jchat_optionsstatus jchat_resetposition">' + jchat_reset_chatboxes + '</span><br clear="all"/><span class="jchat_optionsstatus2 jchat_minimize_chatboxes"></span><span class="jchat_optionsstatus jchat_minimizeall">' + jchat_minimize_chatboxes + '</span><br clear="all"/><span class="jchat_optionsstatus2 jchat_open_privatemess"></span><span class="jchat_optionsstatus jchat_privatemess" data-uri="' + jchat_privatemess_uri + '">' + jchat_open_privatemess + '</span><br clear="all"/><div class="jchat_override_name"><span class="jchat_options_override_name">' + jchat_insert_override_name + '</span></div><div><input type="text" id="override_name" value=""/><div id="jchat_trigger_override_name" class="jchat_trigger_override_name"></div></div><br clear="all"/><div id="jchat_avatar" class="jchat_avatar">Avatar</div><div class="jchat_sounds">' + jchat_audio_onoff + '</div><div class="jchat_wall_sounds">' + jchat_public_audio_onoff + '</div><br clear="all"/><div class="jchat_skype"><span class="jchat_options_skype">' + jchat_insert_skypeid + '</span></div><input type="text" id="skype_id" value=""/><div id="jchat_trigger_skypesave" class="jchat_trigger_skypesave"></div><div id="jchat_trigger_skypedelete" class="jchat_trigger_skypedelete"></div></div>').appendTo($(jchatTargetElement));
        if (JChatNotifications.supportVibration && jchatHasTouch()) {
            $("#jchat_optionsbutton_popup .jchat_sounds").before('<div class="jchat_vibrate">' + jchat_vibrate_onoff + "</div>");
            $("div.jchat_vibrate").on("click", function (jqEvent) {
                vibrate = vibrate == 1 ? 0 : 1;
                restartUpdateSession("vibrate", vibrate);
                $(this).toggleClass("novibrate");
                JChatNotifications.setVibrateStatus(vibrate)
            })
        }
        if (JChatNotifications.supportNotification) {
            $("#jchat_optionsbutton_popup .jchat_sounds").before('<div class="jchat_notification">' + jchat_notification_onoff + "</div>");
            $("div.jchat_notification").on("click", function (jqEvent) {
                notification = notification == 0 ? 1 : 0;
                restartUpdateSession("notification", notification);
                $(this).toggleClass("yesnotification");
                JChatNotifications.setNotificationStatus(notification).requestNotificationPermission()
            })
        }
        $("#jchat_optionsbutton_popup .jchat_minimizeall").on("click", function (jqEvent) {
            minimizedAllChatboxes = minimizedAllChatboxes == 0 ? 1 : 0;
            restartUpdateSession("minimizedallchatboxes", minimizedAllChatboxes);
            $(this).prev().toggleClass("yesminimized");
            var windowWidth = $(window).width();
            var windowHeight = $(window).height();
            var counterBottomedChatboxes = 1;
            var sidebarWidth = 0;
            if (renderingMode == "auto" || (renderingMode == "module" && separateWidgets)) {
                sidebarWidth = $("#jchat_userstab_popup").width()
            }
            if (minimizedAllChatboxes) {
                $("div[id^=jchat_user_].jchat_bottomed").draggable("disable").addClass("jchat_nodraggable")
            } else {
                $("div[id^=jchat_user_].jchat_bottomed").draggable("enable").removeClass("jchat_nodraggable")
            }
            var minimizeAllChatboxes = function ($currentPopup, calculatedLeft, calculatedTop) {
                $currentPopup.animate({
                    left: calculatedLeft + "px",
                    top: calculatedTop + "px",
                    "margin-left": 0,
                    "margin-top": 0,
                    bottom: "auto",
                    right: "auto"
                }, 500)
            };
            var maximizeAllChatBoxes = function (id, popupPosition, marginPosition) {
                $("#jchat_user_" + id + "_popup").animate({
                    left: popupPosition.left,
                    top: popupPosition.top,
                    height: "auto",
                    "margin-left": marginPosition.left,
                    "margin-top": marginPosition.top
                }, 500)
            };
            $.each(openedChatboxes, function (id, index) {
                if (minimizedAllChatboxes && !$("#jchat_user_" + id + "_popup").hasClass("minimized")) {
                    $("div.jchat_minimizebox_bottom", "#jchat_user_" + id).trigger("click", [true])
                } else {
                    if (!minimizedAllChatboxes && $("#jchat_user_" + id + "_popup").hasClass("minimized")) {
                        $("div.jchat_minimizebox_bottom", "#jchat_user_" + id).trigger("click", [true])
                    }
                }
                $("#jchat_user_" + id + "_popup .jchat_tabcontenttext").height(200);
                var $currentPopup = $("#jchat_user_" + id + "_popup").width(230);
                $.jStorage.deleteKey("popupDimensions");
                if (minimizedAllChatboxes) {
                    if (positionmentChatboxes == "bottom") {
                        if (!$currentPopup.hasClass("jchat_bottomed")) {
                            var calculatedLeft = (windowWidth - sidebarWidth - 230);
                            var calculatedTop = (windowHeight - 26 - (counterBottomedChatboxes * 26));
                            minimizeAllChatboxes($currentPopup, calculatedLeft, calculatedTop);
                            popupPositions[id] = {x: calculatedLeft, y: calculatedTop};
                            minimizedIndexes[id] = counterBottomedChatboxes;
                            counterBottomedChatboxes++
                        }
                    } else {
                        var calculatedLeft = (windowWidth - sidebarWidth - 230);
                        var calculatedTop = (windowHeight - (counterBottomedChatboxes * 26));
                        minimizeAllChatboxes($currentPopup, calculatedLeft, calculatedTop);
                        popupPositions[id] = {x: calculatedLeft, y: calculatedTop};
                        minimizedIndexes[id] = counterBottomedChatboxes;
                        counterBottomedChatboxes++
                    }
                } else {
                    if (positionmentChatboxes == "bottom") {
                        if (!$currentPopup.hasClass("jchat_bottomed")) {
                            var lengthOfArray = counterBottomedChatboxes;
                            var microSplacement = parseInt(lengthOfArray) / microSplacementKonstant;
                            var popupPosition = {left: (50 + microSplacement) + "%", top: (50 + microSplacement) + "%"};
                            var marginPosition = {left: "-115px", top: "-167px"};
                            maximizeAllChatBoxes(id, popupPosition, marginPosition);
                            try {
                                delete (popupPositions[id])
                            } catch (e) {
                            }
                            counterBottomedChatboxes++
                        }
                    } else {
                        var lengthOfArray = counterBottomedChatboxes;
                        var microSplacement = parseInt(lengthOfArray) / microSplacementKonstant;
                        var popupPosition = {left: (50 + microSplacement) + "%", top: (50 + microSplacement) + "%"};
                        var marginPosition = {left: "-115px", top: "-167px"};
                        maximizeAllChatBoxes(id, popupPosition, marginPosition);
                        try {
                            delete (popupPositions[id])
                        } catch (e) {
                        }
                        counterBottomedChatboxes++
                    }
                }
            });
            $.jStorage.set("popupPositions", popupPositions);
            $.jStorage.set("minimizedIndexes", minimizedIndexes)
        });
        $("#jchat_optionsbutton_popup .available").click(function (Y) {
            statusClassOp();
            $("#jchat_userstab_icon").addClass("jchat_user_available2");
            $(this).css("text-decoration", "underline");
            postGenericStatus("user_status", "available")
        });
        $("#jchat_optionsbutton_popup .offline").click(function (Y) {
            turnChatOffline();
            $(this).css("text-decoration", "underline");
            $("#jchat_optionsbutton_popup").removeClass("jchat_tabopen")
        });
        $("#jchat_optionsbutton_popup .jchat_donotdisturb").on("click", function (jqEvent) {
            donotdisturb = donotdisturb == 0 ? 1 : 0;
            postGenericStatus("user_status", (donotdisturb ? "busy" : "available"));
            $(this).prev().toggleClass("jchat_enabled");
            $("#jchat_userstab_popup .jchat_userstabtitle").toggleClass("jchat_donotdisturb")
        });
        $("#jchat_optionsbutton_popup .jchat_resetposition").click(function (jqEvent) {
            $.jStorage.deleteKey("popupPositions");
            $.jStorage.deleteKey("popupState");
            $.jStorage.deleteKey("popupMaximizedState");
            $.jStorage.deleteKey("popupDimensions");
            $.jStorage.deleteKey("nofluxchatboxes");
            $.jStorage.deleteKey("minimizedIndexes");
            popupPositions = {};
            popupDimensions = {};
            popupState = {};
            popupMaximizedState = {};
            nofluxChatBoxes = {};
            minimizedIndexes = {};
            minimizedAllChatboxes = 0;
            restartUpdateSession("minimizedallchatboxes", minimizedAllChatboxes);
            $(".jchat_minimize_chatboxes").removeClass("yesminimized");
            var memoizeToReopen = $.extend({}, openedChatboxes);
            $.each(openedChatboxes, function (id, index) {
                $("#jchat_user_" + id + "_popup div.jchat_closebox_bottom").trigger("click");
                $("#jchat_user_" + id + " .jchat_minimizebox_bottom").data("minimized", false)
            });
            $.each(memoizeToReopen, function (id, index) {
                $("#jchat_userlist_" + id).trigger("click")
            })
        });
        $("#jchat_optionsbutton_popup .jchat_privatemess").click(function () {
            var pmUrl = $(this).data("uri");
            window.location.href = pmUrl
        });
        $("#jchat_trigger_override_name").click(function () {
            var nameValue = $(this).prev().val();
            postGenericStatus("override_name", nameValue);
            if (nameValue) {
                injectFloatingMsg(jchat_override_name_saved, nameValue)
            } else {
                injectFloatingMsg(jchat_override_name_deleted, null)
            }
        });
        $("#jchat_trigger_skypesave").click(function () {
            var skypeIdValue = $(this).prev().val();
            postGenericStatus("skypeid", skypeIdValue);
            if (skypeIdValue) {
                injectFloatingMsg(jchat_skypeidsaved, skypeIdValue)
            } else {
                injectFloatingMsg(jchat_skypeid_deleted, null)
            }
        });
        $("#jchat_trigger_skypedelete").click(function () {
            $(this).prevAll("input").val(null);
            postGenericStatus("skypeid", null);
            injectFloatingMsg(jchat_skypeid_deleted, null)
        });
        $(document).on("mouseover", "#jchat_optionsbutton, #jchat_closesidebarbutton, #jchat_loginbutton", function () {
            if ($("#jchat_optionsbutton_popup, #jchat_loginbutton_popup").hasClass("jchat_tabopen")) {
                return
            }
            hoverSidebarTooltips(this, eval($(this).attr("id")))
        });
        $(document).on("mouseover", "#jchat_maximizebutton", function () {
            if ($("#jchat_optionsbutton_popup, #jchat_loginbutton_popup").hasClass("jchat_tabopen")) {
                return
            }
            var appendedStateClass = $(this).hasClass("maximized") ? "_maximized" : "_minimized";
            hoverSidebarTooltips(this, eval($(this).data("translation") + appendedStateClass))
        });
        $(document).on("mouseout", "#jchat_optionsbutton, #jchat_closesidebarbutton, #jchat_loginbutton, #jchat_maximizebutton", function () {
            $(this).removeClass("jchat_tabmouseover");
            $("#jchat_tooltip").css("display", "none")
        });
        $(document).on("click", "#jchat_optionsbutton", function (event) {
            if (r == 0) {
                if (o == 1) {
                    o = 0;
                    $("#jchat_userstab_text").html(chatTitle);
                    ajaxReceive();
                    $("#jchat_optionsbutton_popup .available").click()
                }
                $("#jchat_optionsbutton_popup").toggleClass("jchat_tabopen")
            }
            $("#jchat_optionsbutton").trigger("mouseout");
            $("#jchat_loginbutton_popup").remove();
            popupsReposition();
            event.stopPropagation();
            return false
        });
        $(document).on("click", "#jchat_loginbutton", function (event) {
            $("#jchat_loginbutton").trigger("mouseout");
            if (!$("#jchat_loginbutton_popup").length) {
                $('<div id="jchat_loginbutton_popup" class="jchat_tabpopup jchat_tabopen"></div>').appendTo("body");
                if (isGuest) {
                    $('<div id="jchat_loginpopup_desc">' + jchat_logindesc + "</div>").appendTo("#jchat_loginbutton_popup");
                    if (facebookLoginActive) {
                        $("<span/>").attr("id", "jchat_loginbutton_facebook").appendTo("#jchat_loginbutton_popup");
                        $("#jchat_loginbutton_facebook").on("click", jchatfblogin)
                    }
                    if (googleLoginActive) {
                        $("<span/>").attr("id", "jchat_loginbutton_google").appendTo("#jchat_loginbutton_popup");
                        $("#jchat_loginbutton_google").on("click", function (jqEvent) {
                            if (typeof(jchatGPlusLoginURL) !== "undefined") {
                                window.location.href = jchatGPlusLoginURL
                            }
                        })
                    }
                    if (twitterLoginActive) {
                        $("<span/>").attr("id", "jchat_loginbutton_twitter").appendTo("#jchat_loginbutton_popup");
                        $("#jchat_loginbutton_twitter").on("click", function (jqEvent) {
                            if (typeof(jchatTwitterLoginURL) !== "undefined") {
                                window.location.href = jchatTwitterLoginURL
                            }
                        })
                    }
                } else {
                    $('<span class="jchat_loginbutton_logged">' + jchat_already_logged + my_username + "</span>").appendTo("#jchat_loginbutton_popup")
                }
                popupsReposition()
            } else {
                $("#jchat_loginbutton_popup").remove()
            }
            event.stopPropagation();
            return false
        });
        $(document).on("click", "body > *:not(#jchat_optionsbutton_popup)", function (jqEvent) {
            if ($(jqEvent.target).attr("id") != "jchat_optionsbutton" && !$(jqEvent.target).parents("#jchat_optionsbutton_popup").length) {
                $("#jchat_optionsbutton_popup").removeClass("jchat_tabopen")
            }
        });
        $(document).on("click", "#jchat_maximizebutton", function (jqEvent, keepStatus) {
            if (r == 0) {
                if (o == 1) {
                    o = 0;
                    $("#jchat_userstab_text").html(chatTitle);
                    ajaxReceive();
                    $("#jchat_optionsbutton_popup .available").click()
                }
                $(this).toggleClass("maximized");
                $("#jchat_userstab_popup").toggleClass("maximized");
                $("#jchat_userstab_popup div.jchat_tabcontent.userslist").toggleClass("maximized");
                $("#jchat_userstab").toggleClass("maximized");
                $("#jchat_wall_popup").toggleClass("hidden")
            }
            popupsReposition();
            if (!keepStatus) {
                var invertStatus = !($.jStorage.get("buddylistMaximized", false));
                $.jStorage.set("buddylistMaximized", invertStatus);
                buddylistMaximized = invertStatus
            }
            jqEvent.stopPropagation();
            return false
        });
        $("#jchat_optionsbutton_popup .jchat_userstabtitle").click(function () {
            $("#jchat_optionsbutton_popup").toggleClass("jchat_tabopen")
        });
        $("#jchat_optionsbutton_popup .jchat_userstabtitle").mouseenter(function () {
            $(this).addClass("jchat_chatboxtabtitlemouseover2")
        });
        $("#jchat_optionsbutton_popup .jchat_userstabtitle").mouseleave(function () {
            $(this).removeClass("jchat_chatboxtabtitlemouseover2")
        });
        $("#jchat_avatar").toggle(function (event) {
            avatarUploadTooltip(this)
        }, function (event) {
            $("#jchat_avatar").prevAll(".jchat_avatar_upload_tooltip, .jchat_overlay").remove()
        });
        $("div.jchat_sounds").on("click", function (event) {
            audio = audio == 1 ? 0 : 1;
            restartUpdateSession("audio", audio);
            $(this).toggleClass("noaudio");
            JChatNotifications.setAudioStatus(audio)
        });
        $("div.jchat_wall_sounds").on("click", function (event) {
            wallaudio = wallaudio == 1 ? 0 : 1;
            restartUpdateSession("wallaudio", wallaudio);
            $(this).toggleClass("noaudio");
            JChatNotifications.setWallAudioStatus(wallaudio)
        })
    }

    function initializeWall() {
        var sendTrigger = "";
        if (jchat_wall_sendbutton == 1 || (jchat_wall_sendbutton == 2 && jchatHasTouch())) {
            sendTrigger = '<div data-id="wall" class="jchat_trigger_send jchat_trigger_send_wall"></div>';
            $(document).on("click", "div.jchat_trigger_send[data-id=wall]", function (jqEvent) {
                var customEvent = $.Event("keydown");
                customEvent.keyCode = 13;
                customEvent.shiftKey = false;
                $("#jchat_wall_popup .jchat_textarea").trigger(customEvent)
            })
        }
        $("<div/>").attr("id", "jchat_wall_popup").addClass("jchat_tabpopup jchat_walltab_popup").attr("dir", "ltr").css("display", "none").html('<div class="jchat_userstabtitle"><span class="jchat_publicchattitle">' + jchat_wall_msgs + '</span></div><div class="jchat_tabcontent messagelist"><div class="jchat_tabcontenttext jchat_tabcontenttext_wall"></div><div class="jchat_tabcontentinput">' + writeElementWall + '</div><div class="jchat_trigger_emoticon"></div><div class="jchat_trigger_room"></div><div class="jchat_trigger_users_informations"></div><div class="jchat_trigger_export"></div><div class="jchat_trigger_refresh"></div><div class="jchat_trigger_delete"></div><div class="jchat_trigger_history_wall"></div>' + sendTrigger + "</div>").appendTo($(jchatTargetElement));
        $("#jchat_wall_popup div.jchat_userstabtitle").append('<span id="jchat_maximize_wall" data-translation="jchat_maximizebutton" class="jchat_maximizeimages"></span>');
        $("#jchat_maximize_wall").toggle(function (jqEvent) {
            $("#jchat_wall_popup").addClass("maximized");
            var windowHeight = window.innerHeight ? window.innerHeight : $(window).height();
            $("div.jchat_tabcontenttext_wall").height(windowHeight - 103);
            $("#jchat_wall_popup .jchat_userstabsubtitle").width($("#jchat_wall_popup").width() - 7);
            $("#jchat_wall_popup").addClass("maximized");
            $(this).addClass("maximized");
            popupsReposition(true);
            $("#jchat_wall_popup .jchat_userstabtitle").off("click");
            jqEvent.stopPropagation();
            return false
        }, function (jqEvent) {
            $("#jchat_wall_popup").removeClass("maximized");
            var parentDivHeight = $("div.jchat_tabcontenttext_wall").parents("#jchat_wall_popup").height();
            $("div.jchat_tabcontenttext_wall").height(parentDivHeight - 103);
            $("#jchat_wall_popup .jchat_userstabsubtitle").width($("#jchat_wall_popup").width() - 7);
            $("#jchat_wall_popup").removeClass("maximized");
            $(this).removeClass("maximized");
            popupsReposition();
            $("#jchat_wall_popup .jchat_userstabtitle").on("click", minimizeAllWallContents);
            jqEvent.stopPropagation();
            return false
        });
        $(".jchat_trigger_emoticon", "#jchat_wall_popup").toggle(function (event) {
            var openedPopups = $('.jchat_tabcontent div[class*="tooltip"]', "#jchat_wall_popup").filter(function (index) {
                return $(this).css("display") !== "none"
            });
            if (!!openedPopups.length) {
                openedPopups.next().trigger("click")
            }
            emoticonsTooltip(this, "#jchat_wall_popup", null, true);
            $(this).addClass("toggle_on")
        }, function (event) {
            $(".jchat_emoticonstooltip", "#jchat_wall_popup").remove();
            $(this).removeClass("toggle_on")
        });
        $(".jchat_trigger_room", "#jchat_wall_popup").toggle(function (event) {
            var openedPopups = $('.jchat_tabcontent div[class*="tooltip"]', "#jchat_wall_popup").filter(function (index) {
                return $(this).css("display") !== "none"
            });
            if (!!openedPopups.length) {
                openedPopups.next().trigger("click")
            }
            roomsTooltip(this, "#jchat_wall_popup", true);
            $(this).addClass("toggle_on")
        }, function (event) {
            $(".jchat_roomstooltip", "#jchat_wall_popup").remove();
            $(this).removeClass("toggle_on")
        });
        $(".jchat_trigger_users_informations", "#jchat_wall_popup").toggle(function (event) {
            var openedPopups = $('.jchat_tabcontent div[class*="tooltip"]', "#jchat_wall_popup").filter(function (index) {
                return $(this).css("display") !== "none"
            });
            if (!!openedPopups.length) {
                openedPopups.next().trigger("click")
            }
            chatroomUsersInformationsTooltip(this, "#jchat_wall_popup", true);
            $(this).addClass("toggle_on")
        }, function (event) {
            $(".jchat_chatroom_usersinfo_tooltip", "#jchat_wall_popup").remove();
            $(this).removeClass("toggle_on")
        });
        $("#jchat_wall_popup div.jchat_trigger_refresh").click(function (event) {
            return fetchWallMessages(0)
        });
        $("#jchat_wall_popup div.jchat_trigger_history_wall").click(function (event, noScrollToTop) {
            return fetchWallMessages(1, noScrollToTop)
        });
        $("#jchat_wall_popup div.jchat_trigger_users_informations").mouseover(function () {
            wallTooltipUsers("div.jchat_trigger_users_informations");
            $(this).addClass("jchat_tabmouseover")
        });
        $("#jchat_wall_popup div.jchat_trigger_users_informations").mouseout(function () {
            $(this).removeClass("jchat_tabmouseover");
            $("#jchat_users_informations_tooltip").css("display", "none")
        });
        $("#jchat_wall_popup div.jchat_trigger_refresh").mousedown(function () {
            $(this).addClass("jchat_refresh_mousedown")
        }).mouseup(function () {
            $(this).removeClass("jchat_refresh_mousedown")
        });
        $("#jchat_wall_popup div.jchat_trigger_export").append("<a/>");
        $("#jchat_wall_popup div.jchat_trigger_export a").attr("href", htmlLiveSite + "&task=export.display&chatid=wall");
        $("#jchat_wall_popup div.jchat_trigger_delete").on("click", function () {
            deleteConversation("wall", true)
        });
        $("#jchat_wall_popup .jchat_textarea").keydown(function (event) {
            return sendWallMessage(event, this)
        });
        $("#jchat_wall_popup .jchat_textarea").keyup(function (af) {
            return popupWall(af, this, wallStartHeight)
        });
        $("#jchat_wall_popup .jchat_userstabtitle").mouseenter(function () {
            $(this).addClass("jchat_chatboxtabtitlemouseover2")
        });
        $("#jchat_wall_popup .jchat_userstabtitle").mouseleave(function () {
            $(this).removeClass("jchat_chatboxtabtitlemouseover2")
        });
        publicChatStateClosed = $.jStorage.get("publicChatStateClosed", false);
        buddylistMaximized = $.jStorage.get("buddylistMaximized", false);
        var minimizeAllWallContents = function (jqEvent) {
            $("#jchat_maximize_wall").toggle();
            $(this).nextAll().toggle();
            var invertStatus = !($.jStorage.get("publicChatStateClosed", false));
            $.jStorage.set("publicChatStateClosed", invertStatus);
            publicChatStateClosed = invertStatus;
            $(this).parent().toggleClass("jchat_wall_minimized");
            popupsReposition()
        };
        $("#jchat_wall_popup .jchat_userstabtitle").on("click", minimizeAllWallContents);
        if (publicChatStateClosed === true) {
            $("#jchat_wall_popup .jchat_userstabtitle").nextAll().toggle().parent().toggleClass("jchat_wall_minimized");
            $("#jchat_maximize_wall").hide()
        }
        $("#jchat_wall_popup .jchat_userstabsubtitle").width($("#jchat_wall_popup").width() - 7)
    }

    function popupsReposition(maximized, onlyElement) {
        if (renderingMode == "auto" || (renderingMode == "module" && separateWidgets)) {
            $("#jchat_base").css("width", "100%")
        }
        $("#jchat_userstab_popup").css("right", 0).css("bottom", "0");
        var parentDivHeight = $("div.jchat_tabcontenttext_wall").parents("#jchat_wall_popup").show().height();
        $("div.jchat_tabcontenttext_wall").parents("#jchat_wall_popup").hide();
        $("div.jchat_tabcontenttext_wall").height(parentDivHeight - 103);
        var windowSize = $(window).width();
        wallStartHeight = parseInt($("#jchat_wall_popup div.jchat_tabcontenttext").height());
        $(".jchat_fileuploadtooltip", onlyElement).each(function (index, elem) {
            var uploadContext = $(elem).next(".jchat_trigger_fileupload");
            var leftKonstant = 282;
            if (maximized === true) {
                leftKonstant = 0
            }
            var topKonstant = 60;
            $(elem, uploadContext).css("left", $(uploadContext).offset().left - $(window).scrollLeft() - leftKonstant).css("top", $(uploadContext).offset().top - $(window).scrollTop() - topKonstant)
        });
        $(".jchat_webrtctooltip").each(function (index, elem) {
            if (!$(elem).hasClass("fullscreen")) {
                var uploadContext = $(elem).next(".jchat_trigger_webrtc");
                var leftKonstant = 500;
                var topHeightKonstant = 300;
                if (recordingEnabled && userPermissions.allow_media_recorder) {
                    topHeightKonstant += 50
                }
                var isParentMaximized = !!$(elem).parents("div.jchat_tabpopup").hasClass("maximized");
                if (maximized === true || isParentMaximized) {
                    if (isParentMaximized) {
                        leftKonstant = 0
                    }
                    if (maximized === true) {
                        $(elem, uploadContext).animate({
                            left: ($(uploadContext).offset().left - $(window).scrollLeft() - leftKonstant),
                            top: ($(uploadContext).offset().top - $(window).scrollTop() - topHeightKonstant)
                        }, 300)
                    } else {
                        $(elem, uploadContext).css("left", $(uploadContext).offset().left - $(window).scrollLeft() - leftKonstant).css("top", $(uploadContext).offset().top - $(window).scrollTop() - topHeightKonstant)
                    }
                } else {
                    $(elem, uploadContext).css("left", $(uploadContext).offset().left - $(window).scrollLeft() - leftKonstant).css("top", $(uploadContext).offset().top - $(window).scrollTop() - topHeightKonstant)
                }
            } else {
                $("#jchat_localvideo_placeholder, #jchat_remotevideo_placeholder").height($("#jchat_localvideo").height())
            }
        });
        $(".jchat_emoticonstooltip", onlyElement).each(function (index, elem) {
            var emoticonsContext = $(elem).next(".jchat_trigger_emoticon");
            var leftKonstant = 196;
            if (maximized === true) {
                leftKonstant = 0
            }
            var topKonstant = 133;
            $(elem, emoticonsContext).css("left", $(emoticonsContext).offset().left - $(window).scrollLeft() - leftKonstant).css("top", $(emoticonsContext).offset().top - $(window).scrollTop() - topKonstant)
        });
        $(".jchat_roomstooltip").each(function (index, elem) {
            var ab = $(".jchat_trigger_room").offset();
            var leftKonstant = $(elem).width() - 18;
            if (maximized === true) {
                leftKonstant = 0
            }
            var windowScroll = jchatGetPageScroll();
            $(elem).css("left", ab.left - $(window).scrollLeft() - leftKonstant).css("top", ab.top - parseInt(windowScroll[1]) - 232)
        });
        $(".jchat_chatroom_usersinfo_tooltip").each(function (index, elem) {
            var ab = $(".jchat_trigger_users_informations").offset();
            var leftKonstant = $(elem).width() - 18;
            if (maximized === true) {
                leftKonstant = 0
            }
            var windowScroll = jchatGetPageScroll();
            $(elem).css("left", ab.left - $(window).scrollLeft() - leftKonstant).css("top", ab.top - parseInt(windowScroll[1]) - 152)
        });
        $(".jchat_infoguesttooltip").each(function (index, elem) {
            var uploadContext = $(elem).next(".jchat_trigger_infoguest");
            var leftKonstant = 232;
            var topKonstant = 201;
            if (maximized === true) {
                leftKonstant = 0
            }
            $(elem, uploadContext).css("left", $(uploadContext).offset().left - $(window).scrollLeft() - leftKonstant).css("top", $(uploadContext).offset().top - $(window).scrollTop() - topKonstant)
        });
        $(".jchat_historytooltip").each(function (index, elem) {
            var uploadContext = $(elem).next(".jchat_trigger_history");
            var leftKonstant = 102;
            var topKonstant = 161;
            $(elem, uploadContext).css("left", $(uploadContext).offset().left - $(window).scrollLeft() - leftKonstant).css("top", $(uploadContext).offset().top - $(window).scrollTop() - topKonstant)
        });
        $(".jchat_geolocationtooltip").each(function (index, elem) {
            var uploadContext = $(elem).next(".jchat_trigger_geolocation");
            var leftKonstant = 232;
            var topKonstant = 302;
            if (maximized === true) {
                leftKonstant = 0
            }
            $(elem, uploadContext).css("left", $(uploadContext).offset().left - $(window).scrollLeft() - leftKonstant).css("top", $(uploadContext).offset().top - $(window).scrollTop() - topKonstant)
        });
        var $optionButtonPopup = $("#jchat_optionsbutton_popup");
        var $optionButtonContext = $("#jchat_userstab #jchat_optionsbutton");
        if ($optionButtonPopup.length && $optionButtonContext.length) {
            var calculatedTop = $optionButtonContext.offset().top - $(window).scrollTop() - $optionButtonPopup.height() - 10;
            $optionButtonPopup.removeClass("jchat_optionspopup_reverse");
            if (calculatedTop < 0) {
                calculatedTop += $optionButtonPopup.height() + 40;
                $optionButtonPopup.addClass("jchat_optionspopup_reverse")
            }
            $optionButtonPopup.css("left", $optionButtonContext.offset().left + $(window).scrollLeft() - $optionButtonPopup.width() + 42).css("top", calculatedTop)
        }
        var $loginButtonPopup = $("#jchat_loginbutton_popup");
        var $loginButtonContext = $("#jchat_userstab #jchat_loginbutton");
        if ($loginButtonPopup.length && $loginButtonContext.length) {
            var calculatedTop = $loginButtonContext.offset().top - $(window).scrollTop() - $loginButtonPopup.height() - 18;
            $loginButtonPopup.css("left", $loginButtonContext.offset().left + $(window).scrollLeft() - 105).css("top", calculatedTop)
        }
        var webrtcTooltipOpened = $("#jchat_trigger_webrtc_tooltip");
        if (popupTooltipDependency && webrtcTooltipOpened.length) {
            var triggerReference = $(".jchat_trigger_webrtc", popupTooltipDependency);
            if (!triggerReference.length) {
                return
            }
            var leftKonstant = 244;
            if (maximized) {
                leftKonstant = 0;
                $(webrtcTooltipOpened).css("background-position", "left bottom")
            } else {
                $(webrtcTooltipOpened).css("background-position", "right bottom")
            }
            var topKonstant = 40;
            $(webrtcTooltipOpened).css("left", $(triggerReference).offset().left - $(window).scrollLeft() - leftKonstant).css("top", $(triggerReference).offset().top - $(window).scrollTop() - topKonstant)
        }
        var $defaultSuggestionTooltip = $("#jchat_default_suggestion_tooltip");
        if ($defaultSuggestionTooltip.length && $("#jchat_base:visible").length) {
            var targetElement = $("#jchat_userstab");
            if (renderingMode == "module" && baloonPosition == "bottom" && !separateWidgets) {
                targetElement = $("#jchat_target")
            }
            var ab = targetElement.offset();
            var Y = targetElement.width() + 30;
            var leftKonstant = 16;
            var topKonstant = 30;
            if (chatTemplate == "alternative.css") {
                topKonstant += 20
            }
            $defaultSuggestionTooltip.css("top", ab.top - $(window).scrollTop() - parseInt($defaultSuggestionTooltip.height()) - topKonstant).css("left", ab.left - $(window).scrollLeft() + leftKonstant)
        }
    }

    function b(Y, Z) {
        t[Y] = Z
    }

    function e(Y) {
        if (t[Y]) {
            return t[Y]
        } else {
            return ""
        }
    }

    function restartUpdateSession(Y, Z) {
        s[Y] = Z;
        if (e("initialize") != 1 && e("updatesession") != 1) {
            R = 1;
            clearTimeout(U);
            U = setTimeout(function () {
                ajaxReceive()
            }, restartInterval)
        }
    }

    function getSessionProperty(Y, Z) {
        if (s[Y]) {
            return s[Y]
        } else {
            return ""
        }
    }

    function showDebugMsgs(message) {
        if (debugEnabled) {
            alert(message)
        }
    }

    function ajaxReceive() {
        for (vars in s) {
            t["sessionvars[" + vars + "]"] = s[vars]
        }
        t.task = "stream.display";
        t.searchfilter = $("#jchat_users_search").val();
        t.force_refresh = forceBuddylistRefresh;
        t.conferenceview = $("#jchat_conference_container").length || 0;
        if (languageTranslationEnabled) {
            setLanguageSessionvars()
        }
        if (R == 1) {
            b("updatesession", "1");
            R = 0
        }
        t.last_received_msg_id = n;
        var Y = "";
        var wallMsgs = "";
        $.ajax({
            url: jsonLiveSite, data: t, type: "post", cache: false, dataType: "json", success: function (mixed) {
                $(".jchat_chatboxmessage.dummy_typing").remove();
                if (mixed) {
                    var Z = 0;
                    $.each(mixed, function (ab, ac) {
                        if (ab == "paramslist") {
                            X = ac.chatrefresh * 1000;
                            K = ac.chatrefresh * 1000;
                            avatarUploadEnabled = !!parseInt(ac.avatarupload);
                            avatarEnabled = !!parseInt(ac.avatarenable);
                            attachmentsEnabled = !!parseInt(ac.attachmentsenable);
                            chatboxesOpenMode = !!parseInt(ac.chatboxes_open_mode);
                            showSendButton = parseInt(ac.show_send_button);
                            skypeEnabled = !!parseInt(ac.skypebridge);
                            groupChat = parseInt(ac.groupchat);
                            privateChatEnabled = !!parseInt(ac.privatechat_enabled);
                            typingEnabled = !!parseInt(ac.typing_enabled);
                            buddyListVisible = !!parseInt(ac.buddylist_visible);
                            groupChatMode = ac.groupchatmode;
                            debugEnabled = !!parseInt(ac.enable_debug);
                            resizableChatboxes = !!parseInt(ac.resizable_chatboxes);
                            chatTemplate = ac.chat_template;
                            chatTemplateTooltip = ac.chat_template_tooltip;
                            chatTemplateTooltipVariant = ac.chat_template_tooltip_variant;
                            positionmentChatboxes = ac.positionment_chatboxes;
                            autoClosePopups = !!parseInt(ac.auto_close_popups);
                            resizableSidebar = !!parseInt(ac.resizable_sidebar);
                            searchFieldEnabled = !!parseInt(ac.searchfield);
                            showMyUsername = !!parseInt(ac.show_myusername);
                            showChatroomsUsersDetails = !!parseInt(ac.chatrooms_users_details);
                            chatRoomLatestMessages = !!parseInt(ac.chatrooms_latest);
                            autoClearConversation = !!parseInt(ac.autoclear_conversation);
                            usersBanning = !!parseInt(ac.usersbanning);
                            usersBanningMode = ac.usersbanning_mode;
                            wordsBanning = !!parseInt(ac.wordsbanning);
                            wordsBanned = ac.wordsbanned;
                            wordsBannedReplacement = ac.wordsbanned_replacement;
                            messagesHistory = !!parseInt(ac.history);
                            maximizeButton = parseInt(ac.maximize_box);
                            renderingMode = ac.rendering_mode;
                            baloonPosition = ac.baloon_position;
                            separateWidgets = !!parseInt(ac.separate_widgets);
                            guestEnabledMode = ac.guestenabled;
                            chatTitle = ac.chat_title;
                            privateChatTitle = ac.private_chat_title;
                            publicChatTitle = ac.public_chat_title.trim();
                            microSplacementKonstant = parseInt(ac.micro_splacement_konstant);
                            doubleChatboxesDirection = !!parseInt(ac.double_chatboxes_direction);
                            showSuggestionTooltip = !!parseInt(ac.show_suggestion_tooltip);
                            defaultSuggestionText = ac.suggestion_tooltip_text ? ac.suggestion_tooltip_text : defaultSuggestionText;
                            chatFormLink = ac.chatform_link;
                            excludeOnMobile = parseInt(ac.exclude_onmobile);
                            guestPrefix = ac.guestprefix;
                            asyncSendMessage = ac.async_send_message;
                            notificationsAutoEnable = !!parseInt(ac.notifications_auto_enable);
                            geolocationEnabled = !!parseInt(ac.geolocation_enabled);
                            geolocationService = ac.geolocation_service;
                            wallStartState = !!parseInt(ac.groupchat_start_open_mode);
                            addChatrooms = !!parseInt(ac.addchatroom);
                            deleteChatrooms = !!parseInt(ac.deletechatroom);
                            userAccessLevels = ac.user_access_viewlevels;
                            joomlaAccessLevels = ac.total_access_viewlevels;
                            wallHistoryDelayAutoload = !!parseInt(ac.wall_history_delay_autoload);
                            defaultChatroom = parseInt(ac.default_chatroom);
                            webrtcEnabled = !!parseInt(ac.webrtc_enabled);
                            webrtcRingingTone = ac.webrtc_call_sound;
                            iceServers = ac.ice_servers;
                            timeoutStartCall = parseInt(ac.timeout_start_call);
                            timeoutEndCall = parseInt(ac.timeout_end_call);
                            webrtcFallbackEnabled = !!parseInt(ac.no_webrtc_fallback);
                            hideWebcamWhenDisabled = parseInt(ac.hide_webcam_when_disabled);
                            showWebRTCStats = !!parseInt(ac.show_webrtc_stats);
                            showWebRTCVUMeter = !!parseInt(ac.show_webrtc_vumeter);
                            serverLoadReduction = !!parseInt(ac.serverload_reduction);
                            defaultMicVolume = parseFloat(ac.micvolume_default);
                            defaultAudioVolume = parseFloat(ac.audiovolume_default);
                            videochatAutoMaximize = parseInt(ac.videochat_auto_maximize_box);
                            autoQualityBandwidthMgmt = !!parseInt(ac.auto_quality_bandwidth_management);
                            recordingEnabled = !!parseInt(ac.enable_recording);
                            ticketsForm = !!parseInt(ac.tickets_form);
                            ticketsFormAlwaysVisible = !!parseInt(ac.tickets_form_always_visible);
                            autoOpenRandomAgentBox = !!parseInt(ac.auto_open_agentbox);
                            autoOpenAgentboxDefaultMessage = ac.auto_open_agentbox_defaultmessage || jchat_agentbox_defaultmessage;
                            affectPublicChat = !!parseInt(ac.affect_public_chat);
                            if (!!parseInt(ac.offline_message_switcher)) {
                                jchat_nousers = '<div class="jchat_nousers_placeholder">' + jchatOfflineMessage + "</div>"
                            }
                            if (renderingMode != "auto") {
                                targetElement = "jchat_target"
                            }
                            thirdPartyIntegration = ac["3pdintegration"];
                            PMIntegration = !!parseInt(ac.pm_integration);
                            facebookLoginActive = !!parseInt(ac.fblogin_active);
                            googleLoginActive = !!parseInt(ac.gpluslogin_active);
                            twitterLoginActive = !!parseInt(ac.twitterlogin_active);
                            languageTranslationEnabled = !!parseInt(ac.language_translation_enabled);
                            translateSelfMessages = !!parseInt(ac.language_translation_selfmessages);
                            if (languageTranslationEnabled) {
                                defaultLanguage = ac.default_language || ac.default_fallback_language;
                                defaultTranslateToLanguage = ac.default_to_language;
                                languageFallbacks = {
                                    sourcelang: defaultLanguage,
                                    targetlang: defaultTranslateToLanguage
                                }
                            }
                            isGuest = !!parseInt(ac.isguest);
                            hasSuperUser = !!(ac.superuser);
                            allowGuestAvatarupload = !!parseInt(ac.allow_guest_avatarupload);
                            allowGuestFileupload = !!parseInt(ac.allow_guest_fileupload);
                            allowGuestSkypeBridge = !!parseInt(ac.allow_guest_skypebridge);
                            allowGuestOverrideName = !!parseInt(ac.allow_guest_overridename);
                            allowGuestBanning = !!parseInt(ac.allow_guest_banning);
                            allowGuestBuddylist = !!parseInt(ac.allow_guest_buddylist);
                            allowMediaObjects = !!parseInt(ac.allow_media_objects);
                            allowChatroomsCreation = !!parseInt(ac.addchatroom_permission);
                            allowChatroomsDeletion = !!parseInt(ac.deletechatroom_permission);
                            enableModeration = !!parseInt(ac.enablemoderation);
                            buildUserPermissions(ac, ac.usergroups);
                            var sidebarWidth = originalSidebarWidth = parseInt(ac.sidebar_width);
                            var calculatedRatio = Math.abs(parseInt((260 - sidebarWidth) / 10));
                            if (sidebarWidth < 260) {
                                calculatedRatio += 2;
                                calculatedRatio = -calculatedRatio;
                                buddyLength += calculatedRatio
                            }
                            if (renderingMode == "module" && ac.baloon_position == "bottom") {
                                $("#jchat_base").detach().appendTo(jchatTargetElement)
                            }
                            if (facebookLoginActive || googleLoginActive || twitterLoginActive) {
                                $("<span/>").attr("id", "jchat_loginbutton").addClass("jchat_tab").addClass("jchat_loginimages").appendTo($("#jchat_userstab"))
                            }
                        }
                        if (ab == "buddylist") {
                            var renderNoUsers = function () {
                                $(".jchat_nousers, .jchat_userlist").remove();
                                if ($("#jchat_users_search:focus").length) {
                                    $(".jchat_userscontent").append('<div class="jchat_nousers">' + jchat_nousers_filter + "</div>")
                                } else {
                                    $(".jchat_userscontent").append('<div class="jchat_nousers">' + jchat_nousers + "</div>")
                                }
                                if (ticketsForm && !$("#jchat_users_search:focus").length) {
                                    renderTicketForm("div.jchat_nousers")
                                }
                            };
                            if (!ac) {
                                if (!$("#lamform input[name=lam_name]").val()) {
                                    renderNoUsers();
                                    $("div.jchat_ticketform_collapse, div.jchat_always_ticket_form").remove()
                                }
                                $("#jchat_userstab_text").html(chatTitle + '<span class="jchat_userscount">(0)</span>');
                                buddyList = {};
                                $("span[id^=jchat_user_] div.jchat_closebox_bottom_status").removeClass("jchat_available jchat_busy").addClass("jchat_disconnected").html("");
                                if (typeof(JChatConference) !== "undefined") {
                                    $("#jchat_conference_userslist li.jchat_userbox").remove()
                                }
                            } else {
                                buddyList = ac;
                                $("#jchat_userstab_popup").css("min-height", "inherit");
                                $("#jchat_wall_popup").removeClass("jchat_lamform_hidden");
                                wallStartHeight = jchatGetWallHeight();
                                $("#jchat_wall_popup div.jchat_tabcontenttext").height(wallStartHeight - ($(".jchat_textarea_wall").height() - 22));
                                if (ticketsForm) {
                                    $("span.jchat_lamform_title").remove();
                                    $("#jchat_userstab_popup *.jchat_lamform, #jchat_userstab *.jchat_lamform").removeClass("jchat_lamform")
                                }
                                if (!$("#lamform input[name=lam_name]").val()) {
                                    $(".jchat_nousers").remove();
                                    var numUsers = 0;
                                    if ($("#jchat_private_messaging").length) {
                                        $("span.jchat_usersbox_status").removeClass("jchat_online").empty()
                                    }
                                    $.each(ac, function (ae, ad) {
                                        listaUtenti(ad.id, ad.name, ad.status, ad.message, ad, false);
                                        if (ad.status !== "offline") {
                                            numUsers++
                                        }
                                    });
                                    $("#jchat_userstab_text").html(chatTitle + '<span class="jchat_userscount">(' + numUsers + ")</span>");
                                    if (numUsers == 0) {
                                        renderNoUsers();
                                        $("div.jchat_ticketform_collapse, div.jchat_always_ticket_form").remove()
                                    } else {
                                        if (ticketsFormAlwaysVisible) {
                                            if (!$("div.jchat_ticketform_collapse").length) {
                                                $(".jchat_userscontent").prepend('<div class="jchat_ticketform_collapse">' + jchat_sendus_aticket + "</div>");
                                                $(".jchat_ticketform_collapse").after('<div class="jchat_always_ticket_form"></div>');
                                                renderTicketForm("div.jchat_always_ticket_form");
                                                $(".jchat_ticketform_collapse").on("click", function () {
                                                    $(this).toggleClass("opened");
                                                    $("div.jchat_always_ticket_form").slideToggle()
                                                })
                                            }
                                        }
                                    }
                                }
                                var presentUsers = $("div.jchat_userlist");
                                $.each(presentUsers, function (k, HTMLelem) {
                                    var thisUserId = $(HTMLelem).data("id");
                                    if ($.inArray(thisUserId, mixed.buddylist_ids) == -1) {
                                        $(HTMLelem).remove();
                                        $("#jchat_user_" + thisUserId + " div.jchat_closebox_bottom_status").removeClass("jchat_available jchat_busy").addClass("jchat_disconnected").html("");
                                        if (typeof(JChatConference) !== "undefined") {
                                            var thisSessionId = $(HTMLelem).data("sessionid");
                                            $("#jchat_conference_userslist li.jchat_userbox[data-sessionid=" + thisSessionId + "]").remove()
                                        }
                                    }
                                })
                            }
                        }
                        if (ab == "my_username" && ab != "" && ab != "undefined") {
                            if (ac.length > (chatLength - 2)) {
                                my_username = ac.substr(0, chatLength - 2) + "..."
                            } else {
                                my_username = ac
                            }
                            $("#jchat_myusername").html(jchat_me + "<span>" + my_username + "</span>");
                            $("div.jchat_userslist_myusername").html(jchat_me + "<span>" + my_username + "</span>");
                            if ($("input[name=lam_name]") && !firstCall && !isGuest && !isChatAdmin) {
                                $("input[name=lam_name]").val(my_username)
                            }
                        }
                        if (ab == "my_avatar" && ab != "" && ab !== undefined && ab !== null) {
                            my_avatar = ac
                        }
                        if (ab == "my_email" && ab != "" && ab !== undefined && ab !== null) {
                            my_email = ac;
                            if ($("input[name=lam_email]") && !firstCall && !isGuest && !isChatAdmin) {
                                $("input[name=lam_email]").val(my_email)
                            }
                        }
                        if (ab == "loggedout") {
                            $("#jchat_optionsbutton").addClass("jchat_optionsimages_exclamation");
                            $("#jchat_userstab").hide();
                            $("#jchat_optionsbutton_popup").hide();
                            $("#jchat_wall_popup").hide();
                            $("#jchat_userstab_popup").hide();
                            $(".jchat_tabopen").css("cssText", "display: none !important;");
                            if (T != "") {
                                $("#jchat_user_" + T + "_popup").hide();
                                T = ""
                            }
                            r = 1
                        }
                        if (ab == "userstatus") {
                            $.each(ac, function (ad, ae) {
                                if (ad == "message") {
                                    $("#jchat_optionsbutton_popup .jchat_statustextarea").val(ae)
                                }
                                if (ad == "status") {
                                    chatStatus = ae;
                                    if (ae == "offline") {
                                        turnChatOffline(1)
                                    } else {
                                        if (ae == "busy") {
                                            $("span.jchat_user_donotdisturb").addClass("jchat_enabled");
                                            $("#jchat_userstab_popup .jchat_userstabtitle").addClass("jchat_donotdisturb");
                                            donotdisturb = 1
                                        } else {
                                            statusClassOp();
                                            $("#jchat_userstab_icon").addClass("jchat_user_" + ae + "2");
                                            $("#jchat_optionsbutton_popup ." + ae).css("text-decoration", "underline")
                                        }
                                    }
                                }
                                if (ad == "override_name") {
                                    $("#override_name").val(ae)
                                }
                                if (ad == "skype_id") {
                                    $("#skype_id").val(ae)
                                }
                                if (ad == "room_id") {
                                    currentJoinedChatRoom = ae;
                                    if (currentJoinedChatRoom == null && defaultChatroom) {
                                        chatRoomAutoJoiner(defaultChatroom)
                                    }
                                }
                            })
                        }
                        if (ab == "typing_status") {
                            var typingStatus = ac;
                            if (typingStatus) {
                                $.each(typingStatus, function (index, object) {
                                    var targetChatboxPopup = $("#jchat_user_" + index + "_popup");
                                    var targetChatboxPopupText = $("div.jchat_tabcontenttext", targetChatboxPopup);
                                    if (targetChatboxPopup.length) {
                                        var elementToAppend = null;
                                        var userAvatar = $("span.jchat_userscontentname img", "#jchat_userlist_" + index);
                                        if (userAvatar.length) {
                                            elementToAppend = userAvatar.get(0).outerHTML
                                        } else {
                                            elementToAppend = "<strong>" + $("span.jchat_userscontentname", "#jchat_userlist_" + index).text() + "</strong>"
                                        }
                                        var dummyTypingSnippet = '<div class="jchat_chatboxmessage dummy_typing"><span class="jchat_chatboxmessagefrom">' + elementToAppend + '</span><span class="jchat_chatboxmessagecontent"><span class="dummy_typing_dots">...</span></span></div>';
                                        targetChatboxPopupText.append(dummyTypingSnippet).scrollTop(targetChatboxPopupText.get(0).scrollHeight);
                                        if ($("#jchat_usersmessages").length) {
                                            $("#jchat_usersmessages").append(dummyTypingSnippet).scrollTop($("#jchat_usersmessages").get(0).scrollHeight)
                                        }
                                    }
                                })
                            }
                        }
                        if (ab == "lastreadmessages") {
                            var lastReadMessages = ac;
                            $.each(lastReadMessages, function (chatBoxID, msgID) {
                                var isTherePMVisible = $("#jchat_usersmessages div.jchat_chatboxmessage[data-messageid=" + msgID + "] div.jchat_chatboxmessagedate");
                                if (isTherePMVisible.length && !$("span.jchat_messaging.msg_seen", isTherePMVisible).length) {
                                    $("span.jchat_messaging.msg_seen").remove();
                                    var pmLastMsgID = $("#jchat_usersmessages div.jchat_chatboxmessage:last-child").data("messageid");
                                    if (pmLastMsgID == msgID) {
                                        isTherePMVisible.append('<span class="jchat_messaging msg_seen">' + jchat_seen + "</span>")
                                    }
                                }
                                if ($("#jchat_user_" + chatBoxID + "_popup").length) {
                                    var lastMsg = $("div.jchat_tabcontenttext.private div.jchat_chatboxmessage.selfmessage:last-child", "#jchat_user_" + chatBoxID + "_popup");
                                    var lastMsgID = lastMsg.attr("id");
                                    var nextAdvice = $("#jchat_message_" + msgID).next("span.msg_seen");
                                    if (lastMsgID === "jchat_message_" + msgID && !nextAdvice.length) {
                                        $("#jchat_message_" + msgID).after('<span class="msg_seen">' + jchat_seen + "</span>");
                                        var thisTabContextText = $("#jchat_user_" + chatBoxID + "_popup .jchat_tabcontenttext");
                                        var toScrollValue = $(thisTabContextText)[0].scrollHeight;
                                        $(thisTabContextText).animate({scrollTop: toScrollValue}, 800, "swing")
                                    }
                                }
                            })
                        }
                        if (ab == "webrtc_signaling_channel" && webrtcDirector && userPermissions.allow_videochat && typeof(JChatConference) === "undefined") {
                            webrtcDirector.setListeningData(ac, {tabFocused: isTabFocused, tabTitle: originalPageTitle})
                        }
                        if (ab == "webrtc_conference_signaling_channel" && conferenceDirector && userPermissions.allow_videochat && typeof(JChatConference) !== "undefined") {
                            $.each(ac, function (index, dataObject) {
                                conferenceDirector.setListeningData(dataObject)
                            })
                        }
                        if (ab == "chatrooms") {
                            roomsAvailable = ac;
                            if (!currentJoinedChatRoom && firstCall) {
                                currentJoinedChatRoom = mixed.userstatus["room_id"]
                            }
                            $.each(roomsAvailable, function (k, room) {
                                var isMyJoinedRoom = !!(room.id == currentJoinedChatRoom);
                                if (currentJoinedChatRoom && isMyJoinedRoom && !publicChatStateClosed) {
                                    room.name = room.name.length < chatRoomLength ? room.name : room.name.substr(0, chatRoomLength) + "...";
                                    var subTitle = $("<div/>").addClass("jchat_userstabsubtitle").html('<span class="jchat_roomtitle">' + jchat_chatroom + "</span> " + room.name);
                                    $(".jchat_userstabtitle", "#jchat_wall_popup").next(".jchat_userstabsubtitle").remove().end().after(subTitle);
                                    $("#jchat_wall_popup .jchat_userstabsubtitle").width($("#jchat_wall_popup").width() - 7);
                                    room.numusers++
                                }
                                addChatRoomsUsersDetails(room, isMyJoinedRoom);
                                $(".jchat_roomcount[data-roomcountid=" + room.id + "]").text(jchat_chatroom_users + room.numusers)
                            })
                        }
                        if (ab == "users_inmyroom") {
                            myChatRoomUsers = ac
                        }
                        if (ab == "initialize") {
                            firstCall = true;
                            $("#jchat_userstab").addClass("jchat_tabopen");
                            $.each(ac, function (ad, af) {
                                if (ad == "buddylist") {
                                    if (af == 1) {
                                        $("#jchat_userstab").click()
                                    }
                                    if (chatTitle.length > chatTitleLength) {
                                        $("#jchat_userstab_text").addClass("jchat_userstab_text_long")
                                    }
                                }
                                if (ad == "options") {
                                    if (af == 1) {
                                        $("#jchat_optionsbutton").click()
                                    }
                                }
                                if (ad == "activeChatboxes") {
                                    if (!!parseInt(ac.buddylist) || chatboxesOpenMode) {
                                        var ag = af.split(/,/);
                                        var chatBoxesCounter = 0;
                                        for (var i = 0; i < ag.length; i++) {
                                            var ae = ag[i].split(/\|/);
                                            if (!$("#jchat_userlist_" + ae[0]).length) {
                                                continue
                                            }
                                            if (ae[0]) {
                                                openedChatboxes[ae[0]] = chatBoxesCounter
                                            }
                                            $("#jchat_userlist_" + ae[0]).trigger("dblclick", [true]);
                                            if (parseInt(ae[1]) > 0) {
                                                tabAlert(ae[0], ae[1], 0, {})
                                            }
                                            chatBoxesCounter++
                                        }
                                    }
                                }
                                if (ad == "audio") {
                                    audio = parseInt(af);
                                    if (audio == 0) {
                                        $("div.jchat_sounds").addClass("noaudio")
                                    }
                                }
                                if (ad == "wallaudio") {
                                    wallaudio = parseInt(af);
                                    if (wallaudio == 0) {
                                        $("div.jchat_wall_sounds").addClass("noaudio")
                                    }
                                }
                                if (ad == "vibrate") {
                                    vibrate = parseInt(af);
                                    if (vibrate == 0) {
                                        $("div.jchat_vibrate").addClass("novibrate")
                                    }
                                    JChatNotifications.setVibrateStatus(vibrate)
                                }
                                if (ad == "notification") {
                                    notification = parseInt(af);
                                    if (notification == 1) {
                                        $("div.jchat_notification").addClass("yesnotification")
                                    }
                                    JChatNotifications.setNotificationStatus(notification)
                                }
                                if (ad == "minimizedallchatboxes") {
                                    minimizedAllChatboxes = parseInt(af);
                                    if (minimizedAllChatboxes == 1) {
                                        $("span.jchat_minimize_chatboxes").addClass("yesminimized");
                                        $("div[id^=jchat_user_].jchat_bottomed").draggable("disable").addClass("jchat_nodraggable")
                                    }
                                }
                            });
                            b("initialize", "0");
                            if (notificationsAutoEnable && !ac.notification) {
                                $("div.jchat_notification").trigger("click")
                            }
                            if (typeof(JChatConference) !== "undefined" && userPermissions.allow_videochat) {
                                var webrtcOptions = {
                                    debugEnabled: debugEnabled,
                                    jsonLiveSite: jsonLiveSite,
                                    ringingTone: webrtcRingingTone,
                                    audiostatus: audio,
                                    startCallTimeout: timeoutStartCall * 1000,
                                    endCallTimeout: timeoutEndCall * 1000,
                                    defaultMicVolume: defaultMicVolume,
                                    defaultAudioVolume: defaultAudioVolume,
                                    iceServers: iceServers,
                                    hideWebcamWhenDisabled: hideWebcamWhenDisabled,
                                    showWebRTCStats: showWebRTCStats,
                                    showWebRTCVUMeter: showWebRTCVUMeter,
                                    autoQualityBandwidthMgmt: autoQualityBandwidthMgmt
                                };
                                var videoMyShortName = my_username.length > videoLength ? my_username.substr(0, videoLength) + "..." : my_username;
                                conferenceDirector = new JChatConference(webrtcOptions);
                                conferenceDirector.initializeVideo($("#jchat_right_videocolumn"), videoMyShortName);
                                if (typeof(JChatRecorder) !== "undefined" && recordingEnabled && userPermissions.allow_media_recorder) {
                                    recorderDirector = new JChatRecorder(conferenceDirector, {
                                        debugEnabled: debugEnabled,
                                        permissions: userPermissions
                                    });
                                    var recorderContainerElement = $("#jchat_wrapper_localvideo").append('<div id="jchat_wrapper_localvideo_recorder"></div>');
                                    recorderDirector.initializeVideo($("#jchat_wrapper_localvideo_recorder"), "conference", my_username)
                                }
                            } else {
                                if (typeof(JChatWebrtc) !== "undefined" && webrtcEnabled && userPermissions.allow_videochat) {
                                    var webrtcOptions = {
                                        debugEnabled: debugEnabled,
                                        jsonLiveSite: jsonLiveSite,
                                        ringingTone: webrtcRingingTone,
                                        audiostatus: audio,
                                        startCallTimeout: timeoutStartCall * 1000,
                                        endCallTimeout: timeoutEndCall * 1000,
                                        defaultMicVolume: defaultMicVolume,
                                        defaultAudioVolume: defaultAudioVolume,
                                        iceServers: iceServers,
                                        hideWebcamWhenDisabled: hideWebcamWhenDisabled,
                                        showWebRTCStats: showWebRTCStats,
                                        showWebRTCVUMeter: showWebRTCVUMeter,
                                        autoQualityBandwidthMgmt: autoQualityBandwidthMgmt
                                    };
                                    webrtcDirector = new JChatWebrtc(webrtcOptions);
                                    if (typeof(JChatRecorder) !== "undefined" && recordingEnabled && userPermissions.allow_media_recorder) {
                                        recorderDirector = new JChatRecorder(webrtcDirector, {
                                            debugEnabled: debugEnabled,
                                            permissions: userPermissions
                                        })
                                    }
                                    var webrtcStoredState = $.jStorage.get("jchat_remotepeer_webrtc_tooltip", false);
                                    if (webrtcStoredState) {
                                        $(".jchat_trigger_webrtc", "#jchat_user_" + webrtcStoredState + "_popup").trigger("click");
                                        $.jStorage.deleteKey("jchat_remotepeer_webrtc_tooltip")
                                    }
                                }
                                if (typeof(JChatWebrtc) === "undefined") {
                                    $("div.jchat_trigger_webrtc").remove()
                                }
                            }
                            if (typeof(JChatConference) !== "undefined" && !userPermissions.allow_videochat) {
                                $("#jchat_conference_container").empty();
                                $("#jchat_conference_container").append('<span class="jchat_view_message">' + COM_JCHAT_NOACCESS_VIDEOCONFERENCE + "</span>")
                            }
                            if (window.sessionStorage) {
                                if (!parseInt(jchat_userid) && jchat_guestenabled == 2 && !sessionStorage.getItem("jchat_joined")) {
                                    $("#jchat_userstab").removeClass("jchat_tabclick jchat_userstabclick")
                                }
                            }
                            isChatAdmin = !!parseInt(mixed.ischatadmin);
                            if (!isChatAdmin && autoOpenRandomAgentBox) {
                                autoOpenAgentChatbox()
                            }
                        }
                        if (ab == "wallmessages") {
                            $.each(ac, function (af, ad) {
                                ++Z;
                                var selfClass = "";
                                var userNameWidthClass = "";
                                var msgWidthClass = "";
                                var hasAvatar = false;
                                if (ad.self == 1) {
                                    if (my_avatar !== undefined && my_avatar !== null && my_avatar !== "") {
                                        fromPlaceholder = '<img alt="' + jchat_you + my_username + '" data-time="' + ad.time + '" width="32px" height="32px" src="' + my_avatar + '" />'
                                    } else {
                                        fromPlaceholder = '<strong data-time="' + ad.time + '">' + my_username + "</strong>"
                                    }
                                    selfClass = " selfmessage"
                                } else {
                                    var userName = $("#jchat_userlist_" + ad.fromuserid).triggerHandler("getname");
                                    var userAvatar = $("#jchat_userlist_" + ad.fromuserid).triggerHandler("getavatar");
                                    if (userAvatar === undefined || userAvatar === null || userAvatar === "") {
                                        userAvatar = ad.avatar
                                    }
                                    if (userName === undefined || userName === null || userName === "") {
                                        if (ad.fromuser.length > chatLength) {
                                            userName = ad.fromuser.substr(0, chatLength) + "..."
                                        } else {
                                            userName = ad.fromuser
                                        }
                                    }
                                    var hasAvatar = !!(userAvatar !== undefined && userAvatar !== null && userAvatar !== "" && !userAvatar.match(/default_other.png/) && avatarEnabled === true);
                                    if (!hasAvatar && userName.length >= (chatLength - 13)) {
                                        userNameWidthClass = " jchat_username_large";
                                        msgWidthClass = " jchat_message_small"
                                    }
                                    if (userAvatar !== undefined && userAvatar !== null && userAvatar !== "" && !userAvatar.match(/default_other.png/) && avatarEnabled === true) {
                                        fromPlaceholder = '<img alt="' + userName + '" data-time="' + ad.time + '" width="32px" height="32px" src="' + userAvatar + '" />'
                                    } else {
                                        fromPlaceholder = '<strong data-time="' + ad.time + '">' + userName + "</strong>"
                                    }
                                    if (typeof ad.profilelink !== "undefined" && ad.profilelink !== "" && ad.profilelink !== null) {
                                        fromPlaceholder = '<a href="' + ad.profilelink + '">' + fromPlaceholder + "</a>"
                                    }
                                }
                                ad.message = jchatStripTags(ad.message, allowMediaObjects);
                                ad.message = ad.message.replace(/&/gi, "&amp;");
                                if ($("#jchat_message_" + ad.id).length > 0) {
                                } else {
                                    wallMsgs += ('<div class="jchat_chatboxmessage' + selfClass + '" id="jchat_message_' + ad.id + '"><span class="jchat_chatboxmessagefrom' + userNameWidthClass + '">' + fromPlaceholder + '</span><span class="jchat_chatboxmessagecontent' + selfClass + msgWidthClass + '">' + ad.message + "</span></div>");
                                    if (wallaudio == 1 && firstCall === false) {
                                        JChatNotifications.playWallMessageAlert(ad)
                                    }
                                }
                            })
                        }
                        if (ab == "messages") {
                            var mutexMaximized = function (from) {
                                var validMaximized = false;
                                $("div[id^=jchat_user_]").each(function (index, elem) {
                                    var thisID = $(elem).attr("id");
                                    var hasMaximizedClass = $(elem).hasClass("maximized");
                                    if (hasMaximizedClass && thisID != "jchat_user_" + from + "_popup") {
                                        validMaximized = true
                                    }
                                    $(elem).removeClass("maximized_hover")
                                });
                                if (validMaximized) {
                                    $("#jchat_user_" + from + "_popup").addClass("maximized_hover")
                                }
                            };
                            $("span.jchat_messaging.msg_seen").remove();
                            $.each(ac, function (af, ad) {
                                n = ad.id;
                                var selfClass = "";
                                $("span.msg_seen", "#jchat_user_" + (ad.from) + "_popup").remove();
                                if ($("#jchat_user_" + (ad.from) + "_popup").length > 0) {
                                    if (ad.self == 1) {
                                        if (my_avatar !== undefined && my_avatar !== null && my_avatar !== "" && avatarEnabled === true) {
                                            fromPlaceholder = '<img alt="' + jchat_you + my_username + '" data-time="' + ad.time + '" width="32px" height="32px" src="' + my_avatar + '" />'
                                        } else {
                                            fromPlaceholder = '<strong data-time="' + ad.time + '">' + my_username + "</strong>"
                                        }
                                        selfClass = " selfmessage"
                                    } else {
                                        var userName = $("#jchat_userlist_" + ad.from).triggerHandler("getname");
                                        var userAvatar = $("#jchat_userlist_" + ad.from).triggerHandler("getavatar");
                                        if (userAvatar === undefined || userAvatar === null || userAvatar === "") {
                                            userAvatar = ad.avatar
                                        }
                                        if (userName === undefined || userName === null || userName === "") {
                                            userName = ad.fromuser
                                        }
                                        if (userAvatar !== undefined && userAvatar !== null && userAvatar !== "" && avatarEnabled === true) {
                                            fromPlaceholder = '<img alt="' + userName + '" data-time="' + ad.time + '" width="32px" height="32px" src="' + userAvatar + '" />'
                                        } else {
                                            fromPlaceholder = '<strong data-time="' + ad.time + '">' + userName + "</strong>"
                                        }
                                        if (typeof ad.profilelink !== "undefined" && ad.profilelink !== "" && ad.profilelink !== null) {
                                            fromPlaceholder = '<a href="' + ad.profilelink + '">' + fromPlaceholder + "</a>"
                                        }
                                    }
                                    if (ad.type == "file") {
                                        if (firstCall) {
                                            var sound = false
                                        }
                                        ad.message = jchatTrasformMsgFile(ad.message, ad.id, ad.self, ad.from, ad.status, sound, ad)
                                    } else {
                                        ad.message = jchatStripTags(ad.message, allowMediaObjects);
                                        ad.message = ad.message.replace(/&/gi, "&amp;")
                                    }
                                    if ($("#jchat_message_" + ad.id).length > 0) {
                                    } else {
                                        Y = ('<div class="jchat_chatboxmessage' + selfClass + '" id="jchat_message_' + ad.id + '"><span class="jchat_chatboxmessagefrom">' + fromPlaceholder + '</span><span class="jchat_chatboxmessagecontent' + selfClass + '">' + ad.message + "</span></div>");
                                        if (audio == 1 && firstCall === false && ad.type != "file") {
                                            JChatNotifications.playMessageAlert(ad)
                                        }
                                        if (!ad.old) {
                                            var target = $("#jchat_user_" + ad.from + "_popup .jchat_textarea");
                                            if (target.length && !$(target).is(":focus") && !$(target).data("isblinking")) {
                                                $(target).blink();
                                                $(target).data("isblinking", true)
                                            }
                                        }
                                    }
                                    $("#jchat_user_" + ad.from + "_popup .jchat_tabcontenttext").append(Y);
                                    $("#jchat_user_" + ad.from + "_popup .jchat_tabcontenttext").scrollTop($("#jchat_user_" + ad.from + "_popup .jchat_tabcontenttext")[0].scrollHeight);
                                    $("span.jchat_chatboxmessagefrom img, span.jchat_chatboxmessagefrom strong").mouseover(function (event) {
                                        avatarTooltip(this, $(this).attr("alt"), $(this).data("time"))
                                    }).mouseout(function (event) {
                                        $("#jchat_avatartooltip").remove()
                                    });
                                    $("div.jchat_agent_dummy_message").remove();
                                    if (!isTabFocused && ad.self != 1) {
                                        $("title").text(userName + jchat_newmessage_tab)
                                    }
                                    mutexMaximized(ad.from);
                                    if (ad.type == "file" && ad.self) {
                                        ad.idregistered = $("#jchat_user_" + ad.from).data("loggedid")
                                    }
                                    sendAppendPrivateMessage(ad.idregistered, ad.id, ad.message, ad.self, ad)
                                } else {
                                    var lastMessageReached = !!(af == (ac.length - 1));
                                    var isPrivateMessagingOpened = !!($("#jchat_private_messaging").length && $("#jchat_privatemessaging_textarea").data("loggedid") == ad.idregistered);
                                    $("#jchat_userlist_" + ad.from).trigger("addmessage", [ad.message, ad.self, ad.old, ad.id, ad, lastMessageReached, isPrivateMessagingOpened]);
                                    if (!$("#jchat_userlist_" + ad.from).length && $("#jchat_private_messaging").length && audio == 1) {
                                        ad.type == "file" ? JChatNotifications.playSentFile(ad) : JChatNotifications.playMessageAlert(ad)
                                    }
                                    $("div.jchat_agent_dummy_message").remove();
                                    mutexMaximized(ad.from);
                                    if (ad.type == "file") {
                                        if (firstCall) {
                                            var sound = false
                                        }
                                        ad.message = jchatTrasformMsgFile(ad.message, ad.id, ad.self, ad.from, ad.status, sound, ad)
                                    } else {
                                        ad.message = jchatStripTags(ad.message, allowMediaObjects);
                                        ad.message = ad.message.replace(/&/gi, "&amp;")
                                    }
                                    sendAppendPrivateMessage(ad.idregistered, ad.id, ad.message, false, ad)
                                }
                            });
                            h = 1;
                            K = X
                        }
                        if (ab == "downloads") {
                            $.each(ac, function (k, elem) {
                                refreshMsgFileStatus(elem[0], elem[1])
                            })
                        }
                    });
                    if (wallMsgs) {
                        $("#jchat_wall_popup div.jchat_tabcontenttext").append(wallMsgs);
                        $("#jchat_wall_popup div.jchat_tabcontenttext").scrollTop($("#jchat_wall_popup div.jchat_tabcontenttext")[0].scrollHeight);
                        $("span.jchat_chatboxmessagefrom img, span.jchat_chatboxmessagefrom strong", "#jchat_wall_popup").mouseover(function (event) {
                            avatarTooltip(this, $(this).attr("alt"), $(this).data("time"))
                        }).mouseout(function (event) {
                            $("#jchat_avatartooltip").remove()
                        });
                        if (!$("#jchat_userstab").hasClass("jchat_tabclick") && !$("#jchat_newpublic_msg").length && !firstCall) {
                            $("<span/>").attr("id", "jchat_newpublic_msg").addClass("jchat_tab").appendTo($("#jchat_userstab"));
                            var alertElem = $("#jchat_newpublic_msg");
                            wallMessagesAlertInterval = setInterval(function () {
                                if (alertElem.data("blink")) {
                                    $(alertElem).toggleClass("hidden").data("blink", false)
                                } else {
                                    $(alertElem).toggleClass("hidden").data("blink", true)
                                }
                            }, 600)
                        }
                    }
                }
                b("initialize", "0");
                b("updatesession", "0");
                var webRTCActive = (!!($("div.jchat_webrtctooltip").length) || !!($("#jchat_conference_container").length)) && !serverLoadReduction;
                if (r != 1 && o != 1) {
                    if (!webRTCActive) {
                        h++;
                        if (h > emptyResponse) {
                            K *= 2;
                            h = 1
                        }
                    }
                    if (K > F) {
                        K = F
                    }
                    clearTimeout(U);
                    U = setTimeout(function () {
                        ajaxReceive()
                    }, K)
                }
                if (firstCall && groupChat) {
                    popupsReposition();
                    if ($("#jchat_wall_popup div.jchat_tabcontenttext").length) {
                        $("#jchat_wall_popup div.jchat_tabcontenttext").scrollTop($("#jchat_wall_popup div.jchat_tabcontenttext").get(0).scrollHeight)
                    }
                }
                if (firstCall && showSuggestionTooltip) {
                    suggestionTooltip()
                }
                if (typeof(jchat_hide_sidebar) !== "undefined") {
                    if (firstCall && !!jchat_hide_sidebar) {
                        $("#jchat_base, #jchat_optionsbutton_popup, #jchat_wall_popup, #jchat_userstab_popup, #jchat_default_suggestion_tooltip, #jchat_tooltip").removeClass("jchat_tabopen").addClass("jchat_sidebar_hidden")
                    }
                }
                if (firstCall && isGuest) {
                    $("span.jchat_open_privatemess, span.jchat_privatemess").remove()
                }
                if (firstCall && !wallStartState && $.jStorage.get("publicChatStateClosed") === null) {
                    $("div.jchat_userstabtitle").trigger("click")
                }
                if (firstCall && wallHistoryDelayAutoload && !currentJoinedChatRoom && !window.sessionStorage.getItem("wall_autoload_history")) {
                    $("#jchat_wall_popup div.jchat_trigger_history_wall").trigger("click", [true]);
                    window.sessionStorage.setItem("wall_autoload_history", 1)
                }
                if (firstCall && renderingMode == "module" && separateWidgets) {
                    $("#jchat_userstab_popup, #jchat_base").css({position: "fixed"});
                    $("#jchat_wall_popup").addClass("jchat_tabopen")
                }
                if (firstCall && privateChatTitle) {
                    $("span.jchat_privatechattitle").text(privateChatTitle)
                }
                if (firstCall && publicChatTitle) {
                    $("span.jchat_publicchattitle").text(publicChatTitle)
                }
                firstCall = false;
                sound = true;
                if (!groupChat || (groupChat > 1 && jchatDetectMobileDevice(groupChat))) {
                    $("#jchat_wall_popup").remove();
                    $("span.jchat_contact").hide()
                }
                if (!buddyListVisible || (!allowGuestBuddylist && isGuest)) {
                    $("#jchat_userstab_popup").removeClass("jchat_tabopen").hide();
                    if (renderingMode == "auto" || (renderingMode == "module" && separateWidgets)) {
                        $("#jchat_wall_popup").css({bottom: "27px", top: "inherit"})
                    }
                }
                if (groupChatMode != "invite") {
                    $("span.jchat_contact").hide()
                }
                if (!usersBanning || (isGuest && !allowGuestBanning)) {
                    $("span.jchat_banning").hide()
                }
                if (!enableModeration || !userPermissions.moderation_groups) {
                    $("span.jchat_ban_moderator").hide()
                }
                if (groupChatMode != "chatroom") {
                    $("div.jchat_trigger_room").hide()
                }
                if (!searchFieldEnabled) {
                    $("#jchat_users_search").hide()
                }
                if (!showMyUsername) {
                    $("#jchat_myusername").hide()
                }
                if (!messagesHistory) {
                    $("div.jchat_trigger_history_wall").hide()
                }
                if (!maximizeButton || (maximizeButton > 1 && !jchatDetectMobileDevice(maximizeButton))) {
                    $("#jchat_maximizebutton").remove();
                    $("#jchat_maximize_wall").remove()
                }
                if (window.sessionStorage) {
                    if (isGuest && guestEnabledMode == 2 && !sessionStorage.getItem("jchat_joined")) {
                        clearTimeout(U);
                        $("#jchat_base").hide();
                        $("#jchat_wall_popup, #jchat_userstab_popup").removeClass("jchat_tabopen");
                        if (showSuggestionTooltip) {
                            suggestionTooltip(true)
                        }
                    }
                }
            }
        })
    }

    function makeSidebarResizable() {
        if (!resizableSidebar || renderingMode == "module") {
            return false
        }
        $("#jchat_userstab_popup").resizable({
            handles: "w",
            minWidth: 260,
            maxWidth: 500,
            alsoResize: "#jchat_userstab, #jchat_wall_popup",
            resize: function (event, ui) {
                $(ui.element).css("left", "auto");
                $("#jchat_wall_popup .jchat_userstabsubtitle").width($(ui.element).width() - 7);
                var wallPopupInlineStyles = $("#jchat_wall_popup").attr("style").removeAttr
            },
            stop: function (event, ui) {
                $.jStorage.set("sidebarWidth", ui.size.width);
                var wallPopupInlineStyles = $("#jchat_wall_popup").attr("style");
                $("#jchat_wall_popup").removeAttr("style");
                $("#jchat_wall_popup").attr("style", wallPopupInlineStyles.replace(/\sheight:\s\d+px;/gi, ""))
            }
        });
        $("#jchat_wall_popup").resizable({
            handles: "w",
            minWidth: 260,
            maxWidth: 500,
            alsoResize: "#jchat_userstab, #jchat_userstab_popup",
            resize: function (event, ui) {
                $(ui.element).css("left", "auto");
                $("#jchat_wall_popup .jchat_userstabsubtitle").width($(ui.element).width() - 7);
                popupsReposition()
            },
            stop: function (event, ui) {
                $.jStorage.set("sidebarWidth", ui.size.width)
            }
        })
    }

    function refreshMsgFileStatus(idPopupUser, idMessage) {
        $("#jchat_message_" + idMessage + " span.jchat_chatboxmessagecontent span.filestatus", "#jchat_user_" + idPopupUser + "_popup").prev().remove();
        $("#jchat_message_" + idMessage + " span.jchat_chatboxmessagecontent span.filestatus", "#jchat_user_" + idPopupUser + "_popup").html(jchat_sent_file_downloaded_realtime);
        if ($("#jchat_right_messagescolumn").length) {
            $("span.jchat_chatboxmessagecontent span.filestatus", "div.jchat_chatboxmessage[data-messageid=" + idMessage + "]").prev().remove();
            $("span.jchat_chatboxmessagecontent span.filestatus", "div.jchat_chatboxmessage[data-messageid=" + idMessage + "]").html(jchat_sent_file_downloaded_realtime)
        }
        JChatNotifications.playCompleteFile()
    }

    function injectSkypeControls(userObject) {
        if (!userObject.skypeid || !skypeEnabled) {
            return false
        }
        var appendDomElems = function () {
            if (skypeInstalled) {
                var domElems = '<div class="jchat_skypecall"><a data-text="' + jchat_startskypecall + '" class="skypeicon" href="skype:' + userObject.skypeid + '?call"></a></div>'
            } else {
                var domElems = '<div class="jchat_skypecall disabled"><a class="skypeicon" data-text="' + jchat_startskypedownload + '" href="http://www.skype.com/intl/en/get-skype/" target="_blank"></a></div>'
            }
            $("#jchat_userlist_" + userObject.id).append(domElems);
            $("#jchat_userlist_" + userObject.id + " div.jchat_skypecall a").on("click", function (event) {
                event.stopPropagation()
            }).on("mouseover", function (event) {
                triggerGenericPopover("#jchat_skypecall", this)
            }).on("mouseout", function (event) {
                $("#jchat_skypecall").css("display", "none").remove()
            })
        };
        var skypeInstalled = true;
        appendDomElems();
        $("div.jchat_skypecall a").click(function (event) {
            event.preventDefault();
            var startcallTimeout = 200;
            if (jchatHasTouch()) {
                startcallTimeout = 1500
            }
            setTimeout(function () {
                $(event.target).trigger("mouseout");
                window.location = $(event.target).attr("href")
            }, startcallTimeout)
        });
        return skypeInstalled
    }

    function renderTicketForm(selector) {
        if (!$("#lamform", "div.jchat_nousers").length) {
            var formContainer = $("<form/>").attr("name", "lamform").attr("id", "lamform").attr("class", "ajaxform");
            $(selector).append(formContainer);
            var validationLabel = $("<label/>").text(jchat_lamform_required).attr("class", "jchat_label_validate");
            var nameLabel = $("<label/>").text(jchat_lamform_name).attr("class", "jchat_label_title");
            var nameInput = $("<input/>").attr("name", "lam_name").attr("data-validation", "required");
            $(formContainer).append(nameLabel).append($(validationLabel.clone(true))).append(nameInput);
            var emailLabel = $("<label/>").text(jchat_lamform_email).attr("class", "jchat_label_title");
            var emailInput = $("<input/>").attr("name", "lam_email").attr("data-validation", "required email");
            $(formContainer).append(emailLabel).append($(validationLabel.clone(true))).append(emailInput);
            var messageLabel = $("<label/>").text(jchat_lamform_message).attr("class", "jchat_label_title");
            var messageArea = $("<textarea/>").attr("name", "lam_message").attr("data-validation", "required");
            $(formContainer).append(messageLabel).append($(validationLabel.clone(true))).append(messageArea);
            var submitDivButton = $("<div/>").text(jchat_lamform_submit).attr("class", "jchat_submit_lam_form");
            $(formContainer).append(submitDivButton);
            $(submitDivButton).on("click", function (event) {
                sendTicketMessage(formContainer)
            });
            if (affectPublicChat) {
                $("#jchat_wall_popup").addClass("jchat_lamform_hidden")
            }
            $("#jchat_userstab_popup").css("min-height", "220px");
            $("#jchat_userstab_icon, #jchat_userstab_text").addClass("jchat_lamform");
            $("#jchat_closesidebarbutton,#jchat_optionsbutton,#jchat_maximizebutton,#jchat_users_search,#jchat_myusername,span.jchat_privatechattitle").addClass("jchat_lamform");
            $("span.jchat_lamform_title").remove();
            $("div.jchat_userstabtitle").append('<span class="jchat_lamform_title">' + chatTitle + "</span>")
        }
    }

    function autoOpenAgentChatbox() {
        if ($("#jchat_userstab").hasClass("jchat_userstabclick")) {
            return
        }
        if (window.sessionStorage.getItem("dummy_agent_message")) {
            return
        }
        $(document).one("click", "#jchat_userstab", function (jqEvent) {
            var usersListCollection = $("div.jchat_userlist");
            if (usersListCollection.length) {
                var maxCount = usersListCollection.length - 1;
                var targetAgentUser = Math.floor(Math.random() * (maxCount + 1));
                var targetChatboxContext = $(usersListCollection.get(targetAgentUser));
                targetChatboxContext.trigger("click");
                var userid = targetChatboxContext.data("sessionid");
                var imageAgentAvatar = $("#jchat_userlist_" + userid + " span.jchat_userscontentname > img").clone().prop("outerHTML") || "";
                $("#jchat_user_" + userid + "_popup div.jchat_tabcontent.messagelist").append('<div class="jchat_agent_dummy_message"><span class="jchat_chatboxmessagefrom">' + imageAgentAvatar + '</span><span class="jchat_chatboxmessagecontent">' + autoOpenAgentboxDefaultMessage + "<br></span></div>");
                window.sessionStorage.setItem("dummy_agent_message", 1)
            }
        })
    }

    function updateGroupChatUsers(op, value) {
        var postVars = {task: op, id: value};
        $.ajax({
            url: jsonLiveSite,
            data: postVars,
            type: "post",
            cache: false,
            dataType: "json",
            success: function (response) {
                if (!response.storing.status) {
                    showDebugMsgs(response.storing.details)
                }
            }
        })
    }

    function refreshStream() {
        h = 1;
        if (K > X) {
            K = X;
            clearTimeout(U);
            U = setTimeout(function () {
                ajaxReceive()
            }, X)
        }
    }

    function hasWebRTCSupport() {
        var supportMediaStream = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
        var supportPeerConnection = window.RTCPeerConnection || window.webkitRTCPeerConnection || window.mozRTCPeerConnection;
        if (!!supportMediaStream && !!supportPeerConnection) {
            return true
        } else {
            if (webrtcFallbackEnabled) {
                return true
            }
        }
        return false
    }

    function buildUserPermissions(parameters, userGroups) {
        $.each(userPermissions, function (action, permission) {
            var actionParamGroups = parameters[action];
            var actionGroups = $.map(actionParamGroups, function (value, key) {
                return value
            });
            if (actionGroups[0] == "0" || hasSuperUser) {
                userPermissions[action] = true
            } else {
                var userGroupsArray = $.map(userGroups, function (value, key) {
                    return value
                });
                var hasActionPermission = $.grep(userGroupsArray, function (groupId) {
                    var found = $.inArray(String(groupId), actionGroups) > -1;
                    return found
                });
                userPermissions[action] = !!hasActionPermission.length
            }
        });
        if (debugEnabled) {
            console.warn(userPermissions)
        }
    }

    (function startApp() {
        if (jchat_excludeonmobile && jchatDetectMobileDevice(jchat_excludeonmobile)) {
            if ($("#jchat_private_messaging").length) {
                $("#jchat_base, #jchat_optionsbutton_popup, #jchat_wall_popup, #jchat_userstab_popup, #jchat_default_suggestion_tooltip, #jchat_tooltip").remove()
            } else {
                return false
            }
        }
        initializeOptionsDiv();
        isContentEditable = jchatSupportContentEditable();
        if (!isContentEditable) {
            writeElement = '<textarea class="jchat_textarea" ></textarea>';
            writeElementWall = '<textarea class="jchat_textarea jchat_textarea_wall"></textarea>';
            valFunction = "val"
        }
        initializeWall();
        initializeDom();
        $(window).on("resize scroll", popupsReposition);
        b("buddylist", "1");
        b("initialize", "1");
        b("updatesession", "0");
        $([window, document]).blur(function () {
            J = false
        }).focus(function () {
            if (J == false) {
                l = 1
            }
            J = true
        });
        if ($.jStorage.get("popupPositions", false)) {
            popupPositions = $.jStorage.get("popupPositions")
        }
        if ($.jStorage.get("popupDimensions", false)) {
            popupDimensions = $.jStorage.get("popupDimensions")
        }
        if ($.jStorage.get("popupState", false)) {
            popupState = $.jStorage.get("popupState")
        }
        if ($.jStorage.get("popupMaximizedState", false)) {
            popupMaximizedState = $.jStorage.get("popupMaximizedState")
        }
        if ($.jStorage.get("nofluxchatboxes", false)) {
            nofluxChatBoxes = $.jStorage.get("nofluxchatboxes")
        }
        if ($.jStorage.get("minimizedIndexes", false)) {
            minimizedIndexes = $.jStorage.get("minimizedIndexes")
        }
        var overridenDefaultSidebarWidth = null;
        if (parseInt(jchat_sidebar_default_width_override) > 0) {
            overridenDefaultSidebarWidth = parseInt(jchat_sidebar_default_width_override);
            $("body > #jchat_wall_popup, #jchat_userstab_popup").width(overridenDefaultSidebarWidth);
            $("#jchat_userstab").width(overridenDefaultSidebarWidth - 2)
        }
        if ($.jStorage.get("sidebarWidth", false)) {
            var overridenSidebarWidth = parseInt($.jStorage.get("sidebarWidth"));
            if (overridenSidebarWidth != 260 || overridenDefaultSidebarWidth) {
                $("#jchat_wall_popup, #jchat_userstab_popup").width(overridenSidebarWidth);
                $("#jchat_userstab").width(overridenSidebarWidth - 2)
            }
        }
        if ($.jStorage.get("privateMessagingCounters", false)) {
            privateMessagingCounters = $.jStorage.get("privateMessagingCounters")
        }
        if (sessionStorage.getItem("popupLanguages")) {
            popupLanguages = JSON.parse(sessionStorage.getItem("popupLanguages"))
        }
        if (window.sessionStorage) {
            if (!parseInt(jchat_userid) && jchat_guestenabled == 2 && !sessionStorage.getItem("jchat_joined")) {
                $("#jchat_base").hide();
                $("#jchat_wall_popup, #jchat_userstab_popup").removeClass("jchat_tabopen")
            }
        }
        ajaxReceive();
        if ($("#jchat_privatemessaging_textarea").length) {
            var sendingPrivateMessage = function (jqEvent) {
                var loggedid = $(jqEvent.target).data("loggedid");
                if (jqEvent.keyCode == 13 && jqEvent.shiftKey == 0 && !loggedid) {
                    injectFloatingMsg(jchat_select_user_receiver);
                    jqEvent.preventDefault();
                    return false
                }
                if (loggedid) {
                    var sessionid = $("div.jchat_userlist[data-loggedid=" + loggedid + "] span.jchat_contact", "div.jchat_tabcontent.userslist").data("userid") || 0;
                    var notOpen = !$("#jchat_user_" + sessionid + "_popup").length;
                    return sendMessage(jqEvent, this, sessionid, loggedid, notOpen)
                }
            };
            $(document).on("keydown", "#jchat_privatemessaging_textarea", sendingPrivateMessage);
            $("div.jchat_userslist_reply").on("click", function (jqEvent) {
                jqEvent.keyCode = 13;
                jqEvent.shiftKey = 0;
                jqEvent.target = $("#jchat_privatemessaging_textarea").get(0);
                sendingPrivateMessage.call(jqEvent.target, jqEvent)
            })
        }
    })();
    return function activateStream() {
        ajaxReceive()
    }
}
jQuery(function (a) {
    window.jchatApplicationStreamActivate = a.jchat(a);
    var b = false;
    if (jchatHasTouch()) {
        a(document).on("touchstart", "body", function (c) {
            if (!b) {
                JChatNotifications.initPlayEmptySound();
                b = true
            }
            a(".jchat_generic_tooltip, #jchat_users_informations_tooltip, #jchat_tooltip, #jchat_avatartooltip").hide()
        })
    }
});