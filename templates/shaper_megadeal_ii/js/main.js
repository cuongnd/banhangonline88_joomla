/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
jQuery(function($) {

    // Off Canvas Menu
    $('#offcanvas-toggler').on('click', function(event){
        event.preventDefault();
        $('body').addClass('offcanvas');
    });

    $( '<div class="offcanvas-overlay"></div>' ).insertBefore( '.body-innerwrapper > .offcanvas-menu' );

    $('.close-offcanvas, .offcanvas-overlay').on('click', function(event){
        event.preventDefault();
        $('body').removeClass('offcanvas');
    });

    //add pull-push class in wrapper
    $( ".megadeal-main-wrapper .sppb-col-sm-9" ).addClass( "col-sm-push-3" );
    $( ".megadeal-main-wrapper .sppb-col-sm-3" ).addClass( "col-sm-pull-9" );
    
    //Mega Menu
    $('.sp-megamenu-wrapper').parent().parent().css('position','static').parent().css('position', 'relative');
    $('.sp-menu-full').each(function(){
        $(this).parent().addClass('menu-justify');
    });

    //Sticky Menu
    $(document).ready(function(){
        $("body.sticky-header").find('#sp-top-bar').sticky({topSpacing:0})
    });

    //Tooltip
    $('[data-toggle="tooltip"]').tooltip();

    // Select
    $(document).on('click', function(e) {
        var selector = $('.sp-select');
        if (!selector.is(e.target) && selector.has(e.target).length === 0) {
            selector.find('ul').slideUp();
        }
    });

    $('select').each(function(event) {
        $(this).hide();
        var $self = $(this);
        var spselect  = '<div class="sp-select">';
        spselect += '<div class="sp-select-result">';
        spselect += '<span class="sp-select-text">' + $self.find('option:selected').text() + '</span>';
        spselect += ' <i class="fa fa-angle-down"></i>';
        spselect += '</div>';
        spselect += '<ul class="sp-select-dropdown">';

        $self.children().each(function(event) {
            if($self.val() == $(this).val()) {
                spselect += '<li class="active" data-val="'+ $(this).val() +'">' + $(this).text() + '</li>';
            } else {
                spselect += '<li data-val="'+ $(this).val() +'">' + $(this).text() + '</li>';
            }
        });

        spselect += '</ul>';
        spselect += '</div>';
        $(this).after($(spselect));
    });

    $(document).on('click', '.sp-select', function(event) {
        $('.sp-select').not(this).find('ul').slideUp();
        $(this).find('ul').slideToggle();
    });

    $(document).on('click', '.sp-select ul li', function(event) {
        var $select = $(this).closest('.sp-select').prev('select');
        $(this).parent().prev('.sp-select-result').find('span').html($(this).text());
        $(this).parent().find('.active').removeClass('active');
        $(this).addClass('active');
        $select.val($(this).data('val'));
        $select.change();
    });
    // End Select


    $('.sp-vmslider-ii-slide .owl-stage .owl-item.active:last-child').addClass('test');

    // virtiemart cart
    var toggle = $('.spvm-cart-toggle'),
    vm_cart = $('.megadeal-vm-carts-product-wrapper');

    toggle.on('click', function() {
        vm_cart.fadeToggle();
    });

    // virtuemart product details countdown
    $('.sp-vm-countdown[data-countdown]').each(function() {
      var $this = $(this), finalDate = $(this).data('countdown');
      $this.countdown(finalDate, function(event) {
         $this.html(event.strftime('<span class="sp-vm-slide-day">%D days,</span> <span class="sp-vm-slide-time">%H:%M:%S</span>'))}).on('finish.countdown', function() {
            $(this).html('<span class="sp-vm-slide-finished">Deal is over</span>');
          });
    });
    
    $(document).on('click', '.sp-rating .star', function(event) {
        event.preventDefault();

        var data = {
            'action':'voting',
            'user_rating' : $(this).data('number'),
            'id' : $(this).closest('.post_rating').attr('id')
        };

        var request = {
                'option' : 'com_ajax',
                'plugin' : 'helix3',
                'data'   : data,
                'format' : 'json'
            };

        $.ajax({
            type   : 'POST',
            data   : request,
            beforeSend: function(){
                $('.post_rating .ajax-loader').show();
            },
            success: function (response) {
                var data = $.parseJSON(response.data);

                $('.post_rating .ajax-loader').hide();

                if (data.status == 'invalid') {
                    $('.post_rating .voting-result').text('You have already rated this entry!').fadeIn('fast');
                }else if(data.status == 'false'){
                    $('.post_rating .voting-result').text('Somethings wrong here, try again!').fadeIn('fast');
                }else if(data.status == 'true'){
                    var rate = data.action;
                    $('.voting-symbol').find('.star').each(function(i) {
                        if (i < rate) {
                           $( ".star" ).eq( -(i+1) ).addClass('active');
                        }
                    });

                    $('.post_rating .voting-result').text('Thank You!').fadeIn('fast');
                }

            },
            error: function(){
                $('.post_rating .ajax-loader').hide();
                $('.post_rating .voting-result').text('Failed to rate, try again!').fadeIn('fast');
            }
        });
    });

});