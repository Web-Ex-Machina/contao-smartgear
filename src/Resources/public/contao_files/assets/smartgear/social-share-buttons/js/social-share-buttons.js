var SM = SM || {};
(function() {
	SM.socialShareButtons = SM.socialShareButtons || {
		shares:{
			twitter:{
				name:'twitter',
				icon:'fab fa-twitter',
				url:'https://twitter.com/intent/tweet?url={url}'
			},
			facebook:{
				name:'facebook',
				icon:'fab fa-facebook',
				url:'https://www.facebook.com/sharer/sharer.php?u={url}'
			},
			linkedin:{
				name:'linkedin',
				icon:'fab fa-linkedin',
				url:'https://www.linkedin.com/sharing/share-offsite/?url={url}'
			}
		},
		selectors:{
			container:'.share-buttons__container'
		},
		init:function(){
			let containers = self.getContainers();
			for(var i in containers){
				self.fillContainer(containers[i]);
			}
		},
		getContainers:function(){
			return document.querySelectorAll(self.selectors.container);
		},
		fillContainer:function(container){
			container.innerHTML = self.buildTwitterButton(window.location.href) + self.buildFacebookButton(window.location.href) + self.buildLinkedinButton(window.location.href,'');
		},
		buildShareButton(shareConfig, shareParams){
			var href = shareConfig.url;
			for(var i in shareParams){
				if(0 <= href.indexOf('{'+i+'}')){
					href = href.replace('{'+i+'}',shareParams[i]);
				}else{
					href+= '&'+i+'='+shareParams[i];
				}
			}
			return '<a href="'+href+'" class="share-buttons__btn-'+shareConfig.name+'" target="_blank"><i class="'+shareConfig.icon+'"></i></a>';
		},
		buildTwitterButton:function(url){
			return self.buildShareButton(self.shares.twitter,{url:url});
		},
		buildFacebookButton:function(url){
			return self.buildShareButton(self.shares.facebook,{url:url});
		},
		buildLinkedinButton:function(url){
			return self.buildShareButton(self.shares.linkedin,{url:url});
		}
	}
	var self = SM.socialShareButtons;
})();

