var add_pages = document.getElementsByClassName('upc-add-page'),
	remove_pages = document.getElementsByClassName('upc-remove-page'),
	rows = document.getElementsByClassName('upc-row-post_type'),
	upc_lists = document.getElementsByClassName('upc-pages-list');
for(var n=0;n<upc_lists.length;++n){
	upc_lists[n].addEventListener('change',function(){
		document.getElementById(this.id.replace('upc-pages-list','upc-add-page')).click();
	});
}
document.getElementsByClassName('eos-up-save-btn')[0].addEventListener('click',function(){
	var button = this,
		data = {},
		autoChks = document.getElementsByClassName('upc-auto-chk'),
		autos = [],
		allPages = [],
		page_id = 0,
		rowsObj = {},
		xhr = new XMLHttpRequest(),
		fd = new FormData(),
		msgs = document.getElementsByClassName('eos-up-save-btn-wrp')[0].getElementsByClassName('notice'),
		n = 0,
		k = 0;
	for(n;n < msgs.length;++n){
		msgs[n].className = msgs[n].className.replace( ' eos-hidden','') + ' eos-hidden';
	}
	button.className = button.className.replace(' upc-in-progress','') + ' upc-in-progress';
	for(idx in autoChks){
		if('undefined' !== typeof(autoChks[idx])){
			if('undefined' !== typeof(autoChks[idx].dataset)){
				autos.push([autoChks[idx].dataset.post_type,autoChks[idx].checked]);
			}
		}
	}
	data['autos'] = autos;
	for(n=0;n<rows.length;++n){
		var pages = [];		
		pageEls = rows[n].getElementsByClassName('upc-page-title');
		if(pageEls){
			for(k=0;k<pageEls.length;++k){
				pages.push(pageEls[k].dataset.id);
			}
		}
		allPages.push([rows[n].dataset.post_type,pages.join(',')]);
	}
	data['pages'] = allPages;
	data['nonce'] = document.getElementById('_up_cache_setts_nonce').value;
	fd.append('data',JSON.stringify(data));
	xhr.onload = function(e) {
		if(this.readyState === 4){
			var msg = '1' === e.target.responseText ? document.getElementById('upc-msg-success') : document.getElementById('upc-msg-fail');
			msg.className = msg.className.replace(' eos-hidden','');
			button.className = button.className.replace(' upc-in-progress','');
		}
		return false;
	};		
	xhr.open("POST",upc_js.ajax_url + '?action=eos_upc_save_settings',true);
	xhr.send(fd);
	return false;
});
for(n=0;n<add_pages.length;++n){
	add_pages[n].addEventListener('click',function(){
		var row = document.getElementById(this.dataset.row),
			pages_wrp = row.getElementsByClassName('upc-selected-pages')[0],
			post_type = pages_wrp.dataset.post_type,
			dropdown = row.getElementsByClassName('upc-pages-list')[0],
			selected_option = dropdown.options[dropdown.selectedIndex],
			selected_value = selected_option.value,
			selected_title = selected_option.text.replace(/\s+/g,' ').trim();
		if('false' !== selected_option.value && document.getElementsByClassName('upc-page-' + selected_value).length < 1){
			pages_wrp.innerHTML += '<span id="upc-page-' + post_type + '-' + selected_value + '" class="upc-page-title" data-id="' + selected_value + '" class="upc-page-' + selected_value +'">' + selected_title + '<span class="upc-remove-page upc-remove-page-added dashicons dashicons-no-alt" data-post_type="' + post_type + '" data-id="' + selected_value + '"></span></span>';
			dropdown.options[0].selected = true;
		}
	});
}
for(n=0;n<remove_pages.length;++n){
	remove_pages[n].addEventListener('click',function(){
		upc_remove_page(this.dataset.id,this.dataset.post_type);
	});
}
for(n=0;n<rows.length;++n){
	rows[n].getElementsByClassName('upc-selected-pages')[0].addEventListener('click',function(e){
		if(e.target.className.indexOf('upc-remove-page') > -1){
			upc_remove_page(e.target.dataset.id,e.target.dataset.post_type);
		}
	});
}
function upc_remove_page(id,post_type){
	var el = document.getElementById('upc-page-' + post_type + '-' + id);
	if(el){
		el.parentNode.removeChild(el);
	}
}