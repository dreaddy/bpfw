/**
* @version: 2.1.24
* @author: Torsten Lüders http://www.torsten-lueders.de/
* @copyright: Copyright (c) 2017 Torsten Lüders. All rights reserved.
* @license: Licensed under the MIT license. See http://www.opensource.org/licenses/mit-license.php
* @website: https://www.torsten-lueders.de/
*/



(function ($) {
    "use strict";

    $.dreaddyAjaxList = function (element2, options) 
    {
        
        var defaultSettings = {
            elementsPerPage: 5,
            paginationElements: 6,
            getElement: function (offset, count, prefix, putContentInto, putPaginationContentInto) {
  
                var retval = "";
                for (var i = offset; i < offset + count; i++) {
                    retval += "<li>Element " + i + "</li>";
                }
                return retval;
            }
        }

        var plugin = this;

        plugin.settings = {}

        plugin.prefix = "";
        plugin.putContentInto = "";
        plugin.putPaginationContentInto = "";

  

        var $element = $(element),  // reference to the jQuery version of DOM element the plugin is attached to
            element = element2;        // reference to the actual DOM element

    
        plugin.init = function () {

            if (options) {
            
                plugin.settings = $.extend({}, defaultSettings, options);
            } else {
              
                plugin.settings = defaultSettings;
            }
                    
            $(plugin.putContentInto + " .loadingScreen").hide();

            plugin.prefix = $(element).closest("ul").data("prefix");
            plugin.putContentInto = ".list-content-" + plugin.prefix;
            plugin.putPaginationContentInto = ".list-content-pagination-" + plugin.prefix;

            var putContentInto = plugin.putContentInto;
            var putPaginationContentInto = plugin.putPaginationContentInto;
            var prefix = plugin.prefix;
           
       
            plugin.setPage = function (buttonSelector, offset) {


                if (!$(this).closest("li").hasClass("disabled")) {


                    $(putContentInto + " .loadingScreen").show();

                    $(putPaginationContentInto).find("li").removeClass("active");
                    $(putPaginationContentInto).find("li").removeClass("color-white");
                    $(putPaginationContentInto).find("li").removeClass("disabled");

                    $(this).closest("li").addClass("active");
                    $(this).closest("li").addClass("color-white");
                    $(this).closest("li").addClass("disabled");

                    // data-content-into="mails-content-<?php echo $prefix; ?>" data-pagination-into="mails-content-pagination-<?php echo $prefix; ?>"

                    var count = plugin.settings.elementsPerPage;

                    // $(putContentInto).data("offset", offset);
                    // $(putContentInto).data("count", count);

                    if (isNaN(offset)){
                        offset = 0;
                    }
                    if (isNaN(count)){
                        count = 5;
                    }

                    $(putContentInto).attr("data-offset", offset);
                    $(putContentInto).attr("data-count", count);

                    plugin.settings.getElement(offset, count, plugin.prefix, plugin.putContentInto, plugin.putPaginationContentInto);

                }

            };
            
            // jQuery(document).on("click change", putPaginationContentInto + " .ajaxlist_refresh_content", handleAjaxListRefresh);

            plugin.showLoadingScreen = function (state) {
                if (state === true) {
                    $(plugin.putContentInto + " .loadingScreen").show();
                } else {
                    $(plugin.putContentInto + " .loadingScreen").hide();
                }
            };

            // TODO: is not called multiple times if selector has multiple items . . . (each...) 
            plugin.refresh = function () {


                $(putContentInto + " .loadingScreen").show();

                // data-content-into="mails-content-<?php echo $prefix; ?>" data-pagination-into="mails-content-pagination-<?php echo $prefix; ?>"

                var offset = $(plugin.putContentInto).attr("data-offset");
                var count = $(plugin.putContentInto).attr("data-count");

                if (isNaN(offset)){
                    offset = 0;
                }
                if (isNaN(count)){
                    count = 5;
                }

                plugin.settings.getElement(offset, count, plugin.prefix, plugin.putContentInto, plugin.putPaginationContentInto);

            };


        }


            plugin.init();

        }

        $.fn.dreaddyAjaxList = function(options) {

            return this.each(function () {

                // if plugin has not already been attached to the element
                if (undefined == $(this).data('dreaddyAjaxList')) {

                    // create a new instance of the plugin
                    // pass the DOM element and the user-provided options as arguments
                    var plugin = new $.dreaddyAjaxList(this, options);

                    // in the jQuery version of the element
                    // store a reference to the plugin object
                    // you can later access the plugin and its methods and properties like
                    // element.data('dreaddyAjaxList').publicMethod(arg1, arg2, ... argn) or
                    // element.data('dreaddyAjaxList').settings.propertyName
                    $(this).data('dreaddyAjaxList', plugin);

                }

            });


        }


  
})(jQuery);


jQuery(document).ready(function () {

    jQuery(document).on("click", ".ajaxlist_pagination_button",

        function (e) {

            if (!jQuery(this).parent().hasClass("disabled")) {

                var prefix = jQuery(this).closest("ul.pagination").data("prefix");

                var offset = ($(this).data("offset") - 1) * 5;

                jQuery("#list-content-" + prefix).data('dreaddyAjaxList').setPage(this, offset);

            }

            e.preventDefault();
            
        });

    });