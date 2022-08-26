window.onload = () => {
	document.querySelectorAll('[name="fdm[first_appearance]"]').forEach(function(element,index){
		element.value = Date.now();
	});

	document.querySelectorAll('form').forEach(function(element,index){
		element.addEventListener('keydown',formKeyDownCallback);
		element.addEventListener('keyup',formKeyDownCallback);
		element.addEventListener('click',formKeyDownCallback);
	});

	function formKeyDownCallback(e){
		var form = getParentForm(e.target);
		fillFirstInteractionField(form);
	}

	function fillFirstInteractionField(form){
		var firstInteractionField = form.querySelector('[name="fdm[first_interaction]"]');
		if(firstInteractionField
		&& 0 == firstInteractionField.value.length){
			firstInteractionField.value = Date.now();
		}
		form.removeEventListener('keydown',formKeyDownCallback);
		form.removeEventListener('keyup',formKeyDownCallback);
		form.removeEventListener('click',formKeyDownCallback);
	}

	function getParentForm(element){
		if('FORM' == element.nodeName)
			return element;

		if('undefined' !== typeof element.form)
			return element.form;

		return getParentForm(element.parentNode);
	}
}