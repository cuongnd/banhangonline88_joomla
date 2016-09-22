EasySocial.module("locations", function($){

	var module = this;

	EasySocial.require().library("gmaps").done();

	EasySocial
		.require()
		.library(
			"scrollTo"
		)
		.view(
			"site/location/story.suggestion"
		)
		.language(
			"COM_EASYSOCIAL_AT_LOCATION"
		)
		.done(function(){

			// Constants
			var KEYCODE = {
				BACKSPACE: 8,
				COMMA: 188,
				DELETE: 46,
				DOWN: 40,
				ENTER: 13,
				ESCAPE: 27,
				LEFT: 37,
				RIGHT: 39,
				SPACE: 32,
				TAB: 9,
				UP: 38
			};

			EasySocial.Controller("Locations",
				{
					defaultOptions: {

						view: {
							suggestion: "site/location/story.suggestion"
						},

						map: {
							lat: 0,
							lng: 0
						},

						"{textField}": "[data-location-textField]",
						"{detectLocationButton}": "[data-detect-location-button]",

						"{suggestions}": "[data-location-suggestions]",
						"{suggestion}": "[data-story-location-suggestion]",

						"{mapPreview}": "[data-location-map]",

						"{latitude}" : "[data-location-lat]",
						"{longitude}": "[data-location-lng]"
					}
				},
				function(self) { return {

					init: function() {

						// I have access to:
						// self.panelButton()
						// self.panelContent()

						// Only show auto-detect button if the browser supports geolocation
						if (navigator.geolocation) {
							self.detectLocationButton().show();
						}

						// Allow textfield input only when controller is implemented
						EasySocial.require().library("gmaps")
							.done(function(){
								self.textField().removeAttr("disabled");
							});

					},

					navigate: function(lat, lng) {

						var mapPreview = self.mapPreview(),
							map = self.map;

						// Initialize gmaps if not initialized
						if (map===undefined) {

							map = self.map =
								mapPreview
									.show()
									.gmaps(self.options.map);
						}

						map.setCenter(lat, lng);
						map.removeMarkers();
						map.addMarker({lat: lat, lng: lng});
					},

					// Memoized locations
					locations: {},

					lastQueryAddress: null,

					"{textField} keypress": function(textField, event) {

						switch (event.keyCode) {

							case KEYCODE.UP:

								var prevSuggestion = $(
									self.suggestion(".active").prev(self.suggestion.selector)[0] ||
									self.suggestion(":last")[0]
								);

								// Remove all active class
								self.suggestion().removeClass("active");

								prevSuggestion
									.addClass("active")
									.trigger("activate");

								self.suggestions()
									.scrollTo(prevSuggestion, {
										offset: prevSuggestion.height() * -1
									});

								event.preventDefault();

								break;

							case KEYCODE.DOWN:

								var nextSuggestion = $(
									self.suggestion(".active").next(self.suggestion.selector)[0] ||
									self.suggestion(":first")[0]
								);

								// Remove all active class
								self.suggestion().removeClass("active");

								nextSuggestion
									.addClass("active")
									.trigger("activate");

								self.suggestions()
									.scrollTo(nextSuggestion, {
										offset: nextSuggestion.height() * -1
									});

								event.preventDefault();

								break;

							case KEYCODE.ENTER:

								var activeSuggestion = self.suggestion(".active"),
									location = activeSuggestion.data("location");
									self.set(location);

								self.suggestions().hide();
								break;

							case KEYCODE.ESCAPE:
								break;
						}

					},

					"{textField} keyup": function(textField, event) {

						switch (event.keyCode) {

							case KEYCODE.UP:
							case KEYCODE.DOWN:
							case KEYCODE.ENTER:
							case KEYCODE.ESCAPE:
								// Don't repopulate if these keys were pressed.
								break;

							default:
								var address = $.trim(textField.val());

								if (address==="") {
									self.suggestions().hide();
								}

								if (address==self.lastQueryAddress) return;

								var locations = self.locations[address];

								// If this location has been searched before
								if (locations) {

									// Just use cached results
									self.suggest(locations);

									// And set our last queried address to this address
									// so that it won't repopulate the suggestion again.
									self.lastQueryAddress = address;

								// Else ask google to find it out for us
								} else {

									self.lookup(address);
								}
								break;
						}
					},

					lookup: $._.debounce(function(address){

						$.GMaps.geocode({
							address: address,
							callback: function(locations, status) {

								if (status=="OK") {

									// Store a copy of the results
									self.locations[address] = locations;

									// Suggestion locations
									self.suggest(locations);

									self.lastQueryAddress = address;
								}
							}
						});

					}, 250),

					suggest: function(locations) {

						var suggestions = self.suggestions();

						// Clear location suggestions
						suggestions
							.hide()
							.empty();

						$.each(locations, function(i, location){

							// Create suggestion and append to list
							self.view.suggestion({
									location: location
								})
								.data("location", location)
								.appendTo(suggestions);
						});

						suggestions.show();
					},

					"{suggestion} activate": function(suggestion, event) {

						var location = suggestion.data("location");

						self.navigate(
							location.geometry.location.lat(),
							location.geometry.location.lng()
						);
					},

					"{suggestion} mouseover": function(suggestion) {

						// Remove all active class
						self.suggestion().removeClass("active");

						suggestion
							.addClass("active")
							.trigger("activate");
					},

					"{suggestion} click": function(suggestion, event) {

						var location = suggestion.data("location");

						self.set(location);

						self.suggestions().hide();
					},

					set: function(location) {

						self.currentLocation = location;

						var address = location.formatted_address;

						self.textField().val(address);

						// var caption = $.language("COM_EASYSOCIAL_AT_LOCATION", location.address_components[0].long_name);
						// self.story.addPanelCaption("locations", caption);

						self.latitude()
							.val(location.geometry.location.lat());

						self.longitude()
							.val(location.geometry.location.lng());

						self.trigger("locationChange", [location]);
					},

					"{detectLocationButton} click": function() {

						var map = self.map;

						$.GMaps.geolocate({
							success: function(position) {

								$.GMaps.geocode({
									lat: position.coords.latitude,
									lng: position.coords.longitude,
									callback: function(locations, status){
										if (status=="OK") {
											self.suggest(locations);
											self.textField().focus();
										}
									}
								});
							},
							error: function(error) {
								// error.message
							},
							always: function() {

							}
						});
					},

					"{story} save": function(el, element, save) {

						var currentLocation = self.currentLocation;

						if (!currentLocation) return;

						save.addData(self, {
							short_address    : currentLocation.address_components[0].long_name,
							formatted_address: currentLocation.formatted_address,
							lat              : currentLocation.geometry.location.lat(),
							lng              : currentLocation.geometry.location.lng()
						});
					},

					"{story} clear": function() {

						self.unset();
					}

				}}
			);

			// Resolve module
			module.resolve();

		});

});
