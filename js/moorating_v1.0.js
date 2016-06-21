/*
Title 	: Simple Star Rating Script Using Mootools
Author 	: Nikhil Kunder (nik1409@gmail.com)
Date 	: 2008/01/12
Version : 1.0
    moostar_v1.0.js  is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 	Lesser General Public License for more details.
*/

window.addEvent('domready', function() {
	var moostarwidth= 11; // width of Star Rating container
	var moostarnum = 11 // number of stars
	var inpercent = true; // Set this flag to true , if you require percentage values to be displayed
	var isFractional = true // Set this to false, if you want whole values like 1,2 ...5, rather than 1.24, 1.25, 4.56 etc
	var moostar = $$('.moostar');
	var moostartval = $$('.moostartval');
	// Behavior for each star rating
	moostar.each(function(el,i){
		el.revert =true;
		el.addEvents({
			'mouseenter': function(){},
			'mousemove': function(event){
						w = event.client.x - el.getPosition().x;
						//status=event.client.x; //For test purpose only
						el.getChildren()[0].setStyles({'width': w});
						var x = (w/moostarwidth) * moostarnum;
						if(inpercent){
							var v = Math.round(w/110*100);
							if(v <101) moostartval[i].innerHTML= Math.round(w/110*100)+'%';
						}else{
							if(isFractional){if(x<=5 || x >=0) moostartval[i].innerHTML= formatNumber(x,2);}
							else{moostartval[i].innerHTML= Math.round(x);
						}
					}
			},
			'click': function(){ 
					updateRating(el.id,moostartval[i].innerHTML);
					el.getChildren()[0].title = parseFloat(moostartval[i].innerHTML);
					el.revert = false;
			},
			'mouseleave': function(){
					//status ="left"; //For test purpose only
					if(el.revert){
						var v = parseInt(el.getChildren()[0].title);
						if(inpercent){
							w = (parseInt(el.getChildren()[0].title)/100) * moostarwidth;
							moostartval[i].innerHTML = v +'%';
							}else{
							w = parseInt(el.getChildren()[0].title) * (moostarwidth/moostarnum);
							moostartval[i].innerHTML = v;
						}
						el.getChildren()[0].setStyles({'width': w});
					}
					el.revert = true;
			}
		});
	});
});

function formatNumber(myNum, numOfDec) {
  var decimal = 1
  for(i=1; i<=numOfDec;i++)decimal = decimal *10
  var myFormattedNum = (Math.round(myNum * decimal)/decimal).toFixed(numOfDec)
  return myFormattedNum;
} 

function updateRating(id, rating) {
	xmlhttpPost('store_rating.php', 'view_posts_'+id, id, '');
	//alert(rating);
	//return false;
} 

// submit form
function xmlhttpPost(strURL,formname,responsediv,responsemsg) {
	var xmlHttpReq = false;
    var self = this;
    // Xhr per Mozilla/Safari/Ie7
    if (window.XMLHttpRequest) {
        self.xmlHttpReq = new XMLHttpRequest();
    }

    // per tutte le altre versioni di IE
    else if (window.ActiveXObject) {
        self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
    }
    self.xmlHttpReq.open('POST', strURL, true);
    self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    self.xmlHttpReq.onreadystatechange = function() {
        
		if (self.xmlHttpReq.readyState == 4) {
			// Quando pronta, visualizzo la risposta del form
            updatepage(self.xmlHttpReq.responseText,responsediv);
        }
		else{
			// In attesa della risposta del form visualizzo il msg di attesa
			updatepage(responsemsg,responsediv);
		}
    }
    self.xmlHttpReq.send(getquerystring(formname));
}



function getquerystring(formname) {
    var form = document.forms[formname];
	var qstr = "";
    function GetElemValue(name, value) {
        qstr += (qstr.length > 0 ? "&" : "")
            + escape(name).replace(/\+/g, "%2B") + "="
            + escape(value ? value : "").replace(/\+/g, "%2B");
			//+ escape(value ? value : "").replace(/\n/g, "%0D");
    }
	var elemArray = form.elements;
    for (var i = 0; i < elemArray.length; i++) {
        var element = elemArray[i];
        var elemType = element.type.toUpperCase();
        var elemName = element.name;
		if (elemName) {
            if (elemType == "TEXT"
                    || elemType == "TEXTAREA"
                    || elemType == "PASSWORD"
					|| elemType == "BUTTON"
					|| elemType == "RESET"
					|| elemType == "SUBMIT"
					|| elemType == "FILE"
					|| elemType == "IMAGE"
                    || elemType == "HIDDEN")
                GetElemValue(elemName, element.value);
            else if (elemType == "CHECKBOX" && element.checked)
                GetElemValue(elemName, 
                    element.value ? element.value : "On");
            else if (elemType == "RADIO" && element.checked)
                GetElemValue(elemName, element.value);
            else if (elemType.indexOf("SELECT") != -1)
                for (var j = 0; j < element.options.length; j++) {
                    var option = element.options[j];
                    if (option.selected)
                        GetElemValue(elemName,
                            option.value ? option.value : option.text);
                }
        }
    }
    return qstr;
}

function updatepage(str,responsediv){
	document.getElementById(responsediv).style.visibility = 'hidden';
	document.getElementById('mstarval'+responsediv).style.visibility = 'hidden';
	document.getElementById('wait').innerHTML = 'Carico il tuo voto, attendi...';
	window.location.reload();
}