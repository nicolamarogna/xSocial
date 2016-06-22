$(document).ready(function(){
												
			//start hiding all divs with item='hide'
			$("div[item='hide']").hide();			

			//init rating
			rating();
			
			//fancybox
			function fancybox() {
				$(".fancybox").fancybox();
				$('.fancybox-media').fancybox({
					openEffect  : 'none',
					closeEffect : 'none',
					helpers : {
						media : {}
					}
				});
			}
			
			
			//infinite scroll
			var win = $(window);
			var pageNumber = 1;
			var totalPages = parseInt($("#lastPage").attr('val'));
			win.scroll(function() {
				// End of the document reached?
				if ($(document).height() - win.height() == win.scrollTop()) {
					//$('#loading').show();
					if ((totalPages) && (pageNumber != totalPages)) {
						$.ajax({
							url: '?page='+pageNumber,
							dataType: 'html',
							success: function(html) {
									var content = $( html ).find("#right_content:not(:first)");
									$("#right").fadeIn("slow").append(content);
									
$('select[id^="rating"]').on('change', function() { alert('fffff') });	

									rating();
									pageNumber++;
								//$('#loading').hide();
							}
						})
						.done(function(data){
							//
						});
					}
				}
			});

			//end infinite scroll
			
			//rating
		function rating() {
			$( "select[id^='rating']" ).barrating('show', {
			  theme: 'fontawesome-stars',
			  onSelect: function(value, text, event) {
				if (typeof(event) !== 'undefined') {
					$.post( '', {
								id_status: text,
								rating: value,
								action:'store_rating'
							})
							.done(function( data ) {
								var content = $(data).find("#rating_result"+text).html();
								console.log(content);
								$("#rating_result"+text).html( content );

							});
				} else {
					
				}
			  }
			});
		};
			//end rating
			
			//datepicker
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
			$( "body" ).on("click", ".swing", function( event ) {
				// Stop form from submitting normally
				event.stopPropagation();
				var item = ($(this).attr('item'));
				$("div[item='hide']").slideUp().promise().done(function(){
					$("#"+item).slideDown(500,"swing");
				});
			});
			
			//on click outside div, hide all divs with "item=hide" if open (like Carica foto or Carica Video)
			$( "body" ).click(function( event ) {
				$("div[item='hide']").slideUp().promise().done();
			});
			
		   
		    // comments load more
			function loadmorecomments() {
				$("tr[id^='post_comment']").each(function(i) {
					var item = ($(this).attr('id'));
					var numcomments = ($(this).attr('numcomments'));
					$("tr[id^='"+item+"']").slice(-3, numcomments).show();
				});
				$(".loadMore").click(function ( event ) {
					event.preventDefault();
					var item = ($(this).attr('item'));
					var numcomments = ($(this).attr('numcomments'));
				   $("tr[id^='"+item+"']:hidden").slice(-5, numcomments).fadeIn('slow');
					if ($("tr[id^='"+item+"']:hidden").length == 0) {
						$($(this)).hide();
					}
				});
				// end comments load more
			}

			//ajax submit
			$( "body" ).on("click", ".ajaxsubmit", function( event ) {
					// Stop form from submitting normally
					event.preventDefault();				
					// Get some values from elements on the page:
					var $params = $( this );
					url = $params.attr( "href" );
					id = $params.attr( "id" );
					action = $params.attr( "action" );
					askConfirm = $params.attr( "askConfirm" );
					reloadPage = $params.attr( "reloadPage" );
					//alert(id);
					if (askConfirm) {
						if (!confirm("Sei sicuro?")){
							return false;
						}
					}	
					// Send the data using post
					var posting = $.post( url, { id: id, action: action } );
					// Put the results in a div
					posting.done(function( data ) {
						aftersubmit(data, id, action, reloadPage);
					});
					/*
					posting.always(function() {
						$( "#live"+id ).slideDown(500,"swing");
					});
					*/
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
			//console.log(data);
			//var content = $( data ).find( "#content" );
			//$( "#live"+id ).html( content );	
		}

		
});



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

if (navigator.userAgent.indexOf("Firefox")!=-1)
var load_method = 'domready';
if (navigator.userAgent.indexOf("MSIE")!=-1)
var load_method = 'load';

window.addEvent(load_method, function() {	
	
	//CwComplete Autocompleter
	var ac = new  CwAutocompleter( 'q'  /* ID of the input field */ ,  'search_ajax.php', /* URL of backend ajax script */
{
//   targetfieldForKey: 'key_search', /* The ID of the field where the selected key goes */
   onChoose: function(selection) {
      location.href = "?p=search&q="+selection.value;
	  //alert("You selected: " + selection.value + " (" + selection.key + ")");  /* Optional function to execute on user selection */
   }
});
	
	

	//create our Accordion instance
	var myAccordion = new Accordion($('sidebar'), 'h3.toggler', 'div.element', {
		opacity: false,
		onActive: function(toggler, element){
			toggler.setStyle('text-decoration', 'underline');
		},
		onBackground: function(toggler, element){
			toggler.setStyle('text-decoration', 'none');
		}
	});
	
	myAccordion.togglers.each(function(toggler){
		toggler.addEvent('mouseenter',function(){
			this.fireEvent('click');
		});
	});
	
	enable_upop();
	//enable_youtube_upop();

	if ($chk($('captcha_img'))) {
		$('reload_captcha').addEvent('click', function(e){
			reload_captcha();
			return false;
		});
	}
	if ($chk($('msg'))) {
		var MPop = new Fx.Slide('msg');
		$('msg').addEvent('mouseover', function(e){
			MPop.toggle();
		});
	}
	
	// help
	if ($chk($('help_pop'))) {
		var HPop = new Fx.Slide('help_pop');
		HPop.toggle();
		$('h_pop').addEvent('click', function(e){
			e.stop();
			var req = new Request.HTML({  
					method: 'get',  
					url: $('h_pop').get('href'),
					update: $('help_pop'),
					onComplete: function() {
						HPop.toggle();
					}
			}).send();
		});
	}
	
	// inner
	if ($chk($$('a.innerbox'))) {
		$$('a.innerbox').each(function(ibox){
			var p = ibox.getParent('td');
			var c = p.getChildren('span');
			var ipop = new Fx.Slide(c[0]).hide();
			ibox.addEvent('click', function(e){
				e.stop();
				var req = new Request.HTML({
					method: 'get',
					url: ibox.get('href'),
					update: c[0],
					onComplete: function() {
						ipop.toggle();
					}
				}).send();
			});
		});
	}
	
	// innertable
	if ($chk($$('a.innertable'))) {
		$$('a.innertable').each(function(tbox){
			var p = tbox.getParent('td');
			var c = p.getChildren('span');
			var ipop = new Fx.Slide(c[0]);
			ipop.hide();
			tbox.addEvent('click', function(e){
				e.stop();
				var req = new Request.HTML({
					method: 'get',
					url: tbox.get('href'),
					update: c[0],
					onComplete: function() {
						ipop.toggle();
						enable_upop();
					}
				}).send();
			});
		});
	}
	
	if ($chk($('tree'))) {
		new rAccordion('tree', 'rtoggle', 'relement', {
			alwaysHide: true,
			opacity: false,
			onActive: function(e){
				e.addClass('active');
			},
			onBackground: function(e){
				e.removeClass('active');
			}
		});
	}
	
	if ($chk($('upload'))) {
		new MultiUpload($('upload').name, 0, '[{id}]', true, true );
		
		var ida = $('id_area');
		var link = $('link').get('value');
		ida.addEvent('change', function(e){
			var id = ida.get('value');
			autocomp(id, link);
		});
		ida.fireEvent('change');
	}
	
	/*if ($chk($$('input.checked'))) {
		FancyForm.start();
	}*/
	
	if ($chk($$('img.rover'))) {
		$$('img.rover').each(function(e){
			/* mouseover */
			e.addEvent('mouseover', function() {
				e.setAttribute('src',this.src.replace('_off', '_on'));
			});
			e.addEvent('mouseout', function() {
				e.setAttribute('src',this.src.replace('_on', '_off'));
			});
		});
	}
	
});

var enable_upop = function() {
	if ($chk($('unique_pop'))) {
		var UPop = new Fx.Slide('unique_pop', {wait: true, duration: 500,
				'onComplete' : function() {
					$('unique_pop').getParent().setStyle('height', 'auto');
				}
				});
		UPop.hide();

		if ($chk($$('.u_pop'))) {
			$$('.u_pop').addEvent('click', function(e){
				e.stop();
				var req = new Request.HTML({
						method: 'get', 
						url: this.get('href'),
						update: $('unique_pop'),
						onRequest: function() {
							UPop.hide();
						},
						onComplete: function() {
							UPop.toggle();
							window.scrollTo(0, 0);
							//enable_ipop();
						}
				}).send();
			});
		}
	}
}
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
var autocomp = function(ida, link)  {
	var cat = $('category');
	var subcat = $('subcategory');
	new Autocompleter.Request.JSON(cat, root+'/admin/'+link+'/categories/'+ida, {'minLength': 1, 'postVar': 'category'});
	new Autocompleter.Request.JSON(subcat, root+'/admin/'+link+'/subcategories/'+ida, {'minLength': 1, 'postVar': 'subcategory'});
}

var loadersubmit = function(id_form) {
	new StickyWin.Modal({
		content: '<img src="'+root+'/files/ajax-loader.gif" alt="Loading..." />',
		modalOptions: {
			modalStyle:{
			  'background-color':'#fff',
			  'opacity':.6
			}
		}
	});
	f = document.getElementById(id_form);
	f.submit();
}

var submitform = function(id_form, id_container) {
	//var FPop = new Fx.Slide(id_container);
	var req = new Request.HTML({
		method: 'post',
		url: $(id_form).get('action'),
		data: $(id_form),
		update: $(id_container),
		//onRequest: function() {tinyMCE.triggerSave(true,true);},
		onComplete: function() {
			//FPop.hide().show();
			//close_pop(id_container);
			//if (id_container == 'main') enable_upop()
		}
	}).send();
}

var submitlink = function(id_container, url) {
	var BPop = new Fx.Slide(id_container);
	var req = new Request.HTML({
		method: 'get',
		url: url,
		//data: $(id_form),
		update: $(id_container),
		onComplete: function() {
			BPop.hide().show();
		}
	}).send();
	return false;
}

var close_pop = function(id_pop) {
	var Pop = new Fx.Slide(id_pop);
	Pop.toggle();
}

/* contacts */
var c = 0
var reload_captcha = function() {
	c = c + 1;
	var src = $('reload_captcha').get('href');
	$('captcha_img').dispose();
	
	var newcha = new Element('img', {
		'id': 'captcha_img',
		'src': src + '/' + c,
		'alt': 'captcha'
    });
	newcha.inject('cha', 'top');
}

