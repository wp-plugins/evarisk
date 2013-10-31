<?php

	global $wpdb;
	$query = $wpdb->prepare( "SELECT PM.post_id FROM {$wpdb->postmeta} AS PM INNER JOIN {$wpdb->posts} AS P ON (P.ID = PM.post_id) WHERE P.post_status = 'publish' AND PM.meta_key = %s AND PM.meta_value LIKE ('%%%s%%') AND P.post_type = %s", '_wpes_survey_association',  $_REQUEST['table'], 'wpes_survey');
	$associated_surveys_list = $wpdb->get_results( $query );

	if ( !empty($associated_surveys_list) ) {
		switch ( $_REQUEST['table'] ) {
			case TABLE_GROUPEMENT:
				$element_associated = PAGE_HOOK_EVARISK_GROUPEMENTS;
				break;
			case TABLE_UNITE_TRAVAIL:
				$element_associated = PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL;
				break;
			case TABLE_TACHE:
				$element_associated = PAGE_HOOK_EVARISK_TACHE;
				break;
			case TABLE_ACTIVITE:
				$element_associated = PAGE_HOOK_EVARISK_ACTIVITE;
				break;
		}

		foreach ( $associated_surveys_list as $survey ) {
			/**	Define metabox allowing to associate survey to existing custom post type */
			add_meta_box( $_REQUEST['table'] . '-survey-association-' . $survey->post_id, sprintf( _x('%s', 'Title for metabox displaying survey in associated post type', 'wp_easy_survey'), get_the_title( $survey->post_id ) ), 'display_survey_for_digirisk', $element_associated, 'rightSide', 'default', array( 'parent_element_id' => $survey->post_id, 'parent_element_type' => 'survey' ) );
		}
	}

	function display_survey_for_digirisk( $current_element, $args ) {
		global $wpes_survey;
		$final_survey = $wpes_survey->final_survey_display( $current_element[ 'idElement' ], $args['args']['parent_element_id'] );

		echo $final_survey['content'] . '<input type="hidden" value="' . $wpes_survey->get_total_number_of_issue() . '" name="wpes-final-survey-total-answer-to-give" id="wpes-final-survey-total-answer-to-give" />';
	}

?>