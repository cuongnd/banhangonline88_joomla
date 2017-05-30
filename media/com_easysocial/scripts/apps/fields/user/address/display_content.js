EasySocial.module('apps/fields/user/address/display_content', function($) {
    var module = this;

    EasySocial
    .require()
    .library('gmaps')
    .done(function() {
        EasySocial.Controller('Field.Address.Display', {
            defaultOptions: {
                latitude: null,
                longitude: null,
                ratio: 1,

                '{base}': '[data-location-base]',

                '{map}': '[data-location-map]',
                '{mapImage}': '[data-location-map-image]'
            }
        }, function(self) {
            return {
                init: function() {
                    // Init params
                    var map = self.map();

                    self.options.latitude = map.data('latitude');
                    self.options.longitude = map.data('longitude');

                    self.setLayout();
                },

                '{window} resize': $.debounce(function() {
                    self.setLayout();
                }, 250),

                navigate: function(lat, lng) {
                    var mapImage = self.mapImage(),
                        width = Math.floor(mapImage.width()),
                        height = Math.floor(mapImage.height()),
                        url = $.GMaps.staticMapURL({
                            size: [width, height],
                            lat: lat,
                            lng: lng,
                            markers: [
                                {lat: lat, lng: lng}
                            ]
                        });

                    mapImage
                        .attr("src", url)
                        .data({
                            width: width,
                            height: height
                        });

                    self.base().addClass("has-location");
                },

                setLayout: function() {
                    setTimeout(function() {
                        if (self.options.latitude && self.options.longitude) {
                            self.navigate(self.options.latitude, self.options.longitude);
                        }
                    }, 1);
                },

                '{self} onShow': function() {
                    self.setLayout();
                }
            }
        });

        module.resolve();
    });
});
