jQuery( document ).ready(function() {

    jQuery("input[name='onepgr_setup']").change(function(){
		   if (jQuery(this).val() == "sc"){
		   		jQuery(".onepgr-fields").show();
		   		jQuery(".onepgr-snip").hide();
		   } else {
				jQuery(".onepgr-fields").hide();
		   		jQuery(".onepgr-snip").show();
		   }
		  


		});

});