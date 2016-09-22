/* Fix the mess that artisteer makes */

jQuery(document).ready(function () {

    setTimeout("fsj_fix_artisteer();", 250);
    setTimeout("fsj_fix_artisteer();", 500);
    setTimeout("fsj_fix_artisteer();", 1000);
    setTimeout("fsj_fix_artisteer();", 2000);
    setTimeout("fsj_fix_artisteer();", 4000);
    fsj_fix_artisteer();
})

function fsj_fix_artisteer() {
    try {
        jQuery('.fsj .art-button').removeClass('art-button');
    } catch (e) {
    }
}