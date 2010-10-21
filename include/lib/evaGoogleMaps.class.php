<?php
/**
 * Class who allows to interract with the googleMap API
 *
 * @author Evarisk
 * @version v5.0
 */
include_once(EVA_CONFIG );

class EvaGoogleMaps {
	/**
	 * returns a script that geolocalise the address
	  * @param string $idElementHover Id attribut of the element on witch the script is call.
	  * @param int $postId Id of the element display.
	  * @param string $idLigne1 Id attribut of the first ligne input.
	  * @param string $idLigne2 Id attribut of the second ligne input.
	  * @param string $idCodePostal Id attribut of the postal code input.
	  * @param string $idVille Id attribut of the city input.
	  * @param string $idLatitude Id attribut of the latitude input.
	  * @param string $idLongitude Id attribut of the longitude input.
	  * @return the script for geolocalise the address.
	 */
	static function scriptGeoloc($idElementHover, $postId, $idLigne1, $idLigne2, $idCodePostal, $idVille, $idLatitude, $idLongitude)
	{
		$geolocObligatoire = GEOLOC_OBLIGATOIRE?"true":"false";
		return '
			<script type="text/javascript">
				$(document).ready(function() {	
					geolocPossible = true;
					$(\'#' . $idElementHover . '\').hover(function() {
						geolocPossible = true;
						$(\'#latitude\').removeClass(\'form-input-tip\');
						$(\'#longitude\').removeClass(\'form-input-tip\');
							$(\'#adresseUnite' . $postId . ' input\').each(function(){
								$(this).blur();
							});
							$(\'#adresseUnite' . $postId . ' .form-input-tip\').each(function(){
								if(!($(this).attr("id") == "' . $idLigne2 . '"))
								{
									geolocPossible = false;
								}
							});
						if(!(' . $geolocObligatoire . ' && !geolocPossible))
						{
							if(geolocPossible)
							{
								geocoder = new google.maps.Geocoder();
								geocoder.geocode({"address": $(\'#' . $idLigne1 . '\').val() + " " + $(\'#' . $idLigne2 . '\').val() + ", " + $(\'#' . $idCodePostal . '\').val() + ", " + $(\'#' . $idVille . '\').val()}, function(results, status) {
									$(\'#' . $idLatitude . '\').val(results[0].geometry.location.lat());
									$(\'#' . $idLongitude . '\').val(results[0].geometry.location.lng());
									if($(\'#' . $idLatitude . '\').val() == 0 && $(\'#' . $idLongitude . '\').val() == 0)
									{
										var ligne1 = $(\'#' . $idLigne1 . '\').val();
										ligne1 = ligne1.replace(/[\d]+[\s]?[,]?/, "");
										geocoder.geocode({"address": ligne1 + " " + $(\'#' . $idLigne2 . '\').val() + ", " + $(\'#' . $idCodePostal . '\').val() + ", " + $(\'#' . $idVille . '\').val()}, function(results, status) {
											$(\'#' . $idLatitude . '\').val(results[0].geometry.location.lat());
											$(\'#' . $idLongitude . '\').val(results[0].geometry.location.lng());
										});
									}
								});
							}
						}
					});	
				});	
			</script>';
	}
	
	/**
	  * Returns the script and div for the google map
	  * @param string $idGoogleMapsDiv Id attribut of the div for the google map.
	  * @param array $markers Markers to display. A marker is an array with the keys 'longitude', 'latitude', 'info' and 'image'.
	  * @return the script and div for the google map.
	  */
	static function getGoogleMap($idGoogleMapsDiv, $markers)
	{
		$nbElements = count($markers);
		$sudMax = 180;
		$nordMax = -180;
		$ouestMax = 90;
		$estMax = -90;
		for($indice=0; $indice< $nbElements; $indice++)
		{
			if($markers[$indice]['latitude'] == null OR $markers[$indice]['latitude'] == '')
			{
				unset($markers[$indice]);
			}
		}
		for($indice=0; $indice< $nbElements; $indice++)
		{
			if(isset($markers[$indice]))
			{
				for($indice2=0; $indice2<$nbElements; $indice2++)
				{
					if(isset($markers[$indice]) AND isset($markers[$indice2]))
					{
						if($indice != $indice2 AND $indice < $indice2 AND $markers[$indice]['latitude'] == $markers[$indice2]['latitude'] AND $markers[$indice]['longitude'] == $markers[$indice2]['longitude'])
						{
							$markers[$indice]['info'] = $markers[$indice]['info'] . '<hr />' . $markers[$indice2]['info'];
							unset($markers[$indice2]);
						}
					}
				}
				if($markers[$indice]['latitude'] < $sudMax)
				{
					$sudMax = $markers[$indice]['latitude'];
				}
				if($markers[$indice]['latitude'] > $nordMax)
				{
					$nordMax = $markers[$indice]['latitude'];
				}
				if($markers[$indice]['longitude'] < $ouestMax)
				{
					$ouestMax = $markers[$indice]['longitude'];
				}
				if($markers[$indice]['longitude'] > $estMax)
				{
					$estMax = $markers[$indice]['longitude'];
				}
			}
		}
		if($nordMax == -180)
		{
			$nordMax = 0;
			$sudMax = 0;
			$ouestMax = 0;
			$estMax = 0;
		}
		$scrollWheel = 'false';
		if(ZOOM_SCROLL_MAP)
		{
			$scrollWheel = 'true';
		}
		$googleMap = '
			<script type="text/javascript">
				function initialize() 
				{
					sud = ' . $sudMax . ';
					nord = ' . $nordMax . ';
					ouest = ' . $ouestMax . ';
					est = ' . $estMax . ';
					zoom = 5;
					centerLat = 46.75;
					centerLng = 2.5;					
					if (google.loader.ClientLocation)
					{
						centerLat = google.loader.ClientLocation.latitude;
						centerLng = google.loader.ClientLocation.longitude;
						zoom = 13;
					}
					var myOptions = 
					{
						zoom: zoom,
						center: new google.maps.LatLng(centerLat, centerLng),
						mapTypeId: google.maps.MapTypeId.ROADMAP,
						scrollwheel: ' . $scrollWheel . '
					}
					var map = new google.maps.Map(document.getElementById("' . $idGoogleMapsDiv . '"), myOptions);
					if(sud == nord)
					{
						sud = sud - 0.02;
						nord = nord + 0.02;
					}
					if(est == ouest)
					{
						est = est + 0.02;
						ouest = ouest - 0.02;
					}
					if(!(sud == -0.02 && nord == 0.02 && est == 0.02 && ouest == -0.02))
					{
						var southWest = new google.maps.LatLng(sud,ouest);
						var northEast = new google.maps.LatLng(nord,est);
						var bounds = new google.maps.LatLngBounds(southWest,northEast);
						map.fitBounds(bounds);
					}';
					if(count($markers)>0 AND $markers[0]!=null)
					{
						$i = 0;
						
						foreach($markers as $marker)
						{
							$googleMap = $googleMap . '
							var image = "' . $marker['image'] . '";
							var marker' . $idGoogleMapsDiv . '_' . $i . ' = new google.maps.Marker({
								position: new google.maps.LatLng(' . $marker['latitude'] . ', ' . $marker['longitude'] . '),
								icon: image,
							});
							var infowindow' . $idGoogleMapsDiv . '_' . $i . ' = new google.maps.InfoWindow({
								content: "<div>' . addslashes($marker['info']) . '</div>"
							});
							google.maps.event.addListener(marker' . $idGoogleMapsDiv . '_' . $i . ', \'click\', function() {
								infowindow' . $idGoogleMapsDiv . '_' . $i . '.open(map,marker' . $idGoogleMapsDiv . '_' . $i . ');
							});
							marker' . $idGoogleMapsDiv . '_' . $i . '.setMap(map);';
							$i ++;
						}
					}
					$googleMap = $googleMap . '
				}
				$(document).ready(function(){google.load("maps", "3",  {callback: initialize, other_params:"sensor=false"});});
				
			</script>';
			$googleMap = $googleMap . '<div id="' . $idGoogleMapsDiv . '" style="width: 100%; height: 300px"></div>';
		return $googleMap;
	}
}