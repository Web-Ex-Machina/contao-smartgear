window.onload = () => {
	document.querySelectorAll('.pdm-item__personal_data_single__button_show_file').forEach(function(button){
		button.addEventListener('click',function(e){
			e.preventDefault();
			
			let url = e.target.getAttribute('href');
			let pid = e.target.parentNode.getAttribute('data-pid');
			let ptable = e.target.parentNode.getAttribute('data-ptable');
			let field = e.target.parentNode.getAttribute('data-field');
			showFile(url,pid,ptable,field);
			return false;
		});
	});
	
	document.querySelectorAll('.pdm-item__personal_data_single__button_download_file').forEach(function(button){
		button.addEventListener('click',function(e){
			e.preventDefault();
			
			let url = e.target.getAttribute('href');
			let pid = e.target.parentNode.getAttribute('data-pid');
			let ptable = e.target.parentNode.getAttribute('data-ptable');
			let field = e.target.parentNode.getAttribute('data-field');
			downloadFile(url,pid,ptable,field);

			return false;
		});
	});

	function showFile(url, pid, ptable, field){
		let singleElement = WEM.pdm.getSinglePersonalValueContainer(pid, ptable, field);
		let email = WEM.pdm.getParentItem(singleElement).getAttribute('data-email');
		WEM.pdm.postData(url,{
			'action':'show_file_single_personal_data',
			'pid':pid,
			'ptable':ptable,
			'email':email,
			'field':field
		})
		.then(data => data.json())
		.then(data =>{
			if("success" == data.status){
				var modal = new app.ModalFW({
					name : 'modal-pdm-show-file',
					content: '<iframe src="'+data.content+'" width="100%" height="'+window.innerHeight*.75+'px"></iframe>',
					width:'75%',
					blnDismiss:false,
					onClose: function(){
						modal.destroy();
					},
					onOpen: function(){
						// doesn't work, how can I find a workaround ?
						modal.$el[0].querySelector('iframe').addEventListener('load',function(e){
							e.target.querySelectorAll('img').forEach(function(img){
								img.style = "margin: auto;";
							});
						});
					}
				});
				modal.open();
			}else{
				alert(data.msg);	
			}
		}).catch(function(e) {
			alert("Une erreur est survenue");
			console.log(e);
		});
	};

	function downloadFile(url, pid, ptable, field){
		let singleElement = WEM.pdm.getSinglePersonalValueContainer(pid, ptable, field);
		let email = WEM.pdm.getParentItem(singleElement).getAttribute('data-email');
		WEM.pdm.postData(url,{
			'action':'download_file_single_personal_data',
			'pid':pid,
			'ptable':ptable,
			'email':email,
			'field':field
		})
		.then(response =>{
			if(response.ok) {
				response.blob().then(function(blob) {
					WEM.pdm.downloadBlobFile(blob,response.headers.get('filename'));
				});
			} else {
				response.json().then(function(json){
					alert(json.msg);
				});
			}
		}).catch(function(e) {
			alert("Une erreur est survenue");
			console.log(e);
		});
	};
}