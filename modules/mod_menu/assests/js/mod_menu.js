//huong dan su dung
/*
 $('.mod_menu').mod_menu();

 mod_menu=$('.mod_menu').data('mod_menu');
 console.log(mod_menu);
 */

// jQuery Plugin for SprFlat admin mod_menu
// Control options and basic function of mod_menu
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.mod_menu = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for mod_menu
            //be sure to be same as colors on main.css or custom-variables.less
            module_id:0,
            children_menu_item:{},
            root_url:"",
            max_item_level_2:20,
            max_item_level_3:5

        }

        // current instance of the object
        var plugin = this;

        // this will hold the merged default, and user-provided options
        plugin.settings = {}

        var $element = $(element), // reference to the jQuery version of DOM element
            element = element;    // reference to the actual DOM element

        // the "constructor" method that gets called when the object is created
        plugin.get_list_ul = function (menu_id) {
            var children_menu_item =plugin.settings.children_menu_item;
            var root_url =plugin.settings.root_url;
            var max_item_level_2 =plugin.settings.max_item_level_2;
            var max_item_level_3 =plugin.settings.max_item_level_3;
            var list_ul=[];
            if(typeof children_menu_item[menu_id]!="undefined")
            {

                for(var i=0;i<children_menu_item[menu_id].length;i++){
                    if(i>=max_item_level_2-1)
                        break;
                    var menu_item=children_menu_item[menu_id][i];
                    var menu_id_1=menu_item.id;
                    var params=menu_item.params;
                    var menu_image=params.menu_image;
                    var html_menu_image=menu_image!=''?'<img class="image-menu-item" src="'+root_url+menu_image+'">':'';
                    if(typeof children_menu_item[menu_id_1]!="undefined")
                    {
                        var $ul=$('<ul></ul>');
                        var list_menu_item_level3=children_menu_item[menu_id_1];
                        var current_total_level_3=list_menu_item_level3.length;
                        if(current_total_level_3<max_item_level_3)
                        {
                            for(var k=0;k<max_item_level_3-current_total_level_3;k++){
                                var a_menu_item={};
                                a_menu_item.title='';
                                a_menu_item.link='';
                            }
                        }
                        for(var j=0;j<list_menu_item_level3.length;j++){
                            if(j>max_item_level_3)
                                break;
                            var menu_item1=children_menu_item[menu_id_1][j];
                            if(menu_item1.title!='')
                            {
                                var $li=$('<li><a href="'+menu_item1.flink+'"><span class="icon-caret-right"></span> '+menu_item1.title+'</a></li>');
                            }else{
                                var $li=$('<li>&nbsp;</li>');

                            }
                            $li.appendTo($ul);
                        }

                        var $div=$('<div class="group-menu group-menu-'+menu_item.id+'"><h5 class="title"><a href="'+menu_item.flink+'">'+html_menu_image+menu_item.title+'</a></h5></div>');
                        $ul.appendTo($div);
                    }else{
                        var $div=$('<div class="group-menu group-menu-'+menu_item.id+'"><a href="'+menu_item.link+'&Itemid='+menu_item.id+'">'+html_menu_image+menu_item.title+'</a></div>');
                    }
                    list_ul.push($div);

                }

            }
            return list_ul;
        };
        plugin.hexc=function(colorval) {
            var rgbvals = /rgb\((.+),(.+),(.+)\)/i.exec(colorval);
            var rval = parseInt(rgbvals[1]);
            var gval = parseInt(rgbvals[2]);
            var bval = parseInt(rgbvals[3]);
            return '#' + (
                    rval.toString(16) +
                    gval.toString(16) +
                    bval.toString(16)
                ).toUpperCase();
        }

        plugin.set_mouse_over_menu_item = function ($self) {
            var menu_id=$self.data('menu_id');



            var $container_content=$element.find('.container-content');
            $container_content.find('div.group-menu').remove();
            var list_ul=plugin.get_list_ul(menu_id);



            for(var i=0;i<list_ul.length;i++){
                var $ul=list_ul[i];
                $ul.appendTo($container_content);
            }
            var $container_home_page=$element.find('#container-'+menu_id);
            if($container_home_page.find('#container-'+menu_id).length==0)
            {

                var $container_home_page=$('<div class="container-home-page" id="container-'+menu_id+'"></div>');
                $container_home_page.hover(
                    function () {
                        $(this).addClass('active');
                        $(this).show();
                        var $active_level_1=$element.find('ul.level-0 li.menu-iem.level-1.menu-iem-'+menu_id+' > a');
                        var background_color=$active_level_1.data('background_color');
                        console.log(background_color);
                        $active_level_1.css({
                            background:background_color
                        });
                    },
                    function () {
                        $element.find('ul.level-0 li.menu-iem.level-1 > a').css({
                            background:"none"
                        });
                        $(this).removeClass('active');
                        $(this).hide();

                    }
                );

            }
            $container_content.appendTo($container_home_page);
            $container_home_page.appendTo($element);
            $element.find('.container-home-page').hide();
            $container_home_page.show();
            $.set_height($container_home_page.find('.container-content h5.title'));
        };
        plugin.set_mouseOut_menu_item = function ($self) {
            var menu_id=$self.data('menu_id');
        };
        plugin.init = function() {
            plugin.settings = $.extend({}, defaults, options);
            plugin.settings.active_level_1_background_color=null;
            var list_color='#d60c0c,#ff9800,#7cb342,#2bafa4,#105aa6,#ca64c2,#f57aa5,#ddaa62';
            list_color=list_color.split(',');
            var active_level_1_background_color;
            $element.find('ul.level-0 li.menu-iem.level-1 > a').each(function(){
                var color = list_color[Math.floor(Math.random()*list_color.length)];
                $(this).data('background_color',color);
                $(this).hover(
                    function () {
                        $element.find('ul.level-0 li.menu-iem.level-1 > a').css({
                            background:"none"
                        });
                        var background_color=$(this).data('background_color');
                        $(this).css({
                            background:background_color
                        });
                        plugin.set_mouse_over_menu_item($(this));
                    },
                    function () {

                        $(this).css({
                            background:"none"
                        });
                        plugin.set_mouseOut_menu_item($(this));
                        if (!$element.find('.container-home-page').hasClass('active')) {
                            $element.find('.container-home-page').hide();
                        }else {

                        }

                    }
                );


            });





        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.mod_menu = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('mod_menu')) {
                var plugin = new $.mod_menu(this, options);

                $(this).data('mod_menu', plugin);

            }

        });

    }

})(jQuery);
