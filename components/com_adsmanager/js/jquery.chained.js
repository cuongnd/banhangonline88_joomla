/*
 * Chained - jQuery non AJAX(J) chained selects plugin
 *
 * Copyright (c) 2010-2011 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 */

(function($) {

	var backupoptions = [];
    $.fn.chained = function(parent_selector, options) { 
        
        return this.each(function() {
            
            /* Save this to self because this changes when scope changes. */            
            var self   = this;
            var backup;
			if (this.id in backupoptions) {
				backup = backupoptions[this.id];
			} else {
				backup = $(self).clone();
				backupoptions[this.id] = backup;
			}
                        
            /* Handles maximum two parents now. */
            $(parent_selector).each(function() {
                                                
                $(this).bind("change", function() {
                    $(self).html(backup.html());

                    /* If multiple parents build classname like foo\bar. */
                    var selected = "";
                    $(parent_selector).each(function() {
                        if (typeof $(":selected", this).val() != 'undefined') {
                            selected += "\\" + $(":selected", this).val();
                        } else {
                            selected += "\\" + $(this).val();
                        }
                    });
                    selected = selected.substr(1);

                    /* Also check for first parent without subclassing. */
                    /* TODO: This should be dynamic and check for each parent */
                    /*       without subclassing. */
                    var first = $(parent_selector).first();
                    var selected_first = $(":selected", first).val();
                
                    $("option", self).each(function() {
                        /* Remove unneeded items but save the default value. */
                        if (!$(this).hasClass(selected) && 
                            !$(this).hasClass(selected_first) && $(this).val() !== "") {
                                $(this).remove();
                        }                        
                    });
                
                    /* If we have only the default value disable select. */
                    if (1 == $("option", self).size() && $(self).val() === "") {
                        $(self).attr("disabled", "disabled");
                    } else {
                        $(self).removeAttr("disabled");
                    }
                    $(self).trigger("change");
                });
                
                /* Force IE to see something selected on first page load, */
                /* unless something is already selected */
                if ( !$("option:selected", this).length ) {
                    $("option", this).first().attr("selected", "selected");
                }
	    
                /* Force updating the children. */
                $(this).trigger("change");             

            });
        });
    };
    
    /* Alias for those who like to use more English like syntax. */
    $.fn.chainedTo = $.fn.chained;
    
})(jQ);

/*
 * Remote Chained - jQuery AJAX(J) chained selects plugin
 *
 * Copyright (c) 2010-2011 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 */

(function($) {

    $.fn.remoteChained = function(parent_selector, url, options) { 
    	
    	// default configuration properties
		var defaults = {	
				defaultvalue : ""
		}; 
			
		var options = $.extend(defaults, options); 
        
        return this.each(function() {
            
            /* Save this to self because this changes when scope changes. */            
            var self   = this;
            var backup = $(self).clone();
            
            if (options.defaultvalue) {
            	var option = $("<option selected='selected'/>").val(options.defaultvalue).append("    ");
            	$(self).append(option);   
        	}
                        
            /* Handles maximum two parents now. */
            $(parent_selector).each(function() {
                $(this).bind("change", function() {

                    /* Build data array from parents values. */
                    var data = {};
                    $(parent_selector).each(function() {
                        var id = $(this).attr("id");
                        if (typeof $(":selected", this).val() != 'undefined') {
                        	var value = $(":selected", this).val();
                        } else {
                        	var value = $(this).val();
                        }
                        data[id] = value;
                    });
                    
                    
                    $.getJSON(url, data, function(json) {
                    	
                    	selectedoption = $("option:selected",self).val();

                        /* Clear the select. */
                        $("option", self).remove();

                        /* Add new options from json. */
                        for (var key in json) {
                			if (!json.hasOwnProperty(key)) {
                                continue;
                            }
                            /* This sets the default selected. */
                            if ("selected" == key) {
                                continue;
                            }
                            var option = $("<option />").val(json[key].value).append(json[key].label);
                            $(self).append(option);    
                        }
                        
                        if (selectedoption != "") {
                        	//alert(selectedoption);
                        	if ($("option[value='"+selectedoption+"']",self).size() != 0) {
                        		$("option[value='"+selectedoption+"']",self).attr("selected","selected");
                        		$(self).val(selectedoption);
                        		//alert("set");
                        	}
                    	}

                        /* If we have only the default value disable select. */
                        if (1 == $("option", self).size() && $(self).val() === "") {
                            $(self).attr("disabled", "disabled");
                        } else {
                            $(self).removeAttr("disabled");
                        }
                        
                        /* Force updating the children. */
                        $(self).trigger("change");
                        
                    });
                });	
                /* Force updating the children. */
                if ($("option:selected",this).html() != "    ") {
                	$(this).trigger("change");
                }
            });
        });
    };
    
    /* Alias for those who like to use more English like syntax. */
    $.fn.remoteChainedTo = $.fn.remoteChained;
    
})(jQ);

