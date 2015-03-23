<li>
	<input data-name='service_slug' type='hidden' class='wpeo-service-slug' value='<?php echo !empty($service_slug) ? $service_slug : ''; ?>' />
	<input data-name='service_active' class='wpeo-service-active' <?php echo (int)$array['service_active'] || empty($service_slug) ? 'checked' : ''; ?> type='checkbox' />
  	<input data-name='service_name' type='text' class='wpeo-service-name' value='<?php echo !empty($array['service_name']) ? $array['service_name'] : ''; ?>' />
  	<input data-name='service_size' type='number' class='wpeo-service-size' value='<?php echo !empty($array['service_size']) ? $this->convert_to($array['service_size'], $array['service_size_format'], false) : ''; ?>' />
	<select data-name='service_size_format' class='wpeo-service-size-format'>
    	<?php if(!empty($array_size_format)): ?>
    		<?php foreach($array_size_format as $key => $value): ?>
	       		<option <?php echo selected(!empty($array['service_size_format']) ? $array['service_size_format'] : 'oc', $key); ?> value='<?php echo $key; ?>'><?php echo $value; ?></option>
	      	<?php endforeach; ?>
	    <?php endif; ?>
	</select>
	<input data-name='service_file' type='number' class='wpeo-service-file' value='<?php echo !empty($array['service_file']) ? $array['service_file'] : ''; ?>' />
	<label><?php _e('files', 'wpeologs-i18n'); ?></label>
	
	<input data-name='service_rotate' class='wpeo-service-rotate' <?php echo (int)$array['service_rotate'] ? 'checked' : ''; ?> type='checkbox' />
	
	<?php if(empty($service_slug)): ?>
		<a class='wpeo-service-add' href="#">+</a>
	<?php endif; ?>
</li>