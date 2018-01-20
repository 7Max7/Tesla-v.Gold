var total_stars = 10;
function appreciate_init() {

	var stelators = $('tesla_tto_rate').getElementsByTagName('div');
	for (var y=0; y<stelators.length; y++) {
		if (stelators[y].getAttribute('name') != 'stelator') continue;
		var torrent_id = stelators[y].id.substr(1);
		var imgs = stelators[y].getElementsByTagName('img');
		for (var i=0; i<imgs.length; i++) {
			var stea_nr = i+1;
			
			eval('v=function(e) {stea_mouseover(e,'+torrent_id+','+stea_nr+');}');
			_not_this_addEvent(imgs[i],'mouseover',v);
			
			eval('v=function(e) {stea_mouseout(e,'+torrent_id+','+stea_nr+');}');
			_not_this_addEvent(imgs[i],'mouseout',v);
		
			eval('v=function(e) {stea_mouseclick(e,'+torrent_id+','+stea_nr+');}');
			_not_this_addEvent(imgs[i],'click',v);

			imgs[i].id = 'stea_'+torrent_id+'_'+stea_nr; 
		}
	}
	var imgs = $('tesla_tto_rate').getElementsByTagName('img');
	for (var i=0; i<imgs.length; i++) {

		if (imgs[i].src.indexOf('close_x.gif') != -1) {

			if (imgs[i].parentNode.href.length > 1) {
				eval('v=function(e) {appreciate_skipvote_mouseclick(e,"'+imgs[i].parentNode.href+'&ajax=1");}');
				_not_this_addEvent(imgs[i],'click',v);
			}
		}
	}
}

appreciate_init();

function stea_mouseover(e,torrent_id,nr) {
	stea_mouseover_flag = true;
	for (var i=1; i<=nr; i++) {
		$('stea_'+torrent_id+'_'+i).src = './pic/ratio/star_on.gif';
	}

	$('stea_nota_'+torrent_id).innerHTML = nr;
	if (nr == total_stars) return;
	for (var i=nr+1; i<=total_stars; i++) {
		$('stea_'+torrent_id+'_'+i).src = './pic/ratio/star_off.gif';
	}
}
function stea_mouseout(e,torrent_id,i) {
	stea_mouseover_flag = false;
	setTimeout('stea_mouseout2('+torrent_id+')',1);
}

function stea_mouseout2(torrent_id) {

	if (stea_mouseover_flag == true) return;
	for (var i=1; i<=total_stars; i++) {
		if ($('stea_'+torrent_id+'_'+i)) $('stea_'+torrent_id+'_'+i).src = './pic/ratio/star_off.gif';
	}
	$('stea_nota_'+torrent_id).innerHTML = '';
}

function stea_mouseclick(e,torrent_id,nr) {
	try {
		var staron = '<img src="./pic/ratio/star_on.gif" width=20 height=20>';
		var staroff = '<img src="./pic/ratio/star_off.gif" width=20 height=20>';
		var starred = '<img src="./pic/ratio/star_red.gif" width=20 height=20>';
		var stargreen = '<img src="./pic/ratio/star_green.gif" width=20 height=20>';
		var stars = '';
		for (var i=1; i<=nr; i++) {
			if (i == nr)  {
				if (nr > 6) stars = stars + stargreen;
				else stars = stars + starred;
				continue;
			}
			stars = stars + staron;
			
		}
		if (nr != total_stars) {
			for (var i=nr+1; i<=total_stars; i++) {
				stars = stars + staroff;
			}
		}
		var cur_img = get_object_from_event(e);

		var stelator = cur_img.parentNode.parentNode;
		stelator.innerHTML = stars;
		$('stea_nota_'+torrent_id).innerHTML = nr;
		post_data('takerate.php','torrentid='+torrent_id+'&rate='+nr+'&ajax=1',function(){});

		var td = stelator.parentNode;
		td.previousSibling.previousSibling.innerHTML='+'; 
	} catch(e) {
		return;
	}
	_stop_e_Propagation(e);
}

function appreciate_skipvote_mouseclick(e, link) {
	try {
		var fathertr = get_object_from_event(e).parentNode.parentNode.parentNode;
		fathertr.className = 'hideit';
		var link_data = link.match('(.+)\\?(.+)'); //[0] - entire url [1] - url only [2] - data only
		post_data(link_data[1], link_data[2], function(){} );
	} catch(e) {
		return
	}
	_stop_e_Propagation(e);
}
function _removeNode(node) {
	if (node == undefined) return false;
	return node.parentNode.removeChild(node);
}