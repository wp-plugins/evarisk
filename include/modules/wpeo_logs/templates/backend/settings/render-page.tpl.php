<div class="wrap">
  <h2><?php _e('Log', 'wpeologs-i18n'); ?></h2>

  <!-- Form ajax for add service -->
  <h3><?php _e('My services', 'wpeologs-i18n'); ?></h3>

  <ul class='wpeo-logs-service'>
  	<?php if(!empty($current_option['my_services'])): ?>
  		<?php foreach($current_option['my_services'] as $service_slug => $array): ?>
  			<?php $this->display_model_service($service_slug, $array); ?>
  		<?php endforeach; ?>
  		<?php unset($service_slug); ?>
  		<?php unset($array); ?>
  	<?php endif; ?>
    <?php require( WPEO_LOGS_TEMPLATES_MAIN_DIR . "backend/settings/models/service.tpl.php"); ?>
  </ul>
</div>
