<?php

	/**
	*	Define the different possibilities for the field type in forms containing eav's field
	*/
	$attributeInputType = array();
	$attributeInputType['text'] = __('Champs texte','evarisk');
	$attributeInputType['textarea'] = __('Champs texte multiligne','evarisk');
	$attributeInputType['integer'] = __('Nombre entier','evarisk');
	$attributeInputType['decimal'] = __('Nombre d&eacute;cimal','evarisk');
	$attributeInputType['date'] = __('Champs date','evarisk');
	$attributeInputType['datetime'] = __('Champs date + heure','evarisk');
	$attributeInputType['select'] = __('Liste d&eacute;roulante','evarisk');
	$attributeInputType['file'] = __('Fichier','evarisk');

	/**
	*	Define the storage type available
	*/
	$attributeStorageType = array();
	$attributeStorageType['static'] = __('Champs dans la table de l\'entit&eacute;','evarisk');
	$attributeStorageType['datetime'] = __('Date','evarisk');
	$attributeStorageType['decimal'] = __('Champs date','evarisk');
	$attributeStorageType['int'] = __('Entier','evarisk');
	$attributeStorageType['text'] = __('Texte long','evarisk');
	$attributeStorageType['varchar'] = __('Texte (255 char.)','evarisk');

	/**
	*	Define the connection between the input type and the storage type
	*/
	$attributeInputStorageConnection = array();
	$attributeInputStorageConnection['text'] = 'varchar';
	$attributeInputStorageConnection['textarea'] = 'text';
	$attributeInputStorageConnection['integer'] = 'int';
	$attributeInputStorageConnection['decimal'] = 'decimal';
	$attributeInputStorageConnection['date'] = 'datetime';
	$attributeInputStorageConnection['datetime'] = 'datetime';
	$attributeInputStorageConnection['select'] = 'int';
	$attributeInputStorageConnection['file'] = 'varchar';

?>