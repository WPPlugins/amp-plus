<?php
/*
Plugin Name: AMP+ Plus
Plugin URI: https://www.amp-cloud.de
Description: Accelerated Mobile Pages (AMP) aktivieren auf Deiner Website! - AMP+ installieren, AMP+ aktivieren, fertig! Das AMP-Plugin für WordPress fügt Deiner Website komplett kostenlos und vollkommen automatisiert eine AMP Unterstützung hinzu! Es erstellt automatisch ohne AMP-Kenntnisse Google-konforme AMP-HTML-Versionen Deiner Unterseiten. - Einfach nur "AMP+" installieren, aktivieren und fertig!
Author: Björn Staven
Text Domain: amp-plus
Version: 1.3 - AMP+
Author URI: https://www.facebook.com/AMP-Plus-1147512875294856/
Update Server: https://www.amp-cloud.de
License: GPL
License URI: https://www.gnu.org/licenses/gpl


"AMP+ Plus" is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.
 
"AMP+ Plus" is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with "AMP+". If not, see https://www.gnu.org/licenses/gpl
*/



// Funktionen -----------------------------------------------------------------------------------------------------------------------------


	// Aktuelle URL auslesen ------------------------------------------------------------

		function amp_plus_get_urlaktuellos()
		{
			if(!empty($_SERVER["HTTPS"]))
			{
				$urlprotokoll				= "https://";
			}
			else
			{
				$urlprotokoll				= "http://";
			}
		
		
			$urlaktuell 					= "".$urlprotokoll."".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
			$url_splitt						= parse_url($urlaktuell);
		
		
			if(!empty($url_splitt["user"]) AND !empty($url_splitt["pass"]))
			{
				$urluser					= "".$url_splitt["user"].":".$url_splitt["pass"]."@";
			}
			else
			{
				$urluser					= "";
			}	
				
				
			$urlaktuellos				= "".$url_splitt["scheme"]."://".$urluser."".$url_splitt["host"]."".$url_splitt["path"]."";

			return "".$urlaktuellos."" ;
		}

	
		function amp_plus_get_urlaktuell()
		{
			if(!empty($_SERVER["QUERY_STRING"]))
			{
				$urlquery				= "?".$_SERVER["QUERY_STRING"]."";
			}
			else
			{
				$urlquery				= "".$_SERVER["QUERY_STRING"]."";
			}

				$urlaktuell 			= "".amp_plus_get_urlaktuellos().$urlquery."";
				
			return	trim("".$urlaktuell."");		
		}
	
	
	// AMP-URL-Parameter hinzufügen -----------------------------------------------------
	
		function amp_plus_add_query_amp($url)
		{	
			$url_splitt					= parse_url($url);
			
			if(!empty($url_splitt["query"]))
			{
				$url_parameter			= $url_splitt["query"];
			}
			
			if(!empty($url_parameter))
			{
				// Doppelten AMP-Parameter entfernen ------------------------------------
				
					parse_str($url_parameter, $output);
					unset($output["amp"]);
					unset($output["AMP"]);
					unset($output["aMP"]);
					unset($output["amP"]);
					unset($output["Amp"]);
					unset($output["AMp"]);
					unset($output["AmP"]);
					unset($output["aMp"]);
					
					$url_parameter		= urldecode(http_build_query($output));

					
				// Host und neue Query zusammenführen -----------------------------------
				
					if(!empty($url_parameter))
					{
						$url_neu		= "".amp_plus_get_urlaktuellos()."?".$url_parameter."&amp=1";
					}
					else
					{
						$url_neu		= "".amp_plus_get_urlaktuellos()."?amp=1";
					}	
			}
			else
			{
						$url_neu		= "".amp_plus_get_urlaktuellos()."?amp=1";
			}
			
			return	trim("".$url_neu."");				
		}
	
	
	// URL-Parameter aus URL entfernen --------------------------------------------------
	
		function amp_plus_del_query_amp($url)
		{	
			$url_splitt					= parse_url($url);
			
			if(!empty($url_splitt["query"]))
			{
				$url_parameter			= $url_splitt["query"];
			}
		
			if(!empty($url_parameter))
			{
				parse_str($url_parameter, $output);
				unset($output["amp"]);
				unset($output["AMP"]);
				unset($output["aMP"]);
				unset($output["amP"]);
				unset($output["Amp"]);
				unset($output["AMp"]);
				unset($output["AmP"]);
				unset($output["aMp"]);
				
				$url_query_neu			= urldecode(http_build_query($output));

				if(!empty($url_query_neu))
				{
					$url_neu			= "".amp_plus_get_urlaktuellos()."?".$url_query_neu."";
				}
				else
				{
					$url_neu			= "".amp_plus_get_urlaktuellos()."";
				}				
			}
			else
			{
					$url_neu			= "".amp_plus_get_urlaktuellos()."";
			}
				
			return	trim("".$url_neu."");
		}


	// AMP-Quelltext holen und ausgeben -------------------------------------------------
		
		function amp_plus_plugin_preview() 
		{
			// Get-Parameter Case-Insensitive setzen ------------------------------------
	
				$_GET_lower 		= array_change_key_case($_GET, CASE_LOWER);
				$amp_preview		= isset($_GET_lower['amp']) ? $_GET_lower['amp'] : 'asc';
	
				if($amp_preview == 1)
				{
					// AMP-Cloud AMP-HTML-Adresse ---------------------------------------
			
						$ampcloud_url		= "https://www.amp-cloud.de/amp-plus.php?s=".urlencode(amp_plus_del_query_amp(amp_plus_get_urlaktuell()))."";
				
						ini_set("max_execution_time", 7200);
						
						//echo "<hr>".$ampcloud_url."<hr>".amp_plus_get_urlaktuell()."";
						//exit;
				
						if(function_exists("curl_version"))
						{
							$curl = curl_init();
							curl_setopt($curl, CURLOPT_URL, $ampcloud_url);
							curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
							curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
							curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
							curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
							curl_exec($curl);
						}
						else
						{
							$amp_quelltext	= file_get_contents("".$ampcloud_url."");
							echo "".$amp_quelltext."";
						}
						
						exit;
				}
		}


	// amphtml-Tag setzen ---------------------------------------------------------------
	
		function amp_plus_insert_linkreltag() 
		{					
			if(is_single())
			{	
				// AMP-Cloud AMP-HTML-Adresse -------------------------------------------
	
						$amp_url			= "".amp_plus_add_query_amp(amp_plus_get_urlaktuell())."";
					
						echo "<link rel=\"amphtml\" href=\"".$amp_url."\" />";
			}
		}


	
// WordPress Befehle ----------------------------------------------------------------------------------------------------------------------

	add_action( 'template_redirect', 'amp_plus_plugin_preview', 0 );
	add_action( 'wp_head', 'amp_plus_insert_linkreltag', 1 );
?>