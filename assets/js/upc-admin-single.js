document.getElementById('wp-admin-bar-eos-up-cache').addEventListener('click',function(){
	this.className = 'upc-in-progress';
	var button = this,
		icon = button.getElementsByClassName('dashicons')[0],
		icon_color = icon.style.color,
		xhr = new XMLHttpRequest(),
		data = {'nonce' : upc_js.nonce,'post_id' : upc_js.post_id},
		fd = new FormData();
	fd.append('data',JSON.stringify(data));
	xhr.onload = function(e) {
		if(this.readyState === 4) {
			if('1' === e.target.responseText){
				icon.style.color = 'green';
				setTimeout(function(){
					icon.style.color = icon_color;
				},2000);
			}
			else{
				icon.style.color = 'red';
			}
			button.className = '';

		}
		return false;
	};		
	xhr.open("POST",upc_js.ajax_url + '?action=eos_upc_update_cache',true);
	xhr.send(fd);
	return false;
});