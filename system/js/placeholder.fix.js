(function(){
	if('placeholder' in document.createElement('input')) return;
	function target (e){
		var e = e||window.event;
		return e.target||e.srcElement;
	}
	function _getEmptyHintEl(el){
		var hintEl = el.hintEl;
		return hintEl && g(hintEl);
	}
	function blurFn(e){
		var el = target(e);
		if(!el || el.tagName != 'input' && el.tagName != 'textarea') return;
		var	emptyHintEl = el.__emptyHintEl;
		if(emptyHintEl){
				if(el.value) emptyHintEl.style.display = 'none';
				else emptyHintEl.style.display = '';
		}
	}
	function focusFn(e){
		var el = target(e);
		if(!el || el.tagName != 'input' && el.tagName != 'textarea') return;
		var emptyHintEl = el.__emptyHintEl;
		if(emptyHintEl){
			emptyHintEl.style.display = 'none';
		}
	}
	if(document.addEventListener){
		document.addEventListener('focus', focusFn, true);
		document.addEventListener('blur', blurFn, true);
	}else{
		document.attachEvent('onfocusin', focusFn);
		document.attachEvent('onfocusout', blurFn);
	}

	var elss = [document.getElementsByTagName('input'), document.getElementsByTagName('textarea')];
	for(var n = 0;n<2;n++){
		var els = elss[n];
		for(var i  = 0;i<els.length;i++){
			var el = els[i];
			var placeholder = el.getAttribute('placeholder'), 
				emptyHintEl = el.__emptyHintEl;
			if(placeholder && !emptyHintEl){
				emptyHintEl = document.createElement('span');
				emptyHintEl.innerHTML = placeholder;
				emptyHintEl.className = 'placeholder';
				emptyHintEl.onclick = function (el){ return function(){ try{ el.focus(); }catch(ex){} } }(el);
				if(el.value) emptyHintEl.style.display = 'none';
				el.parentNode.insertBefore(emptyHintEl, el);
				el.__emptyHintEl = emptyHintEl;
			}
		}
	}
})();