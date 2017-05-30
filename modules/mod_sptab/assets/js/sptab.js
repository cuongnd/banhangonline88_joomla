/*---------------------------------------------------------------
# SP Tab - Next generation tab module for joomla
# ---------------------------------------------------------------
# Author - JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2014 JoomShaper.com. All Rights Reserved.
# license - PHP files are licensed under  GNU/GPL V2
# license - CSS  - JS - IMAGE files  are Copyrighted material 
# Websites: http://www.joomshaper.com
-----------------------------------------------------------------*/

// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

!(function($){

	var spTab = {
		init: function( options, elem ) {
			var self = this

			self.elem = elem
			self.$elem = $( elem )
			self.options = $.extend( {}, $.fn.sptab.options, options )

			self.build()
			self.display()
		},

		build: function() {
			var self 		= this

			self.$elem.children().wrapAll('<div class="items_mask" />')
			var items 		= self.$elem.find('.items_mask').children()
			var handlers 	= self.$elem.find('.title')
			var button_code	= '<div class="tabs_buttons"><div class="tabs_mask"><ul class="tabs_container"></ul></div></div>'

			//generate handlers
			var tabs_buttons = ( self.options.btnPos == 'top' ) ? self.$elem.prepend( button_code ) : self.$elem.append( button_code )
			
			var title = '';
			handlers.each(function(index){
				this.cls = (index === 0 ) ? 'tab first-tab active' : 'tab'
				title += '<li class="' + this.cls + '">'
				title += $(this).html()
				title += '</li>'
			});

			$(tabs_buttons).find('.tabs_container').append( title )
			$(items).find('.title').remove()

		},

		display: function() {
			var self 		= this
			var mask 		= self.$elem.find('.items_mask')
			var items 		= self.$elem.find('.items_mask').children()
			var handlers 	= self.$elem.find('.tabs_container').children()
			var isSliding 	= false

			items.removeAttr('style').css({
				'display': 'none'
			}).first().css({
				'display': 'block'
			})

			$(handlers).each( function(index) {

				$(this).on(self.options.activator, function(event) {

					if ( $(this).hasClass('active') ) return
					
					if ( isSliding 	== true ) return

					isSliding 		= true
					
					this.prevIndex 	= handlers.filter('.active').index()
					this.curIndex 	= $(this).index()

					if( this.curIndex > this.prevIndex )
					{
						this.directtion = 'left'
					}
					else
					{
						this.directtion = 'right'
					}

					this.current 	= $(items)[ this.curIndex ]
					this.previous 	= $(items)[ this.prevIndex ]
					
					handlers.removeClass('active');
					$(this.previous).removeClass('active');
					$(this.current).addClass('active');
					$(this).addClass('active');

					//Animation fade
					if( self.options.animation == 'fade' )
					{
						$(mask).animate({
							height: $(this.current).outerHeight()
						}, self.options.duration)

						$( this.previous ).css('display', 'none')
						$( this.current ).fadeIn( self.options.duration, self.options.transition, function(){
							$(mask).removeAttr('style')
							isSliding = false
						})
					}
					else if( self.options.animation == 'scroll:horizontal' )
					{
						$(mask).animate({
							height: $(this.current).outerHeight()
						}, self.options.duration)

						$( this.previous ).css({
							'display': 'block',
							'position': 'absolute',
							'overflow': 'hidden',
							'top': 0,
							'left': 0,
							'width': $(mask).outerWidth(),
							'height': $( this.previous ).outerHeight()
						}).animate({
								'left': (this.directtion == 'left') ? -$(mask).outerWidth() : $(mask).outerWidth()
							}, self.options.duration, self.options.transition, function(){
								$(this).removeAttr('style').css('display', 'none')
								isSliding = false
						})

						$( this.current ).css({
							'display': 'block',
							'position': 'absolute',
							'overflow': 'hidden',
							'top': 0,
							'left': ( this.directtion == 'left' ) ? $(mask).outerWidth() : -$(mask).outerWidth(),
							'width': $(mask).outerWidth(),
							'height': $( this.current ).outerHeight()
						}).animate({
								'left': 0
							}, self.options.duration, self.options.transition, function(){
								$(this).removeAttr('style').css('display', 'block')
								$(mask).removeAttr('style')
								isSliding = false
						})

					}
					else if(  self.options.animation == 'scroll:vertical' )
					{
						$(mask).css( 'height', $( this.current ).outerHeight() )

						$( this.previous ).css({
							'display': 'block',
							'position': 'absolute',
							'overflow': 'hidden',
							'top': 0,
							'left': 0,
							'width': $(mask).outerWidth(),
							'height': $( this.previous ).outerHeight()
						}).animate({
								'top': ( this.directtion == 'left' ) ? -$( this.previous ).outerHeight() : $( this.previous ).outerHeight()
							}, self.options.duration, self.options.transition, function(){
								$(this).removeAttr('style').css('display', 'none')
								isSliding = false
						});

						$( this.current ).css({
							'display': 'block',
							'position': 'absolute',
							'overflow': 'hidden',
							'top': ( this.directtion == 'left' ) ? $( this.current ).outerHeight() : -$( this.current ).outerHeight(),
							'left': 0,
							'width': $(mask).outerWidth(),
							'height': $( this.current ).outerHeight()
						}).animate({
								'top': 0
							}, self.options.duration, self.options.transition, function(){
								$(this).removeAttr('style').css('display', 'block')
								$(mask).removeAttr('style')
								isSliding = false
						});

					}
					else
					{
						$( this.previous ).css('display', 'none')
						$( this.current ).css('display', 'block')
						isSliding = false
					}


				})
			})
		}
	}

	$.fn.sptab = function( options ) {
		return this.each(function() {
			var sptab = Object.create( spTab )
			sptab.init( options, this )

		})
	}

	$.fn.sptab.options = {
		animation: 'scroll:horizontal',
		duration: 400,
		transition: 'linear',
		btnPos: 'top',
		activator: 'click'
	}

})(jQuery);