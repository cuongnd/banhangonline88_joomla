
var openRows = {};

function showMore(rowId) {

    if (typeof openRows[rowId] === 'undefined' || !openRows[rowId]) {
        jQuery('#moreOptionsRow_'+rowId).show();
        openRows[rowId] = true;
    } else {
        jQuery('#moreOptionsRow_'+rowId).hide();
        openRows[rowId] = false;
    }

}