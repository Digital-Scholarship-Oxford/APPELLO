 <?php

/**
* index.php
*
* Processing script for the APPELLO word search Web Service, 
*
* @package    APPELLO
* @author     Henriette Roued <henriette@roued.com>
* @license    AGPL-3.0-or-later https://www.gnu.org/licenses/agpl-3.0.html
* @link       http://roued.com
* @link       https://ora.ox.ac.uk/objects/uuid:9d547661-4dea-4c54-832b-b2f862ec7b25
* @since      File available since 2010
*/

// FILE: Server.php - this file must be on server to run the Web Service - can be downloaded and installed from http://framework.zend.com/
include_once ('Zend/Rest/Server.php');

// ---- GET TABLETS ---- //	
/*
* Function which just returns a list of tablets in this resource	
* NO PARAMETERS
*/
// FUNCTION: get_tablets()
function get_tablets() {
    $source ="Vindolanda Writing Tablets";
	$today_d = date(d);
	$today_m = date(m);
	$today_y = date(Y);
	$today = $today_d . '-' . $today_m . '-' . $today_y;
	$bibl_array = array(
		'Tab.Vindol.I' => 'A.K.Bowman, J.D.Thomas, Vindolanda: the Latin writing- tablets, Britannia Monograph 4. London, 1983 ',
		'Tab.Vindol.II' => 'A.K.Bowman, J.D.Thomas, The Vindolanda Writing-Tablets (Tabulae Vindolandenses II). London, 1994',
		'Tab.Vindol.III' => 'A.K.Bowman, J.D.Thomas, The Vindolanda Writing Tablets (Tabulae Vindolandenses III). London: British Museum Publications, 2003'); 			
	//error_code=0 for Sucesss / error_code=1 for Failure
    $error_code = 0;
	// SET: new DOMDocument
    $doc = new DOMDocument();
    $doc->formatOutput = true;
	// OUTPUT: building a TEI document
	$tei = $doc->createElement("TEI");
	$doc->appendChild($tei);
	// OUTPUT: teiHeader
	$teiheader = $doc->createElement("teiHeader");
	$tei->appendChild($teiheader);
	// OUTPUT: fileDesc
	$fileDesc = $doc->createElement("fileDesc");
	$teiheader->appendChild($fileDesc);
	// OUTPUT: title
	$titleStmt = $doc->createElement("titleStmt");
	$fileDesc->appendChild($titleStmt);
	$title = $doc->createElement("title");
	$title->appendChild($doc->createTextNode('APPELLO method: Get Tablets'));
	$titleStmt->appendChild($title);
	// OUTPUT: publicationStmt
	$publicationStmt = $doc->createElement("publicationStmt");
	$fileDesc->appendChild($publicationStmt);
	$authority = $doc->createElement("authority");
	$authority->appendChild($doc->createTextNode('Made available by Centre for the Study of Ancient Documents, University of Oxford (http://www.csad.ox.ac.uk/) and Henriette Roued-Cunliffe as a part of her D.Phil at the University of Oxford (http://www.roued.com/e-doc/)'));
	$publicationStmt->appendChild($authority);
	$date = $doc->createElement("date");
	$date->appendChild($doc->createTextNode($today));
	$publicationStmt->appendChild($date);
	// OUTPUT: sourceDesc
	$sourceDesc = $doc->createElement("sourceDesc");
	$fileDesc->appendChild($sourceDesc);
	$bibl4 = $doc->createElement("bibl");
	$bibl4->appendChild($doc->createTextNode($bibl_array['Tab.Vindol.II']));
	$sourceDesc->appendChild($bibl4);
	$bibl4->setAttribute("n", 'Tab.Vindol.II');
	$bibl5 = $doc->createElement("bibl");
	$bibl5->appendChild($doc->createTextNode($bibl_array['Tab.Vindol.III']));
	$sourceDesc->appendChild($bibl5);
	$bibl5->setAttribute("n", 'Tab.Vindol.III');
	// OUTPUT: encodingDesc
	$encodingDesc = $doc->createElement("encodingDesc");
	$teiheader->appendChild($encodingDesc);
	$ab5 = $doc->createElement("ab");
	$ab5->appendChild($doc->createTextNode('This file was produced by APPELLO version 0.2'));
	$encodingDesc->appendChild($ab5);
	// OUTPUT: profileDesc
	$profileDesc = $doc->createElement("profileDesc");
	$teiheader->appendChild($profileDesc);
	$textClass = $doc->createElement("textClass");
	$profileDesc->appendChild($textClass);
	$catRef = $doc->createElement("catRef");
	$textClass->appendChild($catRef);
	$catRef->setAttribute("target", 'List of Documents in '.$source);
	// OUTPUT: langUsage
	$langUsage = $doc->createElement("langUsage");
	$profileDesc->appendChild($langUsage);
	$language1 = $doc->createElement("language");
	$language1->appendChild($doc->createTextNode('English'));
	$langUsage->appendChild($language1);
	$language1->setAttribute("ident", 'eng');
	$language2 = $doc->createElement("language");
	$language2->appendChild($doc->createTextNode('Latin'));
	$langUsage->appendChild($language2);
	$language2->setAttribute("ident", 'lat');
	// OUTPUT: Text
	$text = $doc->createElement("text");
	$tei->appendChild($text);
	$body = $doc->createElement("body");
	$text->appendChild($body);
	$list = $doc->createElement("list");
	$body->appendChild($list);
	$list->setAttribute("type", "tablets");
	
	// SET: directory
	$dir="tablets";
	// IF: the directory can be scanned
	if($scans = scandir($dir, 0)){
		// SET: the amount of files in the directory
		$count = count($scans);
		// FOR: each file
		for($i=2;$i<$count;$i++){
			// SET: file address
			$tablet=$dir . '/' . $scans[$i];
			// IF: the file is loaded	
			if($xml_tablet = simplexml_load_file($tablet)){
				//OUTPUT: new APPELLO XML
				$xml_tablet->registerXPathNamespace('tei', 'http://www.tei-c.org/ns/1.0');
				$tabID1=$xml_tablet->xpath('@n');
				$tabID=$tabID1[0];
				$item = $doc->createElement("item");
				$list->appendChild($item);
				$item->setAttribute("n", $tabID);
				$inventory1=$xml_tablet->xpath('//tei:rs[@type="stilusInv"]');
				$inventory=$inventory1[0];
				$category1=$xml_tablet->xpath('//tei:catRef/@target');
				$category=$category1[0];
				$category1=$xml_tablet->xpath('//tei:catRef/@target');
				$category=$category1[0];
				$title_st1=$xml_tablet->xpath('//tei:title/node()');
				$title_st2=$xml_tablet->xpath('//tei:title/*');
				$title_st=$title_st1[0] .$title_st2[0];
				$ref_st='?method=get_tablet&amp;tabletID=' .$tabID;
				$bibls=$xml_tablet->xpath('//tei:bibl[@type="original"]/@n');
				$bibl_id=$bibls[0];
				$bibl_st=$bibl_array[''.$bibl_id.''];
				// IF: there are figures
				if($xml_tablet->xpath('//tei:figure')){
					$ab = $doc->createElement("ab");
					$fig1=$xml_tablet->xpath('//tei:figure/tei:graphic/@url');
					for($k=0;$k<count($fig1);$k++){
						$figure=$fig1[$k];
						$fig = $doc->createElement("figure");
						$ab->appendChild($fig);
						$head = $doc->createElement("head");
						$head->appendChild($doc->createTextNode($figure));
						$fig->appendChild($head);
					} // END: for($k=0;$k<count($fig1);$k++){
					$item->appendChild($ab);
					$ab->setAttribute("type", "images");
				} // END: f($xml_tablet->xpath('//tei:figure')){
				// OUTPUT: dimensions
				$widths=$xml_tablet->xpath('//tei:width');
				$width=$widths[0];
				$widthunits=$xml_tablet->xpath('//tei:width/@unit');
				$widthunit=$widthunits[0];
				$heights=$xml_tablet->xpath('//tei:height');
				$height=$heights[0];
				$heightunits=$xml_tablet->xpath('//tei:height/@unit');
				$heightunit=$heightunits[0];
				// OUTPUT: archaeological context
				$archLocations=$xml_tablet->xpath('//tei:rs[@type="archLocation"]');
				$archLocation=$archLocations[0];
				$archPeriods=$xml_tablet->xpath('//tei:rs[@type="archPeriod"]');
				$archPeriod=$archPeriods[0];
				$plates=$xml_tablet->xpath('//tei:rs[@type="plate"]');
				$plate=$plates[0];
				$cxtDates=$xml_tablet->xpath('//encodingDesc/p/date');
				$cxtDate=$cxtDates[0];
				$cxtNames=$xml_tablet->xpath('//encodingDesc/p/persName');
				$cxtName=$cxtNames[0];
				$cxtEncoded=$xml_tablet->xpath('//encodingDesc');
				if(isSet($cxtEncoded[0])){
					$isCxtEncoded='yes';
				}else{
					$isCxtEncoded='';
				} // END: if(isSet($cxtEncoded[0])){
			}else{
				$error_code=1;
			} // END: if($xml_tablet = simplexml_load_file($tablet)){	
			// OUTPUT: metadata for header
			$ident1 = $doc->createElement("ident");
			$ident1->appendChild($doc->createTextNode($inventory));
			$item->appendChild($ident1);
			$ident1->setAttribute("type", "inventory");			
			$ident2 = $doc->createElement("ident");
			$ident2->appendChild($doc->createTextNode($category));
			$item->appendChild($ident2);
			$ident2->setAttribute("type", "category");			
			$title = $doc->createElement("title");
			$title->appendChild($doc->createTextNode($title_st));
			$item->appendChild($title);			
			$ref = $doc->createElement("ref");
			$ref->appendChild($doc->createTextNode($ref_st));
			$item->appendChild($ref);			
			$bibl = $doc->createElement("bibl");
			$bibl->appendChild($doc->createTextNode($bibl_st));
			$item->appendChild($bibl);
			$bibl->setAttribute("n", $bibl_id);			
			if($width!=''){
				$ab1 = $doc->createElement("ab");
				$item->appendChild($ab1);
				$ab1->setAttribute("type", "measurements");
				$width1 = $doc->createElement("width");
				$width1->appendChild($doc->createTextNode($width));
				$ab1->appendChild($width1);
				$width1->setAttribute("unit", $widthunit);
				$height1 = $doc->createElement("height");
				$height1->appendChild($doc->createTextNode($height));
				$ab1->appendChild($height1);
				$height1->setAttribute("unit", $heightunit);
			}	
			if($plate!=''){
				$ab2 = $doc->createElement("ab");
				$ab2->appendChild($doc->createTextNode($plate));
				$item->appendChild($ab2);
				$ab2->setAttribute("type", "plates");
			}
			if($archLocation!=''){
				$ab3 = $doc->createElement("ab");
				$ab3->appendChild($doc->createTextNode($archLocation));
				$item->appendChild($ab3);
				$ab3->setAttribute("type", "archLocation");
			}
			if($archPeriod!=''){
				$ab4 = $doc->createElement("ab");
				$ab4->appendChild($doc->createTextNode($archPeriod));
				$item->appendChild($ab4);
				$ab4->setAttribute("type", "archPeriod");
			}
   		}
	}else{
		$error_code = 1;	
	} // END: if($scans = scandir($dir, 0)){
    
   //Return status failed, if any error found.
    if ($error_code == 1) {
        $ab7 = $doc->createElement("ab");
		$ab7->appendChild($doc->createTextNode('failed'));
		$encodingDesc->appendChild($ab7);
		$ab7->setAttribute("type", "status");
    }
    // Return status passed, if no error found
    else if ($error_code == 0) {
        $ab7 = $doc->createElement("ab");
		$ab7->appendChild($doc->createTextNode('passed'));
		$encodingDesc->appendChild($ab7);
		$ab7->setAttribute("type", "status");
    }
    return simplexml_load_string($doc->saveXML());
}

// ---- GET TABLET ---- //
/*
* Simple function which just returns the tablet specified in the tablet ID in it's original XML	
* PARAMETERS
* $tabletID: Identified the tablet, which will be returned. 
* The possible tabletID can be identified from the function get_tablets.
*/
// FUNCTION: get_tablet()
function get_tablet($tabletID) {
	// SET: DOMDocument
	$doc = new DOMDocument();
	// SET: directory
	$dir="tablets";
	// SET: tablet file name
	$tablet=$dir . '/' . $tabletID . '.xml';
	// FILE: load tablet XML file
	$doc->load($tablet);
	// OUTPUT: XML file
    return simplexml_load_string($doc->saveXML());
}

// ---- GET WORD ---- //
/* 
* Function which retrieves all the words in the tablets
* OPTIONAL PARAMETER
* $pattern: If a pattern is sent with the function only the words which fit the pattern will be returned.
* Pattern wildcard is *
* Alternative characters can be presented like this: {abc}
*/
// FUNCTION: get_word()
function get_word($pattern = FALSE) {
	// SET: meta for the APPELLO XML file
	$method_title = 'APPELLO method: Get Words';
	$authority1 = 'Made available by Centre for the Study of Ancient Documents, University of Oxford (http://www.csad.ox.ac.uk/) and Henriette Roued-Cunliffe as a part of her D.Phil at the University of Oxford (http://www.roued.com/e-doc/)';
	$today = date(d) . '-' . date(m) . '-' . date(Y);
	$bibl_array = array(
		'Tab.Vindol.I' => 'A.K.Bowman, J.D.Thomas, Vindolanda: the Latin writing- tablets, Britannia Monograph 4. London, 1983 ',
		'Tab.Vindol.II' => 'A.K.Bowman, J.D.Thomas, The Vindolanda Writing-Tablets (Tabulae Vindolandenses II). London, 1994',
		'Tab.Vindol.III' => 'A.K.Bowman, J.D.Thomas, The Vindolanda Writing Tablets (Tabulae Vindolandenses III). London: British Museum Publications, 2003 '); 
	$bibl_ref = array('Tab.Vindol.II', 'Tab.Vindol.III');
	$encoding = 'This file was produced by APPELLO version 0.2';
	$list_of = 'words';
	$source ="Vindolanda Writing Tablets";
	$langs = array(
		'eng' => 'English',
		'lat' => 'Latin',
		'grc' => 'Greek',
		);
	$lang_list = array('eng', 'lat');	
	// IF: the pattern parameter was sent
	if($pattern){
		$search = array(
			'{',
			'}',
			'*', 
		);
		$replace = array(
			'([',
			']{1})',
			'([a-zA-Z]{1})', 
		);
		$pattern = str_replace($search, $replace, $pattern);
		$pattern = '/' . $pattern . '/';
	}
    //error_code=0 for Sucesss / error_code=1 for Failure
    $error_code = 0;
	// SET: DOMDocument
    $doc = new DOMDocument();
    $doc->formatOutput = true;
	// OUTPUT: TEI element
	$tei = $doc->createElement("TEI");
	$doc->appendChild($tei);
	// OUTPUT: teiHeader
	$teiheader = $doc->createElement("teiHeader");
	$tei->appendChild($teiheader);
	// OUTPUT: fileDesc
	$fileDesc = $doc->createElement("fileDesc");
	$teiheader->appendChild($fileDesc);
	// OUTPUT: title
	$titleStmt = $doc->createElement("titleStmt");
	$fileDesc->appendChild($titleStmt);
	$title = $doc->createElement("title");
	$title->appendChild($doc->createTextNode($method_title));
	$titleStmt->appendChild($title);
	// OUTPUT: publicationStmt
	$publicationStmt = $doc->createElement("publicationStmt");
	$fileDesc->appendChild($publicationStmt);
	$authority = $doc->createElement("authority");
	$authority->appendChild($doc->createTextNode($authority1));
	$publicationStmt->appendChild($authority);
	$date = $doc->createElement("date");
	$date->appendChild($doc->createTextNode($today));
	$publicationStmt->appendChild($date);
	// OUTPUT: sourceDesc
	$sourceDesc = $doc->createElement("sourceDesc");
	$fileDesc->appendChild($sourceDesc);
	for($m=0;$m<count($bibl_ref);$m++){
		$bibl = $doc->createElement("bibl");
		$bibl->appendChild($doc->createTextNode($bibl_array[$bibl_ref[$m]]));
		$sourceDesc->appendChild($bibl);
		$bibl->setAttribute("n", $bibl_ref[$m]);
	}
	// OUTPUT: encodingDesc
	$encodingDesc = $doc->createElement("encodingDesc");
	$teiheader->appendChild($encodingDesc);
	$ab5 = $doc->createElement("ab");
	$ab5->appendChild($doc->createTextNode($encoding));
	$encodingDesc->appendChild($ab5);
	// OUTPUT: profileDesc
	$profileDesc = $doc->createElement("profileDesc");
	$teiheader->appendChild($profileDesc);
	$textClass = $doc->createElement("textClass");
	$profileDesc->appendChild($textClass);
	$catRef = $doc->createElement("catRef");
	$textClass->appendChild($catRef);
	$catRef->setAttribute("target", 'List of '.$list_of.' from '.$source);
	// OUTPUT: langUsage
	$langUsage = $doc->createElement("langUsage");
	$profileDesc->appendChild($langUsage);
	for($m=0;$m<count($lang_list);$m++){
		$language = $doc->createElement("language");
		$language->appendChild($doc->createTextNode($langs[$lang_list[$m]]));
		$langUsage->appendChild($language);
		$language->setAttribute("ident", $lang_list[$m]);
	} // END: for($m=0;$m<count($lang_list);$m++){
	// OUTPUT: Text
	$text = $doc->createElement("text");
	$tei->appendChild($text);
	$body = $doc->createElement("body");
	$text->appendChild($body);
	    $list = $doc->createElement("list");
	$body->appendChild($list);
	$list->setAttribute("type", $list_of);
	// SET: tablet directory
	$dir="tablets";
	// IF: there are documents in the directory
	if($scans = scandir($dir, 0)){
		// SET: tablet count
		$count = count($scans);
		// FOR: each tablet
		for($i=2;$i<$count;$i++){
			// SET: file path
			$tablet=$dir . '/' . $scans[$i];	
			// IF: the file is loaded
			if($xml_tablet = simplexml_load_file($tablet)){
				$xml_tablet->registerXPathNamespace('tei', 'http://www.tei-c.org/ns/1.0');
				// SET: tablet number
				$idents=$xml_tablet->xpath('//tei:rs[@n="1"]');
				foreach($idents[0]->attributes() as $a => $b) {
					$$a=$b;
					$tabletNum=$key;
				} // END: of foreach($idents[0]->attributes() as $a => $b)
				// SET: word
				$words=$xml_tablet->xpath('//tei:w[@n]');
				// FOR: each words tag
				foreach($words as $word){
					unset($lemma);
					unset($n);
					unset($subtype);
					unset($rend);
					unset($rend1);
					$match = 'no';
					//For each attribute in the tag
					foreach($word[0]->attributes() as $a => $b) {
						// Put together values
						$$a=$b;
					} // END: of foreach($word[0]->attributes() as $a => $b)
					$lem = "" . $lemma[0] . "";
					$n1 = "" . $n[0] . "";
					$type1 = "" . $subtype[0] . "";
					$tnr = "" . $tabletNum[0] . "";
					$rend1 = "" . $rend[0] . "";
					// Putting together the array
					$l[$lem][0] = $lem;
					$l[$lem][1][$type1][0] = $type1;
					$l[$lem][1][$type1][1][$tnr][0] = $tnr;
					$l[$lem][1][$type1][1][$tnr][1] = $n1;
					$l[$lem][1][$type1][1][$tnr][2][] = $rend1;
					$l[$lem][5] = 'word';
				} // END: of foreach($words as $word)
			} // END: of if($xml_tablet = simplexml_load_file($tablet))
		} // END: of for($i=3;$i<$count;$i++)
				// FOR: each lemma
				foreach ($l as $l1){
					// IF: the pattern fits and pattern is turned on.
					if($pattern){
						// IF: pattern matches lemma
						if(preg_match($pattern, $l1[0])){
							$match = 'yes';
							$matchType1 = 'yes';
						}else{
							$match = 'no';
							foreach ($l1[1] as $l2){
								if(preg_match($pattern, $l2[0])){
									$match = 'yes';
									$matchType1 = 'no';	
								} // END: of if(preg_match($pattern, $l2[0]))
							} // END: of foreach ($l1[1] as $l2)
						} // END: of if(preg_match($pattern, $l1[0]))
					}else{
						$match = 'yes';
					} // END: of if($pattern)
					// IF: match is yes
					if($match == "yes"){					
						$item = $doc->createElement("item");
						$list->appendChild($item);
						$item->setAttribute("n", $l1[0]);
						$ident1 = $doc->createElement("ident");
						$ident1->appendChild($doc->createTextNode($l1[0]));
						$item->appendChild($ident1);
						$ident1->setAttribute("type", 'lemma');
						foreach ($l1[1] as $l2){
							//IF: type match is yes
							if($matchType1=='yes'){
								$matchType='yes';
							}else{
								// IF: pattern is found in type
								if($pattern){
									if(preg_match($pattern, $l2[0])){
										$matchType = 'yes';
									}else{
										$matchType = 'no';
									} // END: of if(preg_match($pattern, $l2[0]))
								}else{
									$matchType = 'yes';
								} // END: of if($pattern)
							} // END: of if($matchType1=='yes')
							// IF: empty
							if($l2[0]!=''){
								// IF: pattern matches type
								if($matchType == 'yes'){
									// OUTPUT: list
									$types_element = $doc->createElement("list");
									$item->appendChild($types_element);
									$types_element->setAttribute("type", "lemma_subtype");
									$type_element = $doc->createElement("item");
									$types_element->appendChild($type_element);
									$type_element->setAttribute("n", $l2[0]);
									$typename_element = $doc->createElement("ident");
									$typename_element->appendChild($doc->createTextNode($l2[0]));
									$type_element->appendChild($typename_element);
									$typename_element->setAttribute("type", "lemma_subtype");
									// OUTPUT: Tablet list
									$tablets_element = $doc->createElement("list");
									$type_element->appendChild($tablets_element);
									$tablets_element->setAttribute("type", "tablets");
									// FOR: each tablet
									foreach ($l2[1] as $l3){
										// OUTPUT: tablet items
										$tablet_element = $doc->createElement("item");
										$tablets_element->appendChild($tablet_element);
										$tablet_element->setAttribute("n", $l3[0]);
										$tabletsnumber_element = $doc->createElement("ident");
										$tabletsnumber_element->appendChild($doc->createTextNode($l3[0]));
										$tablet_element->appendChild($tabletsnumber_element);
										$tabletsnumber_element->setAttribute("type", "tabletID");
										$number_element = $doc->createElement("num");
										$number_element->appendChild($doc->createTextNode($l3[1]));
										$tablet_element->appendChild($number_element);
										$number_element->setAttribute("type", "amount");	
										// OUTPUT: rendered word									
										if($l3[2][0]!=''){
											for($h=0;$h<$l3[1];$h++){
												$render_el = $doc->createElement("w");
												$render_el->appendChild($doc->createTextNode($l3[2][$h]));
												$tablet_element->appendChild($render_el);
												$render_el->setAttribute("n", $h+1);
											} // END: for($h=0;$h<$l3[1];$h++){
										} // END: if($l3[2][0]!=''){
									} // END: foreach ($l2[1] as $l3)
								} // END: if($matchType == 'yes')
							}else{
								// OUTPUT: tablet list
								$tablets_element = $doc->createElement("list");
								$item->appendChild($tablets_element);
								$tablets_element->setAttribute("type", "tablets");
								// FOR: each tablet	
								foreach ($l2[1] as $l3){
									// OUTPUT: tablet items
									$tablet_element = $doc->createElement("item");
									$tablets_element->appendChild($tablet_element);
									$tablet_element->setAttribute("n", $l3[0]);
									$tabletsnumber_element = $doc->createElement("ident");
									$tabletsnumber_element->appendChild($doc->createTextNode($l3[0]));
									$tablet_element->appendChild($tabletsnumber_element);
									$tabletsnumber_element->setAttribute("type", "tabletID");										
									$number_element = $doc->createElement("num");
									$number_element->appendChild($doc->createTextNode($l3[1]));
									$tablet_element->appendChild($number_element);
									$number_element->setAttribute("type", "amount");									
									// OUTPUT: rendered word	
									if($l3[2][0]!=''){
										for($h=0;$h<$l3[1];$h++){
											$render_el = $doc->createElement("w");
											$render_el->appendChild($doc->createTextNode($l3[2][$h]));
											$tablet_element->appendChild($render_el);
											$render_el->setAttribute("n", $h+1);
										} // END: for($h=0;$h<$l3[1];$h++){
									} // END: if($l3[2][0]!=''){
								} // END: of foreach ($l2[1] as $l3)
							} // END: of if($l2[0]!='')
						} // END: of foreach ($l1[1] as $l2)
					} // END: of if($match == "yes")
				} // END: og foreach ($l as $l1)     		
			} // END: of if($scans = scandir($dir, 0))
   //Return status failed, if any error found.
    if ($error_code == 1) {
        $ab7 = $doc->createElement("ab");
		$ab7->appendChild($doc->createTextNode('failed'));
		$encodingDesc->appendChild($ab7);
		$ab7->setAttribute("type", "status");
    }
    // Return status passed, if no error found
    else if ($error_code == 0) {
        $ab7 = $doc->createElement("ab");
		$ab7->appendChild($doc->createTextNode('passed'));
		$encodingDesc->appendChild($ab7);
		$ab7->setAttribute("type", "status");
    }
    return simplexml_load_string($doc->saveXML());
}

// ---- GET RS ---- //
/* 
* Function which retrieves all the calender instances in the tablets
* OPTIONAL PARAMETER
* $pattern: If a pattern is sent with the function only the calender instance, which fit the pattern will be returned.
* Pattern wildcard is *
* Alternative characters can be presented like this: {abc}
*/
// FUNCTION: get_rs
function get_rs($rs_cat, $pattern = FALSE) {
	// SET: rs category
	$cat = $rs_cat;
	$rs_array = array(
		'dates' => 'julian_calendar',
		'military' => 'militaryOfficial',
		'consul' => 'consul',
	);
	// SET: meta for header
	$method_title = 'APPELLO method: Get ' . $cat;
	$authority1 = 'Made available by Centre for the Study of Ancient Documents, University of Oxford (http://www.csad.ox.ac.uk/) and Henriette Roued-Cunliffe as a part of her D.Phil at the University of Oxford (http://www.roued.com/e-doc/)';
	$today = date(d) . '-' . date(m) . '-' . date(Y);
	$bibl_array = array(
		'Tab.Vindol.I' => 'A.K.Bowman, J.D.Thomas, Vindolanda: the Latin writing- tablets, Britannia Monograph 4. London, 1983 ',
		'Tab.Vindol.II' => 'A.K.Bowman, J.D.Thomas, The Vindolanda Writing-Tablets (Tabulae Vindolandenses II). London, 1994',
		'Tab.Vindol.III' => 'A.K.Bowman, J.D.Thomas, The Vindolanda Writing Tablets (Tabulae Vindolandenses III). London: British Museum Publications, 2003 '); 
	$bibl_ref = array('Tab.Vindol.II', 'Tab.Vindol.III');
	$encoding = 'This file was produced by APPELLO version 0.2';
	$list_of = $cat;
	$source ="Vindolanda Writing Tablets";
	$langs = array(
		'eng' => 'English',
		'lat' => 'Latin',
		'grc' => 'Greek',
		);
	$lang_list = array('eng', 'lat');
	// SET: tablet number	
	$tabID_path = '//tei:rs[@n="1"]';
	// SET: attribute for tablet ID
	$tabID_attr ='key';
	// SET: top label
		$topID_array = array(
		'dates' => 'month',
		'military' => 'militaryOfficial',
		'consul' => 'consul',
	);
	// SET: bottom label
	$bottomID_array = array(
		'dates' => 'date',
	);
	// SET: xPath to tag
	$tag_path = '//tei:rs[@type="'.$rs_array[$rs_cat].'"]';
	// SET: local tablets directory
	$dir="tablets";	
	// SET: Attributes for tags
	$attr_top = 'nymRef';
	$attr_bottom = 'rend';
	$attr_nr = 'n';
	$attr_key = 'key';
	// IF: pattern
	if($pattern){
		$search = array(
			'{',
			'}',
			'*', 
		);
		$replace = array(
			'([',
			']{1})',
			'([a-zA-Z]{1})', 
		);
		$pattern = str_replace($search, $replace, $pattern);
		$pattern = '/' . $pattern . '/';
	}
    //SET: error_code=0 for Sucesss / error_code=1 for Failure
    $error_code = 0;
	// SET: DOMDocument
    $doc = new DOMDocument();
    $doc->formatOutput = true;
	// OUTPUT: TEI document
	$tei = $doc->createElement("TEI");
	$doc->appendChild($tei);
	// OUTPUT: teiHeader
	$teiheader = $doc->createElement("teiHeader");
	$tei->appendChild($teiheader);
	// OUTPUT: fileDesc
	$fileDesc = $doc->createElement("fileDesc");
	$teiheader->appendChild($fileDesc);
	// OUTPUT: title
	$titleStmt = $doc->createElement("titleStmt");
	$fileDesc->appendChild($titleStmt);
	$title = $doc->createElement("title");
	$title->appendChild($doc->createTextNode($method_title));
	$titleStmt->appendChild($title);
	// OUTPUT: publicationStmt
	$publicationStmt = $doc->createElement("publicationStmt");
	$fileDesc->appendChild($publicationStmt);
	$authority = $doc->createElement("authority");
	$authority->appendChild($doc->createTextNode($authority1));
	$publicationStmt->appendChild($authority);
	$date = $doc->createElement("date");
	$date->appendChild($doc->createTextNode($today));
	$publicationStmt->appendChild($date);
	// OUTPUT: sourceDesc
	$sourceDesc = $doc->createElement("sourceDesc");
	$fileDesc->appendChild($sourceDesc);
	for($m=0;$m<count($bibl_ref);$m++){
		$bibl = $doc->createElement("bibl");
		$bibl->appendChild($doc->createTextNode($bibl_array[$bibl_ref[$m]]));
		$sourceDesc->appendChild($bibl);
		$bibl->setAttribute("n", $bibl_ref[$m]);
	} // END: for($m=0;$m<count($bibl_ref);$m++){
	// OUTPUT: encodingDesc
	$encodingDesc = $doc->createElement("encodingDesc");
	$teiheader->appendChild($encodingDesc);
	$ab5 = $doc->createElement("ab");
	$ab5->appendChild($doc->createTextNode($encoding));
	$encodingDesc->appendChild($ab5);
	// OUTPUT: profileDesc
	$profileDesc = $doc->createElement("profileDesc");
	$teiheader->appendChild($profileDesc);
	$textClass = $doc->createElement("textClass");
	$profileDesc->appendChild($textClass);
	$catRef = $doc->createElement("catRef");
	$textClass->appendChild($catRef);
	$catRef->setAttribute("target", 'List of '.$list_of.' from '.$source);
	// OUTPUT: langUsage
	$langUsage = $doc->createElement("langUsage");
	$profileDesc->appendChild($langUsage);
	for($m=0;$m<count($lang_list);$m++){
		$language = $doc->createElement("language");
		$language->appendChild($doc->createTextNode($langs[$lang_list[$m]]));
		$langUsage->appendChild($language);
		$language->setAttribute("ident", $lang_list[$m]);
	} // END: for($m=0;$m<count($lang_list);$m++){
	// OUTPUT: Text
	$text = $doc->createElement("text");
	$tei->appendChild($text);
	// OUTPUT: Body
	$body = $doc->createElement("body");
	$text->appendChild($body);
	// OUTPUT: List
	$list = $doc->createElement("list");
	$body->appendChild($list);
	// OUTPUT: Type of list
	$list->setAttribute("type", $list_of);
	// IF: Scan tablets directory
	if($scans = scandir($dir, 0)){
		// FOR: tablets
		for($i=2;$i<count($scans);$i++){
			$tablet=$dir . '/' . $scans[$i];	
			// IF: tablet loads
			if($xml_tablet = simplexml_load_file($tablet)){
			$xml_tablet->registerXPathNamespace('tei', 'http://www.tei-c.org/ns/1.0');
				// SET: tablet nr
				$idents=$xml_tablet->xpath($tabID_path);
				foreach($idents[0]->attributes() as $a => $b) {
					$$a=$b;
					$tabletNum=$$tabID_attr;
				} // END: of foreach($idents[0]->attributes() as $a => $b)
				// OUTPUT: tags
				$tags=$xml_tablet->xpath($tag_path);
				// FOR: each tag
				foreach($tags as $tag){
					// UNSET: attributes
					unset($$attr_top);
					unset($$attr_bottom);
					unset($$attr_nr);
					unset($$attr_key);
					// SET: pattern matches to 'no' for default
					$match = 'no';
					$matchType = 'no';
					// FOR: each attribute in the tag
					foreach($tag[0]->attributes() as $a => $b) {
						$$a=$b;
					} // END: of foreach($tag[0]->attributes() as $a => $b)
					$top1 = $$attr_top;
					$bottom1 = $$attr_bottom;
					$n1 = $$attr_nr;
					$id1 = $$attr_key;
					$bottom = "" . $bottom1[0] . "";
					$nr = "" . $n1[0] . "";
					$id = "" . $id1[0] . "";
					if($top1[0]!=''){
					$top = "" . $top1[0] . "";
					}else{
					$top = "" . $id1[0] . "";
					}
					$tnr = "" . $tabletNum[0] . "";
					$l[$top][0] = $top;
					$l[$top][2] = $id;;
					$l[$top][1][$bottom][0] = $bottom;
					$l[$top][1][$bottom][2] = $id;
					$l[$top][1][$bottom][1][$tnr][0] = $tnr;
					$l[$top][1][$bottom][1][$tnr][1] = $nr;
					$l[$top][5] = $rs_cat;
				} // END: of foreach($tags as $tag)
			} // END: of if($xml_tablet = simplexml_load_file($tablet))
		} // END: of for($i=2;$i<$count;$i++)		
		// FOR: each item in the array
		foreach ($l as $l1){
			// IF: the pattern fits and pattern is turned on.
			if($pattern){
				// IF: pattern matches month
				if(preg_match($pattern, $l1[2])){
					$match = 'yes';
					$matchType1 = 'yes';
				}else{
					$match = 'no';
					foreach ($l1[1] as $l2){
						if(preg_match($pattern, $l2[2])){
							$match = 'yes';
							$matchType1 = 'no';	
						} // END: of if(preg_match($pattern, $l2[0]))
					} // END: of foreach ($l1[1] as $l2)
				} // END: of if(preg_match($pattern, $l1[0]))
			}else{
				$match = 'yes';
			} // END: of if($pattern)
			if($match == "yes"){
				$item = $doc->createElement("item");
				$list->appendChild($item);
				$item->setAttribute("n", $l1[0]);
				$ident1 = $doc->createElement("ident");
				$ident1->appendChild($doc->createTextNode($l1[0]));
				$item->appendChild($ident1);
				$ident1->setAttribute("type", $topID_array[$rs_cat]);
				foreach ($l1[1] as $l2){
					if($matchType1=='yes'){
						$matchType='yes';
					}else{
						if($pattern){
							if(preg_match($pattern, $l2[2])){
								$matchType = 'yes';
							}else{
								$matchType = 'no';
							} // END: of if(preg_match($pattern, $l2[0]))
						}else{
							$matchType = 'yes';
						} // END: of if($pattern)
					} // END: of if($matchType1=='yes')
					if($l2[0]!=''){	
						if($matchType == 'yes'){
							$types_element = $doc->createElement("list");
							$item->appendChild($types_element);
							$types_element->setAttribute("type", $bottomID_array[$rs_cat]);
							$type_element = $doc->createElement("item");
							$types_element->appendChild($type_element);
							$type_element->setAttribute("n", $l2[0]);
							$type_element->setAttribute("xml:id", $l2[2]);
							$typename_element = $doc->createElement("ident");
							$typename_element->appendChild($doc->createTextNode($l2[0]));
							$type_element->appendChild($typename_element);
							$typename_element->setAttribute("type", $bottomID_array[$rs_cat]);
							$tablets_element = $doc->createElement("list");
							$type_element->appendChild($tablets_element);
							$tablets_element->setAttribute("type", "tablets");
							foreach ($l2[1] as $l3){
								// OUTPUT: Tablet number
								$tablet_element = $doc->createElement("item");
								$tablets_element->appendChild($tablet_element);
								$tablet_element->setAttribute("n", $l3[0]);
								// OUTPUT: TabletNumber
								$tabletsnumber_element = $doc->createElement("ident");
								$tabletsnumber_element->appendChild($doc->createTextNode($l3[0]));
								$tablet_element->appendChild($tabletsnumber_element);
								$tabletsnumber_element->setAttribute("type", "tabletID");
								// OUTPUT: Number
								$number_element = $doc->createElement("num");
								$number_element->appendChild($doc->createTextNode($l3[1]));
								$tablet_element->appendChild($number_element);
								$number_element->setAttribute("type", "amount");
							}// END: of foreach ($l2[1] as $l3)
						} // END: of if($matchType == 'yes') 
					}else{
						$item->setAttribute("xml:id", $l1[2]);
						// OUTPUT: Tablets
						$tablets_element = $doc->createElement("list");
						$item->appendChild($tablets_element);
						$tablets_element->setAttribute("type", "tablets");
						foreach ($l2[1] as $l3){
							// OUTPUT: Tablet number
							$tablet_element = $doc->createElement("item");
							$tablets_element->appendChild($tablet_element);
							$tablet_element->setAttribute("n", $l3[0]);
							// OUTPUT: TabletNumber
							$tabletsnumber_element = $doc->createElement("ident");
							$tabletsnumber_element->appendChild($doc->createTextNode($l3[0]));
							$tablet_element->appendChild($tabletsnumber_element);
							$tabletsnumber_element->setAttribute("type", "tabletID");
							// OUTPUT: Number
							$number_element = $doc->createElement("num");
							$number_element->appendChild($doc->createTextNode($l3[1]));
							$tablet_element->appendChild($number_element);
							$number_element->setAttribute("type", "amount");
						}// END: of foreach ($l2[1] as $l3) 
					}// END: of if($l2[0]!='')
				}// END: of foreach ($l1[1] as $l2)
			}// END: of if($match == "yes")
		} // END: of foreach ($l as $l1)     		
	} // END: of if($scans = scandir($dir, 0))
   //Return status failed, if any error found.
    if ($error_code == 1) {
        $ab7 = $doc->createElement("ab");
		$ab7->appendChild($doc->createTextNode('failed'));
		$encodingDesc->appendChild($ab7);
		$ab7->setAttribute("type", "status");
    }
    // Return status passed, if no error found
    else if ($error_code == 0) {
        $ab7 = $doc->createElement("ab");
		$ab7->appendChild($doc->createTextNode('passed'));
		$encodingDesc->appendChild($ab7);
		$ab7->setAttribute("type", "status");
    }
    return simplexml_load_string($doc->saveXML());
} // END: function

// ---- GET PERSON ---- //
/* 
* Function which retrieves all the persons in the tablets
* OPTIONAL PARAMETER
* $pattern: If a pattern is sent with the function only the persons which fit the pattern will be returned.
* Pattern wildcard is *
* Alternative characters can be presented like this: {abc}
*/
// FUNCTION: get_person
function get_person($pattern=FALSE) {
	// SET: category to person
	$cat = 'person';
	// SET: metadata for header
	$method_title = 'APPELLO method: Get ' . $cat;
	$authority1 = 'Made available by Centre for the Study of Ancient Documents, University of Oxford (http://www.csad.ox.ac.uk/) and Henriette Roued-Cunliffe as a part of her D.Phil at the University of Oxford (http://www.roued.com/e-doc/)';
	$today = date(d) . '-' . date(m) . '-' . date(Y);
	$bibl_array = array(
		'Tab.Vindol.I' => 'A.K.Bowman, J.D.Thomas, Vindolanda: the Latin writing- tablets, Britannia Monograph 4. London, 1983 ',
		'Tab.Vindol.II' => 'A.K.Bowman, J.D.Thomas, The Vindolanda Writing-Tablets (Tabulae Vindolandenses II). London, 1994',
		'Tab.Vindol.III' => 'A.K.Bowman, J.D.Thomas, The Vindolanda Writing Tablets (Tabulae Vindolandenses III). London: British Museum Publications, 2003 '); 
	$bibl_ref = array('Tab.Vindol.II', 'Tab.Vindol.III');
	$encoding = 'This file was produced by APPELLO version 0.2';
	$list_of = $cat;
	$source ="Vindolanda Writing Tablets";
	$langs = array(
		'eng' => 'English',
		'lat' => 'Latin',
		'grc' => 'Greek',
		);
	// SET: Array of languages in this document
	$lang_list = array('eng', 'lat');
	// SET: xPath for tablet number	
	$tabID_path = '//tei:rs[@n="1"]';
	// SET: attribute for tablet ID
	$tabID_attr ='key';
	// SET: top label
	$topID_array = array(
	'person' => 'person',
	);
	// SET: bottom label
	$bottomID_array = array(
	);
	// SET: xPath to tag
	$tag_path = '//tei:persName[@nymRef]';
	// SET: local tablets directory
	$dir="tablets";	
	// SET: Attributes for tags
	$attr_top = 'nymRef';
	$attr_nr = 'n';
	$attr_key = 'key';
	$attr_extra = 'rend';
	$extra_array = array(
	'person' => 'profession',
	);
	$attr_orig = 'rendition';
	// IF: there is a pattern - clean it
	if($pattern){
		$search = array(
			'{',
			'}',
			'*', 
		);
		$replace = array(
			'([',
			']{1})',
			'([a-zA-Z]{1})', 
		);
	$pattern = str_replace($search, $replace, $pattern);
	$pattern = '/' . $pattern . '/';
	}
    //SET: error_code=0 for Sucesss / error_code=1 for Failure
    $error_code = 0;
	// SET: DOMDocument
    $doc = new DOMDocument();
    $doc->formatOutput = true;
	// OUTPUT: TEI document
	$tei = $doc->createElement("TEI");
	$doc->appendChild($tei);
	// OUTPUT: teiHeader
	$teiheader = $doc->createElement("teiHeader");
	$tei->appendChild($teiheader);
	// OUTPUT: fileDesc
	$fileDesc = $doc->createElement("fileDesc");
	$teiheader->appendChild($fileDesc);
	// OUTPUT: title
	$titleStmt = $doc->createElement("titleStmt");
	$fileDesc->appendChild($titleStmt);
	$title = $doc->createElement("title");
	$title->appendChild($doc->createTextNode($method_title));
	$titleStmt->appendChild($title);
	// OUTPUT: publicationStmt
	$publicationStmt = $doc->createElement("publicationStmt");
	$fileDesc->appendChild($publicationStmt);
	$authority = $doc->createElement("authority");
	$authority->appendChild($doc->createTextNode($authority1));
	$publicationStmt->appendChild($authority);
	$date = $doc->createElement("date");
	$date->appendChild($doc->createTextNode($today));
	$publicationStmt->appendChild($date);
	// OUTPUT: sourceDesc
	$sourceDesc = $doc->createElement("sourceDesc");
	$fileDesc->appendChild($sourceDesc);
	for($m=0;$m<count($bibl_ref);$m++){
		$bibl = $doc->createElement("bibl");
		$bibl->appendChild($doc->createTextNode($bibl_array[$bibl_ref[$m]]));
		$sourceDesc->appendChild($bibl);
		$bibl->setAttribute("n", $bibl_ref[$m]);
	}
	// OUTPUT: encodingDesc
	$encodingDesc = $doc->createElement("encodingDesc");
	$teiheader->appendChild($encodingDesc);
	$ab5 = $doc->createElement("ab");
	$ab5->appendChild($doc->createTextNode($encoding));
	$encodingDesc->appendChild($ab5);
	// OUTPUT: profileDesc
	$profileDesc = $doc->createElement("profileDesc");
	$teiheader->appendChild($profileDesc);
	$textClass = $doc->createElement("textClass");
	$profileDesc->appendChild($textClass);
	$catRef = $doc->createElement("catRef");
	$textClass->appendChild($catRef);
	$catRef->setAttribute("target", 'List of '.$list_of.' from '.$source);
	// OUTPUT: langUsage
	$langUsage = $doc->createElement("langUsage");
	$profileDesc->appendChild($langUsage);
	for($m=0;$m<count($lang_list);$m++){
		$language = $doc->createElement("language");
		$language->appendChild($doc->createTextNode($langs[$lang_list[$m]]));
		$langUsage->appendChild($language);
		$language->setAttribute("ident", $lang_list[$m]);
	}
	// OUTPUT: Text
	$text = $doc->createElement("text");
	$tei->appendChild($text);
	// OUTPUT: Body
	$body = $doc->createElement("body");
	$text->appendChild($body);
	// OUTPUT: List
	$list = $doc->createElement("list");
	$body->appendChild($list);
	// OUTPUT: Type of list
	$list->setAttribute("type", $list_of);
	// IF: Scan tablets directory
	if($scans = scandir($dir, 0)){
		// FOR: each tablet
		for($i=2;$i<count($scans);$i++){
			// SET: tablet connection
			$tablet=$dir . '/' . $scans[$i];	
			// IF: connected
			if($xml_tablet = simplexml_load_file($tablet)){
				$xml_tablet->registerXPathNamespace('tei', 'http://www.tei-c.org/ns/1.0');
				$idents=$xml_tablet->xpath($tabID_path);
				// FOR: each tablet number
				foreach($idents[0]->attributes() as $a => $b) {
					$$a=$b;
					$tabletNum=$$tabID_attr;
				} // END: of foreach($idents[0]->attributes() as $a => $b)
				// SET: tags
				$tags=$xml_tablet->xpath($tag_path);
				// FOR: each tag
				foreach($tags as $tag){
					// UNSET: attributes
					unset($$attr_top);
					unset($$attr_bottom);
					unset($$attr_nr);
					unset($$attr_key);
					unset($$attr_extra);
					// SET: pattern matches to 'no' for default
					$match = 'no';
					$matchType = 'no';
					// FOR: each attribute in the tag
					foreach($tag[0]->attributes() as $a => $b) {
						// Make the values
						$$a=$b;
					} // END: of foreach($tag[0]->attributes() as $a => $b)
					// SET: attribute settings
					$top1 = $$attr_top;
					$bottom1 = $$attr_bottom;
					$n1 = $$attr_nr;
					$id1 = $$attr_key;
					$extra1 = $$attr_extra;
					$orig1 = $$attr_orig;
					$bottom = "" . $bottom1[0] . "";
					$nr = "" . $n1[0] . "";
					$id = "" . $id1[0] . "";
					// IF: top attribute
					if($top1[0]!=''){
						$top = "" . $top1[0] . "";
					}else{
						$top = "" . $id1[0] . "";
					} // END: if($top1[0]!=''){
					$tnr = "" . $tabletNum[0] . "";
					// IF: attribute extra
					if($extra1[0]!=''){
						$extra = "" . $extra1[0] . "";
					}else{
						$extra='';
					} // END: if($extra1[0]!=''){
					// IF: original attribute
					if($orig1[0]!=''){
						$orig = "" . $orig1[0] . "";
					}else{
						$orig='';
					} // END: if($orig1[0]!=''){
					// SET: attribute array
					$l[$top][0] = $top;
					$l[$top][2] = strtolower($id);
					$l[$top][3] = strtolower($extra);
					$l[$top][1][$bottom][0] = $bottom;
					$l[$top][1][$bottom][2] = $id;
					$l[$top][1][$bottom][1][$tnr][0] = $tnr;
					$l[$top][1][$bottom][1][$tnr][1] = $nr;
					$l[$top][1][$bottom][1][$tnr][2][] = $orig;
					$l[$top][5] = $cat;
				} // END: of foreach($tags as $tag)
			} // END: of if($xml_tablet = simplexml_load_file($tablet))
		} // END: of for($i=3;$i<$count;$i++)
		// FOR: each person
		foreach ($l as $l1){
			// IF: the pattern fits and pattern is turned on.
			if($pattern){
				// IF: pattern matches person
				if(preg_match($pattern, $l1[2])){
					$match = 'yes';
					$matchType1 = 'yes';
				}else{
					$match = 'no';
					// FOR: each pattern in person
					foreach ($l1[1] as $l2){
						if(preg_match($pattern, $l2[2])){
							$match = 'yes';
							$matchType1 = 'no';	
						} // END: of if(preg_match($pattern, $l2[0]))
					} // END: of foreach ($l1[1] as $l2)
				} // END: of if(preg_match($pattern, $l1[0]))
			}else{
				$match = 'yes';
			} // END: of if($pattern)
			// IF: person matches
			if($match == "yes"){
				$item = $doc->createElement("item");
				$list->appendChild($item);
				$item->setAttribute("n", $l1[0]);
				$ident1 = $doc->createElement("ident");
				$ident1->appendChild($doc->createTextNode($l1[0]));
				$item->appendChild($ident1);
				$ident1->setAttribute("type", $topID_array[$cat]);				
				// IF: extra Values
				if($l1[3]){
					$ident2 = $doc->createElement("ident");
					$ident2->appendChild($doc->createTextNode($l1[3]));
					$item->appendChild($ident2);
					$ident2->setAttribute("type", $extra_array[$cat]);
				}
				// FOR: each type match
				foreach ($l1[1] as $l2){
					if($matchType1=='yes'){
						$matchType='yes';
					}else{
						if($pattern){
							// IF: pattern matches type
							if(preg_match($pattern, $l2[2])){
								$matchType = 'yes';
							}else{
								$matchType = 'no';
							} // END: of if(preg_match($pattern, $l2[0]))
						}else{
							$matchType = 'yes';
						} // END: of if($pattern)
					} // END: of if($matchType1=='yes')
					// IF: there is a type
					if($l2[0]!=''){	
						// IF: type is a match
						if($matchType == 'yes'){
							// OUTPUT: list
							$types_element = $doc->createElement("list");
							$item->appendChild($types_element);
							$types_element->setAttribute("type", $bottomID_array[$cat]);
							$type_element = $doc->createElement("item");
							$types_element->appendChild($type_element);
							$type_element->setAttribute("n", $l2[0]);
							$type_element->setAttribute("xml:id", $l2[2]);
							$typename_element = $doc->createElement("ident");
							$typename_element->appendChild($doc->createTextNode($l2[0]));
							$type_element->appendChild($typename_element);
							$typename_element->setAttribute("type", $bottomID_array[$cat]);
							// OUTPUT: Tablets
							$tablets_element = $doc->createElement("list");
							$type_element->appendChild($tablets_element);
							$tablets_element->setAttribute("type", "tablets");
							foreach ($l2[1] as $l3){
								// OUTPUT: Tablet number
								$tablet_element = $doc->createElement("item");
								$tablets_element->appendChild($tablet_element);
								$tablet_element->setAttribute("n", $l3[0]);
								// OUTPUT: TabletNumber
								$tabletsnumber_element = $doc->createElement("ident");
								$tabletsnumber_element->appendChild($doc->createTextNode($l3[0]));
								$tablet_element->appendChild($tabletsnumber_element);
								$tabletsnumber_element->setAttribute("type", "tabletID");
								// OUTPUT: Number
								$number_element = $doc->createElement("num");
								$number_element->appendChild($doc->createTextNode($l3[1]));
								$tablet_element->appendChild($number_element);
								$number_element->setAttribute("type", "amount");
								if($l3[2][0]!=''){
									for($h=0;$h<$l3[1];$h++){
										$render_el = $doc->createElement("w");
										$render_el->appendChild($doc->createTextNode($l3[2][$h]));
										$tablet_element->appendChild($render_el);
										$render_el->setAttribute("n", $h+1);
									}// END: for($h=0;$h<$l3[1];$h++){
								} // END; if($l3[2][0]!=''){
							}// END: of foreach ($l2[1] as $l3)
						} // END: of if($matchType == 'yes') 
					// IF: there is no type	
					}else{
						$item->setAttribute("xml:id", $l1[2]);
						// OUTPUT: Tablets
						$tablets_element = $doc->createElement("list");
						$item->appendChild($tablets_element);
						$tablets_element->setAttribute("type", "tablets");
						foreach ($l2[1] as $l3){
							// OUTPUT: Tablet number
							$tablet_element = $doc->createElement("item");
							$tablets_element->appendChild($tablet_element);
							$tablet_element->setAttribute("n", $l3[0]);
							// OUTPUT: TabletNumber
							$tabletsnumber_element = $doc->createElement("ident");
							$tabletsnumber_element->appendChild($doc->createTextNode($l3[0]));
							$tablet_element->appendChild($tabletsnumber_element);
							$tabletsnumber_element->setAttribute("type", "tabletID");
							// OUTPUT: Number
							$number_element = $doc->createElement("num");
							$number_element->appendChild($doc->createTextNode($l3[1]));
							$tablet_element->appendChild($number_element);
							$number_element->setAttribute("type", "amount");
							if($l3[2][0]!=''){
								for($h=0;$h<$l3[1];$h++){
									$render_el = $doc->createElement("w");
									$render_el->appendChild($doc->createTextNode($l3[2][$h]));
									$tablet_element->appendChild($render_el);
									$render_el->setAttribute("n", $h+1);
								} // END: for($h=0;$h<$l3[1];$h++){
							} // END: if($l3[2][0]!=''){
						}// END: of foreach ($l2[1] as $l3) 
					}// END: of if($l2[0]!='')
				}// END: of foreach ($l1[1] as $l2)
			}// END: of if($match == "yes")
		} // END: of foreach ($l as $l1)     		
	} // END: of if($scans = scandir($dir, 0))
   //Return status failed, if any error found.
    if ($error_code == 1) {
        $ab7 = $doc->createElement("ab");
		$ab7->appendChild($doc->createTextNode('failed'));
		$encodingDesc->appendChild($ab7);
		$ab7->setAttribute("type", "status");
    } // END: if ($error_code == 1) {
    // Return status passed, if no error found
    else if ($error_code == 0) {
        $ab7 = $doc->createElement("ab");
		$ab7->appendChild($doc->createTextNode('passed'));
		$encodingDesc->appendChild($ab7);
		$ab7->setAttribute("type", "status");
    } // END: else if ($error_code == 0) {
    return simplexml_load_string($doc->saveXML());
} // END: function get_person($pattern=FALSE) {


// ---- GET GEOGRAPHICAL NAME ---- //
/* 
* Function which retrieves all the geographical names in the tablets
* OPTIONAL PARAMETER
* $pattern: If a pattern is sent with the function only the geographical names which fit the pattern will be returned.
* Pattern wildcard is *
* Alternative characters can be presented like this: {abc}
*/
// FUNCTION: get_geog()
function get_geog($pattern=FALSE) {
	// SET: category
	$cat = 'placename';
	// SET: title
	$method_title = 'APPELLO method: Get ' . $cat;
	// SET: authority 
	$authority1 = 'Made available by Centre for the Study of Ancient Documents, University of Oxford (http://www.csad.ox.ac.uk/) and Henriette Roued-Cunliffe as a part of her D.Phil at the University of Oxford (http://www.roued.com/e-doc/)';
	// SET: todays date
	$today = date(d) . '-' . date(m) . '-' . date(Y);
	// SET: Array of possible sources with bibliographic references
	$bibl_array = array(
		'Tab.Vindol.I' => 'A.K.Bowman, J.D.Thomas, Vindolanda: the Latin writing- tablets, Britannia Monograph 4. London, 1983 ',
		'Tab.Vindol.II' => 'A.K.Bowman, J.D.Thomas, The Vindolanda Writing-Tablets (Tabulae Vindolandenses II). London, 1994',
		'Tab.Vindol.III' => 'A.K.Bowman, J.D.Thomas, The Vindolanda Writing Tablets (Tabulae Vindolandenses III). London: British Museum Publications, 2003 '); 
	// SET: Array of sources in this document
	$bibl_ref = array('Tab.Vindol.II', 'Tab.Vindol.III');
	// SET: encoding
	$encoding = 'This file was produced by APPELLO version 0.2';
	// SET: category list
	$list_of = $cat;
	// SET: source
	$source ="Vindolanda Writing Tablets";
	// SET: Array of possible languages
	$langs = array(
		'eng' => 'English',
		'lat' => 'Latin',
		'grc' => 'Greek',
		);
	// SET: Array of languages in this document
	$lang_list = array('eng', 'lat');
	// SET: xPath for tablet number	
	$tabID_path = '//tei:rs[@n="1"]';
	// SET: attribute for tablet ID
	$tabID_attr ='key';
	// SET: top label
	$topID_array = array(
	'placename' => 'placename',
	);
	// SET: bottom label
	$bottomID_array = array(
	);
	// SET: xPath to tag
	$tag_path = '//tei:placeName[@key]';
	// SET: local tablets directory
	$dir="tablets";	
	// SET: Attributes for tags
	$attr_top = 'nymRef';
	$attr_nr = 'n';
	$attr_key = 'key';
	$extra_array = array(
	);
	$attr_orig = 'rend';
	// IF:  there is a pattern - clean it
	if($pattern){
		$search = array(
			'{',
			'}',
			'*', 
		);
		$replace = array(
			'([',
			']{1})',
			'([a-zA-Z]{1})', 
		);
		$pattern = str_replace($search, $replace, $pattern);
		$pattern = '/' . $pattern . '/';
	} // END: if($pattern){
    // SET: error_code=0 for Sucesss / error_code=1 for Failure
    $error_code = 0;
	// SET: document
    $doc = new DOMDocument();
    $doc->formatOutput = true;
	// SET: Responce Element - building a TEI document
	$tei = $doc->createElement("TEI");
	$doc->appendChild($tei);
	// SET: teiHeader
	$teiheader = $doc->createElement("teiHeader");
	$tei->appendChild($teiheader);
	// SET: fileDesc
	$fileDesc = $doc->createElement("fileDesc");
	$teiheader->appendChild($fileDesc);
	// SET: title
	$titleStmt = $doc->createElement("titleStmt");
	$fileDesc->appendChild($titleStmt);
	$title = $doc->createElement("title");
	$title->appendChild($doc->createTextNode($method_title));
	$titleStmt->appendChild($title);
	// SET: publicationStmt
	$publicationStmt = $doc->createElement("publicationStmt");
	$fileDesc->appendChild($publicationStmt);
	$authority = $doc->createElement("authority");
	$authority->appendChild($doc->createTextNode($authority1));
	$publicationStmt->appendChild($authority);
	$date = $doc->createElement("date");
	$date->appendChild($doc->createTextNode($today));
	$publicationStmt->appendChild($date);
	// SET: sourceDesc
	$sourceDesc = $doc->createElement("sourceDesc");
	$fileDesc->appendChild($sourceDesc);
	for($m=0;$m<count($bibl_ref);$m++){
		$bibl = $doc->createElement("bibl");
		$bibl->appendChild($doc->createTextNode($bibl_array[$bibl_ref[$m]]));
		$sourceDesc->appendChild($bibl);
		$bibl->setAttribute("n", $bibl_ref[$m]);
	} // END: for($m=0;$m<count($bibl_ref);$m++){
	// SET: encodingDesc
	$encodingDesc = $doc->createElement("encodingDesc");
	$teiheader->appendChild($encodingDesc);
	$ab5 = $doc->createElement("ab");
	$ab5->appendChild($doc->createTextNode($encoding));
	$encodingDesc->appendChild($ab5);
	// SET: profileDesc
	$profileDesc = $doc->createElement("profileDesc");
	$teiheader->appendChild($profileDesc);
	$textClass = $doc->createElement("textClass");
	$profileDesc->appendChild($textClass);
	$catRef = $doc->createElement("catRef");
	$textClass->appendChild($catRef);
	$catRef->setAttribute("target", 'List of '.$list_of.' from '.$source);
	// SET: langUsage
	$langUsage = $doc->createElement("langUsage");
	$profileDesc->appendChild($langUsage);
	for($m=0;$m<count($lang_list);$m++){
		$language = $doc->createElement("language");
		$language->appendChild($doc->createTextNode($langs[$lang_list[$m]]));
		$langUsage->appendChild($language);
		$language->setAttribute("ident", $lang_list[$m]);
	}
	// OUTPUT: Text
	$text = $doc->createElement("text");
	$tei->appendChild($text);
	// OUTPUT: Body
	$body = $doc->createElement("body");
	$text->appendChild($body);
	// OUTPUT: List
	$list = $doc->createElement("list");
	$body->appendChild($list);
	// OUTPUT: Type of list
	$list->setAttribute("type", $list_of);
	// IF:  it is possible to scan tablets directory
	if($scans = scandir($dir, 0)){
		// FOR: Get data
		for($i=2;$i<count($scans);$i++){
			// SET: Connection to each tablet
			$tablet=$dir . '/' . $scans[$i];	
			// IF:  connected
			if($xml_tablet = simplexml_load_file($tablet)){
				// OUTPUT:  tei namespace
				$xml_tablet->registerXPathNamespace('tei', 'http://www.tei-c.org/ns/1.0');
				// SET:  tablet nr
				$idents=$xml_tablet->xpath($tabID_path);
				foreach($idents[0]->attributes() as $a => $b) {
					$$a=$b;
					$tabletNum=$$tabID_attr;
				} // END: of foreach($idents[0]->attributes() as $a => $b)
				// SET: Tags
				$tags=$xml_tablet->xpath($tag_path);
				// FOR: each tag
				foreach($tags as $tag){
					// UNSET: attributes
					unset($$attr_top);
					unset($$attr_bottom);
					unset($$attr_nr);
					unset($$attr_key);
					unset($$attr_extra);
					// SET: pattern matches to 'no' for default
					$match = 'no';
					$matchType = 'no';
					// FOR: each attribute in the tag
					foreach($tag[0]->attributes() as $a => $b) {
						// SET:  values
						$$a=$b;
					} // END: of foreach($tag[0]->attributes() as $a => $b)
					// SET: attributes
					$top1 = $$attr_top;
					$bottom1 = $$attr_bottom;
					$n1 = $$attr_nr;
					$id1 = $$attr_key;
					$extra1 = $$attr_extra;
					$orig1 = $$attr_orig;
					$bottom = "" . $bottom1[0] . "";
					$nr = "" . $n1[0] . "";
					$id = "" . $id1[0] . "";
					// IF: top attribute
					if($top1[0]!=''){
						$top = "" . $top1[0] . "";
					}else{
						$top = "" . $id1[0] . "";
					} // END: if($top1[0]!=''){
					$tnr = "" . $tabletNum[0] . "";
					// IF: attribute extra
					if($extra1[0]!=''){
						$extra = "" . $extra1[0] . "";
					}else{
						$extra='';
					} // END: if($extra1[0]!=''){
					// IF: original attribute
					if($orig1[0]!=''){
						$orig = "" . $orig1[0] . "";
					}else{
						$orig='';
					} // END: if($orig1[0]!=''){
					// SET: attribute array
					$l[$top][0] = $top;
					$l[$top][2] = strtolower($id);
					$l[$top][3] = strtolower($extra);
					$l[$top][1][$bottom][0] = $bottom;
					$l[$top][1][$bottom][2] = $id;
					$l[$top][1][$bottom][1][$tnr][0] = $tnr;
					$l[$top][1][$bottom][1][$tnr][1] = $nr;
					$l[$top][1][$bottom][1][$tnr][2][] = $orig;
					$l[$top][5] = $cat;
				} // END: of foreach($tags as $tag)
			} // END: of if($xml_tablet = simplexml_load_file($tablet))
		} // END: of for($i=3;$i<$count;$i++)
		// FOR: each array part
		foreach ($l as $l1){
			// IF: the pattern fits and pattern is turned on.
			if($pattern){
				// IF: pattern matches 
				if(preg_match($pattern, $l1[2])){
					$match = 'yes';
					$matchType1 = 'yes';
				}else{
					$match = 'no';
					// IF: pattern is bottom tag
					foreach ($l1[1] as $l2){
						if(preg_match($pattern, $l2[2])){
							$match = 'yes';
							$matchType1 = 'no';	
						} // END: of if(preg_match($pattern, $l2[0]))
					} // END: of foreach ($l1[1] as $l2)
				} // END: of if(preg_match($pattern, $l1[0]))
			}else{
				// IF: to pattern not turned on print all. 
				$match = 'yes';
			} // END: of if($pattern)
			//  IF: match
			if($match == "yes"){
				// OUTPUT: item
				$item = $doc->createElement("item");
				$list->appendChild($item);
				$item->setAttribute("n", $l1[0]);
				$ident1 = $doc->createElement("ident");
				$ident1->appendChild($doc->createTextNode($l1[0]));
				$item->appendChild($ident1);
				$ident1->setAttribute("type", $topID_array[$cat]);			
				// IF: extra Values
				if($l1[3]){
					$ident2 = $doc->createElement("ident");
					$ident2->appendChild($doc->createTextNode($l1[3]));
					$item->appendChild($ident2);
					$ident2->setAttribute("type", $extra_array[$cat]);
				} // END: if($l1[3]){
				// FOR: pattern matches type
				foreach ($l1[1] as $l2){
					if($matchType1=='yes'){
						$matchType='yes';
					}else{
						if($pattern){
							if(preg_match($pattern, $l2[2])){
								$matchType = 'yes';
							}else{
								$matchType = 'no';
							} // END: of if(preg_match($pattern, $l2[0]))
						}else{
							$matchType = 'yes';
						} // END: of if($pattern)
					} // END: of if($matchType1=='yes')
					if($l2[0]!=''){	
						if($matchType == 'yes'){
							// OUTPUT: types
							$types_element = $doc->createElement("list");
							$item->appendChild($types_element);
							$types_element->setAttribute("type", $bottomID_array[$cat]);
							$type_element = $doc->createElement("item");
							$types_element->appendChild($type_element);
							$type_element->setAttribute("n", $l2[0]);
							$type_element->setAttribute("xml:id", $l2[2]);
							$typename_element = $doc->createElement("ident");
							$typename_element->appendChild($doc->createTextNode($l2[0]));
							$type_element->appendChild($typename_element);
							$typename_element->setAttribute("type", $bottomID_array[$cat]);
							// OUTPUT: Tablets
							$tablets_element = $doc->createElement("list");
							$type_element->appendChild($tablets_element);
							$tablets_element->setAttribute("type", "tablets");
							// FOR: each tablet
							foreach ($l2[1] as $l3){
								// OUTPUT: Tablet number
								$tablet_element = $doc->createElement("item");
								$tablets_element->appendChild($tablet_element);
								$tablet_element->setAttribute("n", $l3[0]);
								// OUTPUT: TabletNumber
								$tabletsnumber_element = $doc->createElement("ident");
								$tabletsnumber_element->appendChild($doc->createTextNode($l3[0]));
								$tablet_element->appendChild($tabletsnumber_element);
								$tabletsnumber_element->setAttribute("type", "tabletID");
								// OUTPUT: Number
								$number_element = $doc->createElement("num");
								$number_element->appendChild($doc->createTextNode($l3[1]));
								$tablet_element->appendChild($number_element);
								$number_element->setAttribute("type", "amount");
								// OUTPUT: Original spelling of the word in the text
								if($l3[2][0]!=''){
									for($h=0;$h<$l3[1];$h++){
										$render_el = $doc->createElement("w");
										$render_el->appendChild($doc->createTextNode($l3[2][$h]));
										$tablet_element->appendChild($render_el);
										$render_el->setAttribute("n", $h+1);
									} // END: for($h=0;$h<$l3[1];$h++){
								} // END: if($l3[2][0]!=''){
							}// END: of foreach ($l2[1] as $l3)
						} // END: of if($matchType == 'yes') 
					}else{
						$item->setAttribute("xml:id", $l1[2]);
						// OUTPUT: Tablets
						$tablets_element = $doc->createElement("list");
						$item->appendChild($tablets_element);
						$tablets_element->setAttribute("type", "tablets");
						foreach ($l2[1] as $l3){
							// OUTPUT: Tablet number
							$tablet_element = $doc->createElement("item");
							$tablets_element->appendChild($tablet_element);
							$tablet_element->setAttribute("n", $l3[0]);
							// OUTPUT: TabletNumber
							$tabletsnumber_element = $doc->createElement("ident");
							$tabletsnumber_element->appendChild($doc->createTextNode($l3[0]));
							$tablet_element->appendChild($tabletsnumber_element);
							$tabletsnumber_element->setAttribute("type", "tabletID");
							// OUTPUT: Number
							$number_element = $doc->createElement("num");
							$number_element->appendChild($doc->createTextNode($l3[1]));
							$tablet_element->appendChild($number_element);
							$number_element->setAttribute("type", "amount");
							// OUTPUT: Original spelling of the word in the text
							if($l3[2][0]!=''){
								for($h=0;$h<$l3[1];$h++){
									$render_el = $doc->createElement("w");
									$render_el->appendChild($doc->createTextNode($l3[2][$h]));
									$tablet_element->appendChild($render_el);
									$render_el->setAttribute("n", $h+1);
								} // END: for($h=0;$h<$l3[1];$h++){
							} // END: if($l3[2][0]!=''){
						}// END: of foreach ($l2[1] as $l3) 
					}// END: of if($l2[0]!='')
				}// END: of foreach ($l1[1] as $l2)
			}// END: of if($match == "yes")
		} // END: of foreach ($l as $l1)     		
	} // END: of if($scans = scandir($dir, 0))
   //Return status failed, if any error found.
    if ($error_code == 1) {
        $ab7 = $doc->createElement("ab");
		$ab7->appendChild($doc->createTextNode('failed'));
		$encodingDesc->appendChild($ab7);
		$ab7->setAttribute("type", "status");
    } // END: if ($error_code == 1) {
    // Return status passed, if no error found
    else if ($error_code == 0) {
        $ab7 = $doc->createElement("ab");
		$ab7->appendChild($doc->createTextNode('passed'));
		$encodingDesc->appendChild($ab7);
		$ab7->setAttribute("type", "status");
    } // END: else if ($error_code == 0) {
    return simplexml_load_string($doc->saveXML());
} // END:  Function

// ---- GET ALL ---- //
/* 
* Function which retrieves all the words in the tablets
* OPTIONAL PARAMETER
* $pattern: If a pattern is sent with the function only the words which fit the pattern will be returned.
* Pattern wildcard is *
* Alternative characters can be presented like this: {abc}
*/
// FUNCTION: get_all()
function get_all($pattern = FALSE) {
	// SET: array
	$all = array(
	'word',
	'person',
	'placename',
	'military',
	'consul',
	'date'
	);
	// SET: category
	$cat = 'all';
	// SET: title
	$method_title = 'APPELLO method: Get ' . $cat;
	// SET: authority
	$authority1 = 'Made available by Centre for the Study of Ancient Documents, University of Oxford (http://www.csad.ox.ac.uk/) and Henriette Roued-Cunliffe as a part of her D.Phil at the University of Oxford (http://www.roued.com/e-doc/)';
	// SET: todays date
	$today = date(d) . '-' . date(m) . '-' . date(Y);
	// SET: bibl array
	$bibl_array = array(
		'Tab.Vindol.I' => 'A.K.Bowman, J.D.Thomas, Vindolanda: the Latin writing- tablets, Britannia Monograph 4. London, 1983 ',
		'Tab.Vindol.II' => 'A.K.Bowman, J.D.Thomas, The Vindolanda Writing-Tablets (Tabulae Vindolandenses II). London, 1994',
		'Tab.Vindol.III' => 'A.K.Bowman, J.D.Thomas, The Vindolanda Writing Tablets (Tabulae Vindolandenses III). London: British Museum Publications, 2003 '); 
	$bibl_ref = array('Tab.Vindol.II', 'Tab.Vindol.III');
	// SET: encoding
	$encoding = 'This file was produced by APPELLO version 0.2';
	// SET: list of categories
	$list_of = $cat;
	$source ="Vindolanda Writing Tablets";
	// SET: Array of languages
	$langs = array(
		'eng' => 'English',
		'lat' => 'Latin',
		'grc' => 'Greek',
		);
	$lang_list = array('eng', 'lat');
	// SET: xPath for tablet number	
	$tabID_path = '//tei:rs[@n="1"]';
	// SET: attribute for tablet ID
	$tabID_attr ='key';
	// SET: top label
	$topID_array = array(
		'word' => 'lemma',
		'person' => 'person',
		'placename' => 'placename',
		'military' => 'militaryOfficial',
		'consul' => 'consul',
		'date' => 'month',
	);
	// SET: bottom label
	$bottomID_array = array(
		'word' => 'lemma_subtype',
		'date' => 'date',
	);
	// SET: xPath to tag
	$tag_path = array(
		'word' => '//tei:w[@n]',
		'person' => '//tei:persName[@nymRef]',
		'placename' => '//tei:placeName[@key]',
		'military' => '//tei:rs[@type="militaryOfficial"]',
		'consul' => '//tei:rs[@type="consul"]',
		'date' => '//tei:rs[@type="julian_calendar"]',
	);
	// SET: local tablets directory
	$dir="tablets";	
	// SET: Attributes for tags
	$attr_top = array(
		'word' => 'lemma',
		'person' => 'nymRef',
		'placename' => 'nymRef',
		'military' => 'nymRef',
		'consul' => 'nymRef',
		'date' => 'nymRef',
	);
	$attr_bottom = array(
		'word' => 'subtype',
		'date' => 'rend',
	);
	$attr_nr = 'n';
	$attr_key = array(
		'word' => 'lemma',
		'person' => 'key',
		'placename' => 'key',
		'military' => 'key',
		'consul' => 'key',
		'date' => 'key',
	);
	$attr_extra = array(
		'word' => 'lemma',
		'person' => 'person',
		'placename' => 'placename',
		'military' => 'militaryOfficial',
		'consul' => 'consul',
		'date' => 'month',
	);
	$extra_array = array(
		'person' => 'profession',
	);
	$attr_orig = array(
		'word' => 'rend',
		'person' => 'rendition',
		'placename' => 'rend',
	);
	// IF: there is a pattern - clean it
	if($pattern){
		$search = array(
			'{',
			'}',
			'*', 
		);
		$replace = array(
			'([',
			']{1})',
			'([a-zA-Z]{1})', 
		);
		$pattern = str_replace($search, $replace, $pattern);
		$pattern = '/' . $pattern . '/';
	} // END: if($pattern){
    // SET: error_code=0 for Sucesss / error_code=1 for Failure
    $error_code = 0;
	// OUTPUT: the document
    $doc = new DOMDocument();
    $doc->formatOutput = true;
	// OUTPUT: Responce Element - building a TEI document
	$tei = $doc->createElement("TEI");
	$doc->appendChild($tei);
	// OUTPUT: teiHeader
	$teiheader = $doc->createElement("teiHeader");
	$tei->appendChild($teiheader);
	// OUTPUT: fileDesc
	$fileDesc = $doc->createElement("fileDesc");
	$teiheader->appendChild($fileDesc);
	// OUTPUT: title
	$titleStmt = $doc->createElement("titleStmt");
	$fileDesc->appendChild($titleStmt);
	$title = $doc->createElement("title");
	$title->appendChild($doc->createTextNode($method_title));
	$titleStmt->appendChild($title);
	// OUTPUT: publicationStmt
	$publicationStmt = $doc->createElement("publicationStmt");
	$fileDesc->appendChild($publicationStmt);
	$authority = $doc->createElement("authority");
	$authority->appendChild($doc->createTextNode($authority1));
	$publicationStmt->appendChild($authority);
	$date = $doc->createElement("date");
	$date->appendChild($doc->createTextNode($today));
	$publicationStmt->appendChild($date);
	// OUTPUT: sourceDesc
	$sourceDesc = $doc->createElement("sourceDesc");
	$fileDesc->appendChild($sourceDesc);
	for($m=0;$m<count($bibl_ref);$m++){
		$bibl = $doc->createElement("bibl");
		$bibl->appendChild($doc->createTextNode($bibl_array[$bibl_ref[$m]]));
		$sourceDesc->appendChild($bibl);
		$bibl->setAttribute("n", $bibl_ref[$m]);
	} // END: for($m=0;$m<count($bibl_ref);$m++){
	// OUTPUT: encodingDesc
	$encodingDesc = $doc->createElement("encodingDesc");
	$teiheader->appendChild($encodingDesc);
	$ab5 = $doc->createElement("ab");
	$ab5->appendChild($doc->createTextNode($encoding));
	$encodingDesc->appendChild($ab5);
	// OUTPUT: profileDesc
	$profileDesc = $doc->createElement("profileDesc");
	$teiheader->appendChild($profileDesc);
	$textClass = $doc->createElement("textClass");
	$profileDesc->appendChild($textClass);
	$catRef = $doc->createElement("catRef");
	$textClass->appendChild($catRef);
	$catRef->setAttribute("target", 'List of '.$list_of.' from '.$source);
	// OUTPUT: langUsage
	$langUsage = $doc->createElement("langUsage");
	$profileDesc->appendChild($langUsage);
	for($m=0;$m<count($lang_list);$m++){
		$language = $doc->createElement("language");
		$language->appendChild($doc->createTextNode($langs[$lang_list[$m]]));
		$langUsage->appendChild($language);
		$language->setAttribute("ident", $lang_list[$m]);
	} // END: for($m=0;$m<count($lang_list);$m++){
	// OUTPUT: Text
	$text = $doc->createElement("text");
	$tei->appendChild($text);
	// OUTPUT: Body
	$body = $doc->createElement("body");
	$text->appendChild($body);
	// OUTPUT: List
	$list = $doc->createElement("list");
	$body->appendChild($list);
	// OUTPUT: Type of list
	$list->setAttribute("type", $list_of);
	// FOR: For each catecory in ALL
	for($t=0;$t<count($all);$t++){
		// UNSET: array
		unset($l);
		// IF: Scan tablets directory
		if($scans = scandir($dir, 0)){
			// FOR: get data
			for($i=2;$i<count($scans);$i++){
				// SET: Connection to each tablet
				$tablet=$dir . '/' . $scans[$i];	
				// IF: connected
				if($xml_tablet = simplexml_load_file($tablet)){
					// SET: Register tei namespace
					$xml_tablet->registerXPathNamespace('tei', 'http://www.tei-c.org/ns/1.0');
					// SET: tablet nr
					$idents=$xml_tablet->xpath($tabID_path);
					foreach($idents[0]->attributes() as $a => $b) {
						$$a=$b;
						$tabletNum=$$tabID_attr;
					} // END: of foreach($idents[0]->attributes() as $a => $b)
					// SET: tags
					$tags=$xml_tablet->xpath($tag_path[$all[$t]]);
					// FOR: each tag
					foreach($tags as $tag){
						// UNSET: attributes
						unset($$attr_top[$all[$t]]);
						unset($$attr_bottom[$all[$t]]);
						unset($$attr_nr);
						unset($$attr_key[$all[$t]]);
						unset($$attr_extra[$all[$t]]);
						unset($$attr_orig[$all[$t]]);
						// SET: pattern matches to 'no' for default
						$match = 'no';
						$matchType = 'no';
						// SET: each attribute in the tag
						foreach($tag[0]->attributes() as $a => $b) {
							// SET: the values
							$$a=$b;
						} // END: of foreach($tag[0]->attributes() as $a => $b)
						$top1 = $$attr_top[$all[$t]];
						$bottom1 = $$attr_bottom[$all[$t]];
						$n1 = $$attr_nr;
						$id1 = $$attr_key[$all[$t]];
						$extra1 = $$attr_extra[$all[$t]];
						$orig1 = $$attr_orig[$all[$t]];
						$bottom = "" . $bottom1[0] . "";
						$nr = "" . $n1[0] . "";
						$id = "" . $id1[0] . "";
						// IF: person
						if($all[$t]=='person'){
							$id ='_'.$id;
						};
						// IF: military
						if($all[$t]=='military'){
							$id =$id.'_';
						};
						// IF: top attribute
						if($top1[0]!=''){
							$top = "" . $top1[0] . "";
						}else{
							$top = "" . $id1[0] . "";
						} // END: if($top1[0]!=''){
						$tnr = "" . $tabletNum[0] . "";
						// IF: extra attribute
						if($extra1[0]!=''){
							$extra = "" . $extra1[0] . "";
						}else{
							$extra='';
						} // END: if($extra1[0]!=''){
						// IF: original attribute
						if($orig1[0]!=''){
							$orig = "" . $orig1[0] . "";
						}else{
							$orig='';
						} // END: if($orig1[0]!=''){
						$l[$top][0] = $top;
						// IF: date
						if($all[$t]=='date'){
							$l[$top][2] = strtolower($top);
						}else{
							$l[$top][2] = strtolower($id);
						};
						$l[$top][3] = strtolower($extra);
						$l[$top][1][$bottom][0] = $bottom;
						if($all[$t]=='date'){
							$l[$top][1][$bottom][2] = strtolower($id);
						}else{
							$l[$top][1][$bottom][2] = strtolower($bottom);
						};
						$l[$top][1][$bottom][1][$tnr][0] = $tnr;
						$l[$top][1][$bottom][1][$tnr][1] = $nr;
						$l[$top][1][$bottom][1][$tnr][2][] = $orig;
						$l[$top][5] = $cat;
					} // END: foreach($tags as $tag)
				} // END: if($xml_tablet = simplexml_load_file($tablet))
			} // END: for($i=2;$i<count($scans);$i++){
			// FOR: each part of array
			foreach ($l as $l1){
				// SET: cleaning for xml:id
				$search_clean = array(
					'[',
					']',
					'?',
					' ',
					'-',
					'(',
					')',
				);
				$replace_clean = array(
					'',
					'',
					'',
					'_',
					'',
					'',
					'',
				);
				$l1[2] = str_replace($search_clean, $replace_clean, $l1[2]);
				// IF: the pattern fits and pattern is turned on.
				if($pattern){
					// IF: pattern matches top - cleaned id
					if(preg_match($pattern, $l1[2])){
						$match = 'yes';
						$matchType1 = 'yes';
					}else{
						$match = 'no';
						// IF: pattern is in bottom
						foreach ($l1[1] as $l2){
							if(preg_match($pattern, $l2[2])){
								$match = 'yes';
								$matchType1 = 'no';	
							} // END: of if(preg_match($pattern, $l2[0]))
						} // END: of foreach ($l1[1] as $l2)
					} // END: of if(preg_match($pattern, $l1[0]))
				}else{
					$match = 'yes';
				} // END: of if($pattern)
				// IF: match
				if($match == "yes"){
					$item = $doc->createElement("item");
					$list->appendChild($item);
					$item->setAttribute("rend", $l1[0]);
					$item->setAttribute("n", $l1[2]);
					$ident1 = $doc->createElement("ident");
					$ident1->appendChild($doc->createTextNode($l1[0]));
					$item->appendChild($ident1);
					$ident1->setAttribute("type", $topID_array[$all[$t]]);
					// IF: extra Values
					if($l1[3] AND $extra_array[$all[$t]]){
						$ident2 = $doc->createElement("ident");
						$ident2->appendChild($doc->createTextNode($l1[3]));
						$item->appendChild($ident2);
						$ident2->setAttribute("type", $extra_array[$all[$t]]);
					}
					// FOR: eash part of the array
					foreach ($l1[1] as $l2){
						// IF: type matches
						if($matchType1=='yes'){
							$matchType='yes';
						}else{
							if($pattern){
								if(preg_match($pattern, $l2[2])){
									$matchType = 'yes';
								}else{
									$matchType = 'no';
								} // END: of if(preg_match($pattern, $l2[0]))
							}else{
								$matchType = 'yes';
							} // END: of if($pattern)
						} // END: of if($matchType1=='yes')		
						$l2[2] = str_replace($search_clean, $replace_clean, $l2[2]);
						if($l2[0]!=''){	
							// IF: type matches
							if($matchType == 'yes'){
								$types_element = $doc->createElement("list");
								$item->appendChild($types_element);
								$types_element->setAttribute("type", $bottomID_array[$all[$t]]);
								$type_element = $doc->createElement("item");
								$types_element->appendChild($type_element);
								$type_element->setAttribute("rend", $l2[0]);
								$type_element->setAttribute("n", $l2[2]);
								$typename_element = $doc->createElement("ident");
								$typename_element->appendChild($doc->createTextNode($l2[0]));
								$type_element->appendChild($typename_element);
								$typename_element->setAttribute("type", $bottomID_array[$all[$t]]);
								// OUTPUT: Tablets
								$tablets_element = $doc->createElement("list");
								$type_element->appendChild($tablets_element);
								$tablets_element->setAttribute("type", "tablets");							
								foreach ($l2[1] as $l3){
									// OUTPUT: Tablet number
									$tablet_element = $doc->createElement("item");
									$tablets_element->appendChild($tablet_element);
									$tablet_element->setAttribute("n", $l3[0]);							
									// OUTPUT: TabletNumber
									$tabletsnumber_element = $doc->createElement("ident");
									$tabletsnumber_element->appendChild($doc->createTextNode($l3[0]));
									$tablet_element->appendChild($tabletsnumber_element);
									$tabletsnumber_element->setAttribute("type", "tabletID");
									// OUTPUT: Number
									$number_element = $doc->createElement("num");
									$number_element->appendChild($doc->createTextNode($l3[1]));
									$tablet_element->appendChild($number_element);
									$number_element->setAttribute("type", "amount");
									// OUTPUT: Original spelling of the word in the text
									if($l3[2][0]!=''){
										for($h=0;$h<$l3[1];$h++){
											$render_el = $doc->createElement("w");
											$render_el->appendChild($doc->createTextNode($l3[2][$h]));
											$tablet_element->appendChild($render_el);
											$render_el->setAttribute("n", $h+1);
										} // END: for($h=0;$h<$l3[1];$h++){
									}		 // END: if($l3[2][0]!=''){						
								}// END: foreach ($l2[1] as $l3)
							} // END: if($matchType == 'yes') 
						}else{		
							// OUTPUT: Tablets
							$tablets_element = $doc->createElement("list");
							$item->appendChild($tablets_element);
							$tablets_element->setAttribute("type", "tablets");
							foreach ($l2[1] as $l3){
								// OUTPUT: Tablet number
								$tablet_element = $doc->createElement("item");
								$tablets_element->appendChild($tablet_element);
								$tablet_element->setAttribute("n", $l3[0]);							
								// OUTPUT: TabletNumber
								$tabletsnumber_element = $doc->createElement("ident");
								$tabletsnumber_element->appendChild($doc->createTextNode($l3[0]));
								$tablet_element->appendChild($tabletsnumber_element);
								$tabletsnumber_element->setAttribute("type", "tabletID");
								// OUTPUT: Number
								$number_element = $doc->createElement("num");
								$number_element->appendChild($doc->createTextNode($l3[1]));
								$tablet_element->appendChild($number_element);
								$number_element->setAttribute("type", "amount");
								// OUTPUT: Original spelling of the word in the text
								if($l3[2][0]!=''){
									for($h=0;$h<$l3[1];$h++){
										$render_el = $doc->createElement("w");
										$render_el->appendChild($doc->createTextNode($l3[2][$h]));
										$tablet_element->appendChild($render_el);
										$render_el->setAttribute("n", $h+1);
									} // END: for($h=0;$h<$l3[1];$h++){
								} // END: if($l3[2][0]!=''){
							}// END: foreach ($l2[1] as $l3) 
						}// END: if($l2[0]!='')
					}// END: foreach ($l1[1] as $l2)		
				}// END: if($match == "yes")
			} // END: foreach ($l as $l1)     		
		} // END: if($scans = scandir($dir, 0))
	} // END: for($t=0;$t<count($all);$t++)
   // SET: Return status failed, if any error found.
    if ($error_code == 1) {
        $ab7 = $doc->createElement("ab");
		$ab7->appendChild($doc->createTextNode('failed'));
		$encodingDesc->appendChild($ab7);
		$ab7->setAttribute("type", "status");
    } // END: if ($error_code == 1) {
    // SET: Return status passed, if no error found
    else if ($error_code == 0) {
        $ab7 = $doc->createElement("ab");
		$ab7->appendChild($doc->createTextNode('passed'));
		$encodingDesc->appendChild($ab7);
		$ab7->setAttribute("type", "status");
    } // END: else if ($error_code == 0) {
    return simplexml_load_string($doc->saveXML());
} // END: Function

// GET NOTE //
// FUNCTION: get_note()
function get_note($tabletID, $notenr) {
	// SET: error_code=0 for Sucesss / error_code=1 for Failure
    $error_code = 0;
	// SET: new DOMDocument
    $doc = new DOMDocument();
	// SET: Connection
	$dir="tablets";
	$tablet=$dir . '/' . $tabletID . '.xml';	
	// SET: note		
	$xml_note = simplexml_load_file($tablet);
	$xml_note->registerXPathNamespace('tei', 'http://www.tei-c.org/ns/1.0');
	// SET: Retrieve information from the XML
	$notes=$xml_note->xpath('//tei:note[@n="'. $notenr.'" and @type="line"]');
	// FOR: each note
	foreach($notes as $note){
		 $xmlnote=$note->asXML();
	}	
	// OUTPUT: note
	$doc->loadXML($xmlnote);			
    return simplexml_load_string($doc->saveXML());
}

// SET: the server and the functions
$server = new Zend_Rest_Server();
$server->addFunction('get_tablets');
$server->addFunction('get_tablet');
$server->addFunction('get_word');
$server->addFunction('get_rs');
$server->addFunction('get_person');
$server->addFunction('get_geog');
$server->addFunction('get_all');
$server->addFunction('get_note');
// OUTPUT: handle the functions
$server->handle();
?>