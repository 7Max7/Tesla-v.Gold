this.tooltip = function(){
   xOffset = 6;
   yOffset = 16;
   jQuery("[title],[alt]").hover(function(e){
   if(this.title !=''){
      this.t = this.title;
      this.title = "";
   } else {
      this.t = this.alt;
      this.alt = "";
 }
 jQuery("body").append("<p id='tooltip'>"+ this.t +"</p>");
      jQuery("#tooltip")
         .css("top",(e.pageY - xOffset) + "px")
         .css("left",(e.pageX + yOffset) + "px")
         .fadeIn("fast")
         .show();
   },
   function(){
      this.title = this.t;
      this.alt = this.t;
      jQuery("#tooltip").remove();
   });
   jQuery("[title],[alt]").mousemove(function(e){
      jQuery("#tooltip")
         .css("top",(e.pageY - xOffset) + "px")
         .css("left",(e.pageX + yOffset) + "px");
   });
};

jQuery(document).ready(function(){
   tooltip();
});


(function() {
jQuery.keyboardLayout = {};
jQuery.keyboardLayout.indicator = $('<span class="keyboardLayout" />');
jQuery.keyboardLayout.target;
jQuery.keyboardLayout.layout;
jQuery.keyboardLayout.show = function(layout){
this.layout = layout;
this.indicator.text(layout);
this.target.after(this.indicator);
};
jQuery.keyboardLayout.hide = function(){
this.target = null;
this.layout = null;
this.indicator.remove();
};

jQuery.fn.keyboardLayout = function()  {
this.each(function(){

$(this).focus(function(){
jQuery.keyboardLayout.target = $(this);
});

$(this).blur(function(){
jQuery.keyboardLayout.hide();
});

$(this).keypress(function(e){
var c = (e.charCode == undefined ? e.keyCode : e.charCode);
var layout = jQuery.keyboardLayout.layout;

if (c >= 97/*a*/  && c <= 122/*z*/ && !e.shiftKey || c >= 65/*A*/  && c <= 90/*Z*/  &&  e.shiftKey || (c == 91/*[*/  && !e.shiftKey || c == 93/*]*/  && !e.shiftKey || c == 123/*{*/ &&  e.shiftKey || c == 125/*}*/ &&  e.shiftKey || c == 96/*`*/  && !e.shiftKey || c == 126/*~*/ &&  e.shiftKey || c == 64/*@*/  &&  e.shiftKey || c == 35/*#*/  &&  e.shiftKey || c == 36/*$*/  &&  e.shiftKey || c == 94/*^*/ && e.shiftKey || c == 38/*&*/  &&  e.shiftKey || c == 59/*;*/  && !e.shiftKey || c == 39/*'*/ && !e.shiftKey || c == 44/*,*/  && !e.shiftKey || c == 60/*<*/  &&  e.shiftKey || c == 62/*>*/  &&  e.shiftKey) && layout != 'EN') {
layout = 'en'; //Tesla TT
} else if (c >= 65/*A*/ && c <= 90/*Z*/  && !e.shiftKey || c >= 97/*a*/ && c <= 122/*z*/ &&  e.shiftKey) {
layout = 'EN';
} else if (c >= 1072/*¦-*/ && c <= 1103/*TÏ*/ && !e.shiftKey || c >= 1040/*¦Ð*/ && c <= 1071/*¦ï*/ &&  e.shiftKey ||
(c == 1105/*TÑ*/ && !e.shiftKey || c == 1025/*¦Á*/ &&  e.shiftKey || /*Tesla TT*/ c == 8470/*òÄÖ*/ &&  e.shiftKey || c == 59/*;*/  &&  e.shiftKey || c == 44/*,*/   &&  e.shiftKey) && layout != 'RU') {
layout = 'ru';
} else if (c >= 1040/*¦Ð*/ && c <= 1071/*¦ï*/ && !e.shiftKey || c >= 1072/*¦-*/ && c <= 1103/*TÏ*/ &&  e.shiftKey) {
layout = 'RU';
}
 if (layout) {
jQuery.keyboardLayout.show(layout);
}
});});};})();


$(function(){
$(':text').keyboardLayout();
$(':password').keyboardLayout();
});


$(document).ready(function() {
$.get("md5.php",function(hash) {
$("#o_O").append('<input type="hidden" name="tica" value="'+hash+'" />');
});
});