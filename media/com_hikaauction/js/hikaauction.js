/**
 * @package    HikaAuction for Joomla!
 * @version    1.2.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
(function(){
	var timeCounter = function(params,elems,cb) { this.init(params,elems); };
	timeCounter.prototype = {
		_start:0,
		_end:0,
		_callback:null,
		_current:0,
		_diff:0,
		_format:true,
		_target:{},
		_interval:null,
		_period:250,
		init:function(params,elems,cb){
			var date = new Date();
			this._current = (date.getTime() / 1000);
			this._start = params.start;
			this._period = params.period || 200;

			this._callback = cb || null;

			this._end = params.end;

			this._diff = (this._current - this._start);

			if(params.autostart) {
				this.initElems(elems);
				if(params.start)
					this.start();
			} else
				this._elems = elems;
		},
		initElems:function(elems) {
			this._target = {};
			var d = document;
			for(var k in elems) {
				if(!elems.hasOwnProperty(k))
					continue;

				if(typeof elems[k] == 'string')
					this._target[k] = d.getElementById(elems[k]);
				else
					this._target[k] = elems[k];
			}
		},
		onEnd: function(cb) {
			this._callback = cb;
		},
		step:function(){
			if(this._target.length == 0) {
				this.pause();
				return;
			}
			var val = null,
				time = 0,
				date = new Date();

			this._current = (date.getTime() / 1000);
			time = this._end - this._current + this._diff;

			if(time <= 0) {
				this.pause();
				if(this._callback) {
					try{
						this._callback(this._end, this._target, this);
					}catch(e){};
				}
				return;
			}

			for(var k in this._target) {
				if(!this._target.hasOwnProperty(k))
					continue;
				val = null;
				switch(k) {
					case 'd':
						val = this.process(time, 86400, 100000);
						break;
					case 'h':
						val = this.process(time, 3600, 24);
						break;
					case 'm':
						val = this.process(time, 60, 60);
						break;
					case 's':
						val = this.process(time, 1, 60);
						break;
				}
				if(val === null)
					this._target[k].innerHTML = '';
				else
					this._target[k].innerHTML = val;
			}
		},
		process: function(secs, n1, n2) {
					s = ((Math.floor(secs / n1)) % n2).toString();
					if(this._format && s.length < 2)
						s = '0' + s;
					return  s;
			},
		active:function(){
			return (this._interval !== null);
		},
		pause:function(){
			if(this._interval === null)
				return;
			clearInterval(this._interval);
			this._interval = null;
		},
		start:function(){
			if(this._interval !== null)
				return;

			if(this._elems) {
				this.initElems(this._elems);
				this._elems = null;
			}
			var t = this;
			this._interval = setInterval(function(){ t.step(); }, this._period);
		}
	};
	window.timeCounter = timeCounter;

	var auctionPage = {
		prices:{},
		bid: function(el, product_id) {
			var bid = document.getElementById("bid_amount");
			var bid_amount = this.convertNumber(bid.value);
			var starting_price = document.getElementById("starting_price");
			var starting_amount = this.convertNumber(starting_price.value);
			var bid_increment = document.getElementById("bid_increment");
			var bid_increment_value = this.convertNumber(bid_increment.value);

			switch (this.prices.bidding_mode) {
					case 'bid_increment_bidding':
						if (isNaN(bid_amount) || bid_amount == 0.0 || bid_amount < this.prices.base)
								return false;
						break;
					case 'current_price_bidding':
						if (isNaN(bid_amount) || bid_amount == 0.0 || bid_amount < this.prices.base)
							return false;
						break;
					case 'free_bidding':
						if(isNaN(bid_amount) || bid_amount == 0.0 || bid_amount < starting_amount)
							return false;
						break;
			}
			return true;
		},
		convertNumber: function(val) {
			val = val.replace(/\s/g, '');
			var ret = parseFloat(val);
			if(!isNaN(ret))
				return ret;
			return val;
		}
	};
	window.auctionPage = auctionPage;
})();
