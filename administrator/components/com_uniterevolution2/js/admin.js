
// modal js bug protection
window.addEvent = function(){};

//emulate tips
function Tips(var1, var2){}

var SqueezeBox = new function(){
	
	this.onClose = null;
	
	this.initialize = function(){}
	this.assign = function(){}
	
	/**
	 * open fancybox, set onclose function
	 */
	this.open = function(onCloseFunction){
		this.onClose = onCloseFunction;
		
		var url = "index.php?option=com_media&view=images&tmpl=component&author=&fieldid=field_image_dialog_choose";
		var options = {};
		options.handler = "iframe";
		options.size = {x:800,y:500};
		
		var objFancybox = jQuery("#fancybox_trigger");
		objFancybox.attr("href",url);
		jQuery("#fancybox_trigger").trigger("click");
	}
		
	/**
	 * close fancybox
	 */
	this.close = function(){
		jQuery("#fancybox-close").trigger("click");
	}
	
}


/**
 * 
 * on inset image, taken from iframe
 * document.ready is for protection
 */

var jInsertFieldValue, insertValueTimeout;

jQuery(document).ready(function(){
	
	if(!jInsertFieldValue){
		jInsertFieldValue = function(urlImage,fieldID){
			
			if(jQuery.now() < insertValueTimeout)
				return(false);
			
			insertValueTimeout = jQuery.now()+100;
			
			if(typeof SqueezeBox.onClose == "function")
				SqueezeBox.onClose(urlImage);
		}
	}
	
});


// Create Base64 Object
var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9+/=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/rn/g,"n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}



var UniteAdminRev = new function(){
	
	var t = this;
	
	var errorMessageID = null;
	var successMessageID = null;
	var ajaxLoaderID = null;
	var ajaxHideButtonID = null;
	var g_multiple_text_key = [];
	
	//video dialog vars:
	var lastVideoData = null;		//last fetched data
	var lastVideoCallback = null;   //last callback from video dialog return
	var colorPickerCallback = null;
	
	

	/**********************************
		-	SHOW INFO AND HIDE INFO	-
	********************************/

	t.showInfo = function(obj) {

		var info = '<i class="eg-icon-info"></i>';
		if (obj.type=="warning") info = '<i class="eg-icon-cancel"></i>';
		if (obj.type=="success") info = '<i class="eg-icon-ok"></i>';

		obj.showdelay = obj.showdelay != undefined ? obj.showdelay : 0;
		obj.hidedelay = obj.hidedelay != undefined ? obj.hidedelay : 0;

		// CHECK IF THE TOOLBOX WRAPPER EXIST ALREADY
		if (jQuery('#eg-toolbox-wrapper').length==0) jQuery('#viewWrapper').append('<div id="eg-toolbox-wrapper"></div>');

		// ADD NEW INFO BOX
		jQuery('#eg-toolbox-wrapper').append('<div class="eg-toolbox newadded">'+info+obj.content+'</div>');
		var nt = jQuery('#eg-toolbox-wrapper').find('.eg-toolbox.newadded');
		nt.removeClass('newadded');


		// ANIMATE THE INFO BOX
		punchgs.TweenLite.fromTo(nt,0.5,{y:-50,autoAlpha:0,transformOrigin:"50% 50%", transformPerspective:900, rotationX:-90},{autoAlpha:1,y:0,rotationX:0,ease:punchgs.Back.easeOut,delay:obj.showdelay});

		if (obj.hideon != "event") {
			nt.click(function() {
				punchgs.TweenLite.to(nt,0.3,{x:200,ease:punchgs.Power3.easeInOut,autoAlpha:0,onComplete:function() {nt.remove()}});
			})

			if (obj.hidedelay !=0 && obj.hideon!="click")
				punchgs.TweenLite.to(nt,0.3,{x:200,ease:punchgs.Power3.easeInOut,autoAlpha:0,delay:obj.hidedelay + obj.showdelay, onComplete:function() {nt.remove()}});
		} else  {
			jQuery('#eg-toolbox-wrapper').on(obj.event,function() {
				punchgs.TweenLite.to(nt,0.3,{x:200,ease:punchgs.Power3.easeInOut,autoAlpha:0,onComplete:function() {nt.remove()}});
			});
		}
		
	}
    
	/**
	 * escape html, turn html to a string
	 */
	t.htmlspecialchars = function(string){
		  return string
		      .replace(/&/g, "&amp;")
		      .replace(/</g, "&lt;")
		      .replace(/>/g, "&gt;")
		      .replace(/"/g, "&quot;")
		      .replace(/'/g, "&#039;");
	}	
	
	/**
	 * Find absolute position on the screen of some element
	 */	
	t.getAbsolutePos = function(obj){
	  var curleft = curtop = 0;
		if (obj.offsetParent) {
			curleft = obj.offsetLeft;
			curtop = obj.offsetTop;
			while (obj = obj.offsetParent) {
				curleft += obj.offsetLeft;
				curtop += obj.offsetTop;
			}
		}			
		return[curleft,curtop];
	}	
	
	t.stripslashes = function(str) {
		return (str + '').replace(/\\(.?)/g, function (s, n1) {
			switch (n1) {
				case '\\':
				return '\\';
				case '0':
				return '\u0000';
				case '':
				return '';
				default:
				return n1;
			}
		});
	}
	
	
	/**
	 * turn string value ("true", "false") to string 
	 */
	t.strToBool = function(str){
		
		if(str == undefined)
			return(false);
			
		if(typeof(str) != "string")
			return(false);
		
		str = str.toLowerCase();
		
		var bool = (str == "true")?true:false;
		return(bool);
	}
	
	/**
	 * set callback on color picker movement
	 */
	t.setColorPickerCallback = function(callbackFunc){
		colorPickerCallback = callbackFunc;
	}
	
	/**
	 * on color picker event. Pass the event further
	 */
	t.onColorPickerMoveEvent = function(event){
		
		if(typeof colorPickerCallback == "function")
			colorPickerCallback(event);
	}
	
	
	/**
	 * strip html tags
	 */
	t.stripTags = function(input, allowed) {
	    allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
	    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
	        commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
	    return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
	        return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
	    });
	}
	
	/**
	 * change rgb & rgba to hex
	 */
	t.rgb2hex = function(rgb) {
		if (rgb.search("rgb") == -1 || jQuery.trim(rgb) == '') return rgb; //ie6
		
		function hex(x) {
			return ("0" + parseInt(x).toString(16)).slice(-2);
		}
		
		if(rgb.indexOf('-moz') > -1){
			var temp = rgb.split(' ');
			delete temp[0];
			rgb = jQuery.trim(temp.join(' '));
		}
		
		if(rgb.split(')').length > 2){
			var hexReturn = '';
			var rgbArr = rgb.split(')');
			for(var i = 0; i < rgbArr.length - 1; i++){
				rgbArr[i] += ')';
				var temp = rgbArr[i].split(',');
				if(temp.length == 4){
					rgb = temp[0]+','+temp[1]+','+temp[2];
					rgb += ')';
				}else{
					rgb = rgbArr[i];
				}
				rgb = jQuery.trim(rgb);
				
				rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/);
				
				hexReturn += "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3])+" ";
			}
			
			return hexReturn;
		}else{
			var temp = rgb.split(',');
			if(temp.length == 4){
				rgb = temp[0]+','+temp[1]+','+temp[2];
				rgb += ')';
			}
			
			rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/);
			
			return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
		}
		
		
	}
	
	/**
	 * get transparency value from 0 to 100
	 */
	t.getTransparencyFromRgba = function(rgba, inPercent){
		var temp = rgba.split(',');
		if(temp.length == 4){
			inPercent = (typeof inPercent !== 'undefined') ? inPercent : true;
			return (inPercent) ? temp[3].replace(/[^\d.]/g, "") : temp[3].replace(/[^\d.]/g, "") * 100;
		}
		
		return false;
	}
	
	/**
	 * debug html on the top of the page (from the master view)
	 */
	t.debug = function(html){
		jQuery("#div_debug").show().html(html);
	}
	
	/**
	 * output data to console
	 */
	t.trace = function(data,clear){
		if(clear && clear == true)
			console.clear();	
		console.log(data);
	}
	
	/**
	 * show error message or call once custom handler function
	 */
	t.showErrorMessage = function(htmlError){
		//if(errorMessageID !== null){
		//	jQuery("#"+errorMessageID).show().html(htmlError);			
		//}else
		//	jQuery("#error_message").show().html(htmlError);
		
		t.showInfo({content:htmlError, type:"warning", showdelay:0, hidedelay:5, hideon:"click", event:"" });
		
		showAjaxButton();
	}

	/**
	 * hide error message
	 */
	var hideErrorMessage = function(){
		if(errorMessageID !== null){
			jQuery("#"+errorMessageID).hide();
			errorMessageID = null;
		}else
			jQuery("#error_message").hide();
	}
	
	
	/**
	 * set error message id
	 */
	t.setErrorMessageID = function(id){
		errorMessageID = id;
	}
	
	
	
	/**
	 * set success message id
	 */
	t.setSuccessMessageID = function(id){
		successMessageID = id;
	}
	
	/**
	 * show success message
	 */
	var showSuccessMessage = function(htmlSuccess){
		//var id = "#success_message";		
		//var delay = 2000;
		//if(successMessageID){
		//	id = "#"+successMessageID;
		//	delay = 500;
		//}
		
		t.showInfo({content:htmlSuccess, type:"success", showdelay:0, hidedelay:5, hideon:"", event:"" });
		
		//jQuery(id).show().html(htmlSuccess);
		//setTimeout("UniteAdminRev.hideSuccessMessage()",delay);
		
		showAjaxButton();
	}
	
	
	/**
	 * hide success message
	 */
	this.hideSuccessMessage = function(){
		
		if(successMessageID){
			jQuery("#"+successMessageID).hide();
			successMessageID = null;	//can be used only once.
		}
		else
			jQuery("#success_message").slideUp("slow").fadeOut("slow");
		
		showAjaxButton();
	}
	
	
	/**
	 * set ajax loader id that will be shown, and hidden on ajax request
	 * this loader will be shown only once, and then need to be sent again.
	 */
	this.setAjaxLoaderID = function(id){
		ajaxLoaderID = id;
	}
	
	/**
	 * show loader on ajax actions
	 */
	var showAjaxLoader = function(){
		if(ajaxLoaderID)
			jQuery("#"+ajaxLoaderID).show();
	}
	
	/**
	 * hide and remove ajax loader. next time has to be set again before "ajaxRequest" function.
	 */
	var hideAjaxLoader = function(){
		if(ajaxLoaderID){
			jQuery("#"+ajaxLoaderID).hide();
			ajaxLoaderID = null;
		}
	}
	
	/**
	 * set button to hide / show on ajax operations.
	 */
	this.setAjaxHideButtonID = function(buttonID){
		ajaxHideButtonID = buttonID;
	}
	
	/**
	 * if exist ajax button to hide, hide it.
	 */
	var hideAjaxButton = function(){
		if(ajaxHideButtonID){
			var doHide = ajaxHideButtonID.split(',');
			if(doHide.length > 1){
				for(var i = 0; i < doHide.length; i++){
					jQuery("#"+doHide[i]).hide();
				}
			}else{
				jQuery("#"+ajaxHideButtonID).hide();
			}
		}
	}
	
	/**
	 * if exist ajax button, show it, and remove the button id.
	 */
	var showAjaxButton = function(){
		if(ajaxHideButtonID){
			var doShow = ajaxHideButtonID.split(',');
			if(doShow.length > 1){
				for(var i = 0; i < doShow.length; i++){
					jQuery("#"+doShow[i]).show();
				}
			}else{
				jQuery("#"+ajaxHideButtonID).show();
			}
			ajaxHideButtonID = null;
		}		
	}
	
	
	/**
	 * Ajax request function. call wp ajax, if error - print error message.
	 * if success, call "success function" 
	 */
	t.ajaxRequest = function(action,data,successFunction){
			
		var objData = {
			action:g_uniteDirPlagin+"_ajax_action",
			client_action:action,
			nonce:g_revNonce,
			data:data
		}
		
		hideErrorMessage();
		showAjaxLoader();
		hideAjaxButton();
		
		jQuery.ajax({
			type:"post",
			url:ajaxurl,
			dataType: 'json',
			data:objData,
			success:function(response){
				hideAjaxLoader();
				
				if(!response){
					t.showErrorMessage("Empty ajax response!");
					return(false);					
				}

				if(response == -1){
					t.showErrorMessage("ajax error!!!");
					return(false);
				}
				
				if(response == 0){
					t.showErrorMessage("ajax error, action: <b>"+action+"</b> not found");
					return(false);
				}
				
				if(response.success == undefined){
					t.showErrorMessage("The 'success' param is a must!");
					return(false);
				}
				
				if(response.success == false){
					t.showErrorMessage(response.message);
					return(false);
				}
				
				//success actions:

				//run a success event function
				if(typeof successFunction == "function")
					successFunction(response);
				else{
					if(response.message)
						showSuccessMessage(response.message);
				}
				
				if(response.is_redirect)
					location.href=response.redirect_url;
			
			},		 	
			error:function(jqXHR, textStatus, errorThrown){
				hideAjaxLoader();
				
				if(textStatus == "parsererror")
					t.debug(jqXHR.responseText);
				
				t.showErrorMessage("Ajax Error!!! " + textStatus);
			}
		});
		
	}//ajaxrequest
	
	
	/**
	 * open new add image dialog
	 */
	var openNewImageDialog = function(title,onInsert,isMultiple){
		
		if(isMultiple == undefined)
			isMultiple = false;
		
		// Media Library params
		var frame = wp.media({
			title : title,
			multiple : isMultiple,
			library : { type : 'image'},
			button : { text : 'Insert' }
		});

		// Runs on select
		frame.on('select',function(){
			var objSettings = frame.state().get('selection').first().toJSON();
			
			var selection = frame.state().get('selection');
			var arrImages = [];
			
			if(isMultiple == true){		//return image object when multiple
			    selection.map( function( attachment ) {
			    	var objImage = attachment.toJSON();
			    	var obj = {};
			    	obj.url = objImage.url;
			    	obj.id = objImage.id;
			    	arrImages.push(obj);
			    });
				onInsert(arrImages);
			}else{		//return image url and id - when single
				onInsert(objSettings.url,objSettings.id);
			}
			    
		});

		// Open ML
		frame.open();
	}
	
	
	/**
	 * open old add image dialog
	 */
	var openOldImageDialog = function(title,onInsert){
		var params = "type=image&post_id=0&TB_iframe=true";
		
		params = encodeURI(params);
		
		tb_show(title,'media-upload.php?'+params);
		
		window.send_to_editor = function(html) {
			 tb_remove();
			 var urlImage = jQuery(html).attr('src');
			 if(!urlImage || urlImage == undefined || urlImage == "")
				var urlImage = jQuery('img',html).attr('src');
			
			onInsert(urlImage,"");	//return empty id, it can be changed
		}
	}
	
	
	/**
	 * upen "add image" dialog
	 */
	t.openAddImageDialog = function(title, onInsert, isMultiple){
		
		SqueezeBox.open(function(urlImage){
			
			if(urlImage != null && urlImage != ""){
				urlImage = g_urlBase + urlImage;
				onInsert(urlImage);	
			}
		});
		
	}
	
	
	/**
	 * open wordpress image dialog
	 */
	t.openAddImageDialogWP = function(title,onInsert,isMultiple){
		
		if(!title)
			title = 'Select Image';
		
		if(typeof wp != "undefined" && typeof wp.media != "undefined")
			openNewImageDialog(title,onInsert,isMultiple);
		else{
			openOldImageDialog(title,onInsert);
		}
		
	}	
	
	/**
	 * load css file on the fly
	 * replace current item if exists
	 */
	t.loadCssFile = function(urlCssFile, replaceID){
				
		urlCssFile = urlCssFile.replace("&amp;","&"); 
		
		var rand = Math.floor((Math.random()*100000)+1);
		
		if(urlCssFile.indexOf("?") == -1)
			urlCssFile += "?rand="+rand;
		else
			urlCssFile += "&rand="+rand;
		
		if(replaceID)
			jQuery("#"+replaceID).remove();
		
		jQuery("head").append("<link>");
		var css = jQuery("head").children(":last");
		css.attr({
		      rel:  "stylesheet",
		      type: "text/css",
		      href: urlCssFile
		});
		
		//replace current element
		if(replaceID)
			css.attr({id:replaceID});
	}	
	
	
	/**
	 * get show image url
	 */
	t.getUrlShowImage = function(imageID,width,height,exact){
		
		imageID = parseInt(imageID, 0)
		
		var urlImage = g_urlAjaxShowImage+"&img="+imageID;
		
		if(width)
			urlImage += "&w="+width;
		
		if(height)
			urlImage += "&h="+height;
		
		if(exact && exact == true)
			urlImage += "&t=exact";
		
		return(urlImage);
	}
	
	
	/**
	 * set html to youtube dialog
	 * if empty data - clear the dialog
	 */
	var setYoutubeDialogHtml = function(data){
		
		//if empty data - clear the dialog
		if(!data){
			jQuery("#video_content").html("");
			return(false);
		}
		
		var thumb = data.thumb_medium;
		
		var useURL = (jQuery.trim(jQuery('#input_video_preview').val()) != '') ? jQuery('#input_video_preview').val() : thumb.url;
		
		var html = '<div class="video-content-title">'+data.title+'</div>';
		html += '<img id="video-thumbnail-preview" src="'+useURL+'" width="'+thumb.width+'" height="'+thumb.height+'" alt="thumbnail">';
		html += '<div class="video-content-description">';
		if(typeof data.desc_small != "undefined") html += data.desc_small;
		html += '</div>';
		
		jQuery("#video_content").html(html);
	}
	
	
	/**
	 * pass youtube id or youtube url, and get the id
	 */
	var getYoutubeIDFromUrl = function(url){
		url = jQuery.trim(url);
		
		var video_id = url.split('v=')[1];
		if(video_id){
			var ampersandPosition = video_id.indexOf('&');
			if(ampersandPosition != -1) {
			  video_id = video_id.substring(0, ampersandPosition);
			}
		}else{
			video_id = url;
		}
		
		return(video_id);
	}

	
	/**
	 * get vimeo id from url
	 */
	var getVimeoIDFromUrl = function(url){
		url = jQuery.trim(url);
		
		var video_id = url.replace(/[^0-9]+/g, '');
		video_id = jQuery.trim(video_id);
		
		return(video_id);
	}
	
	
	
	/**
	 * youtube callback script, set and store youtube data, and add it to dialog
	 */
	t.onYoutubeCallback = function(obj){
		jQuery("#youtube_loader").hide();
		var desc_small_size = 200;
		
		//prepare data
		var data = {};
		data.id = jQuery("#youtube_id").val();
		data.id = jQuery.trim(data.id);
		data.video_type = "youtube";
		if(obj[0].width <= 170 || obj[0].height <= 140){
			data.title = 'YouTube: Maybe wrong YoutTube ID given';
		}else{
			data.title = 'YouTube';
		}
		data.author = 'YouTube';
		data.link = '';
		data.description = '';
		data.desc_small = '';
		
		if(data.description.length > desc_small_size)
			data.desc_small = data.description.slice(0,desc_small_size)+"...";
		
		data.thumb_small = {url:obj[0].src,width:320,height:240};
		data.thumb_medium = {url:obj[0].src,width:320,height:240};
		data.thumb_big = {url:obj[0].src,width:obj[0].width,height:obj[0].height};
		
		//set html in dialog
		setYoutubeDialogHtml(data);
		
		//set the youtube arguments
		var objArguments = jQuery("#input_video_arguments");
		if(objArguments.val() == "")
			objArguments.val(objArguments.data("youtube"));
		
		//store last video data
		lastVideoData = data;
		
		//show controls:
		jQuery("#video_hidden_controls").show();
	}
	
	
	/**
	 * vimeo callback script, set and store vimeo data, and add it to dialog
	 */	
	t.onVimeoCallback = function(obj){
		jQuery("#vimeo_loader").hide();
		
		var desc_small_size = 200;
		obj = obj[0];
		
		var data = {};
		data.video_type = "vimeo";
		data.id = obj.id;
		data.id = jQuery.trim(data.id);
		data.title = obj.title;
		data.link = obj.url;
		data.author = obj.user_name;
		
		data.description = obj.description;
		if(data.description.length > desc_small_size)
			data.desc_small = data.description.slice(0,desc_small_size)+"...";
		
		data.thumb_large = {url:obj.thumbnail_large,width:640,height:360};
		data.thumb_medium = {url:obj.thumbnail_medium,width:200,height:150};
		data.thumb_small = {url:obj.thumbnail_small,width:100,height:75};
		
		//set html in dialog
		setYoutubeDialogHtml(data);
		
		//set the youtube arguments
		var objArguments = jQuery("#input_video_arguments");
		objArguments.val(objArguments.data("vimeo"));
		
		//store last video data
		lastVideoData = data;
		
		//show controls:
		jQuery("#video_hidden_controls").show();
	}

	
	/**
	 * show error message on the dialog
	 */
	t.videoDialogOnError = function(){
		//if ok, don't do nothing
		if(jQuery("#video_hidden_controls").is(":visible"))
			return(false);
		
		//if error - show message
		jQuery("#youtube_loader").hide();
		var html = "<div class='video-content-error'>Video Not Found!</div>";
		jQuery("#video_content").html(html);
	}
	
	
	/**
	 * update video size enabled disabled according fullwidth properties
	 */
	var updateVideoSizeProps = function(){
		var isFullwidth = jQuery("#input_video_fullwidth").is(":checked");
		if(isFullwidth == true){	//disable
			//jQuery("#video_size_wrapper").hide();
			jQuery("#input_video_width, #input_video_height, #input_video_height_lbl, #input_video_width_lbl").hide();
			
		}else{		//enable
			//jQuery("#video_size_wrapper").show();
			jQuery("#input_video_width, #input_video_height, #input_video_height_lbl, #input_video_width_lbl").show();
			jQuery("#input_video_cover").prop("checked",false);
		}
		
		var isCover = jQuery("#input_video_cover").is(":checked");
		if(isCover == true){	//disable
			jQuery("#input_video_ratio_lbl, #input_video_ratio, #input_video_dotted_overlay_lbl, #input_video_dotted_overlay").show();
		}else{		//enable
			jQuery("#input_video_ratio_lbl, #input_video_ratio, #input_video_dotted_overlay_lbl, #input_video_dotted_overlay").hide();
		}
	}
	
	/**
	 * open dialog for youtube or vimeo import , add / update
	 */
	t.openVideoDialog = function(callback,objCurrentVideoData){
		
		lastVideoCallback = callback;
		
		var dialogVideo = jQuery("#dialog_video");
		
		//set buttons:
		var buttons = {
			"Close":function(){
				dialogVideo.dialog("close");
			}
		};
		
		//clear the dialog content
		setYoutubeDialogHtml(false);
		
		//enable fields:
		jQuery("#video_type_chooser").show();
		jQuery("#youtube_id,#vimeo_id").prop("disabled","").removeClass("input-disabled");
		
		//clear the fields
		jQuery("#input_video_arguments").val("");
		jQuery("#input_video_autoplay").prop("checked","");
		jQuery("#showautoplayfirsttime").hide();
		jQuery("#input_video_autoplay_first_time").prop("checked","");
		jQuery("#input_video_nextslide").prop("checked","");
		jQuery("#input_video_force_rewind").prop("checked","");
		jQuery("#input_video_fullwidth").prop("checked","");
		jQuery("#input_video_control").prop("checked","");
		jQuery("#input_video_mute").prop("checked","");
		jQuery("#input_disable_on_mobile").prop("checked","");
		jQuery("#input_video_cover").prop("checked","");
		jQuery("#input_video_dotted_overlay option[value='none']").attr("selected",true);
		jQuery("#input_video_ratio option[value='16:9']").attr("selected",true);
		jQuery('#input_video_preload option[value="auto"]').attr("selected",true);
		jQuery('#input_video_speed option[value="1"]').attr("selected",true);
		jQuery('#input_video_loop option[value="none"]').attr("selected",true);
		jQuery("#input_video_preview").val("");
		jQuery("#input_use_poster_on_mobile").prop("checked","");
		
		jQuery("#youtube_id").val("");
		jQuery("#vimeo_id").val("");
		
		jQuery("#video_hidden_controls").hide();
		
		jQuery("#fullscreenvideofun").hide();
				
		var buttonVideoAdd = jQuery("#button-video-add");
		buttonVideoAdd.text(buttonVideoAdd.data("textadd"));
		
		//open the dialog
		dialogVideo.dialog({
				buttons:buttons,
				minWidth:830,
				minHeight:820,
				modal:true,
				dialogClass:"tpdialogs"
		});
		
		//if update dialog open:		
		if(objCurrentVideoData)
			setVideoDialogUpdateMode(objCurrentVideoData);
		
		updateVideoSizeProps();
	}
	
	
	/**
	 * prepare the dialog for video update
	 */
	var setVideoDialogUpdateMode = function(data){
		
		data.id = jQuery.trim(data.id);
		
		jQuery("#video_type_chooser").hide();
		
		//disable fields:
		//jQuery("#youtube_id,#vimeo_id").prop("disabled","disabled").addClass("input-disabled");
		
		//set mode and video id
		switch(data.video_type){
			case "youtube":
				jQuery("#video-dialog-wrap").removeClass("html5select");
				jQuery("#video_radio_youtube").trigger("click");			
				jQuery("#youtube_id").val(data.id);	
				jQuery("#fullscreenvideofun").hide();				
			break;
			case "vimeo":
				jQuery("#video-dialog-wrap").removeClass("html5select");
				jQuery("#video_radio_vimeo").trigger("click");
				jQuery("#vimeo_id").val(data.id);
				jQuery("#fullscreenvideofun").hide();				
			break;
			case "html5":
				jQuery("#video-dialog-wrap").addClass("html5select");
				jQuery("#html5_url_poster").val(data.urlPoster);
				jQuery("#html5_url_mp4").val(data.urlMp4);
				jQuery("#html5_url_webm").val(data.urlWebm);
				jQuery("#html5_url_ogv").val(data.urlOgv);
				jQuery("#video_radio_html5").trigger("click");
				jQuery("#fullscreenvideofun").show();				
			break;
		}
		
		//set width and height:
		jQuery("#input_video_width").val(data.width);
		jQuery("#input_video_height").val(data.height);
		jQuery("#input_video_arguments").val(data.args);
		
		jQuery("#input_video_preview").val(data.previewimage);
		
		if(data.autoplay && data.autoplay == true){
			jQuery("#input_video_autoplay").prop("checked","checked");
			jQuery("#showautoplayfirsttime").show();
		}else{
			jQuery("#input_video_autoplay").prop("checked","");
			jQuery("#showautoplayfirsttime").hide();
		}

		if(data.use_poster_on_mobile && data.use_poster_on_mobile == true){
			jQuery("#input_use_poster_on_mobile").prop("checked","checked");
		}else{
			jQuery("#input_use_poster_on_mobile").prop("checked","");
		}
		
		if(data.autoplayonlyfirsttime && data.autoplayonlyfirsttime == true)
			jQuery("#input_video_autoplay_first_time").prop("checked","checked");
		else
			jQuery("#input_video_autoplay_first_time").prop("checked","");
			
		if(data.nextslide && data.nextslide == true)
			jQuery("#input_video_nextslide").prop("checked","checked");
		else
			jQuery("#input_video_nextslide").prop("checked","");
	
		if(data.forcerewind && data.forcerewind == true)
			jQuery("#input_video_force_rewind").prop("checked","checked");
		else
			jQuery("#input_video_force_rewind").prop("checked","");

		if(data.fullwidth && data.fullwidth == true)
			jQuery("#input_video_fullwidth").prop("checked","checked");
		else
			jQuery("#input_video_fullwidth").prop("checked","");
						
		if(data.controls && data.controls == true)
			jQuery("#input_video_control").prop("checked","checked");
		else
			jQuery("#input_video_control").prop("checked","");
			
		if(data.mute && data.mute == true)
			jQuery("#input_video_mute").prop("checked","checked");
		else
			jQuery("#input_video_mute").prop("checked","");
		
		if(data.disable_on_mobile && data.disable_on_mobile == true)
			jQuery("#input_disable_on_mobile").prop("checked","checked");
		else
			jQuery("#input_disable_on_mobile").prop("checked","");
		
		
		if(data.cover && data.cover == true)
			jQuery("#input_video_cover").prop("checked","checked");
		else
			jQuery("#input_video_cover").prop("checked","");

		if(data.preload){
			jQuery("#input_video_preload option").each(function(){
				if(jQuery(this).val() == data.preload)
					jQuery(this).attr('selected', true);
			});
		}
		
		if(data.videospeed){
			jQuery("#input_video_speed option").each(function(){
				if(jQuery(this).val() == data.videospeed)
					jQuery(this).attr('selected', true);
			});
		}
		
		
		if(data.dotted){
			jQuery("#input_video_dotted_overlay option").each(function(){
				if(jQuery(this).val() == data.dotted)
					jQuery(this).attr('selected', true);
			});
		}
		if(data.ratio){
			jQuery("#input_video_ratio option").each(function(){
				if(jQuery(this).val() == data.ratio)
					jQuery(this).attr('selected', true);
			});
		}

		if(data.videoloop){
			if(data.videoloop == true){
				jQuery('#input_video_loop option[value="loop"]').attr("selected",true);
			}else{
				jQuery("#input_video_loop option").each(function(){
					if(jQuery(this).val() == data.videoloop)
						jQuery(this).attr('selected', true);
				});
			}
		}
		
		//change button text:
		var buttonVideoAdd = jQuery("#button-video-add");
		buttonVideoAdd.text(buttonVideoAdd.data("textupdate"));
		
		//search
		switch(data.video_type){
			case "youtube":
				jQuery("#button_youtube_search").trigger("click");
			break;
			case "vimeo":
				jQuery("#button_vimeo_search").trigger("click");
			break;
		}
		
		//if(data.previewimage != '') jQuery("#video-thumbnail-preview").attr('src', data.previewimage);
	}
	
	//add params from textboxes to object
	var addTextboxParamsToObj = function(obj){
		
		switch(obj.video_type){
			case "youtube":
				obj.id = jQuery("#youtube_id").val();
			break;
			case "vimeo":
				obj.id = jQuery("#vimeo_id").val();
			break;
		}
		
		obj.width = jQuery("#input_video_width").val();
		obj.height = jQuery("#input_video_height").val();
		obj.args = jQuery("#input_video_arguments").val();
		obj.previewimage = jQuery("#input_video_preview").val();
		obj.autoplay = jQuery("#input_video_autoplay").is(":checked");
		obj.use_poster_on_mobile = jQuery("#input_use_poster_on_mobile").is(":checked");
		obj.autoplayonlyfirsttime = jQuery("#input_video_autoplay_first_time").is(":checked");
		obj.nextslide = jQuery("#input_video_nextslide").is(":checked");
		obj.forcerewind = jQuery("#input_video_force_rewind").is(":checked");
		obj.fullwidth = jQuery("#input_video_fullwidth").is(":checked");
		obj.videoloop = jQuery("#input_video_loop").is(":checked");
		obj.controls = jQuery("#input_video_control").is(":checked");
		obj.mute = jQuery("#input_video_mute").is(":checked");
		obj.disable_on_mobile = jQuery("#input_disable_on_mobile").is(":checked");
		obj.cover = jQuery("#input_video_cover").is(":checked");
		obj.dotted = jQuery("#input_video_dotted_overlay option:selected").val();
		obj.preload = jQuery("#input_video_preload option:selected").val();
		obj.videospeed = jQuery("#input_video_speed option:selected").val();
		obj.ratio = jQuery("#input_video_ratio option:selected").val();
		obj.videoloop = jQuery("#input_video_loop option:selected").val();
		return(obj);
	}
	
	
	/**
	 * init video dialog buttons
	 */
	var initVideoDialog = function(){
		
		//set youtube radio checked:
		jQuery("#video_radio_youtube").prop("checked",true);
		
		//set radio boxes:
		jQuery("#video_radio_vimeo").click(function(){
			jQuery("#video_block_youtube").hide();
			jQuery("#video_block_html5").hide();
			jQuery("#rev-html5-options").hide();
			jQuery("#rev-youtube-options").hide();
			jQuery("#video_hidden_controls").hide();
			jQuery("#rev-video-loop-wrap").hide();
			//jQuery("#video_content").show();
			jQuery("#video_block_vimeo").show();
			jQuery("#preview-image-video-wrap").show();
			jQuery("#video-dialog-wrap").removeClass("html5select");
			jQuery("#fullscreenvideofun").hide();
		});
		
		jQuery("#video_radio_youtube").click(function(){
			jQuery("#video_block_vimeo").hide();
			jQuery("#video_block_html5").hide();			
			jQuery("#rev-html5-options").hide();			
			jQuery("#rev-youtube-options").show();			
			jQuery("#video_hidden_controls").hide();
			jQuery("#rev-video-loop-wrap").hide();
			//jQuery("#video_content").show();
			jQuery("#video_block_youtube").show();
			jQuery("#preview-image-video-wrap").show();
			jQuery("#video-dialog-wrap").removeClass("html5select");
			jQuery("#fullscreenvideofun").hide();
		});
		
		jQuery("#video_radio_html5").click(function(){
			jQuery("#video_block_vimeo").hide();
			jQuery("#video_block_youtube").hide();
			jQuery("#video_block_html5").show();
			jQuery("#rev-youtube-options").hide();
			jQuery("#rev-html5-options").show();
			jQuery("#video_content").hide();
			jQuery("#video_hidden_controls").show();
			jQuery("#rev-video-loop-wrap").show();
			jQuery("#preview-image-video-wrap").hide();
			jQuery("#video-dialog-wrap").addClass("html5select");
			jQuery("#fullscreenvideofun").show();	
		});
		
		
		jQuery("#input_video_autoplay").click(function(){
			if(jQuery(this).is(":checked")){
				jQuery("#showautoplayfirsttime").show();
			}else{
				jQuery("#showautoplayfirsttime").hide();
			}
		});
		
		jQuery("#input_video_cover").click(function(){
			if(jQuery(this).is(":checked")){
				if(!jQuery('#input_video_fullwidth').is(":checked")) jQuery('#input_video_fullwidth').prop("checked",true);
			}
			updateVideoSizeProps();
		});
		
		//set youtube search action
		jQuery("#button_youtube_search").click(function(){
			//init data
			setYoutubeDialogHtml(false);
			jQuery("#video_hidden_controls").hide();
			
			jQuery("#youtube_loader").show();
			var youtubeID = jQuery("#youtube_id").val();
			youtubeID = jQuery.trim(youtubeID);
			
			youtubeID = getYoutubeIDFromUrl(youtubeID);
			
			var img = new Image();
			img.onload = function() {
				var img = jQuery(this)
				UniteAdminRev.onYoutubeCallback(img);
			}
			img.src = "https://img.youtube.com/vi/"+youtubeID+"/sddefault.jpg"
			
			jQuery("#video_content").show();
			
			//handle not found:
			setTimeout("UniteAdminRev.videoDialogOnError()",2000);
		});
		
		
		//add the selected video to the callback function
		jQuery("#button-video-add").click(function(){
			var html5Checked = jQuery("#video_radio_html5").prop("checked");
			
			jQuery("#video_content").hide();
			
			if(html5Checked){	//in case of html5
				var obj = {};
				obj.video_type = "html5";
				obj.urlPoster = jQuery("#html5_url_poster").val();
				obj.urlMp4 = jQuery("#html5_url_mp4").val();
				obj.urlWebm = jQuery("#html5_url_webm").val();
				obj.urlOgv = jQuery("#html5_url_ogv").val();
				obj.width = jQuery("#input_video_width").val();
				obj.height = jQuery("#input_video_height").val();

				obj = addTextboxParamsToObj(obj);
				
				if(typeof lastVideoCallback == "function")
					lastVideoCallback(obj);
				
				jQuery("#dialog_video").dialog("close");
				
			}else{		//in case of vimeo and youtube 
				if(!lastVideoData)
					return(false);
								
				lastVideoData = addTextboxParamsToObj(lastVideoData);
								
				if(typeof lastVideoCallback == "function")
					lastVideoCallback(lastVideoData);
				
				jQuery("#dialog_video").dialog("close");
			}
			
		});
		
		
		//set vimeo search
		jQuery("#button_vimeo_search").click(function(){
			//init data
			setYoutubeDialogHtml(false);
			jQuery("#video_hidden_controls").hide();
			
			jQuery("#vimeo_loader").show();
			
			jQuery("#video_content").show();
			
			var vimeoID = jQuery("#vimeo_id").val();
			vimeoID = jQuery.trim(vimeoID);
			vimeoID = getVimeoIDFromUrl(vimeoID);
			
			var urlAPI = 'https://www.vimeo.com/api/v2/video/' + vimeoID + '.json?callback=UniteAdminRev.onVimeoCallback'; 
			
			jQuery.getScript(urlAPI);
		});
		
		
		jQuery("#input_video_fullwidth").click(updateVideoSizeProps);
		
	}//end initVideoDialog
	
	
	/**
	 * init general settings dialog
	 */
	var initGeneralSettings = function(){
		
		//button general settings - open dialog
		jQuery("#button_general_settings").click(function(){
			
			jQuery("#loader_general_settings").hide();
			
			jQuery("#dialog_general_settings").dialog({
				minWidth:800,
				minHeight:500,
				modal:true,
				dialogClass:"tpdialogs"
			});
			
		});
		
		//button save general settings
		jQuery("#button_save_general_settings").click(function(){
			var data = UniteSettingsRev.getSettingsObject("form_general_settings");
			jQuery("#loader_general_settings").show();
			UniteAdminRev.ajaxRequest("update_general_settings",data,function(response){
				jQuery("#loader_general_settings").hide();
				jQuery("#dialog_general_settings").dialog("close");
			});
		});
		
		
	}
	
	
	//adds the update/deactivate option
	var initUpdateRoutine = function(){
		
		jQuery('#rs-validation-activate').click(function(){
			
			UniteAdminRev.setAjaxLoaderID("rs_purchase_validation");
			UniteAdminRev.setAjaxHideButtonID("rs-validation-activate");
			
			var data = {
				code: jQuery('input[name="rs-validation-token"]').val(),
				username: jQuery('input[name="rs-validation-username"]').val(),
				api_key: jQuery('input[name="rs-validation-api-key"]').val()
			}
			
			UniteAdminRev.ajaxRequest("activate_purchase_code",data);
		});
		
		jQuery('#rs-validation-deactivate').click(function(){
			
			UniteAdminRev.setAjaxLoaderID("rs_purchase_validation");
			UniteAdminRev.setAjaxHideButtonID("rs-validation-deactivate");
			
			UniteAdminRev.ajaxRequest("deactivate_purchase_code",'');
			
		});
		
	}
	
	
	/**
	 * init joomla 3 fancybox changes
	 */
	function initJoomla35FancyboxChanges(){
		
		var objContent = jQuery("#fancybox-content");
		
		//check when the frame is ready
		function checkFancyboxIframe(){
			
			var objIframe = jQuery("#fancybox-frame");
			
			var isFound = false;
			if(objIframe.length){
				var objDocument = objIframe.contents();
				var objButton = objDocument.find(".button-save-selected");
				if(objButton.length)
					isFound = true;
			}
			
			if(isFound == false){
				setTimeout(checkFancyboxIframe, 500);
				return(false);
			}
			
			objButton.click(function(){
				var urlImage = objDocument.find("#f_url").val();
				if(jQuery.trim(urlImage) != ""){
					jInsertFieldValue(urlImage);
					SqueezeBox.close();
				}
			});
			
		}
		
		setTimeout(checkFancyboxIframe, 500);
		
	}
	
	
	/**
	 * init fancybox open media dialog
	 */
	function initFancyboxTrigger(){
		
		//init fancybox trigger
		jQuery("#fancybox_trigger").fancybox({
			'width'				: 800,
			'height'			: 500,
			'autoScale'			: false,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'type'				: 'iframe',
			'onStart':function(){
								
				if(g_isJoomla35 == true)
					initJoomla35FancyboxChanges();
			}
		});
		
		
	}
	
	
	//run the init function
	jQuery(document).ready(function(){
		initVideoDialog();
		
		initFancyboxTrigger();
		
		//init update dialog:
		jQuery("#button_upload_plugin").click(function(){
			
			jQuery("#dialog_update_plugin").dialog({
				minWidth:600,
				minHeight:400,
				modal:true,
				dialogClass:"tpdialogs"
			});
			
		});
		
		//update text operation
		jQuery("#button_update_text").click(function(){
			UniteAdminRev.ajaxRequest("update_text","",function(response){
				alert(response.message);
			});
		});
		
		initGeneralSettings();
		
		initSliderMultipleText();
		
		initUpdateRoutine();
		
		jQuery('#rs-dismiss-notice').click(function(){
			t.ajaxRequest("dismiss_notice",'',function(response){});
			jQuery('.rs-update-notice-wrap').hide();
		});
	});
	
	/**
	 * set multiple key values
	 */
	t.setMultipleTextKey = function(name, key){
		g_multiple_text_key[name] = key;
	}
	
	/**
	 * set multiple key values
	 */
	t.getMultipleTextKey = function(name){
		return g_multiple_text_key[name];
	}
	
	var initSliderMultipleText = function(){
	
		jQuery("body").on("click",".remove_multiple_text",function(){ //remove element
			jQuery("#"+jQuery(this).data('remove')).remove();
			jQuery(this).parent().remove();
		});
		
		jQuery(".multiple_text_add").click(function(){ //add element
			
			var handle = jQuery(this).data('name');
			var key = t.getMultipleTextKey(handle) + 1;
			var template = jQuery('.'+handle+'_TEMPLATE').html();
			
			template = template.replace(/##ID##/ig, handle+'_'+key).replace(/##NAME##/ig, handle);
			jQuery('#'+handle+'_row .setting_input').append(template);
			
			t.setMultipleTextKey(handle, key);
		});
		
	}
	
	/**
	 * set multiple key values
	 */
	t.parseCssMultiAttribute = function(value){
		if(value == '') return false;
		var raw = value.split(' ');
		var retObj = [];
		
		switch(raw.length){
			case 1:
				retObj[0] = raw[0];
				retObj[1] = raw[0];
				retObj[2] = raw[0];
				retObj[3] = raw[0];
			break;
			case 2:
				retObj[0] = raw[0];
				retObj[1] = raw[1];
				retObj[2] = raw[0];
				retObj[3] = raw[1];
			break;
			case 3:
				retObj[0] = raw[0];
				retObj[1] = raw[1];
				retObj[2] = raw[2];
				retObj[3] = raw[1];
			break;
			case 4:
				retObj[0] = raw[0];
				retObj[1] = raw[1];
				retObj[2] = raw[2];
				retObj[3] = raw[3];
			break;
			case 0:
			default:
			return false;
			break;
		}
		
		return retObj;
	}
	
	/**
	 * get rgb from hex values
	 */
	t.convertHexToRGB = function(hex) {
		var hex = parseInt(((hex.indexOf('#') > -1) ? hex.substring(1) : hex), 16);
		return [hex >> 16,(hex & 0x00FF00) >> 8,(hex & 0x0000FF)];
	}
	
	t.sanitize_input = function(raw){
		return raw.toLowerCase().replace(/ /g, '-').replace(/[^-0-9a-z]/g,'');
	}
	
	t.initAccordion = function(){
		jQuery(".postbox-arrow").each(function(i) {

			jQuery(this).closest('h3').click(function(){
				var handle = jQuery(this);

				//open
				if(!handle.hasClass("box-closed")){
					handle.closest('.postbox').find('.inside').slideUp("fast");
					handle.addClass("box-closed");

				}else{	//close
					jQuery('.postbox-arrow').each(function() {
						var handle = jQuery(this).closest('h3');
						handle.closest('.postbox').find('.inside').slideUp("fast");
						handle.addClass("box-closed");
					})
					handle.closest('.postbox').find('.inside').slideDown("fast");
					handle.removeClass("box-closed");

				}
			});

		});
	}
	
	
}


//user functions:
function trace(data,clear){
	UniteAdminRev.trace(data,clear);
}

function debug(data){
	UniteAdminRev.debug(data);
}

