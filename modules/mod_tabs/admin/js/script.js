
jQuery(document).ready(function(){
    var typeTabs =  jQuery('#jformparamstype');
    var selectPosition =  jQuery('#jformparamsmoduleID-position').parent().parent();
    //console.log(selectPosition);

    loadPosition();
    function loadPosition() {
        if(typeTabs.val()=='moduleID') {
            selectPosition.show();
        } else {
            selectPosition.hide();
        }
    }
    typeTabs.on('change', function() {
        loadPosition();
    });


   var position = jQuery('#jform_params_title_position');
    var align = jQuery('#jform_params_tab_alignment-lbl').parent().parent();

    align.hide();
    loadAlign();
    function loadAlign(){
        if(position.val()=='bot' || position.val()=='top') {
            align.show();
        } else {
            align.hide();
        }
    }
    // change position on general tabs
    position.on('change',function(e){
        loadAlign();
    });
});
