window.document.addEventListener("readystatechange", function(){
	//alert(document.readyState);
	if(document.readyState == "loading"){
		
	}
}, false);

$(document).ready(function(){
	$(window).load(function() {		
		
		// youtube and live url //
		var curImages = new Array();
		
		$('#statusbox').liveUrl({
			loadStart : function(){
				$('.liveurl-loader').show();
			},
			loadEnd : function(){
				$('.liveurl-loader').hide();
			},
			success : function(data) 
			{     
				var output = $('.liveurl');
				output.find('.title').text(data.title);
				output.find('.description').text(data.description);
				output.find('.url').text(data.url);
				output.find('.image').empty();
				
				output.find('.close').one('click', function() 
				{
					var liveUrl     = $(this).parent();
					liveUrl.hide('fast');
					liveUrl.find('.video').html('').hide();
					liveUrl.find('.image').html('');
					liveUrl.find('.controls .prev').addClass('inactive');
					liveUrl.find('.controls .next').addClass('inactive');
					liveUrl.find('.thumbnail').hide();
					liveUrl.find('.image').hide();
					$('#statusbox_extra').val('');

					$('#statusbox').trigger('clear'); 
					curImages = new Array();
				});
				
				output.show('fast');
				
				if (data.video != null) {                       
					var ratioW        = data.video.width  /350;
					data.video.width  = 350;
					data.video.height = data.video.height / ratioW;

					var video = 
					'<object width="' + data.video.width  + '" height="' + data.video.height  + '">' +
						'<param name="movie"' +
							  'value="' + data.video.file  + '"></param>' +
						'<param name="allowScriptAccess" value="always"></param>' +
						'<embed src="' + data.video.file  + '"' +
							  'type="application/x-shockwave-flash"' +
							  'allowscriptaccess="always"' +
							  'width="' + data.video.width  + '" height="' + data.video.height  + '"></embed>' +
					'</object>';
					output.find('.video').html(video).show();
				}
			},
			addImage : function(image)
			{   
				var output  = $('.liveurl');
				var jqImage = $(image);
				jqImage.attr('alt', 'Preview');
				
				if ((image.width / image.height)  > 7 
				||  (image.height / image.width)  > 4 ) {
					// we dont want extra large images...
					return false;
				} 

				curImages.push(jqImage.attr('src'));
				output.find('.image').append(jqImage);
					
				if (curImages.length == 1) {
					// first image...
					
					output.find('.thumbnail .current').text('1');
					//output.find('.thumbnail').show();
					output.find('.image').show();
					jqImage.addClass('active');
				}

				if (curImages.length == 2) {
					output.find('.controls .next').removeClass('inactive');
				}
				
				//niku implement
				var linkImg = $('#status_msg').find('.liveurl .image > *').attr('src');
				var linkTitle = $('#status_msg').find('.liveurl .title').html();
				var linkDescription = $('#status_msg').find('.liveurl .description').html();
				var linkUrl = $('#status_msg').find('.liveurl .url').html();
				$('#statusbox_extra').val($('#statusbox_extra').val() + '<div class=\'padtop\'><a href=\''+linkUrl+'\' target=\'blank\'><img class=\'fleft thumb\' src=\''+linkImg+'\'><b>'+linkTitle+'</b></a></br><span class=\'xsmall\'>'+linkDescription+'<br>'+linkUrl+'</div>');
				//end niku implement	
				
				output.find('.thumbnail .max').text(curImages.length);
			}
		});
	  
		$('.liveurl ').on('click', '.controls .button', function() 
		{
			var self        = $(this);
			var liveUrl     = $(this).parents('.liveurl');
			var content     = liveUrl.find('.image');
			var images      = $('img', content);
			var activeImage = $('img.active', content);

			if (self.hasClass('next')) 
				 var elem = activeImage.next("img");
			else var elem = activeImage.prev("img");

			if (elem.length > 0) {
				activeImage.removeClass('active');
				elem.addClass('active');  
				liveUrl.find('.thumbnail .current').text(elem.index() +1);
				
				if (elem.index() +1 == images.length || elem.index()+1 == 1) {
					self.addClass('inactive');
				}
			}

			if (self.hasClass('next')) 
				 var other = elem.prev("img");
			else var other = elem.next("img");
			
			if (other.length > 0) {
				if (self.hasClass('next')) 
					   self.prev().removeClass('inactive');
				else   self.next().removeClass('inactive');
		   } else {
				if (self.hasClass('next')) 
					   self.prev().addClass('inactive');
				else   self.next().addClass('inactive');
		   }
		   
		});
		// end youtube and live url //

	

		// overlay only in dashboard //
		if ($("#statusbox").length) {
			$('#right_content:first').on("click", function( event ) {
				if (!$("#overlay").length) {
					var docHeight = $(document).height();
					
					$("#right_content:first")
					  .css({
						 'position': 'relative',
						 'z-index': 5010
					});
					
					$("body").append("<div id='overlay'></div>");
	
					$("#overlay")
					.hide()
					  .height(docHeight)
					  .css({
						 'opacity' : 0.7,
						 'position': 'absolute',
						 'top': 0,
						 'left': 0,
						 'background-color': 'black',
						 'width': '100%',
						 'z-index': 5000
					});
					$("#overlay").fadeIn();
				}
				$('#overlay').on("click", function( event ) {
					$("#overlay").fadeOut().promise().done(function(){
						$(this).remove();
					});
				});
			});
		}
		
		$(document).scroll(function(){
			if ($("#overlay")) {
				$("#overlay").fadeOut().promise().done(function(){
					$(this).remove();
				});
			}
		});
		
		// end overlay only in dashboard //
			
				
			//start hiding all divs with item='hide'
			$("div[item='hide']").hide();			

			//init rating
			$( "select[id^='rating']" ).barrating('show', {theme: 'fontawesome-stars-o',});
			
			//init comments
			viewcomments();
			
			//init fancybox
			fancybox();
						
			// init fancybox
			function fancybox() {
				$(".fancybox").fancybox({
					'closeBtn' : false,
					helpers: {
						overlay: {
						  locked: false
						}
					  }
				});
				$('.fancybox-media').fancybox({
					openEffect  : 'none',
					closeEffect : 'none',
					'closeBtn' : false,
					helpers : {
						overlay: {
						  locked: false
						},
						media : {}
					}
				});
			}

			$('.alertbox').on("click", function( event ) {
				$.alert({
					title: $(this).attr('title'),
					content: $(this).attr('content'),
				});
			});
			
			$('.confirmbox').confirm(this.$target);
			
			
			
			//status post button check
			$('#publishButton').prop('disabled',true);
			/*
			$('#publishButton').on("click", function( event ) {
				event.preventDefault()();
			});
			*/
			//textboxDisabled('statusbox','publishButton');
			$('#statusbox.checkIfEmpty').on("change keyup input", function( event ) {
				$('#publishButton').prop('disabled', this.value == "" ? checkIfEmpty() : false);
			})
			$('#load_photo.checkIfEmpty').on("change keyup input", function( event ) {
				checkIfEmpty();
			});
			function checkIfEmpty() {
				if (($('input[id="img"]').val()=='') && ($('#statusbox').val()=='')) {
					$('#publishButton').prop('disabled',true);
				} else {
					$('#publishButton').prop('disabled',false);
				}
			}
			
			if ($("#statusbox").length) {
				$('#img').on('change', function(){
					$('#hideField').css('display','none');
					$('#icon_del_photo').remove();
					$('#load_photo').append('<img id="displayImg" style="max-width:470px;"><i id="icon_del_photo" class="fa fa-trash-o fa-2x" aria-hidden="true" style="color:#3b5f94;cursor:pointer;" title="Elimina"></i>');
					
					var fileExtension = ['jpeg', 'jpg', 'png', 'mp3', 'mp4'];
					if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
						$.alert({
							content: 'Formato non valido',
						});
						return false;
					}
					
					readURL(this, function(e) {
						// use result in callback...
						$('#displayImg').attr('src',e.target.result);
						$('#load_photo').slideDown();
						$('#icon_del_photo').on('click', function() {
							$("#load_photo").slideUp().promise().done(function(){
								$('#displayImg').remove();
								$('#img').val('');							
								checkIfEmpty();
							});
						});
					});
				});
			}
			//end status post button check


			//read url
			function readURL(input, onLoadCallback) {
				if (input.files && input.files[0]) {
					Pace.restart();
					var reader = new FileReader();
					reader.onload = onLoadCallback;
					reader.readAsDataURL(input.files[0]);
			}
				}


			//init jcrop
			var jcrop_api;
			$("#displayImg").css('display', 'none');
			
			function startjcrop() {
				$('#displayImg').Jcrop({
					onChange: showCoords,
					onSelect: showCoords,
					bgColor:     'black',
					bgOpacity:   .4,
					setSelect:   [ 0, 0, 150, 150 ],
					bgFade: true,
					boxWidth: 483, 
				   // boxHeight: 300,
					aspectRatio: 1,
					//onRelease: clearInfo
				},function(){
				  jcrop_api = this;
				});
			}
			
			function showCoords(c)
			{
				$('#x').val(c.x);
				$('#y').val(c.y);
				$('#x2').val(c.x2);
				$('#y2').val(c.y2);
				$('#w').val(c.w);
				$('#h').val(c.h);
			};
			
			//not in board page
			if (!$("#statusbox").length) {
				$("#img").change(function(){
					$("#displayImg").fadeOut();
					var oFile = $('#img')[0].files[0];
					
					// check for image type (jpg and png are allowed)
					var fileExtension = ['jpeg', 'jpg'];
					if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
						$.alert({
							content: 'Formato non valido',
						});
						return false;
					}
					
					// check for file size
					if (oFile.size > 1000 * 1024) {
						$.alert({
							content: 'L\'immagine non deve superare i 1000 Kb',
						});
						return false;
					}
	
					var oImage = document.getElementById('displayImg');
					
					var oReader = new FileReader();
					Pace.restart();
					oReader.onload = function (e) {
						oImage.src = e.target.result;
						oImage.onload = function () {
							if (typeof jcrop_api != 'undefined') {
								jcrop_api.destroy();
								jcrop_api = null;
								$('#displayImg').width(oImage.naturalWidth);
								$('#displayImg').height(oImage.naturalHeight);
							}
							startjcrop();
						}
					}
						
					// read the image file as a data URL.
					oReader.readAsDataURL(this.files[0]);
				});
			}
			//end jcrop

//////////////////////////////////////////////////////////////////////////////////////////////			

			//infinite scroll
			var win = $(window);
			var pageNumber = 1;
			var totalPages = parseInt($("#lastPage").attr('val'));
			win.scroll(function() {
				// End of the document reached?
				if ($(document).height() - win.height() == win.scrollTop()) {
					//$('#loading').show();
					if ((totalPages) && (pageNumber != totalPages)) {
						if (getQuerystring()) {
							url = '?page='+pageNumber+'&'+getQuerystring();
						} else {
							url = '?page='+pageNumber;
						}
						$.ajax({
							url: url,
							dataType: 'html',
							success: function(data) {
									//find div with new posts
									var content = $( data ).find("#right_content:not(:first)");
									//set custom attribute "inpage" to query correct page
									content.find("select[id^='rating']").attr('inpage',pageNumber);
									content.find("div[id^='post']").attr('inpage',pageNumber);
									//view more posts
									$("#right").fadeIn("slow").append(content);
									viewcomments();							
									//apply widget rating
									$( "select[id^='rating']" ).barrating('show', {theme: 'fontawesome-stars-o',});		
									pageNumber++;
									//$('#loading').hide();
							}
						})
						.done(function(data){
							//nothing to do for now
						});
					}
				}
			});
			//end infinite scroll
	
			//rating  
			$(document).on("change", "select[id^='rating']", function( event ) {
				var value = $(this).val();
				var id_status = $(this).find("option:selected").text();
				var page = $(this).attr('inpage');
				if (getQuerystring()) {
					url = '?page='+page+'&'+getQuerystring();
				} else {
					url = '?page='+page;
				}
				$.post( url , {
								id_status: id_status,
								rating: value,
								action:'store_rating'
							})
							.done(function( data ) {
								var content = $(data).find("#rating_result"+id_status).html();
								$("#rating_result"+id_status).html( content );
							});
			});
			//end rating
		
		/*
		$.urlParam = function(name){
			var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
			if (results==null){
			   return null;
			}
			else{
			   return results[1] || 0;
			}
		}
		console.log(decodeURIComponent($.urlParam('userboard'))); 
		*/
		function getQuerystring() {
			if (document.URL.split('?')[1]) {
				return document.URL.split('?')[1];
			} else {
				return false;
			}
		}
		
			$(function() {
				$.datepicker.setDefaults($.datepicker.regional['it']);
				$(".dashboard_notime").datepicker({
				inline:true,      
				//disabled: true,      
				dateFormat: 'dd-mm-yy',
				//defaultDate: "+1w",
				changeMonth: true,
				changeYear: true,
				numberOfMonths: 1,
				});
			});
			//end datepicker
			
			//swing
			$( document ).on("click", ".swing", function( event ) {
				// Stop form from submitting normally
				event.stopPropagation();
				var item = ($(this).attr('item'));
				$("div[item='hide']").slideUp().promise().done(function(){
					$("#"+item).slideDown(200,"swing");
				});
			});
			
			//open file dialog from button
			$( document ).on("click", ".openFileDialog", function( event ) {
				$("#"+$(this).attr('id')).click();
			});
			
			//on click outside div, hide all divs with "item=hide" if open (like Carica foto or Carica Video)
			$( document ).click(function( event ) {
				if (!$('#overlay')) {
					var selector = $("div[item='hide']");
					if ($(event.target).closest(selector).length === 0)
					{
						selector.slideUp().promise().done();
					}
				}
			});
			
		    
		    // comments & load more
			function viewcomments() {
				$("tr[id^='post_comment']").each(function(i) {
					var item = ($(this).attr('id'))
					var numcomments = ($(this).attr('numcomments'));
					$("tr[id^='"+item+"']").slice(-3, numcomments).fadeIn();
				});
			};
			$( document ).on("click", ".loadMore", function( event ) {
				event.preventDefault();
				var item = ($(this).attr('item'));
				var numcomments = ($(this).attr('numcomments'));
			   $("tr[id^='"+item+"']:hidden").slice(-5, numcomments).fadeIn('slow');
				if ($("tr[id^='"+item+"']:hidden").length == 0) {
					$($(this)).hide();
				}
			});
			// end comments load more

			//ajax submit
			$( document ).on("click", ".ajaxsubmit", function( event ) {
				// Stop form from submitting normally
				event.preventDefault();
				// Get some values from elements on the page:
				var $params = $( this );
				askConfirm = $params.attr( "askConfirm" );
				url = $params.attr( "href" );
				id = $params.attr( "id" );
				action = $params.attr( "action" );
				reloadPage = $params.attr( "reloadPage" );
				if (askConfirm) {
					$.confirm({
						confirmButtonClass: 'btn-info',
    					cancelButtonClass: 'btn-danger',
						confirm: function(){
							var posting = $.post( url, { id: id, action: action } );
							// Put the results in a div
							posting.done(function( data ) {
								aftersubmit(data, id, action, reloadPage);
							})
						}
					});
				} else {
				//alert(id);
				
				// Send the data using post
				var posting = $.post( url, { id: id, action: action } );
				// Put the results in a div
				posting.done(function( data ) {
					aftersubmit(data, id, action, reloadPage);
				});
				}
			});
			//end ajax submit
		
			//form submit
			$( document ).on("click", ".formsubmit", function( event ) {
					// Stop form from submitting normally
					event.preventDefault();
					// Get some values from elements on the page
					var $params = $( this );
					var idform = $params.attr( "idform" );
						var id = idform.replace('submit_comment', '');
						var url = $('#'+idform).attr( "action" );

						$.ajax({
						   type: "POST",
						   url: url,
						   data: $('#'+idform).serialize(),
						   success: function(data)
						   {
							   aftersubmit(data, id, 'post_comment'); // show response from the php script.
						   }
						 });
					
				});
				//end ajax submit


			//form submit
			$( '#ajaxform' ).on("submit", function( event ) {
				// Stop form from submitting normally
				event.preventDefault();
				// Get some values from elements on the page
				var $params = $( this );
				var url = $params.attr( "action" );
				var formData = new FormData($(this)[0]);
				$.ajax({
				   type: "POST",
				   url: url,
				   cache: false,
				   contentType: false,
				   processData: false,
				   data: formData,
				   success: function(data)
				   {
					   aftersubmit(data, false, false, true);
				   }
				 });
			});
			$(document).ajaxStart(function(){
				Pace.restart;
			});
			//end ajax submit
			

		function aftersubmit(data, id, action, reloadPage) {
			if (reloadPage) {
				location.reload();
			}
			if (action == 'delete_post') {
				$('div[id=post'+id+']').toggle(500,"swing").promise().done(function(){
					$('div[id=post'+id+']').parent().toggle(200,"swing");
				});
			}
			if (action == 'delete_comment') {
				$('tr[cid='+id+']').toggle(500,"swing").promise().done(function(){
					$('tr[cid='+id+']').remove();
				});
			}
			if (action == 'post_comment') {
				var addcomment = $(data).find('#comments'+id);
				$('#comments'+id).html(addcomment);
				viewcomments();
			}
			if (action == 'share_post') {
				$.alert({
					title: false,
					content: 'Post condiviso sulla tua bacheca',
				});
			}
		}

	});	
	
	
	
	
	
});


/*
var rAccordion = new Class({
	
	initialize: function(container, toggleClass, elementClass, options){
		this.container = container;
		this.tClass = toggleClass;
		this.eClass = elementClass;
		this.options = options;
		this.selector = '#' + this.container + ' > .';
		this.makeAccordion();
	},
	
	makeAccordion: function(){
		new Accordion(
			$$(this.selector+this.tClass),
			$$(this.selector+this.eClass),
			this.options
		).addEvents({
			// The onActive and onComplete events added to the stack here to
			// attempt to address some of the css issues.
			'onActive': function(toggle){
				if(toggle.getParent().getStyle('height') != 0)
					toggle.getParent().setStyle('height', '');
			},
			'onComplete': function(a){
				if ($defined(a)) {
					var height = 0;
					a.getParent().getChildren().each(function(e){
						height = height + e.offsetHeight;
					});
					if(height != a.getParent().offsetHeight && a.getParent().offsetHeight != 0)
						a.getParent().setStyle('height','');
				}
			}
		});
		this.selector += this.eClass + ' > .';
		if($defined($$(this.selector)[0]))
			this.makeAccordion();
	}
	
});
*/



/*
* FancyForm 0.95
* By Vacuous Virtuoso, lipidity.com
* ---
* Checkbox and radio input replacement script.
* Toggles defined class when input is selected.
*/

/*
var FancyForm = {
	start: function(elements, options){
		if(FancyForm.initing != undefined) return;
		if($type(elements)!='array') elements = $$('input');
		if(!options) options = [];
		FancyForm.onclasses = ($type(options['onClasses']) == 'object') ? options['onClasses'] : {
			checkbox: 'checked',
			radio: 'selected'
		}
		FancyForm.offclasses = ($type(options['offClasses']) == 'object') ? options['offClasses'] : {
			checkbox: 'unchecked',
			radio: 'unselected'
		}
		if($type(options['extraClasses']) == 'object'){
			FancyForm.extra = options['extraClasses'];
		} else if(options['extraClasses']){
			FancyForm.extra = {
				checkbox: 'f_checkbox',
				radio: 'f_radio',
				on: 'f_on',
				off: 'f_off',
				all: 'fancy'
			}
		} else {
			FancyForm.extra = {};
		}
		FancyForm.onSelect = $pick(options['onSelect'], function(el){});
		FancyForm.onDeselect = $pick(options['onDeselect'], function(el){});
		FancyForm.chks = [];
		FancyForm.add(elements);
		$each($$('form'), function(x) {
			x.addEvent('reset', function(a) {
				window.setTimeout(function(){FancyForm.chks.each(function(x){FancyForm.update(x);x.inputElement.blur()})}, 200);
			});
		});
	},
	add: function(elements){
		if($type(elements) == 'element')
			elements = [elements];
		FancyForm.initing = 1;
		var keeps = [];
		var newChks = elements.filter(function(chk){
			if($type(chk) != 'element' || chk.inputElement || (chk.get('tag') == 'input' && chk.getParent().inputElement))
				return false;
			if(chk.get('tag') == 'input' && (FancyForm.onclasses[chk.getProperty('type')])){
				var el = chk.getParent();
				if(el.getElement('input')==chk){
					el.type = chk.getProperty('type');
					el.inputElement = chk;
					this.push(el);
				} else {
					chk.addEvent('click',function(f){
						if(f.event.stopPropagation) f.event.stopPropagation();
					});
				}
			} else if((chk.inputElement = chk.getElement('input')) && (FancyForm.onclasses[(chk.type = chk.inputElement.getProperty('type'))])){
				return true;
			}
			return false;
		}.bind(keeps));
		newChks = newChks.combine(keeps);
		newChks.each(function(chk){
			var c = chk.inputElement;
			c.setStyle('position', 'absolute');
			c.setStyle('left', '-9999px');
			chk.addEvent('selectStart', function(f){f.stop()});
			chk.name = c.getProperty('name');
			FancyForm.update(chk);
		});
		newChks.each(function(chk){
			var c = chk.inputElement;
			chk.addEvent('click', function(f){
				f.stop(); f.type = 'prop';
				c.fireEvent('click', f, 1);
			});
			chk.addEvent('mousedown', function(f){
				if($type(c.onmousedown) == 'function')
					c.onmousedown();
				f.preventDefault();
			});
			chk.addEvent('mouseup', function(f){
				if($type(c.onmouseup) == 'function')
					c.onmouseup();
			});
			c.addEvent('focus', function(f){
				if(FancyForm.focus)
					chk.setStyle('outline', '1px dotted');
			});
			c.addEvent('blur', function(f){
				chk.setStyle('outline', 0);
			});
			c.addEvent('click', function(f){
				if(f.event.stopPropagation) f.event.stopPropagation();
				if(c.getProperty('disabled')) // c.getStyle('position') != 'absolute'
					return;
				if (!chk.hasClass(FancyForm.onclasses[chk.type]))
					c.setProperty('checked', 'checked');
				else if(chk.type != 'radio')
					c.setProperty('checked', false);
				if(f.type == 'prop')
					FancyForm.focus = 0;
				FancyForm.update(chk);
				FancyForm.focus = 1;
				if(f.type == 'prop' && !FancyForm.initing && $type(c.onclick) == 'function')
					 c.onclick();
			});
			c.addEvent('mouseup', function(f){
				if(f.event.stopPropagation) f.event.stopPropagation();
			});
			c.addEvent('mousedown', function(f){
				if(f.event.stopPropagation) f.event.stopPropagation();
			});
			if(extraclass = FancyForm.extra[chk.type])
				chk.addClass(extraclass);
			if(extraclass = FancyForm.extra['all'])
				chk.addClass(extraclass);
		});
		FancyForm.chks.combine(newChks);
		FancyForm.initing = 0;
	},
	update: function(chk){
		if(chk.inputElement.getProperty('checked')) {
			chk.removeClass(FancyForm.offclasses[chk.type]);
			chk.addClass(FancyForm.onclasses[chk.type]);
			if (chk.type == 'radio'){
				FancyForm.chks.each(function(other){
					if (other.name == chk.name && other != chk) {
						other.inputElement.setProperty('checked', false);
						FancyForm.update(other);
					}
				});
			}
			if(extraclass = FancyForm.extra['on'])
				chk.addClass(extraclass);
			if(extraclass = FancyForm.extra['off'])
				chk.removeClass(extraclass);
			if(!FancyForm.initing)
				FancyForm.onSelect(chk);
		} else {
			chk.removeClass(FancyForm.onclasses[chk.type]);
			chk.addClass(FancyForm.offclasses[chk.type]);
			if(extraclass = FancyForm.extra['off'])
				chk.addClass(extraclass);
			if(extraclass = FancyForm.extra['on'])
				chk.removeClass(extraclass);
			if(!FancyForm.initing)
				FancyForm.onDeselect(chk);
		}
		if(!FancyForm.initing)
			chk.inputElement.focus();
	},
	all: function(){
		FancyForm.chks.each(function(chk){
			chk.inputElement.setProperty('checked', 'checked');
			FancyForm.update(chk);
		});
	},
	none: function(){
		FancyForm.chks.each(function(chk){
			chk.inputElement.setProperty('checked', false);
			FancyForm.update(chk);
		});
	}};

*/

/*
var enable_youtube_upop = function() {
	if ($chk($('youtube_unique_pop'))) {
		var youtube_UPop = new Fx.Slide('youtube_unique_pop', {wait: true, duration: 500,
				'onComplete' : function() {
					$('youtube_unique_pop').getParent().setStyle('height', 'auto');
				}
				});
		youtube_UPop.hide();

		if ($chk($$('.youtube_u_pop'))) {
			$$('.youtube_u_pop').addEvent('click', function(e){
				e.stop();
				var req = new Request.HTML({
						method: 'get', 
						url: this.get('href'),
						update: $('youtube_unique_pop'),
						onRequest: function() {
							youtube_UPop.hide();
						},
						onComplete: function() {
							youtube_UPop.toggle();
							window.scrollTo(0, 0);
							//enable_ipop();
						}
				}).send();
			});
		}
	}
}

*/
