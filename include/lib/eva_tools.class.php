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
		$MyVar = html_entity_decode(str_replace("&rsquo;", "'", htmlentities($MyVar, ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8');

		return $MyVar;
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
		$pattern = Array("�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�");
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
		$rep_pat = Array("�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "'",'"');
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

	function slugify_noaccent($text){
		$pattern  = Array("/&eacute;/", "/&egrave;/", "/&ecirc;/", "/&ccedil;/", "/&agrave;/", "/&acirc;/", "/&icirc;/", "/&iuml;/", "/&ucirc;/", "/&ocirc;/", "/&Egrave;/", "/&Eacute;/", "/&Ecirc;/", "/&Euml;/", "/&Igrave;/", "/&Iacute;/", "/&Icirc;/", "/&Iuml;/", "/&Ouml;/", "/&Ugrave;/", "/&Ucirc;/", "/&Uuml;/","/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/");
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

	function slugify_noaccent_no_utf8decode($text){
		$pattern  = Array("/&eacute;/", "/&egrave;/", "/&ecirc;/", "/&ccedil;/", "/&agrave;/", "/&acirc;/", "/&icirc;/", "/&iuml;/", "/&ucirc;/", "/&ocirc;/", "/&Egrave;/", "/&Eacute;/", "/&Ecirc;/", "/&Euml;/", "/&Igrave;/", "/&Iacute;/", "/&Icirc;/", "/&Iuml;/", "/&Ouml;/", "/&Ugrave;/", "/&Ucirc;/", "/&Uuml;/","/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/", "/�/");
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

	function stripAccents($string)
	{
		$newString = str_replace(array('�', '�', '�', '�', '�'), 'a', $string);
		$newString = str_replace(array('�', '�', '�', '�', '�'), 'A', $newString);
		$newString = str_replace(array('�', '�', '�', '�'), 'e', $newString);
		$newString = str_replace(array('�', '�', '�', '�'), 'E', $newString);
		$newString = str_replace(array('�', '�', '�', '�'), 'i', $newString);
		$newString = str_replace(array('�', '�', '�', '�'), 'I', $newString);
		$newString = str_replace(array('�', '�', '�', '�', '�'), 'o', $newString);
		$newString = str_replace(array('�', '�', '�', '�', '�'), 'O', $newString);
		$newString = str_replace(array('�', '�', '�', '�'), 'u', $newString);
		$newString = str_replace(array('�', '�', '�', '�'), 'U', $newString);
		$newString = str_replace(array('�', '�'), 'y', $newString);
		$newString = str_replace(array('�', '�'), 'Y', $newString);
		$newString = str_replace('�', 'c', $newString);
		$newString = str_replace('�', 'C', $newString);
		$newString = str_replace('�', 'n', $newString);
		$newString = str_replace('�', 'N', $newString);
		$newString = str_replace('n�', '', $newString);
		$newString = str_replace('�', '_', $newString);
		return $newString;
	}

	function make_recursiv_dir($directory)
	{
		$directoryComponent = explode('/',$directory);
		$str = '';
		foreach($directoryComponent as $k => $component)
		{
			if((trim($component) != '') && (trim($component) != '..') && (trim($component) != '.'))
			{
				$str .= '/' . trim($component);
				if(long2ip(ip2long($_SERVER["REMOTE_ADDR"])) == '127.0.0.1')
				{
					if(!is_dir(substr($str,1)) && (!is_file(substr($str,1)) ) )
					{
						mkdir( substr($str,1) );
					}
				}
				else
				{
					if(!is_dir($str) && (!is_file($str) ) )
					{
						mkdir( $str );
					}
				}
			}
		}
		eva_tools::changeAccesAuthorisation($directory);
	}

	function changeAccesAuthorisation($dir)
	{
		$tab=explode('/',$dir);
		$str='';
		foreach($tab as $k => $v )
		{
			if((trim($v)!=''))
			{
				$str.='/'.trim($v);
				if( (trim($v)!='..') &&(trim($v)!='.') )
				{
					if(!is_dir(substr($str,1)) && (!is_file(substr($str,1)) ) )
					{
						@chmod(str_replace('//','/',$str), 0755);
					}
				}
			}
		}
	}

	function copyEntireDirectory($sourceDirectory, $destinationDirectory)
	{
		if(is_dir($sourceDirectory))
		{
			if(!is_dir($destinationDirectory))
			{
				mkdir($destinationDirectory, 0755, true);
			}
			$hdir = opendir($sourceDirectory);
			while($item = readdir($hdir))
			{
				if(is_dir($sourceDirectory . '/' . $item) && ($item != '.') && ($item != '..')  && ($item != '.svn') )
				{
					eva_tools::copyEntireDirectory($sourceDirectory . '/' . $item, $destinationDirectory . '/' . $item);
				}
				elseif(is_file($sourceDirectory . '/' . $item))
				{
					copy($sourceDirectory . '/' . $item, $destinationDirectory . '/' . $item);
				} 
			}
			closedir( $hdir );
		}
	}

}