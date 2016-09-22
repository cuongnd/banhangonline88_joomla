Joomla.submitbutton = function (pressbutton) {
    if (pressbutton == 'show_link')
    {
        $jMaQma('.nav-tabs li:eq(11) a').tab('show');
        showLink(0);
        return;
    }

    var form = document.adminForm;
    if ($jMaQma("#integrate_mtree1").is(":checked") && $jMaQma("#integrate_sobi1").is(":checked")) {
        alert(IMQM_WARNING);
        return false;
    }
    Joomla.submitform(pressbutton, document.getElementById('adminForm'));
}

function getLinks() {
    $jMaQma("div#loading").show();
    $jMaQma("div#linkscpanel").load("index.php?option=com_maqmahelpdesk&task=config_ajax&page=links&tmpl=component&format=raw",
        {
            action:'list',
            section:'A'
        },
        function () {
            $jMaQma("div#loading").hide();
        }
    );
}

function showLink(ID) {
    if (ID == 0) {
        ACTION = 'new';
    } else {
        ACTION = 'edit';
    }
    $jMaQma("div#loading").show();
    $jMaQma("div#linkscpanel").load("index.php?option=com_maqmahelpdesk&task=config_ajax&page=links&tmpl=component&format=raw",
        {
            action:ACTION,
            section:'A',
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
    $jMaQma("div#loading").show();
    $jMaQma("div#linkscpanel").load("index.php?option=com_maqmahelpdesk&task=config_ajax&page=links&tmpl=component&format=raw",
        {
            action:'save',
            id_wk:0,
            id:document.adminForm.link_id.value,
            name:document.adminForm.link_name.value,
            description:document.adminForm.link_description.value,
            link:document.adminForm.link_url.value,
            ordering:document.adminForm.link_ordering.value,
            image:document.adminForm.link_image.value,
            section:'A',
            public:0,
            published:PUBLISHED
        },
        function () {
            $jMaQma("div#loading").hide();
        }
    );
}

function deleteLink(ID) {
    $jMaQma("div#loading").show();
    $jMaQma("div#linkscpanel").load("index.php?option=com_maqmahelpdesk&task=config_ajax&page=links&tmpl=component&format=raw",
        {
            action:'delete',
            id_wk:0,
            section:'A',
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
    $jMaQma("div#linkscpanel").load("index.php?option=com_maqmahelpdesk&task=config_ajax&page=links&tmpl=component&format=raw",
        {
            action:'saveorder',
            section:'A',
            orders:ORDERS
        },
        function () {
            $jMaQma("div#loading").hide();
        });
}

$jMaQma(document).ready(function () {
    getLinks();
    $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
});
