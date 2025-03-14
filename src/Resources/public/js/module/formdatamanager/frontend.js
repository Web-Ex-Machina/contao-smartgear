// var SG = SG || {};
// SG.modules = SG.modules || {};
// (function() {
// 	SG.modules.formDataManager = SG.modules.formDataManager || {
// 		init:function(){
// 			self.manageNotYetManagedFormsNotInModal();

// 			var mutationObserver = new MutationObserver(function(mutations) {
// 				mutations.forEach(function(mutation) {
// 					if (mutation.type === 'attributes'
// 					&& mutation.attributeName === 'class'
// 					&& mutation.oldValue.indexOf('active') > -1
// 					){
// 						console.log(mutation.target);
// 						self.manageNotYetManagedFormsInModal(mutation.target);
// 					}
// 				});
// 			});
// 			document.querySelectorAll('.modalFW').forEach(function(element, index){
// 				mutationObserver.observe(element, {
// 					attributes: true,
// 					attributeOldValue: true,
// 					characterDataOldValue: true
// 				});
// 			});
// 		},
// 		manageNotYetManagedFormsNotInModal:function(){
// 			document.querySelectorAll('form:not([data-fdm="true"]):not(.modalFW form)').forEach(function(element,index){
// 				self.fillFirstAppearanceField(element);
// 				self.applyFormListeners(element);
// 				element.setAttribute('data-fdm','true');
// 			});
// 		},
// 		manageNotYetManagedFormsInModal:function(modal){
// 			modal.querySelectorAll('form:not([data-fdm="true"])').forEach(function(element,index){
// 				self.fillFirstAppearanceField(element);
// 				self.applyFormListeners(element);
// 				element.setAttribute('data-fdm','true');
// 			});
// 		},
// 		applyFormListeners:function(form){
// 			form.addEventListener('keydown',self.formKeyDownCallback);
// 			form.addEventListener('keyup',self.formKeyDownCallback);
// 			form.addEventListener('click',self.formKeyDownCallback);
// 		},
// 		formKeyDownCallback:function(e){
// 			var form = self.getParentForm(e.target);
// 			self.fillFirstInteractionField(form);
// 		},
// 	 	fillFirstAppearanceField:function(form){
// 	 		var field = form.querySelector('[name="fdm[first_appearance]"]');
// 	 		if(field){
// 		 		field.value = Date.now();
// 		 	}
// 	 	},
// 	 	fillFirstInteractionField:function(form){
// 			var firstInteractionField = form.querySelector('[name="fdm[first_interaction]"]');
// 			if(firstInteractionField
// 			&& 0 == firstInteractionField.value.length){
// 				firstInteractionField.value = Date.now();
// 			}
// 			form.removeEventListener('keydown',self.formKeyDownCallback);
// 			form.removeEventListener('keyup',self.formKeyDownCallback);
// 			form.removeEventListener('click',self.formKeyDownCallback);
// 		},
// 		getParentForm:function(element){
// 			if('FORM' == element.nodeName)
// 				return element;

// 			if('undefined' !== typeof element.form)
// 				return element.form;

// 			return self.getParentForm(element.parentNode);
// 		}
// 	}
// 	var self = SG.modules.formDataManager;
// })();
// window.onload = () => {
// 	SG.modules.formDataManager.init();
// }