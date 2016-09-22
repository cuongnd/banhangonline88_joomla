EasySocial.module( 'site/search/map' , function($){
	var module	= this;

    // Create search template first
    $.template('easysocial/maps.suggestion', '<div class="es-story-location-suggestion" data-location-suggestion><span class="formatted_address">[%= location.formatted_address %]</span></div>');

	EasySocial.require()
	.library('gmaps')
	.done( function(){

		EasySocial.Controller(
		'Search.Map',
		{
			defaultOptions:
			{
                "{icon}" : "[data-loaction-icon]",
                "{locationLabel}" : "[data-location-label]",

                "{detectButton}" : "[data-location-detect]",
                "{suggestions}"  : "[data-location-suggestions]",
                "{suggestion}"      : "[data-location-suggestion]",
                "{autocomplete}" : "[data-location-autocomplete]",

                // form elements
                "{dataCondition}" : "[data-condition]",
                "{frmDistance}" : "[data-distance]",
                "{frmAddress}" : "[data-address]",
                "{frmLatitude}" : "[data-latitude]",
                "{frmLongitude}" : "[data-longitude]",

                view: {
                    suggestion: 'maps.suggestion'
                }
			}
		},
		function( self ){
			return {

				init : function()
				{

				},

                "{detectButton} click": function() {

                    self.icon()
                            .removeClass('ies-power')
                            .addClass('btn-loading');

                    clearTimeout(self.detectTimer);

                    self.detectTimer = setTimeout(function() {
                        self.base().removeClass("is-busy");
                    }, 8000);

                    $.GMaps.geolocate({
                        success: function(position) {
                            $.GMaps.geocode({
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                                callback: function(locations, status) {
                                    if (status=="OK") {
                                        self.suggest(locations);
                                    }
                                }
                            });
                        },
                        error: function(error) {
                            var message = "";

                            switch (error.code) {

                                case 1:
                                    message = $.language("COM_EASYSOCIAL_LOCATION_PERMISSION_ERROR");
                                    break;

                                case 2:
                                    message = $.language("COM_EASYSOCIAL_LOCATION_TIMEOUT_ERROR");
                                    break;

                                case 3:
                                default:
                                    message = $.language("COM_EASYSOCIAL_LOCATION_UNAVAILABLE_ERROR");
                                    break;
                            }

                            EasySocial.dialog({
                                content: message
                            });
                        },
                        always: function() {
                            clearTimeout(self.detectTimer);

                            self.icon()
                                    .removeClass('btn-loading')
                                    .addClass('ies-power');
                        }
                    });
                },

                "{suggestion} click": function(suggestion, event) {

                    var location = suggestion.data("location");

                    var lat = location.geometry.location.lat(),
                        lng = location.geometry.location.lng(),
                        address = location.formatted_address,
                        distance = self.frmDistance().val();

                    self.frmAddress().val(address);
                    self.frmLatitude().val(lat);
                    self.frmLongitude().val(lng);

                    var computedVal = distance + '|' + lat + '|' + lng + '|' + address;
                    self.dataCondition().val(computedVal);

                    self.locationLabel().html(address);
                    self.locationLabel().removeClass('hide');

                    self.clearSuggestions();
                },

                suggest: function(locations) {

                    self.clearSuggestions();

                    var suggestions = self.suggestions();

                    if (locations.length < 0) return;

                    self.results = locations;

                    $.each(locations, function(i, location){
                        // Create suggestion and append to list
                        self.view.suggestion({
                                location: location
                            })
                            .data("location", location)
                            .appendTo(suggestions);
                    });

                    self.autocomplete().addClass('active');
                },

                clearSuggestions: function() {
                    self.autocomplete().removeClass('active');
                    // Clear location suggestions
                    self.suggestions().empty();
                }


			} //return
		});

		module.resolve();

	});

});
