<?php
/****************************************************
*Date: 01/10/2009      file:tools.class.php     	*
*Author: Eoxia										*
*Comment:											*
****************************************************/

class eva_tools
{
	
	/*	CLEAN UP A VAR BEFORE SENDING IT TO OUTPUT OR DATABASE	*/
	function IsValid_Variable($MyVar2Test,$DefaultValue='')
	{
		$MyVar = (trim(strip_tags(stripslashes($MyVar2Test)))!='') ? trim(strip_tags(stripslashes(($MyVar2Test)))) : $DefaultValue ;

		return $MyVar;
	}

	function DoPagination($link, $nbitems=1, $page=1, $nbitemsparpage=1, $offset=2, $background='#CCCCCC',$color='#333333',$slctdbackground='#000000', $slctdcolor='#FFFFFF' , $option=0 )
	{
		if($nbitems<0)$nbitems=0;
		$nbpage=ceil($nbitems/$nbitemsparpage);

		if($page<1)$page=1;if($page>$nbpage)$page=$nbpage;
		$pluriel='';if($nbitems>1)$pluriel='s';
		$pluriel1='';if($nbpage>1)$pluriel1='s';

		$pagination='<div style="display:table;width:100%;margin:3px 0 0 0;padding:3 0 3px 0;background:'.$background.';color:'.$color.';" >
		<span onmouseover="this.style.textDecoration=\'underline\';" onmouseout="this.style.textDecoration=\'\';" 
		'.str_replace('#PAGE#',1, $link).' 
				style="display:table;cursor:pointer;float:left;padding:1px 6px;margin:0 3px;" >';
				if($option==0)$pagination.=''.number_format($nbitems,0,'',' ').'&nbsp;';
				if($option!=-1)$pagination.=__('r&eacute;sultat','annonces').$pluriel.'&nbsp;/';
				$pagination.='&nbsp;'.$nbpage.'&nbsp;'.__('page','annonces').$pluriel1.'</span>
				<span style="display:table;float:left;padding:1px;margin:0 3px;" >&nbsp;:&nbsp;</span>';

		$min=$page-$offset;
		if($min<1)$min=1;

		$max=$min+(2*$offset);
		if($max>=$nbpage){$max=$nbpage;$min=$max-(2*$offset)-1;}

		if($min<1)$min=1;

		$minto=$min - ($offset)-1;
		if($minto<1){$minto=1;}
		$maxto=$min + (3*$offset)+1;
		if($maxto>$nbpage){$max=$nbpage;}

		if($option<2){	//	sens croissant
			if($min>1)$pagination.='<span onmouseover="this.style.textDecoration=\'underline\';" onmouseout="this.style.textDecoration=\'\';" 
			'.str_replace('#PAGE#',$minto, $link).' style="display:table;cursor:pointer;float:left;padding:1px 6px;margin:0 3px;" ><<</span>';
			else $pagination.='<span style="display:table;float:left;padding:1px 6px;margin:0 3px;" >&nbsp;&nbsp;&nbsp;&nbsp;</span>';

			for($i=$min;$i<=$max;$i++){
				$selected='';if($i==$page)$selected='color:'.$slctdcolor.';background:'.$slctdbackground.';font-weight:bold;';
				$pagination.='<span onmouseover="this.style.textDecoration=\'underline\';" onmouseout="this.style.textDecoration=\'\';" 
				'.str_replace('#PAGE#',$i, $link).' style="display:table;cursor:pointer;float:left;padding:1px 6px;margin:0 3px;'.$selected.' " >'.$i.'</span>';
			}

			if($max<$nbpage)$pagination.='<span onmouseover="this.style.textDecoration=\'underline\';" onmouseout="this.style.textDecoration=\'\';" 
			'.str_replace('#PAGE#',$maxto, $link).' style="display:table;cursor:pointer;float:left;padding:1px 6px;margin:0 3px;" >>></span>';
		}
		else{						//	sens decroissant
			if($max<$nbpage)$pagination.='<span onmouseover="this.style.textDecoration=\'underline\';" onmouseout="this.style.textDecoration=\'\';" 
			'.str_replace('#PAGE#',$maxto, $link).' style="display:table;cursor:pointer;float:left;padding:1px 6px;margin:0 3px;" ><<</span>';

			for($i=$max;$i>=$min;$i--){
				$selected='';if($i==$page)$selected='color:'.$slctdcolor.';background:'.$slctdbackground.';font-weight:bold;';
				$pagination.='<span onmouseover="this.style.textDecoration=\'underline\';" onmouseout="this.style.textDecoration=\'\';" 
			'.str_replace('#PAGE#',$i, $link).' style="display:table;cursor:pointer;float:left;padding:1px 6px;margin:0 3px;'.$selected.' " >'.$i.'</span>';
			}

			if($min>1)$pagination.='<span onmouseover="this.style.textDecoration=\'underline\';" onmouseout="this.style.textDecoration=\'\';" 
			'.str_replace('#PAGE#',$minto, $link).' style="display:table;cursor:pointer;float:left;padding:1px 6px;margin:0 3px;" >>></span>';
			else $pagination.='<span style="display:table;float:left;padding:1px 6px;margin:0 3px;" >&nbsp;&nbsp;&nbsp;&nbsp;</span>';
		}

		$pagination.='</div >';

		return $pagination;
	}

	function slugify_nospace($text)
	{
	  if (empty($text))
	  {
		return '';
	  }else{
	  
	   $text = preg_replace('/\s/', '+', $text);
	   $text = trim($text);
	  
	  }
	 
	  return $text;
	}
	
	function slugify($text)
	{
		$pattern = Array("é", "è", "ê", "ç", "à", "â", "î", "ï", "ù", "ô", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï", "Ö", "Ù", "Û", "Ü");
		$rep_pat = Array("e", "e", "e", "c", "a", "a", "i", "i", "u", "o", "E", "E", "E", "E", "I", "I", "I", "I", "O", "U", "U", "U");
		if(!(empty($text)))
		{
			$text = str_replace($pattern, $rep_pat, utf8_decode($text));
			$text = preg_replace('/\s/', '_', $text);
			$text = trim($text);
		}
		return $text;
	}

	function slugify_accent($text){
		$pattern  = Array("/&eacute;/", "/&egrave;/", "/&ecirc;/", "/&ccedil;/", "/&agrave;/", "/&acirc;/", "/&icirc;/", "/&iuml;/", "/&ucirc;/", "/&ocirc;/", "/&Egrave;/", "/&Eacute;/", "/&Ecirc;/", "/&Euml;/", "/&Igrave;/", "/&Iacute;/", "/&Icirc;/", "/&Iuml;/", "/&Ouml;/", "/&Ugrave;/", "/&Ucirc;/", "/&Uuml;/", "/&#146;/","/&#34;/");
		$rep_pat = Array("é", "è", "ê", "ç", "à", "â", "î", "ï", "ù", "ô", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï", "Ö", "Ù", "Û", "Ü", "'",'"');
		if ($text == '')
		{
			return '';
		}
		else
		{
			$text = preg_replace($pattern, $rep_pat, utf8_decode($text));
	  }
	  
	  return $text;
	}

	public function slugify_noaccent($text){
		$pattern  = Array("/&eacute;/", "/&egrave;/", "/&ecirc;/", "/&ccedil;/", "/&agrave;/", "/&acirc;/", "/&icirc;/", "/&iuml;/", "/&ucirc;/", "/&ocirc;/", "/&Egrave;/", "/&Eacute;/", "/&Ecirc;/", "/&Euml;/", "/&Igrave;/", "/&Iacute;/", "/&Icirc;/", "/&Iuml;/", "/&Ouml;/", "/&Ugrave;/", "/&Ucirc;/", "/&Uuml;/","/é/", "/è/", "/ê/", "/ç/", "/à/", "/â/", "/î/", "/ï/", "/ù/", "/ô/", "/È/", "/É/", "/Ê/", "/Ë/", "/Ì/", "/Í/", "/Î/", "/Ï/", "/Ö/", "/Ù/", "/Û/", "/Ü/");
		$rep_pat = Array("e", "e", "e", "c", "a", "a", "i", "i", "u", "o", "E", "E", "E", "E", "I", "I", "I", "I", "O", "U", "U", "U","e", "e", "e", "c", "a", "a", "i", "i", "u", "o", "E", "E", "E", "E", "I", "I", "I", "I", "O", "U", "U", "U");
		if ($text == '')
		{
			return '';
		}
		else
		{
			$text = preg_replace($pattern, $rep_pat, utf8_decode($text));
	  }
	  
	  return $text;
	}

	public function slugify_noaccent_no_utf8decode($text){
		$pattern  = Array("/&eacute;/", "/&egrave;/", "/&ecirc;/", "/&ccedil;/", "/&agrave;/", "/&acirc;/", "/&icirc;/", "/&iuml;/", "/&ucirc;/", "/&ocirc;/", "/&Egrave;/", "/&Eacute;/", "/&Ecirc;/", "/&Euml;/", "/&Igrave;/", "/&Iacute;/", "/&Icirc;/", "/&Iuml;/", "/&Ouml;/", "/&Ugrave;/", "/&Ucirc;/", "/&Uuml;/","/é/", "/è/", "/ê/", "/ç/", "/à/", "/â/", "/î/", "/ï/", "/ù/", "/ô/", "/È/", "/É/", "/Ê/", "/Ë/", "/Ì/", "/Í/", "/Î/", "/Ï/", "/Ö/", "/Ù/", "/Û/", "/Ü/");
		$rep_pat = Array("e", "e", "e", "c", "a", "a", "i", "i", "u", "o", "E", "E", "E", "E", "I", "I", "I", "I", "O", "U", "U", "U","e", "e", "e", "c", "a", "a", "i", "i", "u", "o", "E", "E", "E", "E", "I", "I", "I", "I", "O", "U", "U", "U");
		if ($text == '')
		{
			return '';
		}
		else
		{
			$text = preg_replace($pattern, $rep_pat, $text);
	  }
	  
	  return $text;
	}

	function transformeDate($date, $ajoutJour=0, $ajoutMois=0, $ajoutAnnee=0)
	{	
		if($date == "" OR $date == '00-00-0000')
		{
			$date = "NA";
		}
		else
		{
			$moisAnnee['01']['nom'] = __('Janvier', 'evarisk');
			$moisAnnee['02']['nom'] = __('F&eacute;vrier', 'evarisk');
			$moisAnnee['03']['nom'] = __('Mars', 'evarisk');
			$moisAnnee['04']['nom'] = __('Avril', 'evarisk');
			$moisAnnee['05']['nom'] = __('Mai', 'evarisk');
			$moisAnnee['06']['nom'] = __('Juin', 'evarisk');
			$moisAnnee['07']['nom'] = __('Juillet', 'evarisk');
			$moisAnnee['08']['nom'] = __('Ao&uuml;t', 'evarisk');
			$moisAnnee['09']['nom'] = __('Septembre', 'evarisk');
			$moisAnnee['10']['nom'] = __('Octobre', 'evarisk');
			$moisAnnee['11']['nom'] = __('Novembre', 'evarisk');
			$moisAnnee['12']['nom'] = __('D&eactue;cembre', 'evarisk');
			$elementDate = explode("-",$date); 
			$annee = $elementDate[0];
			$mois = $elementDate[1];
			$jour = $elementDate[2];
			$date = date('Y-m-d', mktime(0,0,0, $elementDate[1], $elementDate[2], $elementDate[0]));
			$elementDate = explode("-",$date); 
			$annee = $elementDate[0];
			$mois = $elementDate[1];
			$jour = $elementDate[2];
			$date = $jour . ' ' . $moisAnnee[$mois]['nom'] . ' ' . $annee;
		}
		return $date;
	}

	function stripAccents($string)
	{
		$newString = str_replace(array('à', 'á', 'â', 'ã', 'ä'), 'a', $string);
		$newString = str_replace(array('À', 'Á', 'Â', 'Ã', 'Ä'), 'A', $newString);
		$newString = str_replace(array('é', 'è', 'ê', 'ë'), 'e', $newString);
		$newString = str_replace(array('É', 'È', 'Ê', 'Ë'), 'E', $newString);
		$newString = str_replace(array('ì', 'í', 'î', 'ï'), 'i', $newString);
		$newString = str_replace(array('Ì', 'Í', 'Î', 'Ï'), 'I', $newString);
		$newString = str_replace(array('ò', 'ó', 'ô', 'ö', 'õ'), 'o', $newString);
		$newString = str_replace(array('Ò', 'Ó', 'Ô', 'Ö', 'Õ'), 'O', $newString);
		$newString = str_replace(array('ù', 'ú', 'û', 'ü'), 'u', $newString);
		$newString = str_replace(array('Ù', 'Ú', 'Û', 'Ü'), 'U', $newString);
		$newString = str_replace(array('ý', 'ÿ'), 'y', $newString);
		$newString = str_replace(array('Ý', 'Ÿ'), 'Y', $newString);
		$newString = str_replace('ç', 'c', $newString);
		$newString = str_replace('Ç', 'C', $newString);
		$newString = str_replace('ñ', 'n', $newString);
		$newString = str_replace('Ñ', 'N', $newString);
		$newString = str_replace('n°', '', $newString);
		$newString = str_replace('°', '_', $newString);
		return $newString;
	}

	function make_recursiv_dir($dir){
		$tab=explode('/',$dir);
		$str='';
		foreach($tab as $k => $v ){
			if((trim($v)!='')){
				$str.='/'.trim($v);
				if( (trim($v)!='..') &&(trim($v)!='.') ){
					if(!is_dir(substr($str,1)) && (!is_file(substr($str,1)) ) ){
						if(!mkdir(substr($str,1), 0755))echo '<hr>erreur mkdir ! '.$str;
						if(!chmod(substr($str,1), 0755))echo '<hr>erreur chmod ! '.$str;				
					}
				}
			}
		}
	}
}