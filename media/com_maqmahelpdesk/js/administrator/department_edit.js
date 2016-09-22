Joomla.submitbutton = function (pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'workgroup') {
        Joomla.submitform(pressbutton);
        return;
    }

    if (form.wkdesc.value == "") {
        alert(MQM_NAME_REQUIRED);
    } else {
        CheckHTMLEditor();
        Joomla.submitform(pressbutton, document.getElementById('adminForm'));
    }
}

/* Assignments JS */
function getAssigns() {
    $jMaQma("div#loading").show();
    $jMaQma("div#assigns").load("index.php?option=com_maqmahelpdesk&task=workgroup_ajax&page=assignments&tmpl=component&format=raw",
        {
            action:'list',
            id_wk:$jMaQma("#id").val()
        },
        function () {
            $jMaQma("div#loading").hide();
        });
}

function showAssign(WK, CAT, CIT, USER) {
    if (CAT == 0) {
        ACTION = 'new';
    } else {
        ACTION = 'edit';
    }

    $jMaQma("div#loading").show();
    $jMaQma("div#assigns").load("index.php?option=com_maqmahelpdesk&task=workgroup_ajax&page=assignments&tmpl=component&format=raw",
        {
            action:ACTION,
            id_wk:$jMaQma("#id").val(),
            id_cat:CAT,
            city:CIT,
            id_user:USER
        },
        function () {
            $jMaQma("div#loading").hide();
        });
}

function cancelAssign() {
    getAssigns();
}

function saveAssign() {
    $jMaQma("div#loading").show();
    $jMaQma("div#assigns").load("index.php?option=com_maqmahelpdesk&task=workgroup_ajax&page=assignments&tmpl=component&format=raw",
        {
            action:'save',
            id_wk:$jMaQma("#id").val(),
            id_cat:$jMaQma("#assign_cat").val(),
            city:$jMaQma("#city").val(),
            id_user:$jMaQma("#assign_user").val()
        },
        function () {
            $jMaQma("div#loading").hide();
        });
}

function deleteAssign(WK, CAT, CIT, USER) {
    $jMaQma("div#loading").show();
    $jMaQma("div#assigns").load("index.php?option=com_maqmahelpdesk&task=workgroup_ajax&page=assignments&tmpl=component&format=raw",
        {
            action:'delete',
            id_wk:WK,
            id_cat:CAT,
            city:CIT,
            id_user:USER
        },
        function () {
            $jMaQma("div#loading").hide();
        });
}

/* Links JS */
function getLinks() {
    $jMaQma("div#loading").show();
    $jMaQma("div#links").load("index.php?option=com_maqmahelpdesk&task=workgroup_ajax&page=links&tmpl=component&format=raw",
        {
            action:'list',
            section:'F'
        },
        function () {
            $jMaQma("div#loading").hide();
        });
}

function showLink(ID) {
    if (ID == 0) {
        ACTION = 'new';
    } else {
        ACTION = 'edit';
    }

    $jMaQma("div#loading").show();
    $jMaQma("div#links").load("index.php?option=com_maqmahelpdesk&task=workgroup_ajax&page=links&tmpl=component&format=raw",
        {
            action:ACTION,
            id_wk:$jMaQma("#id").val(),
            section:'F',
            id:ID
        },
        function () {
            $jMaQma("div#loading").hide();
        });
}

function cancelLink() {
    getLinks();
}

function saveLink() {
    if (document.adminForm.link_published0.checked) {
        PUBLISHED = 0;
    } else {
        PUBLISHED = 1;
    }

    if (document.adminForm.public0.checked) {
        PUBLIC = 0;
    } else {
        PUBLIC = 1;
    }

    $jMaQma("div#loading").show();
    $jMaQma("div#links").load("index.php?option=com_maqmahelpdesk&task=workgroup_ajax&page=links&tmpl=component&format=raw",
        {
            action:'save',
            id_wk:$jMaQma("#id").val(),
            id:document.adminForm.link_id.value,
            name:document.adminForm.link_name.value,
            description:document.adminForm.link_description.value,
            link:document.adminForm.link_url.value,
            ordering:document.adminForm.link_ordering.value,
            image:document.adminForm.link_image.value,
            section:'F',
            published:PUBLISHED,
            public:PUBLIC
        },
        function () {
            $jMaQma("div#loading").hide();
        });
}

function deleteLink(ID) {
    $jMaQma("div#loading").show();
    $jMaQma("div#links").load("index.php?option=com_maqmahelpdesk&task=workgroup_ajax&page=links&tmpl=component&format=raw",
        {
            action:'delete',
            id_wk:$jMaQma("#id").val(),
            section:'F',
            id:ID
        },
        function () {
            $jMaQma("div#loading").hide();
        });
}

function saveLinkOrder() {
    $jMaQma("div#loading").show();

    ORDERS = '';
    for (i = 0; i < document.adminForm.nr_links.value; i++) {
        ORDERS = ORDERS + document.getElementById('link' + i).value + '|' + document.getElementById('order' + i).value + ';';
    }

    $jMaQma("div#links").load("index.php?option=com_maqmahelpdesk&task=workgroup_ajax&page=links&tmpl=component&format=raw",
        {
            action:'saveorder',
            section:'F',
            orders:ORDERS
        },
        function () {
            $jMaQma("div#loading").hide();
        });
}

$jMaQma(document).ready(function () {
    $jMaQma(".equalheight").equalHeights();
    getLinks();
    getAssigns();
});