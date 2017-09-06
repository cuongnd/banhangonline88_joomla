(function(ns){
// Enqueue class
var enqueue = function(fn) {
	var queue = [], locked = 1, working = 0, fn = fn,
		instance = function(){
			queue.push([this, arguments]);
			if (!locked) instance.execute();
		};
		instance.execute = function(){
			if (working) return;
			working = 1; locked = 0;
			var q; while(q = queue.shift()) { fn.apply(q[0], q[1]) };
			working = 0;
		};
		instance.lock = function(){
			locked = 0;
		};
	return instance;
};

// Private variables
var $, options, components = {}, initialized = 0, installers = [];

var self = window[ns] = {

	setup: function(o) {
		options = o; // Keep a copy of the options
		self.init(); // Try to initialize.
	},

	jquery: function(jquery) {
		if ($) return; // If jquery is already available, stop.
		$ = jquery; // Set self.$ to jquery object
		self.init(); // Try to initialize.
	},

	init: function() {
		if (initialized) {
			return; // If initialized, stop.
		}

		if ($ && options) { // If options & jquery is available,
			self.$ = $.initialize(options); // Initialize jquery
			self.plugin.execute(); // Execute any pending plugins
			initialized = 1;
		}
	},

	plugin: enqueue(function(name, factory) {
		factory.apply(self, [$]);
	}),

	module: enqueue(function(name, factory) {
		$.module(name, factory);
	}),

	installer: function(recipient, name, factory) {
		if (!installers[recipient]) installers[recipient] = []; // Create package array if this is the first time
		if (!name) return installers[recipient];
		var component = components[recipient]; // Get component
		if (component.registered) return component.install(name, factory); // If component exist, install straight away
		installers[recipient].push([name, factory]); // Keep the package to install later
	},

	component: function(name, options) {

		// Getter
		if (!name) {
			return components; // return list of components
		}

		if (!options) {
			return components[name]; // return component
		}

		// Registering
		if (typeof options === "function") {
			var component = options;
			component.registered = true;
			return components[name] = component;
		}

		// Setter
		var queue = [];

		var abstractQueue = function(method, context, args) {
			return {method: method, context: this, args: args};
		};

		var abstractMethod = function(method, parent, chain) {
			return function(){
				(chain || queue).push(abstractQueue(method, this, arguments));
				return parent;
			};
		};

		var abstractInstance = function(instance, methods, chain) {
			var i = 0;
			for (; i < methods.length; i++) {
				var method = methods[i];
				instance[method] = abstractMethod(method, instance, chain);
			};
			return instance;
		};

		var abstractChain = function(name, methods) {
			return function(){
				var chain = [abstractQueue(name, this, arguments)];
					queue.push(chain);
				return abstractInstance({}, methods, chain);
			};
		};

		queue.execute = function(){
			var component = components[name], i = 0;
			for (; i < queue.length; i++) {
				var fn = queue[i];
				if (Object.prototype.toString.call(fn)==='[object Array]') {
					var chain = fn, context = component, j = 0;
					for (; j < chain.length; j++) {
						context = context[chain[j].method].apply(context, chain[j].args);
					}
				} else {
					component[fn.method].apply(component, fn.args)
				}
			}
		};

		// Create abstract component
		var component = abstractInstance(
				function(){component.run.apply(this.arguments)},
				["run","ready","template","dialog"]
			);

			// Set reference to options & queue
			component.className = name;
			component.options = options;
			component.queue = queue;

			// Create abstract module method
			component.module = abstractChain(
				"module",
				["done","always","fail","progress"]
			);

			// Create abstract require method
			component.require = abstractChain(
				"require",
				["library","script","stylesheet","language","template","app","view","done","always","fail","progress"]
			);

		// Register component in global namespace
		window[name] = components[name] = component;

		if (initialized) {
			$.Component.register(component);
		}

		return component;
	}
};

})("FD40");

// Setup foundry
FD40.setup({
	"environment": window.es.environment,
	"source": "local",
	"mode": window.es.environment == "production" ? "compressed" : "uncompressed",
	"path": window.es.rootUrl + "/media/com_easysocial/scripts/vendors",
	"cdn": "",
	"extension":".js",
	"cdnPath": "",
	"rootPath": window.es.rootUrl,
	"basePath": window.es.rootUrl,
	"indexUrl": window.es.rootUrl + '/index.php',
	"token": window.es.token,
	"joomla":{
		"appendTitle": window.es.appendTitle,
		"sitename": window.es.siteName
	},
	"locale":{
		"lang": window.es.locale
	}
});

FD40.component("EasySocial", {
	"environment": window.es.environment,
	"source":"local",
	"mode": window.es.environment == "production" ? "compressed" : "uncompressed",
	"mode": "compressed",
	"baseUrl": window.es.baseUrl,
	"version":"2.0",
	"momentLang": window.es.momentLang,
	"ajaxUrl": window.es.ajaxUrl
});

!function(a){var x,b=" ",c="width",d="height",e="replace",f="classList",g="className",h="parentNode",i="fit-width",j="fit-height",k="fit-both",l="fit-small",m=i+b+j+b+k+b+l,n=function(a,b){return a.getAttribute("data-"+b)},o=function(a,b){return a["natural"+b[0].toUpperCase()+b.slice(1)]},p=function(a,b){return parseInt(n(a,b)||o(a,b)||a[b])},q=function(a,c){a[f]?a[f].add(c):a[g]+=b+c},r=function(a,c){a[g]=a[g][e](new RegExp("\\b("+c[e](/\s+/g,"|")+")\\b","g"),b)[e](/\s+/g,b)[e](/^\s+|\s+$/g,"")},s=function(a,b,c){a.style[b]=c+"px"},u=function(a,b,e,f,g,t,v,x,y,z){return!n(a,c)&&0===o(a,c)&&(a._retry||(a._retry=0))<=25?setTimeout(function(){a._retry++,u(a)},200):(b=a[h],e=b[h],f=e[h],g=n(b,"mode"),t=n(b,"threshold"),v=p(a,c),x=p(a,d),y=b.offsetWidth,z=b.offsetHeight,r(f,m),q(f,t>v&&t>x?function(){return s(a,c,v),s(a,d,x),l}():"cover"==g?function(b,c,d){return 1>y||1>z?(w.push(a),k):(b=y/z,c=y/v,d=z/x,1>b?z>x*c?j:i:b>1?y>v*d?i:j:1==b?1>=v/x?i:j:void 0)}():function(){return w.push(a),a.style.maxHeight="none",s(a,"maxHeight",b.offsetHeight),k}()),a.removeAttribute("onload"),void 0)},v=function(a,b){for(b=w,w=[];a=b.shift();)a[h]&&u(a)},w=[],y=function(){clearTimeout(x),x=setTimeout(v,500)},z=a.ESImageList||[];for(a.ESImage=u,a.ESImageRefresh=v,a.addEventListener?a.addEventListener("resize",y,!1):a.attachEvent("resize",y);z.length;)u(z.shift())}(window);
