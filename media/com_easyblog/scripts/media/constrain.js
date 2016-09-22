EasyBlog.module('media/constrain', function($) {
	var module = this;

	EasyBlog.require().library('image').done(function() {
		$.fn.constrain = function() {
			var constrain = this.data('constrain');
			if(constrain instanceof $.Constrain) {
				if(arguments.length > 0) {
					constrain.update(arguments[0]);
				} else {
					return constrain;
				}
			} else {
				constrain = new $.Constrain(this, arguments[0]);
				this.data('constrain', constrain);
			}
		};

		$.Constrain = function(element, options) {

			var self = this;

			var defaultOptions = {
				selector: {
					width: '.inputWidth',
					height: '.inputHeight',
					constrain: '.inputConstrain'
				},
				forceConstrain: false
			}

			var userOptions = options ? options : {};

			self.options = $.extend(true, {}, defaultOptions, userOptions);

			self.options.element = {
				width: element.find(self.options.selector.width).data('type', 'width'),
				height: element.find(self.options.selector.height).data('type', 'height'),
				constrain: element.find(self.options.selector.constrain)
			}

			self.options.initial = self.options.initial || self.options.source;

			if(self.options.allowedMax !== undefined) {
				self.options.max = $.Image.resizeWithin(self.options.source.width, self.options.source.height, self.options.allowedMax.width, self.options.allowedMax.height);

				self.options.initial.width = Math.min(self.options.max.width, self.options.initial.width);
				self.options.initial.height = Math.min(self.options.max.height, self.options.initial.height);
			}

			self.options.element.width.data('initial', self.options.initial.width);
			self.options.element.height.data('initial', self.options.initial.height);

			self.fieldValues(self.calculate('width', self.options.initial));

			$.each([self.options.element.width, self.options.element.height], function(i, element) {

				var	thisType = element.data('type'),
					oppositeType = self.getOppositeType(thisType),
					element = $(element),
					opposite = self.options.element[oppositeType];

				element.bind('keyup', function(event) {
					if(event.keyCode == 9 || event.keyCode == 16) {
						return false;
					}

					self.fieldValues(self.calculate(thisType));
				});

				element.bind('blur', function() {
					if(!self.options.element.constrain.is(':checked') && $.trim(element.val()) == '') {
						element.val(self.options.initial[thisType]);
					}

					if($.trim(element.val()) == '' && $.trim(opposite.val()) == '') {
						element.val(self.options.initial[thisType]);
						opposite.val(self.options.initial[oppositeType]);
					}
				});
			});

			$(self.options.element.constrain).bind('change', function() {
				if($(this).is(':checked')) {
					var values = self.fieldValues(),
						type = (values.width === '') ? 'height' : 'width';

					self.fieldValues(self.calculate(type));
				}
			});
		};

		$.extend($.Constrain.prototype, {
			calculate: function(thisType, values) {
				var self = this,
					values = (values !== undefined) ? values : self.fieldValues(),
					oppositeType = self.getOppositeType(thisType),
					thisMax = self.options.max ? self.options.max[thisType] : undefined,
					oppositeMax = self.options.max ? self.options.max[oppositeType] : undefined,
					thisSource = self.options.source[thisType],
					oppositeSource = self.options.source[oppositeType],
					thisVal = values[thisType],
					oppositeVal = values[oppositeType];

				thisVal = (thisVal != '' && thisMax && thisVal > thisMax) ? thisMax : thisVal;

				if(this.enforceConstrain()) {
					if(thisVal == '') {
						oppositeVal = '';
					} else {
						var ratio = thisSource / thisVal;

						oppositeVal = Math.round(oppositeSource / ratio);

						if(oppositeMax && oppositeVal > oppositeMax) {
							oppositeVal = oppositeMax;
						}
					}
				}

				var result = {};
				result[thisType] = thisVal;
				result[oppositeType] = oppositeVal;
				return result;
			},

			getOppositeType: function(type) {
				return (type == 'width') ? 'height' : 'width';
			},

			getInput: function(type) {
				var self = this,
					value = self.options.element[type].val();

				value = $.trim(value);
				value = value.replace(new RegExp('[^0-9.]','g'), "");
				value = parseInt(value, 10);

				return isNaN(value) ? '' : value;
			},

			fieldValues: function(values) {
				var self = this,
					result = {};

				if(values === undefined) {
					// getter
					result.width = self.getInput('width');
					result.height = self.getInput('height');

				} else {
					// setter
					self.options.element.width.val(Math.floor(values.width));
					self.options.element.height.val(Math.floor(values.height));
					result = values;
				}

				return result;
			},

			enforceConstrain: function() {
				var self = this;
				return self.options.forceConstrain ? true : (self.options.element.constrain.length < 1) ? true : self.options.element.constrain.is(':checked');
			},

			update: function(options) {
				var self = this;

				// set initial first because initial might have previous source value
				self.options.initial = options.initial || options.source || self.options.initial;

				if(options.allowedMax !== undefined && options.source !== undefined) {
					options.max = $.Image.resizeWithin(options.source.width, options.source.height, options.allowedMax.width, options.allowedMax.height);

					self.options.initial.width = Math.min(options.max.width, self.options.initial.width);
					self.options.initial.height = Math.min(options.max.height, self.options.initial.height);
				}

				self.options = $.extend(true, {}, self.options, options);

				values = this.calculate('width', {
					width: self.options.initial.width || self.options.source.width,
					height: self.options.initial.height || self.options.source.height
				});

				this.fieldValues(values);
			}
		});

		module.resolve();
	});
});
