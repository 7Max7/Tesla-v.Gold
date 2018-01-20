/* --- BoxOver ---
/* --- v 2.1 17th June 2006
By Oliver Bryant with help of Matthew Tagg
http://boxover.swazz.org */

if (typeof document.attachEvent!='undefined') {
   window.attachEvent('onload',init);
   document.attachEvent('onmousemove',moveMouse);
   document.attachEvent('onclick',checkMove); }
else {
   window.addEventListener('load',init,false);
   document.addEventListener('mousemove',moveMouse,false);
   document.addEventListener('click',checkMove,false);
}

var oDv=document.createElement("div");
var dvHdr=document.createElement("div");
var dvBdy=document.createElement("div");
var windowlock,boxMove,fixposx,fixposy,lockX,lockY,fixx,fixy,ox,oy,boxLeft,boxRight,boxTop,boxBottom,evt,mouseX,mouseY,boxOpen,totalScrollTop,totalScrollLeft;
boxOpen=false;
ox=10;
oy=10;
lockX=0;
lockY=0;

function init() {
	oDv.appendChild(dvHdr);
	oDv.appendChild(dvBdy);
	oDv.style.position="absolute";
	oDv.style.visibility='hidden';
	document.body.appendChild(oDv);	
}

function defHdrStyle() {
	dvHdr.innerHTML='<img  style="vertical-align:middle"  src="info.gif">&nbsp;&nbsp;'+dvHdr.innerHTML;
	dvHdr.style.fontWeight='bold';
	dvHdr.style.width='150px';
	dvHdr.style.fontFamily='arial';
	dvHdr.style.border='1px solid #A5CFE9';
	dvHdr.style.padding='3';
	dvHdr.style.fontSize='11';
	dvHdr.style.color='#4B7A98';
	dvHdr.style.background='#D5EBF9';
	dvHdr.style.filter='alpha(opacity=85)'; // IE
	dvHdr.style.opacity='0.85'; // FF
}

function defBdyStyle() {
	dvBdy.style.borderBottom='1px solid #A5CFE9';
	dvBdy.style.borderLeft='1px solid #A5CFE9';
	dvBdy.style.borderRight='1px solid #A5CFE9';
	dvBdy.style.width='150px';
	dvBdy.style.fontFamily='arial';
	dvBdy.style.fontSize='11';
	dvBdy.style.padding='3';
	dvBdy.style.color='#1B4966';
	dvBdy.style.background='#FFFFFF';
	dvBdy.style.filter='alpha(opacity=85)'; // IE
	dvBdy.style.opacity='0.85'; // FF
}

function checkElemBO(txt) {
if (!txt || typeof(txt) != 'string') return false;
if ((txt.indexOf('header')>-1)&&(txt.indexOf('body')>-1)&&(txt.indexOf('[')>-1)&&(txt.indexOf('[')>-1)) 
   return true;
else
   return false;
}

function scanBO(curNode) {
	  if (checkElemBO(curNode.title)) {
         curNode.boHDR=getParam('header',curNode.title);
         curNode.boBDY=getParam('body',curNode.title);
			curNode.boCSSBDY=getParam('cssbody',curNode.title);			
			curNode.boCSSHDR=getParam('cssheader',curNode.title);
			curNode.IEbugfix=(getParam('hideselects',curNode.title)=='on')?true:false;
			curNode.fixX=parseInt(getParam('fixedrelx',curNode.title));
			curNode.fixY=parseInt(getParam('fixedrely',curNode.title));
			curNode.absX=parseInt(getParam('fixedabsx',curNode.title));
			curNode.absY=parseInt(getParam('fixedabsy',curNode.title));
			curNode.offY=(getParam('offsety',curNode.title)!='')?parseInt(getParam('offsety',curNode.title)):10;
			curNode.offX=(getParam('offsetx',curNode.title)!='')?parseInt(getParam('offsetx',curNode.title)):10;
			curNode.fade=(getParam('fade',curNode.title)=='on')?true:false;
			curNode.fadespeed=(getParam('fadespeed',curNode.title)!='')?getParam('fadespeed',curNode.title):0.04;
			curNode.delay=(getParam('delay',curNode.title)!='')?parseInt(getParam('delay',curNode.title)):0;
			if (getParam('requireclick',curNode.title)=='on') {
				curNode.requireclick=true;
				document.all?curNode.attachEvent('onclick',showHideBox):curNode.addEventListener('click',showHideBox,false);
				document.all?curNode.attachEvent('onmouseover',hideBox):curNode.addEventListener('mouseover',hideBox,false);
			}
			else {// Note : if requireclick is on the stop clicks are ignored   			
   			if (getParam('doubleclickstop',curNode.title)!='off') {
   				document.all?curNode.attachEvent('ondblclick',pauseBox):curNode.addEventListener('dblclick',pauseBox,false);
   			}	
   			if (getParam('singleclickstop',curNode.title)=='on') {
   				document.all?curNode.attachEvent('onclick',pauseBox):curNode.addEventListener('click',pauseBox,false);
   			}
   		}
			curNode.windowLock=getParam('windowlock',curNode.title).toLowerCase()=='off'?false:true;
			curNode.title='';
			curNode.hasbox=1;
	   }
	   else
	      curNode.hasbox=2;   
}


function getParam(param,list) {
	var reg = new RegExp('([^a-zA-Z]' + param + '|^' + param + ')\\s*=\\s*\\[\\s*(((\\[\\[)|(\\]\\])|([^\\]\\[]))*)\\s*\\]');
	var res = reg.exec(list);
	var returnvar;
	if(res)
		return res[2].replace('[[','[').replace(']]',']');
	else
		return '';
}

function Left(elem){	
	var x=0;
	if (elem.calcLeft)
		return elem.calcLeft;
	var oElem=elem;
	while(elem){
		 if ((elem.currentStyle)&& (!isNaN(parseInt(elem.currentStyle.borderLeftWidth)))&&(x!=0))
		 	x+=parseInt(elem.currentStyle.borderLeftWidth);
		 x+=elem.offsetLeft;
		 elem=elem.offsetParent;
	  } 
	oElem.calcLeft=x;
	return x;
	}

function Top(elem){
	 var x=0;
	 if (elem.calcTop)
	 	return elem.calcTop;
	 var oElem=elem;
	 while(elem){		
	 	 if ((elem.currentStyle)&& (!isNaN(parseInt(elem.currentStyle.borderTopWidth)))&&(x!=0))
		 	x+=parseInt(elem.currentStyle.borderTopWidth); 
		 x+=elem.offsetTop;
	         elem=elem.offsetParent;
 	 } 
 	 oElem.calcTop=x;
 	 return x;
 	 
}

var ah,ab;
function applyStyles() {
	if(ab)
		oDv.removeChild(dvBdy);
	if (ah)
		oDv.removeChild(dvHdr);
	dvHdr=document.createElement("div");
	dvBdy=document.createElement("div");
	CBE.boCSSBDY?dvBdy.className=CBE.boCSSBDY:defBdyStyle();
	CBE.boCSSHDR?dvHdr.className=CBE.boCSSHDR:defHdrStyle();
	dvHdr.innerHTML=CBE.boHDR;
	dvBdy.innerHTML=CBE.boBDY;
	ah=false;
	ab=false;
	if (CBE.boHDR!='') {		
		oDv.appendChild(dvHdr);
		ah=true;
	}	
	if (CBE.boBDY!=''){
		oDv.appendChild(dvBdy);
		ab=true;
	}	
}

var CSE,iterElem,LSE,CBE,LBE, totalScrollLeft, totalScrollTop, width, height ;
var ini=false;

// Customised function for inner window dimension
function SHW() {
   if (document.body && (document.body.clientWidth !=0)) {
      width=document.body.clientWidth;
      height=document.body.clientHeight;
   }
   if (document.documentElement && (document.documentElement.clientWidth!=0) && (document.body.clientWidth + 20 >= document.documentElement.clientWidth)) {
      width=document.documentElement.clientWidth;   
      height=document.documentElement.clientHeight;   
   }   
   return [width,height];
}


var ID=null;
function moveMouse(e) {
   //boxMove=true;
	e?evt=e:evt=event;
	
	CSE=evt.target?evt.target:evt.srcElement;
	
	if (!CSE.hasbox) {
	   // Note we need to scan up DOM here, some elements like TR don't get triggered as srcElement
	   iElem=CSE;
	   while ((iElem.parentNode) && (!iElem.hasbox)) {
	      scanBO(iElem);
	      iElem=iElem.parentNode;
	   }	   
	}
	
	if ((CSE!=LSE)&&(!isChild(CSE,dvHdr))&&(!isChild(CSE,dvBdy))){		
	   if (!CSE.boxItem) {
			iterElem=CSE;
			while ((iterElem.hasbox==2)&&(iterElem.parentNode))
					iterElem=iterElem.parentNode; 
			CSE.boxItem=iterElem;
			}
		iterElem=CSE.boxItem;
		if (CSE.boxItem&&(CSE.boxItem.hasbox==1))  {
			LBE=CBE;
			CBE=iterElem;
			if (CBE!=LBE) {
				applyStyles();
				if (!CBE.requireclick)
					if (CBE.fade) {
						if (ID!=null)
							clearTimeout(ID);
						ID=setTimeout("fadeIn("+CBE.fadespeed+")",CBE.delay);
					}
					else {
						if (ID!=null)
							clearTimeout(ID);
						COL=1;
						ID=setTimeout("oDv.style.visibility='visible';ID=null;",CBE.delay);						
					}
				if (CBE.IEbugfix) {hideSelects();} 
				fixposx=!isNaN(CBE.fixX)?Left(CBE)+CBE.fixX:CBE.absX;
				fixposy=!isNaN(CBE.fixY)?Top(CBE)+CBE.fixY:CBE.absY;			
				lockX=0;
				lockY=0;
				boxMove=true;
				ox=CBE.offX?CBE.offX:10;
				oy=CBE.offY?CBE.offY:10;
			}
		}
		else if (!isChild(CSE,dvHdr) && !isChild(CSE,dvBdy) && (boxMove))	{
			// The conditional here fixes flickering between tables cells.
			if ((!isChild(CBE,CSE)) || (CSE.tagName!='TABLE')) {   			
   			CBE=null;
   			if (ID!=null)
  					clearTimeout(ID);
   			fadeOut();
   			showSelects();
			}
		}
		LSE=CSE;
	}
	else if (((isChild(CSE,dvHdr) || isChild(CSE,dvBdy))&&(boxMove))) {
		totalScrollLeft=0;
		totalScrollTop=0;
		
		iterElem=CSE;
		while(iterElem) {
			if(!isNaN(parseInt(iterElem.scrollTop)))
				totalScrollTop+=parseInt(iterElem.scrollTop);
			if(!isNaN(parseInt(iterElem.scrollLeft)))
				totalScrollLeft+=parseInt(iterElem.scrollLeft);
			iterElem=iterElem.parentNode;			
		}
		if (CBE!=null) {
			boxLeft=Left(CBE)-totalScrollLeft;
			boxRight=parseInt(Left(CBE)+CBE.offsetWidth)-totalScrollLeft;
			boxTop=Top(CBE)-totalScrollTop;
			boxBottom=parseInt(Top(CBE)+CBE.offsetHeight)-totalScrollTop;
			doCheck();
		}
	}
	
	if (boxMove&&CBE) {
		// This added to alleviate bug in IE6 w.r.t DOCTYPE
		bodyScrollTop=document.documentElement&&document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop;
		bodyScrollLet=document.documentElement&&document.documentElement.scrollLeft?document.documentElement.scrollLeft:document.body.scrollLeft;
		mouseX=evt.pageX?evt.pageX-bodyScrollLet:evt.clientX-document.body.clientLeft;
		mouseY=evt.pageY?evt.pageY-bodyScrollTop:evt.clientY-document.body.clientTop;
		if ((CBE)&&(CBE.windowLock)) {
			mouseY < -oy?lockY=-mouseY-oy:lockY=0;
			mouseX < -ox?lockX=-mouseX-ox:lockX=0;
			mouseY > (SHW()[1]-oDv.offsetHeight-oy)?lockY=-mouseY+SHW()[1]-oDv.offsetHeight-oy:lockY=lockY;
			mouseX > (SHW()[0]-dvBdy.offsetWidth-ox)?lockX=-mouseX-ox+SHW()[0]-dvBdy.offsetWidth:lockX=lockX;			
		}
		oDv.style.left=((fixposx)||(fixposx==0))?fixposx:bodyScrollLet+mouseX+ox+lockX+"px";
		oDv.style.top=((fixposy)||(fixposy==0))?fixposy:bodyScrollTop+mouseY+oy+lockY+"px";		
		
	}
}

function doCheck() {	
	if (   (mouseX < boxLeft)    ||     (mouseX >boxRight)     || (mouseY < boxTop) || (mouseY > boxBottom)) {
		if (!CBE.requireclick)
			fadeOut();
		if (CBE.IEbugfix) {showSelects();}
		CBE=null;
	}
}

function pauseBox(e) {
   e?evt=e:evt=event;
	boxMove=false;
	evt.cancelBubble=true;
}

function showHideBox(e) {
	oDv.style.visibility=(oDv.style.visibility!='visible')?'visible':'hidden';
}

function hideBox(e) {
	oDv.style.visibility='hidden';
}

var COL=0;
var stopfade=false;
function fadeIn(fs) {
		ID=null;
		COL=0;
		oDv.style.visibility='visible';
		fadeIn2(fs);
}

function fadeIn2(fs) {
		COL=COL+fs;
		COL=(COL>1)?1:COL;
		oDv.style.filter='alpha(opacity='+parseInt(100*COL)+')';
		oDv.style.opacity=COL;
		if (COL<1)
		 setTimeout("fadeIn2("+fs+")",20);		
}


function fadeOut() {
	oDv.style.visibility='hidden';
	
}

function isChild(s,d) {
	while(s) {
		if (s==d) 
			return true;
		s=s.parentNode;
	}
	return false;
}

var cSrc;
function checkMove(e) {
	e?evt=e:evt=event;
	cSrc=evt.target?evt.target:evt.srcElement;
	if ((!boxMove)&&(!isChild(cSrc,oDv))) {
		fadeOut();
		if (CBE&&CBE.IEbugfix) {showSelects();}
		boxMove=true;
		CBE=null;
	}
}

function showSelects(){
   var elements = document.getElementsByTagName("select");
   for (i=0;i< elements.length;i++){
      elements[i].style.visibility='visible';
   }
}

function hideSelects(){
   var elements = document.getElementsByTagName("select");
   for (i=0;i< elements.length;i++){
   elements[i].style.visibility='hidden';
   }
}





















/*

CUSTOM FORM ELEMENTS

Created by Ryan Fait
www.ryanfait.com

The only thing you need to change in this file is the following
variables: checkboxHeight, radioHeight and selectWidth.

Replace the first two numbers with the height of the checkbox and
radio button. The actual height of both the checkbox and radio
images should be 4 times the height of these two variables. The
selectWidth value should be the width of your select list image.

You may need to adjust your images a bit if there is a slight
vertical movement during the different stages of the button
activation.

Visit http://ryanfait.com/ for more information.

*/

var checkboxHeight = "25";
var radioHeight = "25";
var selectWidth = "190";

/* No need to change anything after this */

document.write('<style type="text/css">input.styled { display: none; } select.styled { position: relative; width: ' + selectWidth + 'px; opacity: 0; filter: alpha(opacity=0); z-index: 5; }</style>');

var Custom = {
	init: function() {
		var inputs = document.getElementsByTagName("input"), span = Array(), textnode, option, active;
		for(a = 0; a < inputs.length; a++) {
			if((inputs[a].type == "checkbox" || inputs[a].type == "radio") && inputs[a].className == "styled") {
				span[a] = document.createElement("span");
				span[a].className = inputs[a].type;

				if(inputs[a].checked == true) {
					if(inputs[a].type == "checkbox") {
						position = "0 -" + (checkboxHeight*2) + "px";
						span[a].style.backgroundPosition = position;
					} else {
						position = "0 -" + (radioHeight*2) + "px";
						span[a].style.backgroundPosition = position;
					}
				}
				inputs[a].parentNode.insertBefore(span[a], inputs[a]);
				inputs[a].onchange = Custom.clear;
				span[a].onmousedown = Custom.pushed;
				span[a].onmouseup = Custom.check;
				document.onmouseup = Custom.clear;
			}
		}
		inputs = document.getElementsByTagName("select");
		for(a = 0; a < inputs.length; a++) {
			if(inputs[a].className == "styled") {
				option = inputs[a].getElementsByTagName("option");
				active = option[0].childNodes[0].nodeValue;
				textnode = document.createTextNode(active);
				for(b = 0; b < option.length; b++) {
					if(option[b].selected == true) {
						textnode = document.createTextNode(option[b].childNodes[0].nodeValue);
					}
				}
				span[a] = document.createElement("span");
				span[a].className = "select";
				span[a].id = "select" + inputs[a].name;
				span[a].appendChild(textnode);
				inputs[a].parentNode.insertBefore(span[a], inputs[a]);
				inputs[a].onchange = Custom.choose;
			}
		}
	},
	pushed: function() {
		element = this.nextSibling;
		if(element.checked == true && element.type == "checkbox") {
			this.style.backgroundPosition = "0 -" + checkboxHeight*3 + "px";
		} else if(element.checked == true && element.type == "radio") {
			this.style.backgroundPosition = "0 -" + radioHeight*3 + "px";
		} else if(element.checked != true && element.type == "checkbox") {
			this.style.backgroundPosition = "0 -" + checkboxHeight + "px";
		} else {
			this.style.backgroundPosition = "0 -" + radioHeight + "px";
		}
	},
	check: function() {
		element = this.nextSibling;
		if(element.checked == true && element.type == "checkbox") {
			this.style.backgroundPosition = "0 0";
			element.checked = false;
		} else {
			if(element.type == "checkbox") {
				this.style.backgroundPosition = "0 -" + checkboxHeight*2 + "px";
			} else {
				this.style.backgroundPosition = "0 -" + radioHeight*2 + "px";
				group = this.nextSibling.name;
				inputs = document.getElementsByTagName("input");
				for(a = 0; a < inputs.length; a++) {
					if(inputs[a].name == group && inputs[a] != this.nextSibling) {
						inputs[a].previousSibling.style.backgroundPosition = "0 0";
					}
				}
			}
			element.checked = true;
		}
	},
	clear: function() {
		inputs = document.getElementsByTagName("input");
		for(var b = 0; b < inputs.length; b++) {
			if(inputs[b].type == "checkbox" && inputs[b].checked == true && inputs[b].className == "styled") {
				inputs[b].previousSibling.style.backgroundPosition = "0 -" + checkboxHeight*2 + "px";
			} else if(inputs[b].type == "checkbox" && inputs[b].className == "styled") {
				inputs[b].previousSibling.style.backgroundPosition = "0 0";
			} else if(inputs[b].type == "radio" && inputs[b].checked == true && inputs[b].className == "styled") {
				inputs[b].previousSibling.style.backgroundPosition = "0 -" + radioHeight*2 + "px";
			} else if(inputs[b].type == "radio" && inputs[b].className == "styled") {
				inputs[b].previousSibling.style.backgroundPosition = "0 0";
			}
		}
	},
	choose: function() {
		option = this.getElementsByTagName("option");
		for(d = 0; d < option.length; d++) {
			if(option[d].selected == true) {
				document.getElementById("select" + this.name).childNodes[0].nodeValue = option[d].childNodes[0].nodeValue;
			}
		}
	}
}
window.onload = Custom.init;














/*
 * jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
 *
*/

// t: current time, b: begInnIng value, c: change In value, d: duration
jQuery.easing['jswing'] = jQuery.easing['swing'];

jQuery.extend( jQuery.easing,
{
	def: 'easeOutQuad',
	swing: function (x, t, b, c, d) {
		//alert(jQuery.easing.default);
		return jQuery.easing[jQuery.easing.def](x, t, b, c, d);
	},
	easeInQuad: function (x, t, b, c, d) {
		return c*(t/=d)*t + b;
	},
	easeOutQuad: function (x, t, b, c, d) {
		return -c *(t/=d)*(t-2) + b;
	},
	easeInOutQuad: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t + b;
		return -c/2 * ((--t)*(t-2) - 1) + b;
	},
	easeInCubic: function (x, t, b, c, d) {
		return c*(t/=d)*t*t + b;
	},
	easeOutCubic: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t + 1) + b;
	},
	easeInOutCubic: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t + b;
		return c/2*((t-=2)*t*t + 2) + b;
	},
	easeInQuart: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t + b;
	},
	easeOutQuart: function (x, t, b, c, d) {
		return -c * ((t=t/d-1)*t*t*t - 1) + b;
	},
	easeInOutQuart: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
		return -c/2 * ((t-=2)*t*t*t - 2) + b;
	},
	easeInQuint: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t*t + b;
	},
	easeOutQuint: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t*t*t + 1) + b;
	},
	easeInOutQuint: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
		return c/2*((t-=2)*t*t*t*t + 2) + b;
	},
	easeInSine: function (x, t, b, c, d) {
		return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
	},
	easeOutSine: function (x, t, b, c, d) {
		return c * Math.sin(t/d * (Math.PI/2)) + b;
	},
	easeInOutSine: function (x, t, b, c, d) {
		return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
	},
	easeInExpo: function (x, t, b, c, d) {
		return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
	},
	easeOutExpo: function (x, t, b, c, d) {
		return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
	},
	easeInOutExpo: function (x, t, b, c, d) {
		if (t==0) return b;
		if (t==d) return b+c;
		if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
		return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
	},
	easeInCirc: function (x, t, b, c, d) {
		return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;
	},
	easeOutCirc: function (x, t, b, c, d) {
		return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
	},
	easeInOutCirc: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
		return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
	},
	easeInElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
	},
	easeOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
	},
	easeInOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
		return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
	},
	easeInBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*(t/=d)*t*((s+1)*t - s) + b;
	},
	easeOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
	},
	easeInOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158; 
		if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
		return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
	},
	easeInBounce: function (x, t, b, c, d) {
		return c - jQuery.easing.easeOutBounce (x, d-t, 0, c, d) + b;
	},
	easeOutBounce: function (x, t, b, c, d) {
		if ((t/=d) < (1/2.75)) {
			return c*(7.5625*t*t) + b;
		} else if (t < (2/2.75)) {
			return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
		} else if (t < (2.5/2.75)) {
			return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
		} else {
			return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
		}
	},
	easeInOutBounce: function (x, t, b, c, d) {
		if (t < d/2) return jQuery.easing.easeInBounce (x, t*2, 0, c, d) * .5 + b;
		return jQuery.easing.easeOutBounce (x, t*2-d, 0, c, d) * .5 + c*.5 + b;
	}
});














































/* idTabs ~ Sean Catchpole - Version 2.2 - MIT/GPL */
(function(){var dep={"jQuery":"http://code.jquery.com/jquery-latest.min.js"};var init=function(){(function($){$.fn.idTabs=function(){var s={};for(var i=0;i<arguments.length;++i){var a=arguments[i];switch(a.constructor){case Object:$.extend(s,a);break;case Boolean:s.change=a;break;case Number:s.start=a;break;case Function:s.click=a;break;case String:if(a.charAt(0)=='.')s.selected=a;else if(a.charAt(0)=='!')s.event=a;else s.start=a;break;}}
if(typeof s['return']=="function")
s.change=s['return'];return this.each(function(){$.idTabs(this,s);});}
$.idTabs=function(tabs,options){var meta=($.metadata)?$(tabs).metadata():{};var s=$.extend({},$.idTabs.settings,meta,options);if(s.selected.charAt(0)=='.')s.selected=s.selected.substr(1);if(s.event.charAt(0)=='!')s.event=s.event.substr(1);if(s.start==null)s.start=-1;var showId=function(){if($(this).is('.'+s.selected))
return s.change;var id="#"+this.href.split('#')[1];var aList=[];var idList=[];$("a",tabs).each(function(){if(this.href.match(/#/)){aList.push(this);idList.push("#"+this.href.split('#')[1]);}});if(s.click&&!s.click.apply(this,[id,idList,tabs,s]))return s.change;for(i in aList)$(aList[i]).removeClass(s.selected);for(i in idList)$(idList[i]).hide();$(this).addClass(s.selected);$(id).show();return s.change;}
var list=$("a[href*='#']",tabs).unbind(s.event,showId).bind(s.event,showId);list.each(function(){$("#"+this.href.split('#')[1]).hide();});var test=false;if((test=list.filter('.'+s.selected)).length);else if(typeof s.start=="number"&&(test=list.eq(s.start)).length);else if(typeof s.start=="string"&&(test=list.filter("[href*='#"+s.start+"']")).length);if(test){test.removeClass(s.selected);test.trigger(s.event);}
return s;}
$.idTabs.settings={start:0,change:false,click:null,selected:".selected",event:"!click"};$.idTabs.version="2.2";$(function(){$(".idTabs").idTabs();});})(jQuery);}
var check=function(o,s){s=s.split('.');while(o&&s.length)o=o[s.shift()];return o;}
var head=document.getElementsByTagName("head")[0];var add=function(url){var s=document.createElement("script");s.type="text/javascript";s.src=url;head.appendChild(s);}
var s=document.getElementsByTagName('script');var src=s[s.length-1].src;var ok=true;for(d in dep){if(check(this,d))continue;ok=false;add(dep[d]);}if(ok)return init();add(src);})();























































/* www.nulled.ws JS theme for dynamic panels  = coded by jurassik */

function divon(onnum, startelement, endelement)
{
	var obj = document.getElementById( "div" + onnum );
	if(obj.className != "vis")
		{
			for(i = startelement; i < (endelement+1); i++)
			{
				if(i != onnum) divoff(i);
			}
			obj.className = "vis";
		}
return(true);
}

function divoff(offnum)
{
	var obj = document.getElementById( "div" + offnum );
	if(obj.className == "hid") return; 
	else obj.className = "hid";
return;
}



function tabon(onnum, startelement, endelement)
{
	var obj = document.getElementById( "tab" + onnum );
	if(obj.className == "off")
		{
			for(i = startelement; i < (endelement+1); i++)
			{
				if(i != onnum) taboff(i);
			}
			obj.className = "on";
			var kuka = "tab" + startelement + "-" + endelement;
			document.cookie = kuka + "=" + onnum + "; path=/";
		}
return(true);
}

function taboff(offnum)
{
	var obj = document.getElementById( "tab" + offnum );
	if(obj.className == "off") return; 
	else obj.className = "off";
return;
}




function toggles(ofnum)
{
	var obj = document.getElementById( "fornum" + ofnum );
	var mover = document.getElementById( "move" + ofnum );
	var dno = document.getElementById( "sub" + ofnum );
	if(obj.className == "hid")
	{
		obj.className = "vis"; 
		mover.className = "on"; 
		dno.className = ""; 
	}
	
	else
		{
		obj.className = "hid"; 
		mover.className = "off"; 
		dno.className = "of"; 
	}
return;
}

function togglechat(ofnum)
{
    var dno = ofnum + 1;
    var obj = document.getElementById( "fornum" + ofnum );
    dno = document.getElementById( "fornum" + dno );
    
    if(obj.className == "hid")
    {
        obj.className = "vis"; 
        dno.className = "hid"; 
setCookie('shoutbox','show',0,'/');
    }
    
    else
        {
        obj.className = "hid"; 
        dno.className = "vis"; 
setCookie('shoutbox','hide',0,'/');
    }
return;
}





function setCookie(name, value, expiredays, path, domain, secure) {
   if (expiredays) {
      var exdate=new Date();
      exdate.setDate(exdate.getDate()+expiredays);
      var expires = exdate.toGMTString();
   }
   document.cookie = name + "=" + escape(value) +
   ((expiredays) ? "; expires=" + expires : "") +
   ((path) ? "; path=" + path : "") +
   ((domain) ? "; domain=" + domain : "") +
   ((secure) ? "; secure" : "");
}

function getCookie(name) {
   var cookie = " " + document.cookie;
   var search = " " + name + "=";
   var setStr = null;
   var offset = 0;
   var end = 0;
   if (cookie.length > 0) {
      offset = cookie.indexOf(search);
      if (offset != -1) {
         offset += search.length;
         end = cookie.indexOf(";", offset)
         if (end == -1) {
            end = cookie.length;
         }
         setStr = unescape(cookie.substring(offset, end));
      }
   }
   return setStr;
}

function initchat(){
    if(getCookie('shoutbox')=='hide'){
        var dno = 101;
        var obj = document.getElementById( "fornum" + 100 );
        dno = document.getElementById( "fornum" + 101 );
    
        obj.className = "hid"; 
        dno.className = "vis";
    }else{
        obj.className = "vis"; 
        dno.className = "hid";
    }
}















































var ieStyle=true;

if (typeof document.attachEvent!='undefined') {
   window.attachEvent('onload',initNuTooltips);
} else {
	ieStyle=false;
   window.addEventListener('load',initNuTooltips,false);
}


var tipsRepository=new Array();

function initNuTooltips(){
	for(var i=0; i<document.images.length; i++){
		var im=document.images[i];
		
		if(im.id.match(/^(i|b|m)_/)){
			tipsRepository.push(new NuTooltip(im));
		}
	}
}

//check if 's' child of 'd'
function getTipDiv(s) {
	while(s) {
		if (s.id!=null && s.id.match(/^vbrp_(i|b|m)_/i))
			return s;
		s=s.parentNode;
	}
	return false;
}

function unlockAll(exceptClass){
	for(var i=0; i<tipsRepository.length; i++){
		var tClass=tipsRepository[i];
		if(tClass!=exceptClass && tClass.isLocked){
			tClass.unlock();
		}
	}
}

var NuTooltip=function(img){
	this.isLocked=false;
	this.img=img;
	this.tip=tip=document.getElementById('vbrp_'+img.id);
	
	tip.style.position='absolute';
	tip.style.visibility='visible';
	
	img.tipClass=this;
	tip.tipClass=this;
	//div.style.display='none';
	
	if(ieStyle){
		img.attachEvent('onmousemove',this.mouseMoveHandler);
		img.attachEvent('onmouseout',this.mouseOutHandler);
		img.attachEvent('onmouseover',this.mouseOverHandler);
		img.attachEvent('onclick',this.mouseClickHandler);
	}else{
		img.addEventListener('mousemove',this.mouseMoveHandler,false);
		img.addEventListener('mouseout',this.mouseOutHandler,false);
		img.addEventListener('mouseover',this.mouseOverHandler,false);
		img.addEventListener('click',this.mouseClickHandler,true);
	}
	
}

NuTooltip.prototype.mouseMoveHandler=function(e){
	if(!e) e=event;
	
	image=(e.target ? e.target : e.srcElement);
	image.tipClass.tip.style.display='block';
	
	tipClass=image.tipClass;
	
	if(tipClass.isLocked){
		return ;
	}
	
	ox=10;
	oy=10;
	lockX=10;
	lockY=10;
	// This added to alleviate bug in IE6 w.r.t DOCTYPE
	bodyScrollTop=document.documentElement&&document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop;
	bodyScrollLet=document.documentElement&&document.documentElement.scrollLeft?document.documentElement.scrollLeft:document.body.scrollLeft;
	mouseX=e.pageX?e.pageX-bodyScrollLet:e.clientX-document.body.clientLeft;
	mouseY=e.pageY?e.pageY-bodyScrollTop:e.clientY-document.body.clientTop;
	
	tipClass.tip.style.left=bodyScrollLet+mouseX+ox+lockX+"px";
	tipClass.tip.style.top=bodyScrollTop+mouseY+oy+lockY+"px";
}

NuTooltip.prototype.mouseOutHandler=function(e){
	if(!e) e=event;
	image=(e.target ? e.target : e.srcElement);
	
	tipClass=image.tipClass;
	
	if(tipClass.isLocked){
		return ;
	}
	
	tipClass.tip.style.display='none';
}

NuTooltip.prototype.mouseOverHandler=function(e){
	if(!e) e=event;
	image=(e.target ? e.target : e.srcElement);
}

NuTooltip.prototype.mouseClickHandler=function(e){
	if(!e) e=event;
	image=(e.target ? e.target : e.srcElement);
	
	tipClass=image.tipClass;
	
	if(tipClass.isLocked){
		tipClass.unlock();
	}else{
		tipClass.lock();
	}

	if(ieStyle){
		e.cancelBubble=true;
	}else{
		e.stopPropagation();
	}
	
	return false;
}

NuTooltip.prototype.unlock=function(){
	if(ieStyle){
		document.detachEvent('onclick',this.documentClickHandler);
	}else{
		document.removeEventListener('click',this.documentClickHandler,false);
	}
	
	this.isLocked=false;
	this.tip.style.display='none';
}

NuTooltip.prototype.lock=function(){
	this.isLocked=true;
	this.tip.style.display='block';
	
	if(ieStyle){
		document.attachEvent('onclick',this.documentClickHandler);
	}else{
		document.addEventListener('click',this.documentClickHandler,false);
	}
	
	unlockAll(this);
}

NuTooltip.prototype.documentClickHandler=function(e){
	if(!e) e=event;
	src= (e.target ? e.target : e.srcElement);
	
	if(getTipDiv(src))
		return ;
	
	unlockAll();
}

