if (navigator.userAgent.indexOf("Firefox")!=-1)
var load_method = 'domready';
if (navigator.userAgent.indexOf("MSIE")!=-1)
var load_method = 'load';

window.addEvent(load_method, function() {
	
	//create our Accordion instance
	var myAccordion = new Accordion($('sidebar'), 'h3.toggler', 'div.element', {
		display: 2,
		opacity: true,
		onActive: function(toggler, element){
			toggler.setStyle('color', '#333');
		},
		onBackground: function(toggler, element){
			toggler.setStyle('color', '#666');
		}
	});
	
	//create our Accordion instance
	var homeAccordion = new Accordion($('topic'), 'h3.htogg', 'div.item', {
		opacity: true,
		onActive: function(toggler, element){
			toggler.setStyle('color', '#333');
		},
		onBackground: function(toggler, element){
			toggler.setStyle('color', '#666');
		}
	});
	
	$$('.toggler', '.htogg').addEvent('mouseenter', function() { this.fireEvent('click'); });  
	// mouseenter, click
	// help
	
	if ($chk($('help_pop'))) {
		
		var HPop = new Fx.Slide('help_pop');
		HPop.hide();
		
		$('h_pop').addEvent('click', function(e){
			e.stop();
			HPop.toggle();
		});
	}
	
	

	if ($chk($('form_pop'))) {
		var FPop = new Fx.Slide('form_pop');
		// FPop.hide();
	}


	if ($chk($('view_pop'))) {
		var VPop = new Fx.Slide('view_pop');
		VPop.hide();
		
		$('v_pop').addEvent('click', function(e){
			e.stop();
			VPop.toggle();
		});
	}

	
	
	var tip1 = new Tips($$('.tooltip'), {
		//className: 'tool1'
	  });
	
	

	
});
	
	
/*		var gotopage = function(url, id_container) {
		var req = new Request.HTML({
			url: url,
			update: $(id_container),
		}).send();
	}	

*/	
	var loadpage = function(url, id_container) {
		//tinyMCE.triggerSave(true,true);
		var APop = new Fx.Slide(id_container);
		APop.hide();		
		var req = new Request.HTML({  
			method: 'post',  
			url: url, //this.get('href'),
			update: $(id_container),
		onRequest: function() {
			
		},
		onComplete: function() {
			APop.toggle();
		}
		}).send();
	}
	
	var loadpage_noslide = function(url, id_container) {
		//tinyMCE.triggerSave(true,true);
		var req = new Request.HTML({  
			method: 'post',  
			url: url, //this.get('href'),
			update: $(id_container),
		}).send();
	}
	

/*	
	var editform = function(url, id_container) {
		var FPop = new Fx.Slide('form_pop');
		FPop.hide();
		FPop.toggle();
		var req = new Request.HTML({
			method: 'post',
			url: $(url),
			//data: $(id_form),
			//update: $('form_risultati'),
			update: $(id_container),
			
				
			
		}).send();
	}	
*/

	var submitform = function(id_form, id_container) {
		tinyMCE.triggerSave(true,true);
		var APop = new Fx.Slide(id_container);
		var req = new Request.HTML({
			method: 'post',
			url: $(id_form).get('action'),
			data: $(id_form),
			onRequest: function() {
			//$('form_risultati').set ('html', 'Operazione in corso, attendere...');

			 //alert($(id_form).get('action'));
		},
		onFailure: function() {
			alert("Non è stato possibile eseguire la richiesta correttamente");
		},

		update: $('form_risultati'),
			//update: $(id_container),
			onComplete: function() {
				APop.toggle();
				//$('form_risultati').load("admin/risultati.php"); 
			}
		}).send();
	}

