/**
 * JCH Optimize - Plugin to aggregate and minify external resources for
 * optmized downloads
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall
 * @license GNU/GPLv3, See LICENSE file
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

var timer = null;
var files=[], subdirs=[], current=0, total=0, optimize=0, dir='', log_path='', params={};
 
function jchOptimizeImages(page) {
       
        params.pro_downloadid = jQuery("input[id$='pro_downloadid']").val();
        params.hidden_api_secret = jQuery("input[id$='hidden_api_secret']").val();
        params.kraken_backup = jQuery("input[id$='kraken_backup']").val();
      
        if (params.pro_downloadid.length == 0)
        {
                alert(jch_noproid);
                return false;
        }

        var li = jQuery("#file-tree-container ul.jqueryFileTree").find("li.expanded").last();

        if (jQuery("#files-container input[type=checkbox]:checked").length) {
                dir = {path: li.find("a").attr("rel")};

                jQuery("#files-container li.directory input[type=checkbox]:checked").each(function () {
                        subdirs.push(jQuery(this).val());
                });

                jQuery("#files-container li.file input[type=checkbox]:checked").each(function () {
                        var file = {};
                        
                        file.path = jQuery(this).val();
                        
                        if(jQuery(this).parent().parent().find("input[name=width]").val().length){
                                file.width = jQuery(this).parent().parent().find("input[name=width]").val();
                        };
                        
                        if(jQuery(this).parent().parent().find("input[name=height]").val().length){
                                file.height = jQuery(this).parent().parent().find("input[name=height]").val();
                        };
                        
                        files.push(file);
                });

                jQuery("#optimize-images-container")
                        .html('<div id="progressbar"></div>\
                         <div><ul id="optimize-log"><li>Optimizing images. Please wait...</li></ul></div>');
                jQuery("#progressbar").progressbar({value: 0});

                updateStatus(page, dir, params, 'getfiles');
        } else {
                alert(jch_message);
        }
}
;

function updateStatus(page, file, params, task) {

        var timestamp = getTimeStamp();
        
        var xhr = jQuery.ajax({
                dataType: 'json',
                url: jch_ajax_optimizeimages + '&_=' + timestamp,
                data: {'dir': file, 'subdirs': subdirs, 'params': params, 'task': task},
                success: function (response) {
                       
                        if (current == 0)
                        {
                                files = jQuery.merge(files, response.data.files);
                                total = files.length;
                                
                                log_path = response.data.log_path;
                        }
                        else
                        {
                                pbvalue = Math.floor((current / total) * 100);

                                if (pbvalue > 0) {
                                        jQuery('#progressbar').progressbar({
                                                value: pbvalue
                                        });

                                        jQuery('ul#optimize-log').append('<li>' + response.message + '</li>');
                                }
                                
                                if(response.success)
                                {
                                        optimize++;
                                }
                        }

                        if (response.code === 500 || response.code === 403)
                        {
                                window.location.href = page + "&status=fail&msg=" + encodeURIComponent(response.message);
                                return false;
                        }
                        
                        if (total == current)
                        {
                                done = true;
                                jQuery("ul#optimize-log").append('<li>Adding logs to ' + log_path + '/plg_jch_optimize.logs.php...</li>');
                                setTimeout(function () {
                                        jQuery("ul#optimize-log").append('<li>Done!</li>');
                                }, 1000);
                                window.location.href = page + "&dir=" + encodeURIComponent(dir.path) + "&cnt=" + optimize;
                        }
                        else
                        {
                                file = files[current];
                                current++;
                                
                                updateStatus(page, file, params, 'optimize');
                        }
                },
                fail: function (jqXHR) {

                        jQuery("#progressbar").progressbar({
                                value: 100
                        });
                        window.location.href = page + "&status=fail&msg=" + encodeURIComponent(jqXHR.status + ": " + jqXHR.statusText);
                }
        });
}

