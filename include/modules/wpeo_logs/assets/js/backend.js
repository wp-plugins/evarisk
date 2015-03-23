jQuery(document).ready(function() {
	/** 
	 * Tools 
	 */
	var current_parent_name = "";
	
	/** Menu */
	jQuery('.wpeo-logs-wrap .wpeo-logs-menu .wpeo-logs-parent').click(function() {
		var element = jQuery(this);
		
		if(!element.closest('li').hasClass('wpeo-logs-li-active')) {
			jQuery('.wpeo-logs-parent').removeClass('wpeo-logs-li-active');
			element.closest('li').toggleClass('wpeo-logs-li-active');
			
			current_parent_name = element.html();
			
			/** Load child with ajax */		
			var data = {
				"action": "wpeo-get-bulleted-list",
				"current_file_name": current_parent_name,
			};
			
			jQuery(".wpeo-logs-archive-file").find('ul').load(ajaxurl, data, function() {});
			
			data = {
				"action": "wpeo-render-csv",
				"current_parent_name": element.html(),
				"current_file_name": element.html(),
				"current_index": 0,
			}
			
			jQuery(".wpeo-logs-table").load(ajaxurl, data, function() {});
		}
	});
	
	/** Archives */
	jQuery(document).on("click", '.wpeo-logs-wrap .wpeo-archive-file', function() {
		var element = jQuery(this);
		
		if(!element.hasClass('wpeo-logs-archive-file-active')) {
			jQuery('.wpeo-archive-file').removeClass('wpeo-logs-archive-file-active');
			
			
			if(element.attr('data-name'))
				var current_file_name = element.attr('data-name');
			else {
				element.toggleClass('wpeo-logs-archive-file-active');
				var current_file_name = element.html();
			}
			
			/** Load archive ajax */
			var data = {
				"action": "wpeo-render-csv",
				"current_parent_name": current_parent_name,
				"current_file_name": current_file_name,
				"current_index": 0,
				"get_archive": true,
			};
			
			jQuery(".wpeo-logs-table").load(ajaxurl, data, function() {});
		}
	});
	
	/**
	 *  Settings 
	 */
	
	/** Add service */
	jQuery('.wpeo-service-add').click(function() {
		var closest = jQuery(this).closest('li');

		// Get form value
		var service_active = closest.find('.wpeo-service-active').is(':checked');
		var service_rotate = closest.find('.wpeo-service-rotate').is(':checked');
		var service_name = closest.find('.wpeo-service-name').val();
		var service_size = closest.find('.wpeo-service-size').val();
		var service_size_format = closest.find('.wpeo-service-size-format option:selected').val();
		var service_file = closest.find('.wpeo-service-file').val();
    
		closest.find('.wpeo-service-rotate').prop('checked', false);
		closest.find('.wpeo-service-name').val('');
		closest.find('.wpeo-service-size').val('');
		closest.find('.wpeo-service-size-format option[value="octet"]').prop('selected', true);
  	 	closest.find('.wpeo-service-file').val('');

	    var data = {
	      'action': 'wpeo-update-service',
	      'service_active': service_active,
	      'service_name': service_name,
	      'service_size': service_size,
	      'service_size_format': service_size_format,
	      'service_file': service_file,
	      'service-rotate': service_rotate,
	     
	    };

	    // Ajax post
	    jQuery.post(ajaxurl, data, function(response) {
	    	// Render new li service
	    	jQuery('.wpeo-logs-service').prepend(response.render);
	    });
	  });
  
	  /** Update service */
	  jQuery(document).on('blur', '.wpeo-logs-service input', function() {
		  // Get slug
		  var input_blur = jQuery(this).closest('li');
		  
		  // Form value
		  var slug = input_blur.find('.wpeo-service-slug').val();
		  var file_format = input_blur.find('.wpeo-service-size-format option:selected').val();
		  
		  if(slug != '') {
			  if('checkbox' == jQuery(this).attr('type'))
				  update_service_data(slug, file_format, jQuery(this).attr('data-name'), jQuery(this).is(':checked'));
			  else
				  update_service_data(slug, file_format, jQuery(this).attr('data-name'), jQuery(this).val());
		  }
	  });
	  
});

function update_service_data(slug, file_format, data_name, value) {
	var data = {
		'action': 'wpeo-update-service',
		'service_slug': slug,
		'service_size_format': file_format,
	};
	
	data[data_name] = value;
	
	jQuery.post(ajaxurl, data, function() {});
}