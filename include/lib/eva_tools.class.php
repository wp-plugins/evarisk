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
		$pattern  = Array("/&eacute;/", "/&egrave;/", "/&ecirc;/", "/&ccedil;/", "/&agrave;/", "/&acirc;/", "/&icirc;/", "/&iuml;/", "/&ucirc;/", "/&ocirc;/", "/&Egrave;/", "/&Eacute;/", "/&Ecirc;/", "/&Euml;/", "/&Igrave;/", "/&Iacute;/", "/&Icirc;/", "/&Iuml;/", "/&Ouml;/", "/&Ugrave;/", "/&Ucirc;/", "/&Uuml;/","/é/", "/è/", "/ê/", "/ç/", "/à/", "/â/", "/î/", "/ï/", "/ù/", "/ô/", "/È/", "/É/", "/Ê/", "/Ë/", "/Ì/", "/Í/", "/Î/", "/Ï/", "/Ö/", "/Ù/", "/Û/", "/Ü/","/'/");
		$rep_pat = Array("e", "e", "e", "c", "a", "a", "i", "i", "u", "o", "E", "E", "E", "E", "I", "I", "I", "I", "O", "U", "U", "U","e", "e", "e", "c", "a", "a", "i", "i", "u", "o", "E", "E", "E", "E", "I", "I", "I", "I", "O", "U", "U", "U", "&#146;");
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
		$pattern  = Array("/&eacute;/", "/&egrave;/", "/&ecirc;/", "/&ccedil;/", "/&agrave;/", "/&acirc;/", "/&icirc;/", "/&iuml;/", "/&ucirc;/", "/&ocirc;/", "/&Egrave;/", "/&Eacute;/", "/&Ecirc;/", "/&Euml;/", "/&Igrave;/", "/&Iacute;/", "/&Icirc;/", "/&Iuml;/", "/&Ouml;/", "/&Ugrave;/", "/&Ucirc;/", "/&Uuml;/","/é/", "/è/", "/ê/", "/ç/", "/à/", "/â/", "/î/", "/ï/", "/ù/", "/ô/", "/È/", "/É/", "/Ê/", "/Ë/", "/Ì/", "/Í/", "/Î/", "/Ï/", "/Ö/", "/Ù/", "/Û/", "/Ü/","/'/",'/"/');
		$rep_pat = Array("e", "e", "e", "c", "a", "a", "i", "i", "u", "o", "E", "E", "E", "E", "I", "I", "I", "I", "O", "U", "U", "U","e", "e", "e", "c", "a", "a", "i", "i", "u", "o", "E", "E", "E", "E", "I", "I", "I", "I", "O", "U", "U", "U", "&#146;","&#34;");
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