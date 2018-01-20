function setPointer(theRow, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor)
{
    var theCells = null;
    if ((thePointerColor == '' && theMarkColor == '')
        || typeof(theRow.style) == 'undefined') {
        return false;
    }
    if (typeof(document.getElementsByTagName) != 'undefined') {
        theCells = theRow.getElementsByTagName('td');
    }
    else if (typeof(theRow.cells) != 'undefined') {
        theCells = theRow.cells;
    }
    else {
        return false;
    }
    var rowCellsCnt  = theCells.length;
    var domDetect    = null;
    var currentColor = null;
    var newColor     = null;
    if (typeof(window.opera) == 'undefined'
        && typeof(theCells[0].getAttribute) != 'undefined') {
        currentColor = theCells[0].getAttribute('bgcolor');
        domDetect    = true;
    }
    else {
        currentColor = theCells[0].style.backgroundColor;
        domDetect    = false;
    } 
    if (currentColor == ''
        || currentColor.toLowerCase() == theDefaultColor.toLowerCase()) {
        if (theAction == 'over' && thePointerColor != '') {
            newColor              = thePointerColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
        }
    }

    else if (currentColor.toLowerCase() == thePointerColor.toLowerCase()
             && (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {
        if (theAction == 'out') {
            newColor              = theDefaultColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
        }
    }

    else if (currentColor.toLowerCase() == theMarkColor.toLowerCase()) {
        if (theAction == 'click') {
            newColor              = (thePointerColor != '')
                                  ? thePointerColor
                                  : theDefaultColor;
            marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])
                                  ? true : null;
        }
    } 
    if (newColor) {
        var c = null;
        if (domDetect) {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].setAttribute('bgcolor', newColor, 0);
            } 
        }  else {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].style.backgroundColor = newColor;
            }
        }
    } 
    return true;
} 
function imgFit (img, maxImgWidth) 
{ 
   if (typeof img.naturalWidth == 'undefined') { 
      img.naturalHeight = img.height; 
      img.naturalWidth = img.width; 
   } 
   if (img.width > maxImgWidth) { 
      img.height = Math.round(((maxImgWidth)/img.width)*img.height); 
      img.width = maxImgWidth; 
      img.title = 'Нажмите на картинку для увеличения'; 
      img.style.cursor = 'move'; 
   } else if (img.width == maxImgWidth && img.width < img.naturalWidth) { 
      img.height = img.naturalHeight; 
      img.width = img.naturalWidth; 
      img.title = 'Нажмите на картинку для помещения в размер окна'; 
   } 
}

var tid = 0, x = 0, y = 0;
var obj;

document.onmousemove=track;

function track(e)
{
    x = (document.all) ? window.event.x + document.body.scrollLeft : e.pageX;
    y = (document.all) ? window.event.y + document.body.scrollTop : e.pageY;
}

function show(id)
{
    obj = document.getElementById(id);
    obj.style.left = x - 120;
    obj.style.top = y + 25;
    obj.style.display = "block";
    tid = window.setTimeout("show("+id+")",10);
}

function hide(id)
{
    obj = document.getElementById(id);
    window.clearTimeout(tid);
    obj.style.display = "none";
}
function show_hide(id)
{
        var klappText = document.getElementById('s' + id);
        var klappBild = document.getElementById('pic' + id);

        if (klappText.style.display == 'none') {
                  klappText.style.display = 'block';
                  klappBild.src = 'pic/minus.gif';
                  klappBild.title = 'Скрыть';
        } else {
                  klappText.style.display = 'none';
                  klappBild.src = 'pic/plus.gif';
                  klappBild.title = 'Показать';
        }
}

$(document).ready(function() {
	$('div.spoiler-body img').each(function() {
		$(this).attr({
			alt: $(this).attr('src'),
			src: 'pic/loading_by_strong.gif'
		});
	});
	$('div.spoiler-head').live("click", function() {
		$(this).toggleClass('unfolded');
		var c = $(this).parent().children('div.spoiler-body');
		c.find('img').each(function() {
			$(this).attr('src', $(this).attr('alt'));
		});
		c.is(':visible') ? c.hide('fast') : c.show('fast');
	});
});


function klappe(id)
{
var klappText = document.getElementById('k' + id);
var klappBild = document.getElementById('pic' + id);

if (klappText.style.display == 'none') {
 klappText.style.display = 'block'; 
}
else {
 klappText.style.display = 'none'; 
}
}

function klappe_news(id)
{
var klappText = document.getElementById('k' + id);
var klappBild = document.getElementById('pic' + id);

if (klappText.style.display == 'none') {
 klappText.style.display = 'block';
 klappBild.src = dimagedir + 'minus.gif';
}
else {
 klappText.style.display = 'none';
 klappBild.src = dimagedir + 'plus.gif';
}
}


function changeText(text, id){
document.getElementById(id).value = text;
}


var azWin = '     Ё               ё       АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдежзийклмнопрстуфхцчшщъыьэюя'
var azKoi = 'ё                Ё           юабцдефгхийклмнопярстужвьызшэщчъЮАБЦДЕФГХИЙКЛМНОПЯРСТУЖВЬЫЗШЭЩЧЪ'
var AZ=azWin
var azURL = '0123456789ABCDEF'
var b64s  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'
var b64a  = b64s.split('')
function enBASE64(str) {
  var a=Array(), i
  for( i=0; i<str.length; i++ ){
    var cch=str.charCodeAt(i)
    if( cch>127 ){  cch=AZ.indexOf(str.charAt(i))+163; if(cch<163) continue; }
    a.push(cch)
  };
  var s=Array(), lPos = a.length - a.length % 3
  for(i=0;i<lPos;i+=3){
    var t=(a[i]<<16)+(a[i+1]<<8)+a[i+2]
    s.push( b64a[(t>>18)&0x3f]+b64a[(t>>12)&0x3f]+b64a[(t>>6)&0x3f]+b64a[t&0x3f] )
  }
  switch ( a.length-lPos ) {
    case 1 : var t=a[lPos]<<4; s.push(b64a[(t>>6)&0x3f]+b64a[t&0x3f]+'=='); break
    case 2 : var t=(a[lPos]<<10)+(a[lPos+1]<<2); s.push(b64a[(t>>12)&0x3f]+b64a[(t>>6)&0x3f]+b64a[t&0x3f]+'='); break
  }
  return s.join('')
}
function deBASE64(str) {
  while(str.substr(-1,1)=='=')str=str.substr(0,str.length-1);
  var b=str.split(''), i
  var s=Array(), t
  var lPos = b.length - b.length % 4
  for(i=0;i<lPos;i+=4){
    t=(b64s.indexOf(b[i])<<18)+(b64s.indexOf(b[i+1])<<12)+(b64s.indexOf(b[i+2])<<6)+b64s.indexOf(b[i+3])
    s.push( ((t>>16)&0xff), ((t>>8)&0xff), (t&0xff) )
  }
  if( (b.length-lPos) == 2 ){ t=(b64s.indexOf(b[lPos])<<18)+(b64s.indexOf(b[lPos+1])<<12); s.push( ((t>>16)&0xff)); }
  if( (b.length-lPos) == 3 ){ t=(b64s.indexOf(b[lPos])<<18)+(b64s.indexOf(b[lPos+1])<<12)+(b64s.indexOf(b[lPos+2])<<6); s.push( ((t>>16)&0xff), ((t>>8)&0xff) ); }
  for( i=s.length-1; i>=0; i-- ){
    if( s[i]>=168 ) s[i]=AZ.charAt(s[i]-163)
    else s[i]=String.fromCharCode(s[i])
  };
  return s.join('')
}

function placeholderSetup(id) {
	var el = ge(id);
	if(!el) return;
	if(el.type != 'text') return;
	if(el.type != 'text') return;

	var ph = el.getAttribute("placeholder");
	if( ph && ph != "" ) {
		el.value = ph;
		el.style.color = '#777';
		el.is_focused = 0;
		el.onfocus = placeholderFocus;
		el.onblur = placeholderBlur;
	}
}

function placeholderFocus() {
  if(!this.is_focused) {
    this.is_focused = 1;
    this.value = '';
    this.style.color = '#000';

    var rs = this.getAttribute("radioselect");
    if( rs && rs != "" ) {
      var re = document.getElementById(rs);
      if(!re) { return; }
      if(re.type != 'radio') return;

      re.checked=true;
    }
  }
}

function placeholderBlur() {
  var ph = this.getAttribute("placeholder")
  if( this.is_focused && ph && this.value == "" ) {
		this.is_focused = 0;
    this.value = ph;
    this.style.color = '#777';
  }
}

var dhtmlgoodies_slideSpeed = 10;	// Higher value = faster
var dhtmlgoodies_timer = 10;	// Lower value = faster

var objectIdToSlideDown = false;
var dhtmlgoodies_activeId = false;
var dhtmlgoodies_slideInProgress = false;
function showHideContent(e,inputId)
{
	if(dhtmlgoodies_slideInProgress)return;
	dhtmlgoodies_slideInProgress = true;
	if(!inputId)inputId = this.id;
	inputId = inputId + '';
	var numericId = inputId.replace(/[^0-9]/g,'');
	var answerDiv = document.getElementById('dhtmlgoodies_a' + numericId);

	objectIdToSlideDown = false;
	
	if(!answerDiv.style.display || answerDiv.style.display=='none'){		
		if(dhtmlgoodies_activeId &&  dhtmlgoodies_activeId!=numericId){			
			objectIdToSlideDown = numericId;
			slideContent(dhtmlgoodies_activeId,(dhtmlgoodies_slideSpeed*-1));
		}else{
			
			answerDiv.style.display='block';
			answerDiv.style.visibility = 'visible';
			
			slideContent(numericId,dhtmlgoodies_slideSpeed);
		}
	}else{
		slideContent(numericId,(dhtmlgoodies_slideSpeed*-1));
		dhtmlgoodies_activeId = false;
	}	
}

function slideContent(inputId,direction)
{
	
	var obj =document.getElementById('dhtmlgoodies_a' + inputId);
	var contentObj = document.getElementById('dhtmlgoodies_ac' + inputId);
	height = obj.clientHeight;
	if(height==0)height = obj.offsetHeight;
	height = height + direction;
	rerunFunction = true;
	if(height>contentObj.offsetHeight){
		height = contentObj.offsetHeight;
		rerunFunction = false;
	}
	if(height<=1){		height = 1;		rerunFunction = false;	}

	obj.style.height = height + 'px';
	var topPos = height - contentObj.offsetHeight;
	if(topPos>0)topPos=0;
	contentObj.style.top = topPos + 'px';
	if(rerunFunction){
		setTimeout('slideContent(' + inputId + ',' + direction + ')',dhtmlgoodies_timer);
	}else{
		if(height<=1){
			obj.style.display='none'; 
			if(objectIdToSlideDown && objectIdToSlideDown!=inputId){
				document.getElementById('dhtmlgoodies_a' + objectIdToSlideDown).style.display='block';
				document.getElementById('dhtmlgoodies_a' + objectIdToSlideDown).style.visibility='visible';
				slideContent(objectIdToSlideDown,dhtmlgoodies_slideSpeed);				
			}else{
				dhtmlgoodies_slideInProgress = false;
			}
		}else{
			dhtmlgoodies_activeId = inputId;
			dhtmlgoodies_slideInProgress = false;
		}
	}
}



function initShowHideDivs()
{
	var divs = document.getElementsByTagName('DIV');
	var divCounter = 1;
	for(var no=0;no<divs.length;no++){
		if(divs[no].className=='dhtmlgoodies_question'){
			divs[no].onclick = showHideContent;
			divs[no].id = 'dhtmlgoodies_q'+divCounter;
			var answer = divs[no].nextSibling;
			while(answer && answer.tagName!='DIV'){
				answer = answer.nextSibling;
			}
			answer.id = 'dhtmlgoodies_a'+divCounter;	
			contentDiv = answer.getElementsByTagName('DIV')[0];
			contentDiv.style.top = 0 - contentDiv.offsetHeight + 'px'; 	
			contentDiv.className='dhtmlgoodies_answer_content';
			contentDiv.id = 'dhtmlgoodies_ac' + divCounter;
			answer.style.display='none';
			answer.style.height='1px';
			divCounter++;
		}		
	}	
}
window.onload = initShowHideDivs;



var Paginator = function(paginatorHolderId, pagesTotal, pagesSpan, pageCurrent, baseUrl){
	if(!document.getElementById(paginatorHolderId) || !pagesTotal || !pagesSpan) return false;

	this.inputData = {
		paginatorHolderId: paginatorHolderId,
		pagesTotal: pagesTotal,
		pagesSpan: pagesSpan < pagesTotal ? pagesSpan : pagesTotal,
		pageCurrent: pageCurrent,
		baseUrl: baseUrl ? baseUrl : '/pages/'
	};

	this.html = {
		holder: null,

		table: null,
		trPages: null, 
		trScrollBar: null,
		tdsPages: null,

		scrollBar: null,
		scrollThumb: null,
			
		pageCurrentMark: null
	};


	this.prepareHtml();

	this.initScrollThumb();
	this.initPageCurrentMark();
	this.initEvents();

	this.scrollToPageCurrent();
} 
Paginator.prototype.prepareHtml = function(){

	this.html.holder = document.getElementById(this.inputData.paginatorHolderId);
	this.html.holder.innerHTML = this.makePagesTableHtml();

	this.html.table = this.html.holder.getElementsByTagName('table')[0];

	var trPages = this.html.table.getElementsByTagName('tr')[0]; 
	this.html.tdsPages = trPages.getElementsByTagName('td');

	this.html.scrollBar = getElementsByClassName(this.html.table, 'div', 'scroll_bar')[0];
	this.html.scrollThumb = getElementsByClassName(this.html.table, 'div', 'scroll_thumb')[0];
	this.html.pageCurrentMark = getElementsByClassName(this.html.table, 'div', 'current_page_mark')[0];
	if(this.inputData.pagesSpan == this.inputData.pagesTotal){
		addClass(this.html.holder, 'fullsize');
	}
}

Paginator.prototype.makePagesTableHtml = function(){
	var tdWidth = (100 / this.inputData.pagesSpan) + '%';

	var html = '' +
	'<table width="100%">' +
		'<tr>' 
			for (var i=1; i<=this.inputData.pagesSpan; i++){
				html += '<td width="' + tdWidth + '"></td>';
			}
			html += '' + 
		'</tr>' +
		'<tr>' +
			'<td colspan="' + this.inputData.pagesSpan + '">' +
				'<div class="scroll_bar">' + 
					'<div title="Перейти к этому участку страниц" class="scroll_trough"></div>' + 
					'<div class="scroll_thumb">' + 
						'<div title="Нажмите и удерживайте этот указатель" class="scroll_knob"></div>' + 
					'</div>' + 
					'<div class="current_page_mark"></div>' + 
				'</div>' +
			'</td>' +
		'</tr>' +
	'</table>';

	return html;
}
Paginator.prototype.initScrollThumb = function(){
	this.html.scrollThumb.widthMin = '8'; // minimum width of the scrollThumb (px)
	this.html.scrollThumb.widthPercent = this.inputData.pagesSpan/this.inputData.pagesTotal * 100;

	this.html.scrollThumb.xPosPageCurrent = (this.inputData.pageCurrent - Math.round(this.inputData.pagesSpan/2))/this.inputData.pagesTotal * this.html.table.offsetWidth;
	this.html.scrollThumb.xPos = this.html.scrollThumb.xPosPageCurrent;

	this.html.scrollThumb.xPosMin = 0;
	this.html.scrollThumb.xPosMax;

	this.html.scrollThumb.widthActual;

	this.setScrollThumbWidth();
	
}

Paginator.prototype.setScrollThumbWidth = function(){
	this.html.scrollThumb.style.width = this.html.scrollThumb.widthPercent + "%";
	this.html.scrollThumb.widthActual = this.html.scrollThumb.offsetWidth;
	if(this.html.scrollThumb.widthActual < this.html.scrollThumb.widthMin){
		this.html.scrollThumb.style.width = this.html.scrollThumb.widthMin + 'px';
	}
	this.html.scrollThumb.xPosMax = this.html.table.offsetWidth - this.html.scrollThumb.widthActual;
}

Paginator.prototype.moveScrollThumb = function(){
	this.html.scrollThumb.style.left = this.html.scrollThumb.xPos + "px";
}
Paginator.prototype.initPageCurrentMark = function(){
	this.html.pageCurrentMark.widthMin = '3';
	this.html.pageCurrentMark.widthPercent = 100 / this.inputData.pagesTotal;
	this.html.pageCurrentMark.widthActual;

	this.setPageCurrentPointWidth();
	this.movePageCurrentPoint();
}

Paginator.prototype.setPageCurrentPointWidth = function(){
	this.html.pageCurrentMark.style.width = this.html.pageCurrentMark.widthPercent + '%';
	this.html.pageCurrentMark.widthActual = this.html.pageCurrentMark.offsetWidth;
	if(this.html.pageCurrentMark.widthActual < this.html.pageCurrentMark.widthMin){
		this.html.pageCurrentMark.style.width = this.html.pageCurrentMark.widthMin + 'px';
	}
}

Paginator.prototype.movePageCurrentPoint = function(){
	if(this.html.pageCurrentMark.widthActual < this.html.pageCurrentMark.offsetWidth){
		this.html.pageCurrentMark.style.left = (this.inputData.pageCurrent - 1)/this.inputData.pagesTotal * this.html.table.offsetWidth - this.html.pageCurrentMark.offsetWidth/2 + "px";
	} else {
		this.html.pageCurrentMark.style.left = (this.inputData.pageCurrent - 1)/this.inputData.pagesTotal * this.html.table.offsetWidth + "px";
	}
}

Paginator.prototype.initEvents = function(){
	var _this = this;

	this.html.scrollThumb.onmousedown = function(e){
		if (!e) var e = window.event;
		e.cancelBubble = true;
		if (e.stopPropagation) e.stopPropagation();

		var dx = getMousePosition(e).x - this.xPos;
		document.onmousemove = function(e){
			if (!e) var e = window.event;
			_this.html.scrollThumb.xPos = getMousePosition(e).x - dx;
			_this.moveScrollThumb();
			_this.drawPages();
			
			
		}
		document.onmouseup = function(){
			document.onmousemove = null;
			_this.enableSelection();
		}
		_this.disableSelection();
	}

	this.html.scrollBar.onmousedown = function(e){
		if (!e) var e = window.event;
		if(matchClass(_this.paginatorBox, 'fullsize')) return;
		
		_this.html.scrollThumb.xPos = getMousePosition(e).x - getPageX(_this.html.scrollBar) - _this.html.scrollThumb.offsetWidth/2;
		
		_this.moveScrollThumb();
		_this.drawPages();
		
		
	}

	addEvent(window, 'resize', function(){Paginator.resizePaginator(_this)});
}

Paginator.prototype.drawPages = function(){
	var percentFromLeft = this.html.scrollThumb.xPos/(this.html.table.offsetWidth);
	var cellFirstValue = Math.round(percentFromLeft * this.inputData.pagesTotal);
	
	var html = "";
	if(cellFirstValue < 1){
		cellFirstValue = 1;
		this.html.scrollThumb.xPos = 0;
		this.moveScrollThumb();
	} else if(cellFirstValue >= this.inputData.pagesTotal - this.inputData.pagesSpan) {
		cellFirstValue = this.inputData.pagesTotal - this.inputData.pagesSpan + 1;
		this.html.scrollThumb.xPos = this.html.table.offsetWidth - this.html.scrollThumb.offsetWidth;
		this.moveScrollThumb();
	}

	

	for(var i=0; i<this.html.tdsPages.length; i++){
		var cellCurrentValue = cellFirstValue + i;
		if(cellCurrentValue == this.inputData.pageCurrent){
			html = "<span title='Вы на данной страничке'>" + "<strong>" + cellCurrentValue + "</strong>" + "</span>";
		} else {
			html = "<span title='Перейти к этой страничке'>" + "<a href='" + this.inputData.baseUrl + cellCurrentValue + "'>" + cellCurrentValue + "</a>" + "</span>";
		}
		this.html.tdsPages[i].innerHTML = html;
	}
}

Paginator.prototype.scrollToPageCurrent = function(){
	this.html.scrollThumb.xPosPageCurrent = (this.inputData.pageCurrent - Math.round(this.inputData.pagesSpan/2))/this.inputData.pagesTotal * this.html.table.offsetWidth;
	this.html.scrollThumb.xPos = this.html.scrollThumb.xPosPageCurrent;
		this.moveScrollThumb();
	this.drawPages();
	}



Paginator.prototype.disableSelection = function(){
	document.onselectstart = function(){
		return false;
	}
	this.html.scrollThumb.focus();	
}

Paginator.prototype.enableSelection = function(){
	document.onselectstart = function(){
		return true;
	}
}

Paginator.resizePaginator = function (paginatorObj){

	paginatorObj.setPageCurrentPointWidth();
	paginatorObj.movePageCurrentPoint();

	paginatorObj.setScrollThumbWidth();
	paginatorObj.scrollToPageCurrent();
}

function getElementsByClassName(objParentNode, strNodeName, strClassName){
	var nodes = objParentNode.getElementsByTagName(strNodeName);
	if(!strClassName){
		return nodes;	
	}
	var nodesWithClassName = [];
	for(var i=0; i<nodes.length; i++){
		if(matchClass( nodes[i], strClassName )){
			nodesWithClassName[nodesWithClassName.length] = nodes[i];
		}	
	}
	return nodesWithClassName;
}


function addClass( objNode, strNewClass ) {
	replaceClass( objNode, strNewClass, '' );
}

function removeClass( objNode, strCurrClass ) {
	replaceClass( objNode, '', strCurrClass );
}

function replaceClass( objNode, strNewClass, strCurrClass ) {
	var strOldClass = strNewClass;
	if ( strCurrClass && strCurrClass.length ){
		strCurrClass = strCurrClass.replace( /\s+(\S)/g, '|$1' );
		if ( strOldClass.length ) strOldClass += '|';
		strOldClass += strCurrClass;
	}
	objNode.className = objNode.className.replace( new RegExp('(^|\\s+)(' + strOldClass + ')($|\\s+)', 'g'), '$1' );
	objNode.className += ( (objNode.className.length)? ' ' : '' ) + strNewClass;
}

function matchClass( objNode, strCurrClass ) {
	return ( objNode && objNode.className.length && objNode.className.match( new RegExp('(^|\\s+)(' + strCurrClass + ')($|\\s+)') ) );
}


function addEvent(objElement, strEventType, ptrEventFunc) {
	if (objElement.addEventListener)
		objElement.addEventListener(strEventType, ptrEventFunc, false);
	else if (objElement.attachEvent)
		objElement.attachEvent('on' + strEventType, ptrEventFunc);
}
function removeEvent(objElement, strEventType, ptrEventFunc) {
	if (objElement.removeEventListener) objElement.removeEventListener(strEventType, ptrEventFunc, false);
		else if (objElement.detachEvent) objElement.detachEvent('on' + strEventType, ptrEventFunc);
}


function getPageY( oElement ) {
	var iPosY = oElement.offsetTop;
	while ( oElement.offsetParent != null ) {
		oElement = oElement.offsetParent;
		iPosY += oElement.offsetTop;
		if (oElement.tagName == 'BODY') break;
	}
	return iPosY;
}

function getPageX( oElement ) {
	var iPosX = oElement.offsetLeft;
	while ( oElement.offsetParent != null ) {
		oElement = oElement.offsetParent;
		iPosX += oElement.offsetLeft;
		if (oElement.tagName == 'BODY') break;
	}
	return iPosX;
}

function getMousePosition(e) {
	if (e.pageX || e.pageY){
		var posX = e.pageX;
		var posY = e.pageY;
	}else if (e.clientX || e.clientY) 	{
		var posX = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
		var posY = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
	}
	return {x:posX, y:posY}	
}
var scrolltotop={
	setting: {startline:600, scrollduration:500, fadeduration:[500, 100]},
	controlHTML: '<img src="./pic/up.png" style="width:35px; height:35px" />',
	controlattrs: {offsetx:15, offsety:50},
	anchorkeyword: '#atop',
	state: {isvisible:false, shouldvisible:false},
	scrollup:function(){
		if (!this.cssfixedsupport)
			this.$control.css({opacity:0})
		this.$body.animate({scrollTop: 0}, this.setting.scrollduration);
	},
	keepfixed:function(){
		var $window=jQuery(window)
		var controlx=$window.scrollLeft() + $window.width() - this.$control.width() - this.controlattrs.offsetx
		var controly=$window.scrollTop() + $window.height() - this.$control.height() - this.controlattrs.offsety
		this.$control.css({left:controlx+'px', top:controly+'px'})
	},
	togglecontrol:function(){
		var scrolltop=jQuery(window).scrollTop()
		if (!this.cssfixedsupport) this.keepfixed()
		this.state.shouldvisible=(scrolltop>=this.setting.startline)? true : false
		if (this.state.shouldvisible && !this.state.isvisible){
			this.$control.stop().animate({opacity:1}, this.setting.fadeduration[0])
			this.state.isvisible=true
		}
		else if (this.state.shouldvisible==false && this.state.isvisible){
			this.$control.stop().animate({opacity:0}, this.setting.fadeduration[1])
			this.state.isvisible=false
		}
	},
	init:function(){
		jQuery(document).ready(function($){
			var mainobj=scrolltotop
			var iebrws=document.all
			mainobj.cssfixedsupport=!iebrws || iebrws && document.compatMode=="CSS1Compat" && window.XMLHttpRequest
			mainobj.$body=$('html,body')
			mainobj.$control=$('<div id="topcontrol">'+mainobj.controlHTML+'</div>')
				.css({position:mainobj.cssfixedsupport? 'fixed' : 'absolute', bottom:mainobj.controlattrs.offsety, right:mainobj.controlattrs.offsetx, opacity:0, cursor:'pointer'})
			///	.attr({title:'Наверх странички!'})
				.click(function(){mainobj.scrollup(); return false})
				.appendTo('body')
			if (document.all && !window.XMLHttpRequest && mainobj.$control.text()!='')
				mainobj.$control.css({width:mainobj.$control.width()})
			mainobj.togglecontrol()
			$('a[href="' + mainobj.anchorkeyword +'"]').click(function(){
				mainobj.scrollup()
				return false
			})
			$(window).bind('scroll resize', function(e){
				mainobj.togglecontrol()
			})
		})
	}
}
scrolltotop.init()