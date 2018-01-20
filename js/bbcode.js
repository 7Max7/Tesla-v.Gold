// BBCode control. (based on bbcode.js from http://forum.dklab.ru)
function BBCode(textarea) { this.construct(textarea) }
BBCode.prototype = {
	VK_TAB:		 9,
	VK_ENTER:	 13,
	VK_PAGE_UP: 33,
	BRK_OP:		 '[',
  BRK_CL:     ']',
  textarea:   null,
  stext:      '',
  quoter:     null,
  collapseAfterInsert: false,
  replaceOnInsert: false,

  // Create new BBCode control.
  construct: function(textarea) {
    this.textarea = textarea
    this.tags     = new Object();
    // Tag for quoting.
    this.addTag(
      '_quoter',
      function() { return '[quote="'+th.quoter+'"]' },
      '[/quote]\n',
      null,
      null,
      function() { th.collapseAfterInsert=true; return th._prepareMultiline(th.quoterText) }
    );

    // Init events.
    var th = this;
    addEvent(textarea, 'keydown',   function(e) { return th.onKeyPress(e, window.HTMLElement? 'down' : 'press') });
    addEvent(textarea, 'keypress',  function(e) { return th.onKeyPress(e, 'press') });
  },

  // Insert poster name or poster quotes to the text.
  onclickPoster: function(name) {
    var sel = this.getSelection()[0];
		if (sel) {
			this.quoter = name;
			this.quoterText = sel;
			this.insertTag('_quoter');
		} else {
			this.insertAtCursor("[b]" + name + '[/b]\n');
		}
		return false;
	},

	// Quote selected text
	onclickQuoteSel: function() {
		var sel = this.getSelection()[0];
		if (sel) {
			this.insertAtCursor('[quote]' + sel + '[/quote]\n');
		}
		else {
			alert('Пожалуйста выделите текст для цитирования');
		}
		return false;
	},

	// Quote selected text
	emoticon: function(em) {
		if (em) {
			this.insertAtCursor(' ' + em + ' ');
		}
		else {
			return false;
		}
		return false;
	},

	refreshSelection: function(get) {
    if (get) this.stext = this.getSelection()[0];
    else this.stext = '';
  },

  getSelection: function() {
    var w = window;
    var text='', range;
    if (w.getSelection) {
      text = w.getSelection();
    } else if (w.document.getSelection) {
      text = w.document.getSelection();
    } else if (w.document.selection && w.document.selection.createRange) {
      range = w.document.selection.createRange();
      text = range.text;
    } else {
      return [null, null];
    }
    if (text == '') text = this.stext;
    text = ""+text;
    text = text.replace("/^\s+|\s+$/g", "");
    return [text, range];
  },

  insertAtCursor: function(text) {
    var t = this.textarea;
    t.focus();
    if (document.selection && document.selection.createRange) {
      var r = document.selection.createRange();
      if (!this.replaceOnInsert) r.collapse();
      r.text = text;
    } else if (t.setSelectionRange) {
      var start = this.replaceOnInsert? t.selectionStart : t.selectionEnd;
      var end   = t.selectionEnd;
      var sel1  = t.value.substr(0, start);
      var sel2  = t.value.substr(end);
      t.value   = sel1 + text + sel2;
      t.setSelectionRange(start+text.length, start+text.length);
    } else{
      t.value += text;
    }

    setTimeout(function() { t.focus() }, 100);
  },

  surround: function(open, close, fTrans) {
    var t = this.textarea;
    t.focus();
    if (!fTrans) fTrans = function(t) { return t; };

    var rt    = this.getSelection();
    var text  = rt[0];
    var range = rt[1];
    if (text == null) return false;

    var notEmpty = text != null && text != '';

    // Surround.
    if (range) {
      var notEmpty = text != null && text != '';
      var newText = open + fTrans(text) + (close? close : '');
      range.text = newText;
      range.collapse();
      if (text != '') {
        // Correction for stupid IE: \r for moveStart is 0 character.
        var delta = 0;
        for (var i=0; i<newText.length; i++) if (newText.charAt(i)=='\r') delta++;
        range.moveStart("character", -close.length-text.length-open.length+delta);
        range.moveEnd("character", -0);
      } else {
        range.moveEnd("character", -close.length);
      }
      if (!this.collapseAfterInsert) range.select();
    } else if (t.setSelectionRange) {
      var start = t.selectionStart;
      var end   = t.selectionEnd;
      var top   = t.scrollTop;
      var sel1  = t.value.substr(0, start);
      var sel2  = t.value.substr(end);
      var sel   = fTrans(t.value.substr(start, end-start));
      var inner = open + sel + close;
      t.value   = sel1 + inner + sel2;
      if (sel != '') {
        t.setSelectionRange(start, start+inner.length);
        notEmpty = true;
      } else {
        t.setSelectionRange(start+open.length, start+open.length);
        notEmpty = false;
      }
      t.scrollTop = top;
      if (this.collapseAfterInsert) t.setSelectionRange(start+inner.length, start+inner.length);
    } else {
      t.value += open + text + close;
    }
    this.collapseAfterInsert = false;
    return notEmpty;
  },

  // Internal function for cross-browser event cancellation.
  _cancelEvent: function(e) {
    if (e.preventDefault) e.preventDefault();
    if (e.stopPropagation) e.stopPropagation();
    return e.returnValue = false;
  },

  // Available key combinations and these interpretaions for phpBB are
  //     TAB              - Insert TAB char
  //     CTRL-TAB         - Next form field (usual TAB)
  //     SHIFT-ALT-PAGEUP - Add an Attachment
  //     ALT-ENTER        - Preview
  //     CTRL-ENTER       - Submit
  // The values of virtual codes of keys passed through event.keyCode are
  // Rumata, http://forum.dklab.ru/about/todo/BistrieKlavishiDlyaOtpravkiForm.html
  onKeyPress: function(e, type) {
    // Try to match all the hot keys.
    var key = String.fromCharCode(e.keyCode? e.keyCode : e.charCode);
    for (var id in this.tags) {
      var tag = this.tags[id];
      if (tag.ctrlKey && !e[tag.ctrlKey+"Key"]) continue;
      if (!tag.key || key.toUpperCase() != tag.key.toUpperCase()) continue;
      if (e.type == "keydown") this.insertTag(id);
      return this._cancelEvent(e);
    }

    // Tab.
    if (type == 'press' && e.keyCode == this.VK_TAB && !e.shiftKey && !e.ctrlKey && !e.altKey) {
    //this.surround("\t", "");
      this.insertAtCursor('[tab]');
      return this._cancelEvent(e);
    }

    // Ctrl+Tab.
    if (e.keyCode == this.VK_TAB && !e.shiftKey && e.ctrlKey && !e.altKey) {
      this.textarea.form.post.focus();
      return this._cancelEvent(e);
    }

    var form = this.textarea.form;
    var submitter = null;
    if (e.keyCode == this.VK_PAGE_UP &&  e.shiftKey && !e.ctrlKey &&  e.altKey)
      submitter = form.add_attachment_box;
    if (e.keyCode == this.VK_ENTER   &&!e.shiftKey && !e.ctrlKey &&  e.altKey)
      submitter = form.preview;
    if (e.keyCode == this.VK_ENTER   && !e.shiftKey &&  e.ctrlKey && !e.altKey)
      submitter = form.post;
    if (submitter) {
      submitter.click();
      return this._cancelEvent(e);
    }

    return true;
  },

  // Adds a BB tag to the list.
  addTag: function(id, open, close, key, ctrlKey, multiline) {
    if (!ctrlKey) ctrlKey = "ctrl";
    var tag = new Object();
    tag.id        = id;
    tag.open      = open;
    tag.close     = close;
    tag.key       = key;
    tag.ctrlKey   = ctrlKey;
    tag.multiline = multiline;
    tag.elt       = this.textarea.form[id]
    this.tags[id] = tag;
    // Setup events.
    var elt = tag.elt;
    if (elt) {
      var th = this;
      if (elt.type && elt.type.toUpperCase()=="BUTTON") {
        addEvent(elt, 'click', function() { th.insertTag(id); return false; });
      }
      if (elt.tagName && elt.tagName.toUpperCase()=="SELECT") {
        addEvent(elt, 'change', function() { th.insertTag(id); return false; });
      }
    } else {
      if (id && id.indexOf('_') != 0) return alert("addTag('"+id+"'): no such element in the form");
    }
  },

  insertTag: function(id) {
    var tag = this.tags[id];
    if (!tag) return alert("Unknown tag ID: "+id);
    var op = tag.open;
    if (typeof(tag.open) == "function") op = tag.open(tag.elt);
    var cl = tag.close!=null? tag.close : "/"+op;

    // Use "[" if needed.
    if (op.charAt(0) != this.BRK_OP) op = this.BRK_OP+op+this.BRK_CL;
    if (cl && cl.charAt(0) != this.BRK_OP) cl = this.BRK_OP+cl+this.BRK_CL;

    this.surround(op, cl, !tag.multiline? null : tag.multiline===true? this._prepareMultiline : tag.multiline);
  },

  _prepareMultiline: function(text) {
    text = text.replace(/\s+$/, '');
    text = text.replace(/^([ \t]*\r?\n)+/, '');
    if (text.indexOf("\n") >= 0) text = "\n" + text + "\n";
    return text;
  }

}

function checkForm(form) {
  var formErrors = false;
  if (form.message.value.length < 2) {
    formErrors = "Please enter the message.";
  }
  if (formErrors) {
    setTimeout(function() { alert(formErrors) }, 100);
    return false;
  }
  return true;
}

if (window.HTMLElement && window.HTMLElement.prototype.__defineSetter__) {
  HTMLElement.prototype.__defineSetter__("innerText", function (sText) {
     this.innerHTML = sText.replace(/\&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
  });
  HTMLElement.prototype.__defineGetter__("innerText", function () {
     var r = this.ownerDocument.createRange();
     r.selectNodeContents(this);
     return r.toString();
  });
}

function AddSelectedText(BBOpen, BBClose) {
 if (document.post.message.caretPos) document.post.message.caretPos.text = BBOpen + document.post.message.caretPos.text + BBClose;
 else document.post.message.value += BBOpen + BBClose;
 document.post.message.focus()
}

function InsertBBCode(BBcode)
{
	AddSelectedText('[' + BBcode + ']','[/' + BBcode + ']');
}

function storeCaret(textEl) {
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}