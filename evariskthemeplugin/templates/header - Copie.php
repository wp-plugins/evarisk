<!-- header START -->
<div id="header-container">
<div id="header">
<div id="header-flash">
<object type="application/x-shockwave-flash"data="<?php bloginfo('url') ?>/flash/Evarisk_header.swf" width="634" height="96">
<param name="movie" value="<?php bloginfo('url') ?>/flash/Evarisk_header.swf" />
<param name="wmode" value="transparent" />
<param name="quality" value="high"/>
<p>L'animation flash n'est pas prise en charge</p>
</object> 
</div>
	<!-- banner START -->
	<?php if( $options['banner_content'] && (
		($options['banner_registered'] && $user_ID) || 
		($options['banner_commentator'] && !$user_ID && isset($_COOKIE['comment_author_'.COOKIEHASH])) || 
		($options['banner_visitor'] && !$user_ID && !isset($_COOKIE['comment_author_'.COOKIEHASH]))
	) ) : ?>
		<div class="banner">
			<?php echo($options['banner_content']); ?>
		</div>
	<?php endif; ?>
	<!-- banner END -->

	<div id="caption">
		<!-- <h1 id="title"><a href="<?php bloginfo('url'); ?>//"><?php bloginfo('name'); ?></a></h1>
		<div id="tagline"><?php bloginfo('description'); ?></div>-->
	</div>

	<div class="fixed"></div>
</div>
<!-- header END -->

<!-- navigation START -->
<div id="navigation">
	<!-- menus START -->
	<!--ul id="menus">
		<li class="<?php echo($home_menu); ?>"><a class="home" title="<?php _e('Home', 'inove'); ?>" href="<?php echo get_settings('home'); ?>/"><?php _e('Home', 'inove'); ?></a></li>
		<?php
			if($options['menu_type'] == 'categories') {
				wp_list_categories('title_li=0&orderby=name&show_count=0');
			} else {
				wp_list_pages('title_li=0&sort_column=menu_order');
			}
		?>
		<li><a class="lastmenu" href="javascript:void(0);"></a></li>
	</ul-->
	<ul id="menus">
		<li class="accueil"><a href="<?php bloginfo('url'); ?>/">Accueil</a>
			<ul style="visibility: hidden; display: block; left: 192px; top: 154px;">
				<li class="page_item page-item-30"><a title="Qui sommes nous ?" href="<?php bloginfo('url'); ?>/accueil/prevention-risques-professionnels/">Qui sommes nous ?</a></li>
				<li class="page_item page-item-6"><a title="Notre d&eacute;marche" href="<?php bloginfo('url'); ?>/accueil/document-unique-demarche/" class=" last">Notre d&eacute;marche</a></li>
			</ul>
		</li>
		<li class="actu"><a href="<?php bloginfo('url'); ?>/actualites/">Actualit&eacute;s</a></li>
		<li class="eval"><a href="<?php bloginfo('url'); ?>/document-unique-formation/">&Eacute;valutation des risques</a>
			<ul style="visibility: hidden; display: block;">
				<li class="page_item page-item-20"><a title="Offre de formation" href="<?php bloginfo('url'); ?>/document-unique-formation/formation-reduction-des-risque/">Offre de formation</a></li>
				<li class="page_item page-item-22"><a title="Audits et conseils" href="<?php bloginfo('url'); ?>/document-unique-formation/audit-conseil/">Audits et conseils</a></li>
				<li class="page_item page-item-23"><a title="Mesures de bruit" href="<?php bloginfo('url'); ?>/document-unique-formation/mesures-de-bruit/">Mesures de bruit</a></li>
				<li class="page_item page-item-670"><a title="Document unique Mairies &amp; Collectivit&eacute;s" href="<?php bloginfo('url'); ?>/document-unique-formation/evaluation-de-risques-mairies-collectivites/" class=" last">Document unique Mairies &amp; Collectivit&eacute;s</a></li>
			</ul>
		</li>
		<li class="logiciel"><a href="<?php bloginfo('url'); ?>/document-unique-logiciel/">Logiciel</a>
			<ul style="visibility: hidden; display: block; left: 475px; top: 154px;">
				<li class="page_item page-item-32"><a title="T&eacute;l&eacute;charger" href="<?php bloginfo('url'); ?>/document-unique-logiciel/telecharger/" class=" subtitle ">T&eacute;l&eacute;charger</a>
					<ul style="visibility: hidden; display: block; left: 203px; top: -1px;">
						<li class="page_item page-item-35"><a title="Identification" href="<?php bloginfo('url'); ?>/document-unique-logiciel/telecharger/identification/">Identification</a></li>
						<li class="page_item page-item-34"><a title="Archives" href="<?php bloginfo('url'); ?>/document-unique-logiciel/telecharger/archives/" class=" last">Archives</a></li>
					</ul>
				</li>
				<li class="page_item page-item-15"><a title="Mode d'emploi" href="<?php bloginfo('url'); ?>/document-unique-logiciel/mode-demploi/" class=" last">Mode d'emploi</a></li>
			</ul>
		</li>
		<li class="references"><a href="<?php bloginfo('url'); ?>/document-unique-references/">R&eacute;f&eacute;rences</a></li>
		<li class="forum"><a href="<?php bloginfo('url'); ?>/forums/">Forum</a></li>
		<li class="contact"><a href="<?php bloginfo('url'); ?>/contact/">Contact</a></li>
		<li class="faq"><a href="<?php bloginfo('url'); ?>/document-unique-faq/">F.A.Q</a></li>
	</ul>
	<!-- menus END -->

	<!-- searchbox START -->

<script type="text/javascript">
//<![CDATA[
	var searchbox = MGJS.$("searchbox");
	var searchtxt = MGJS.getElementsByClassName("textfield", "input", searchbox)[0];
	var searchbtn = MGJS.getElementsByClassName("button", "input", searchbox)[0];
	var tiptext = "<?php _e('Type text to search here...', 'inove'); ?>";
	if(searchtxt.value == "" || searchtxt.value == tiptext) {
		searchtxt.className += " searchtip";
		searchtxt.value = tiptext;
	}
	searchtxt.onfocus = function(e) {
		if(searchtxt.value == tiptext) {
			searchtxt.value = "";
			searchtxt.className = searchtxt.className.replace(" searchtip", "");
		}
	}
	searchtxt.onblur = function(e) {
		if(searchtxt.value == "") {
			searchtxt.className += " searchtip";
			searchtxt.value = tiptext;
		}
	}
	searchbtn.onclick = function(e) {
		if(searchtxt.value == "" || searchtxt.value == tiptext) {
			return false;
		}
	}
//]]>
</script>
	<!-- searchbox END -->

	<div class="fixed"></div>
</div>
<!-- navigation END -->
</div>
