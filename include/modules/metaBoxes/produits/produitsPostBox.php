<?php

	add_meta_box('postBoxProduits', __('Produits', 'evarisk'), array('digirisk_product', 'getProductPostBox'), PAGE_HOOK_EVARISK_GROUPEMENTS, 'rightSide', 'default');
	add_meta_box('postBoxProduits', __('Produits', 'evarisk'), array('digirisk_product', 'getProductPostBox'), PAGE_HOOK_EVARISK_UNITES_DE_TRAVAIL, 'rightSide', 'default');
	add_meta_box('postBoxProduits', __('Produits', 'evarisk'), array('digirisk_product', 'getProductPostBox'), PAGE_HOOK_EVARISK_TACHE, 'rightSide', 'default');
	add_meta_box('postBoxProduits', __('Produits', 'evarisk'), array('digirisk_product', 'getProductPostBox'), PAGE_HOOK_EVARISK_ACTIVITE, 'rightSide', 'default');