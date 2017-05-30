
if (typeof jQuery != 'undefined') {
    (function($) {
        $(document).ready(function(){
            $('.carousel').each(function(index, element) {
                $(this)[index].slide = null;
            });
        });
    })(jQuery);
}

/*
if (typeof jQuery != 'undefined' && typeof MooTools != 'undefined' ) {
    Element.implement({
        slide: function(how, mode){
            return this;
        }
    });
}
    */