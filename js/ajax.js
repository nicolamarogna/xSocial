// JavaScript Document
// JavaScript Document
/*
This is a dynamic ajax query script. It uses the variables, enabling it to work with any page without having to recreate a new query script. Currently this is only for querying data. 
I am working on one for inserting and updating data also.

whichElement and whichLink are defined in the page calling the function.

I.E.

whichLink = "nameQuery.asp" this is the page making the query to the database.
whichElement = "nameList" this is the element where the data will be returned on the original page.

this function uses both the "name" attribute and the "id" attribute but could just as easily only use one or the other.

-created by krstofer@teamshibby.com. Based off of script found @ w3schools.com
*/
var xmlHttp

function showData()
{ 
//this shows the "working" graphic when the query page is retrieving data
document.getElementById(whichElement).innerHTML = "<div align=center><b>Working....</b><br><br><img src='workingBar.gif'></div>"

//by setting these values to "" it prevents an undefined error. Also defining these outside of the function will add existing data to the variable which will cause an error.
var theForm = ""
var howManyElement = ""
var daString = ""


theForm = document.form1
//this finds the number of elements on the form and assigns the number to the howManyElement variable.
howManyElement = theForm.elements.length;


xmlHttp=GetXmlHttpObject();

//this for block creates the string with all the data in the url. The loop goes through each element and assigns the form name and the value to the string creating a "first=brian&last=collier' string if the form contains a 'first' field and a 'last' field.

for (i=0; i<howManyElement; i++){
	
 daString = daString + theForm.elements[i].name+ "="+theForm.elements[i].value+"&";

}
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 
 
//the 'whichLink' variable is assigned on the page making the request. 
var url=whichLink;
url=url+"?"+daString;
url=url+"sid="+Math.random();
xmlHttp.onreadystatechange=stateChanged;
xmlHttp.open("GET",url,true);
xmlHttp.send(null);

//alert(url)//this is used to view the URL being sent to the query page.
}

function stateChanged() 
{ 

if (xmlHttp.readyState==4)
{ 

document.getElementById(whichElement).innerHTML=xm  lHttp.responseText;
}
}

function GetXmlHttpObject()
{
var xmlHttp=null;
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
return xmlHttp;
}