<?php
/* * * * * * * * * * * * * * * * * * * * * * * *
Plugin Name: Sola Scripturae Catenata
Plugin URI: http://www.versorbooks.com/
Description: Sola Scripturae Catenata ("Only Links to Scripture") will convert Bible references in your posts and comments into hyperlinks to the online translation of your choice through BibleGateway.com.
Version: 2.3.0
Author Name: Nathaniel Turner
Author URI: http://www.versorbooks.com/
 * * * * * * * * * * * * * * * * * * * * * * * *
Original Plugin Name: Catholic Bible Scripturizer
Original Plugin URI: http://www.bibliacatolica.com.br/
Original Author: Wellington Campos Pinho
 * * * * * * * * * * * * * * * * * * * * * * * *
**/

///////////////////////////////////////////////////////////////////////////////
////////////////////////////// PRIMARY FUNCTIONS //////////////////////////////
///////////////////////////////////////////////////////////////////////////////
/* --- PatternSearch FUNCTION --- */
/* CALLED BY: 
   CALLS: vol, ConcatLinks
   INPUT VARIABLES:
		$content => Str. The string of text being searched for Bible references
   RETURNS: Str. the input variable with all references changed to links
   
   Finds all instances of Bible references in the content of a post or comment
   and returns the same text, but with a link to the preferred translation (or
   to a local page, if settings allow). */
function PatternSearch($content){
    $bok = 'Genesis|Gen|Ge|Gn|Exodus|Exod?|Ex|Leviticus|Lev?|Levit?|Lv|'	  .
		   'Numbers|Nmb|Numb?|Nu|Deuteronomy|Deut?|Dt|Du|De|Joshua|Josh?|Jo|' .
		   'Judges|Ju?dg?|Jgs|Ruth|Ru|1Samuel|1Sam?|1Sm|2Samuel|2Sam?|2Sm|'   .
		   'Samuel|Sam?|Sm|1Kings|1Kgs?|1Ki|Kings|Kn?gs?|Kin?|2Kings|2Kgs?|'  .
		   '2Ki|1Chr?|1Chron|1Chronicles|2Chr?|2Chron|2Chronicles|Chronicles|'.
		   'Chr?|Chron|Ezra|Ezr|Nehemiah|Nehem?|Neh?|Tobit|Tob|Tb|Judith|Jdt|'.
		   'Jth|Esther|Esth?|Es|1Maccabees|1Macc|1Mc|1Ma|2Maccabees|2Macc|'	  .
		   '2Mc|2Ma|Maccabees|Macc|Mc|Ma|Jo?b|Psalms?|Psa?s?|Proverbs?|Prov?|'.
		   'Prv?|Ecclesiastes|Eccles|Eccl?|Ec|Qoheleth|Qoh|SongofSongs|'	  .
		   'CanticleofCanticles|Song of Songs|Songs? of Solomon|'			  .
		   'Canticle of Canticles|Songs?ofSolomon|Song?|Songs|SS|So|Sg|Cant?|'.
		   'Wisdom|Wi?s|Sirach|Ecclesiasticus|Sir|Ecclus|Isaiah|Isa?|'		  .
		   'Jeremiah|Jer?|Jerem|Lamentations|Lam?|Lament?|Baruch|Bar?|'		  .
		   'Ezekiel|Ezek?|Ezk?|Daniel|Dan?|Dn|Hosea|Hos?|Joel?|Jl|Amos|Am|'	  .
		   'Obadiah|Obad?|Ob|Jonah|Jon|Micah|Mic?|Nahum|Nah?|Habakkuk|Ha?b|'  .
		   'Habak|Zephaniah|Zeph?|Haggai|Ha?g|Hagg|Zechariah|Zech?|Malachi|'  .
		   'Malac?|Ma?l|1Esdras|1Es|2Esdras|2Es|3Esdras|3Es|4Esdras|4Es|'	  .
		   'Esdras|Es|Azariah|Aza|Susanna|Sus|Bel|Bel and the Dragon|'		  .
		   'Manasses|Prayer of Manasseh|Man|3Maccabees|3Macc|3Mc|3Ma|'		  .
		   '4Maccabees|4Macc|4Mc|4Ma|Matthew|Ma?t|Matt|Mark?|Mr?k|Luke|Lu?k|' .
		   'Lu|John|Jh?n|Jo|Acts?|Ac|Acts of the Apostles|Romans|Ro?m|Ro|'    .
		   'Corinthians|Cor?|1Corinthians|2Corinthians|1Cor?|2Cor?|Corin|'    .
		   'Galatians|Gal?|Galat|Ephesians|Eph?|Ephes|Philippians|Phili?|'    .
		   'Phil?|Php|Colossians|Col?|Colos|1Thessalonians|1Thess?|1Th|'	  .
		   '2Thessalonians|2Thess?|2Th|Thessalonians|Thess?|Th|1Timothy|'	  .
		   '1Tim?|2Timothy|2Tim?|Timothy|Tim?|Titus|Tts|Tit?|Philemon|Phl?m|' .
		   'Philem|Hebrews|Hebr?|He|James|Jam|Jms|Jas?|1Peter|1Pe?t|2Peter|'  .
		   '2Pe?t|Peter|Pete?|Pe?t|1John|1Jn|1Jo|2John|2Jn|2Jo|3John|3Jn|3Jo|'.
		   'Jude|Judas|Ju|Revelations?|Rev?|Rv|Revel|Apocalypse|Apoc|Ap'	  ;
    
	$nogo1 = '(\[noref])?';
	$nogo2 = '(\[\/noref])?';
    $ver = '(?:;?\\s*\\d+(?:(?:-\\d+)|(?::\\d+(?:-\\d+)*(?:,\\s?\\d+(?:-\\d+)*)*)*)(?! +(?:'.voldBooks().')))+';
    $regex = '/('.$nogo1.'\\b(?:('.vol().')\\s+)?(?:('.$bok.')\\s+)('.$ver.')\\b'.$nogo2.')/';
    
    preg_match_all($regex, $content, $urls);
    $urls = preg_replace('/\//', '\/', $urls[0]);
	$urls = preg_replace('/\[/', '\[', $urls);

	foreach ($urls as $i => $url) {
        $urls[$i] = '/' . '(?<!blank\"\>)' . $url . '/';
    }

    $content = preg_replace_callback($urls, 'ConcatLinks', $content, 1);

    return $content;      
}

/* --- ConcatLinks FUNCTION --- */
/* CALLED BY: PatternSearch
   CALLS: getTranslation, vol, standardBookName, translationIncludes,
		  fixPsalms, generateTargetLink
   INPUT VARIABLES:
		$url => Str. a single Bible reference, text only
   RETURNS: Str. a link to an online Bible surrounding the original text
   
   When given a Bible reference string, returns the same surrounded by a link
   to the relevant Scripture (either at BibleGateway.com or the local site,
   depending on settings and availability). */
function ConcatLinks($url) {
	if (strpos($url[0],'[noref]') !== FALSE) {
		$ret = substr($url[0],strpos($url[0],']')+1);
		$ret = substr($ret,0,strpos($ret,'['));
		return $ret;
	}
	$array = explode(" ", $url[0]);
    $translation = getTranslation();
    $book = $array[0];
	$vol = '/(' . vol() . ')/';
	if (preg_match($vol,$book)) { $book .= "+" . $array[1]; $d = 2; }
    else { $d = 1; }
    $cv = $array[$d];
	$book = standardBookName($book);
    while (count($array) > $d + 1) { $cv .= $array[++$d]; }
	if (!translationIncludes($translation,$book,$cv,$canon)) {
		if ($canon == 'Eastern') { $translation = 'NRSV'; }
		elseif ($canon == 'Catholic') { $translation = 'NRSVCE'; }
		elseif ($canon == 'OT') { $translation = 'NASB'; }
	}
	if ($book == 'Psalm' && $translation == 'DRA') { $cv = fixPsalms($cv); }
	$book = str_replace(' ','+',$book);
	return generateTargetLink($book,$cv,$translation,$url[0]);
}
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
/////////////////////////////// HELPER FUNCTIONS //////////////////////////////
///////////////////////////////////////////////////////////////////////////////
/* --- getTranslation FUNCTION --- */
/* CALLED BY: ConcatLinks, catenata_options
   CALLS: opt_name
   RETURNS: Str. the preferred translation as defined in system settings
   
   Accesses the option table of the database to return the preferred
   translation of the site administrator. */
function getTranslation() {
	$opt_name = opt_name();
	$o = get_option($opt_name[0],"NRSV");
	return $o;
}

/* --- fixPsalms FUNCTION --- */
/* CALLED BY: 
   CALLS: 
   INPUT VARIABLES:
		$cv => Str. a chapter-verse combo from the Book of Psalms
   RETURNS: Str. a chapter-verse combo for the LXX numbering that will
			definitely include the desired chapter-verse combo
   
   When a reference to the Book of Psalms uses the Septuagint numbering (as the
   Douay-Rheims does), the reference will not line up perfectly with the linked
   text. This function returns a renumbered and expanded link to ensure that
   the target reference is still included in the result. */
function fixPsalms($cv) {
	$allcvs = explode(';',$cv);
	foreach ($allcvs as $onecv) {
		$cthenv = explode(':',$onecv);
		$c = intval($cthenv[0]);
		$vvv = explode(',',$cthenv[1]);
		$min = 10000;
		$max = 0;
		foreach($vvv as $versegroup) {
			$vv = explode('-',$versegroup);
			foreach($vv as $verse) {
				if ($verse < $min) { $min = $verse; }
				if ($verse > $max) { $max = $verse; }
			}
		}
		$newc = $c;
		if ($c < 10) {}
		elseif ($c < 115) { $newc--; }
		elseif ($c < 116) { $newc -= 2; }
		elseif ($c == 116) {
			if ($min < 10 && $max < 10) { $newc -= 2; }
			elseif ($min < 10) { $newc = '114-115'; }
			else { $newc--; }
		} elseif ($c < 147) { $newc--; }
		elseif ($c == 147) {
			if ($min < 11 && $max < 11) { $newc--; }
			elseif ($min < 11) { $newc = '146-147'; }
		}
		
		if ($c == 116 || $c == 147) {
			$output[] = $newc;
		} else {
			$output[] = $newc . ':' . $min . '-' . ($max + 2);
		}
	}
	foreach ($output as $i => $cvcombo) {
		$ret .= $cvcombo . ($i < count($output) - 1 ? ';' : '');
	}
	return $ret;
}

/* --- translationIncludes FUNCTION --- */
/* CALLED BY: ConcatLinks
   INPUT VARIABLES:
		$translation=> Str. the translation being checked
		$book		=> Str. the book being referenced
		$cv			=> Str. the chapter-verse combo being referenced
		&$canon 	=> Str. is set to the canon in which the book is available
   RETURNS: Bool. TRUE if the given $translation includes the given $book;
			FALSE otherwise
   
   In order to confirm that we only link to books that exist, we check the 
   preferred translation for the book/chapter we want to link to before
   creating the link. This function returns TRUE if the preferred translation
   includes the desired book and chapter, FALSE otherwise; it also makes the
   $canon variable available for use by the caller. */
function translationIncludes($translation,$book,$cv,&$canon) {
	$a = array('OT' => array('Genesis','Exodus','Leviticus','Numbers',
							 'Deuteronomy','Joshua','Judges','Ruth',
							 '1 Samuel','2 Samuel','1 Kings','2 Kings',
							 '1 Chronicles','2 Chronicles','Ezra',
							 'Nehemiah','Esther','Job','Psalm',
							 'Proverbs','Ecclesiastes','Song of Songs',
							 'Isaiah','Jeremiah','Lamentations',
							 'Ezekiel','Daniel','Hosea','Joel','Amos',
							 'Obadiah','Jonah','Micah','Nahum','Habakkuk',
							 'Zephaniah','Haggai','Zechariah','Malachi'),
			   'NT' => array('Matthew','Mark','Luke','John','Acts','Romans',
							 '1 Corinthians','2 Corinthians','Galatians',
							 'Ephesians','Philippians','Colossians',
							 '1 Thessalonians','2 Thessalonians','1 Timothy',
							 '2 Timothy','Titus','Philemon','Hebrews','James',
							 '1 Peter','2 Peter','1 John','2 John','3 John',
							 'Jude','Revelation'),
			   'Catholic' => array('Tobit','Judith','1 Maccabees',
								   '2 Maccabees','Wisdom','Sirach','Baruch',
								   'Bel','Susanna','Azariah'),
			   'Eastern' => array('1 Esdras','2 Esdras','Manasses',
								  '3 Maccabees','4 Maccabees'));
	$allcv = explode(';',$cv);
	foreach ($allcv as $onecv) {
		$chapters[] = explode(':',$onecv)[0];
	}
	if ($book == 'Daniel' && (in_array('13',$chapters) ||
							  in_array('14',$chapters))) {
		$canon = 'Catholic';
	} elseif ($book == 'Psalm' && in_array('151',$chapters)) {
		$canon = 'Eastern';
	} else {
		foreach ($a as $canon => $books) {
			if (in_array($book,$books)) { break; }
		}
	}
	$c = array('OT' => array('KJ21','ASV','AMP','AMPC','BRG','CEB','CJB','CEV',
							 'DARBY','DRA','ERV','ESV','ESVUK','EXB','GNV',
							 'GW','GNT','ICB','ISV','JUB','KJV','AKJV','LEB',
							 'TLB','MSG','MEV','NOG','NABRE','NASB','NCV',
							 'NET','NIRV','NIV','NIVUK','NKJV','NLV','NLT',
							 'NRSV','NRSVA','NRSVACE','NRSVCE','OJB','RSV',
							 'RSVCE','TLV','VOICE','WEB','WYC','YLT'),
			   'NT' => array('KJ21','ASV','AMP','AMPC','BRG','CEB','CJB','CEV',
							 'DARBY','DLNT','DRA','ERV','ESV','ESVUK','EXB',
							 'GNV','GW','GNT','ICB','ISV','PHILLIPS','JUB',
							 'KJV','AKJV','LEB','TLB','MSG','MEV','MOUNCE',
							 'NOG','NABRE','NASB','NCV','NET','NIRV','NIV',
							 'NIVUK','NKJV','NLV','NLT','NRSV','NRSVA',
							 'NRSVACE','NRSVCE','OJB','RSV','RSVCE','TLV',
							 'VOICE','WEB','WE','WYC','YLT'),
			   'Catholic' => array('WYC','RSV','NRSVA','NRSV','GNT','DRA',
								   'CEB','NRSVCE','NRSVACE','RSVCE'),
			   'Eastern' => array('RSV','NRSVA','NRSV','CEB'));
	
	if (in_array($translation,$c[$canon])) { return true; }
	else { return false; }
}

/* --- standardBookName FUNCTION --- */
/* CALLED BY: ConcatLinks
   CALLS: vol
   INPUT VARIABLES:
		$book => Str. the original book name
   RETURNS: Str. the standarized book name
   
   Because several functions require always being sure that we're dealing with
   the correct book, we use this function to return a standardized book name
   (rather than dealing with regular expressions to identify books throughout
   the plugin). */
function standardBookName($book) {
	$first = '(\\bI|First|1st|1)';
	$second = '(\\bII|Second|2nd|2)';
	$third = '(III|Third|3rd|3)';
	$fourth = '(IV|Fourth|4th|4)';
	$vol = '(' . vol() . ')';
	$a = array('Genesis' 		=> 'Genesis|Gen|Ge|Gn',
			   'Exodus' 		=> 'Exodus|Exod?|Ex',
			   'Leviticus' 		=> 'Leviticus|Lev?|Levit?|Lv',
			   'Numbers' 		=> 'Numbers|Nmb|Numb?|Nu',
			   'Deuteronomy' 	=> 'Deuteronomy|Deut?|Dt|Du|De',
			   'Joshua' 		=> 'Joshua|Josh?',
			   'Judges' 		=> 'Judges|Ju?dg?|Jgs',
			   'Ruth' 			=> 'Ruth|Ru',
			   '1 Samuel' 		=> $first . '(\\s|\\+)?(Samuel|Sam?|Sm)',
			   '2 Samuel' 		=> $second . '(\\s|\\+)?(Samuel|Sam?|Sm)',
			   '1 Kings' 		=> $first . '(\\s|\\+)?(Kings|Kn?gs?|Kin?)',
			   '2 Kings' 		=> $second . '(\\s|\\+)?(Kings|Kn?gs?|Kin?)',
			   '1 Chronicles' 	=> $first. '(\\s|\\+)?(Chronicles|Chr?|Chron)',
			   '2 Chronicles' 	=> $second.'(\\s|\\+)?(Chronicles|Chr?|Chron)',
			   'Ezra' 			=> 'Ezra|Ezr',
			   'Nehemiah' 		=> 'Nehemiah|Nehem?|Neh?',
			   'Tobit' 			=> 'Tobit|Tob|Tb',
			   'Judith' 		=> 'Judith|Jdt|Jth',
			   'Esther' 		=> 'Esther|Esth?|((?<!' . $vol . '(\\s|\\+))' .
								   '(?<!' . $vol . '))Es',
			   '1 Maccabees' 	=> $first. '(\\s|\\+)?(Maccabees|Macc|Mc|Ma)',
			   '2 Maccabees' 	=> $second. '(\\s|\\+)?(Maccabees|Macc|Mc|Ma)',
			   '3 Maccabees' 	=> $third. '(\\s|\\+)?(Maccabees|Macc|Mc|Ma)',
			   '4 Maccabees' 	=> $fourth. '(\\s|\\+)?(Maccabees|Macc|Mc|Ma)',
			   'Job' 			=> 'Jo?b',
			   'Psalm' 			=> 'Psalms?|Psa?s?',
			   'Proverbs' 		=> 'Proverbs?|Prov?|Prv?',
			   'Ecclesiastes' 	=> 'Ecclesiastes|Eccles|Eccl?|Ec|Qoheleth|Qoh',
			   'Song of Songs' 	=> 'SongofSongs|CanticleofCanticles|' .
									'Song of Songs|Songs? of Solomon|' .
									'Canticle of Canticles|Songs?ofSolomon|' .
									'Song?|Songs|SS|So|Sg|Cant?',
			   'Wisdom' 		=> 'Wisdom|Wi?s',
			   'Sirach' 		=> 'Sirach|Ecclesiasticus|Sir|Ecclus',
			   'Isaiah' 		=> 'Isaiah|Isa',
			   'Jeremiah' 		=> 'Jeremiah|Jer?|Jerem',
			   'Lamentations' 	=> 'Lamentations|Lam?|Lament?',
			   'Baruch' 		=> 'Baruch|Bar?',
			   'Ezekiel' 		=> 'Ezekiel|Ezek?|Ezk?',
			   'Daniel' 		=> 'Daniel|Dan?|Dn',
			   'Hosea' 			=> 'Hosea|Hos?',
			   'Joel' 			=> 'Joel?|Jl',
			   'Amos' 			=> 'Amos|Am',
			   'Obadiah' 		=> 'Obadiah|Obad?|Ob',
			   'Jonah' 			=> 'Jonah|Jon',
			   'Micah' 			=> 'Micah|Mic?',
			   'Nahum' 			=> 'Nahum|Nah?',
			   'Habakkuk' 		=> 'Habakkuk|Ha?b|Habak',
			   'Zephaniah' 		=> 'Zephaniah|Zeph?',
			   'Haggai' 		=> 'Haggai|Ha?g|Hagg',
			   'Zechariah' 		=> 'Zechariah|Zech?',
			   'Malachi' 		=> 'Malachi|Malac?|Ma?l',
			   '1 Esdras' 		=> $first . '(\\s|\\+)?(Esdras|Es)|' . 
									$third . '(\\s|\\+)?(Esdras|Es)',
			   '2 Esdras' 		=> $second . '(\\s|\\+)?(Esdras|Es)|' .
									$fourth . '(\\s|\\+)?(Esdras|Es)',
			   'Azariah' 		=> 'Azariah|Aza',
			   'Susanna' 		=> 'Susanna|Sus',
			   'Bel' 			=> 'Bel|Bel and the Dragon',
			   'Manasses' 		=> 'Manasses|Prayer of Manasseh|Man',
			   'Matthew' 		=> 'Matthew|Ma?t|Matt',
			   'Mark'			=> 'Mark?|Mr?k',
			   'Luke'			=> 'Luke|Lu?k|Lu',
			   'John'			=> '((?<!' . $vol . '(\\s|\\+))(?<!' . $vol .
								   '))(John|Jh?n)',
			   'Acts'			=> 'Acts?|Ac|Acts of the Apostles',
			   'Romans'			=> 'Romans|Ro?m|Ro',
			   '1 Corinthians'	=> $first . '(\\s|\\+)?' .
								   '(Corinthians|Cor?|Corin)',
			   '2 Corinthians'	=> $second . '(\\s|\\+)?' .
								   '(Corinthians|Cor?|Corin)',
			   'Galatians'		=> 'Galatians|Gal?|Galat',
			   'Ephesians'		=> 'Ephesians|Eph?|Ephes',
			   'Philippians'	=> 'Philippians|Phili?|Phil?|Php',
			   'Colossians'		=> 'Colossians|((?<!' . $vol . '(\\s|\\+))' .
								   '(?<!' . $vol . '))Col?|Colos',
			   '1 Thessalonians'=> $first . '(\\s|\\+)?' .
								   '(Thessalonians|Thess?|Th)',
			   '2 Thessalonians'=> $second . '(\\s|\\+)?' .
								   '(Thessalonians|Thess?|Th)',
			   '1 Timothy'		=> $first . '(\\s|\\+)?(Timothy|Tim?)',
			   '2 Timothy'		=> $second . '(\\s|\\+)?(Timothy|Tim?)',
			   'Titus'			=> 'Titus|Tts|((?<!' . $vol . '(\\s|\\+))(?<!'
								   . $vol . '))Tit?',
			   'Philemon'		=> 'Philemon|Phl?m|Philem',
			   'Hebrews'		=> 'Hebrews|Hebr?|He',
			   'James'			=> 'James|Jam|Jms|Jas?',
			   '1 Peter'		=> $first . '(\\s|\\+)?(Peter|Pete?|Pe?t)',
			   '2 Peter'		=> $second . '(\\s|\\+)?(Peter|Pete?|Pe?t)',
			   '1 John'			=> $first . '(\\s|\\+)?(John|Jh?n|Jo)',
			   '2 John'			=> $second . '(\\s|\\+)?(John|Jh?n|Jo)',
			   '3 John'			=> $third . '(\\s|\\+)?(John|Jh?n|Jo)',
			   'Jude'			=> 'Jude|Judas|Ju',
			   'Revelation'		=> 'Revelations?|Rev?|Rv|Revel|Apocalypse|' .
									'Apoc|Ap');
	foreach ($a as $title => $rgx) {
		$regex = '/(' . $rgx . ')/';
		if (preg_match($regex,$book)) {
			return $title;
		}
	}
	return $book;
}
/* --- generateTargetLink FUNCTION --- */
/* CALLED BY: ConcatLinks, generateTargetLink
   CALLS: opt_name, generateTargetLink
   INPUT VARIABLES:
		$book		=> Str. the book being referenced
		$cv			=> Str. the chapter-verse combo being referenced
		$translation=> Str. the target translation
		$text		=> Str. the display text
   RETURNS: Str. a complete link and display text to the target reference
   
   Depending on settings, either returns an anchor tag, with link and display
   text, where href => BibleGateway.com and the appropriate translation, or
   where href => a relevant local post. */
function generateTargetLink($book,$cv,$translation,$text) {
	$linkOpen = '<a href="';
	$linkMid = '" target="_blank">';
	$linkEnd = '</a>';
	$useLocal = opt_name();
	$useLocal = get_option($useLocal[1],'off');
	if ($useLocal == 'on') {
		$ref = $book;
		$cvall = explode(';',$cv);
		if (count($cvall) > 1) {
			$text = explode(';',$text);
			foreach ($cvall as $i => $newcv) {
				if ($i > 0) { $op .= '; '; }
				$op .= generateTargetLink($book,$newcv,$translation,$text[$i]);
			}
			return $op;
		} else {
			$cthenv = explode(':',$cvall[0]);
			$c = $cthenv[0];
			$c = explode('-',$c[0]);
			$c = $c[0];
			$v = explode(',',$cthenv[1]);
			$v = explode('-',$v[0]);
			$v = $v[0];
			$ref .= ' ' . $c;
			global $wpdb;
			$tablename = $wpdb->prefix . 'posts';
			$results = $wpdb->get_results('SELECT * FROM ' . $tablename .
				' WHERE post_title LIKE "' . $ref .
				'" AND post_status LIKE "publish"', ARRAY_A );
			if (count($results) == 0) { goto NoInternalPost; }
			$slugs = get_option('permalink_structure');
			switch($slugs) {
				case '/%postname%/':
					$items[0] = 'post_name';
					break;
				case '/archives/%post_id%':
					$items[0] = 'ID';
					break;
				case '/%year%/%monthnum%/%postname%/':
				case '/%year%/%monthnum%/%day%/%postname%/':
					$items[0] = 'post_date';
					$items[1] = 'post_name';
					break;
				default:
					$items[0] = 'ID';
					$slugs = '/?p=%post_id%';
					break;
			}
			if ($items[0] == 'post_date') {
				$date = $results[0][$items[0]];
				$date = explode('-',$date);
				$output[] = $date[0];
				$output[] = $date[1];
				if (strpos($slugs,'day') !== false) {$output[] = $date[2];}
				$output[] = $results[0][$items[1]];
			} else {
				foreach($items as $tag) {
					$output[] = $results[0][$tag];
				}
			}
			foreach($output as $datum) {
				$slugs = preg_replace('/(%\w+%)/',$datum,$slugs);
			}
			$base = get_option('siteurl');
			return $linkOpen . $base . $slugs .
				   (is_numeric($v) ? "#c" . $c . 'v' . $v : '') .
				   $linkMid . $text . $linkEnd;
		}
	} else {
NoInternalPost:
		return $linkOpen . 'http://www.biblegateway.com/passage/?search='
				. $book . '+' . $cv . '&version=' . $translation . $linkMid . 
				$text . $linkEnd;
	}
}
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
////////////////////////////// SETTINGS FUNCTIONS /////////////////////////////
///////////////////////////////////////////////////////////////////////////////
/* --- catenata SUBROUTINE --- */
/* CALLED BY: [main]
   
   Creates the SSC settings page under the Settings menu in WP admin */
function catenata() {
	add_options_page('Sola Scripturae Catenata Settings',
					 'Sola Scripturae Catenata','manage_options',
					 'biblelinks-settings','catenata_options');
}

/* --- catenata_options SUBROUTINE --- */
/* CALLED BY: catenata
   CALLS: getTranslation, translations
   
   Generates the actual Settings page, complete with form, submit button, and
   current settings */
function catenata_options() {
	if (!current_user_can('manage_options')) {
	 wp_die(__('You do not have sufficient permissions to access this page.'));
	}
	
	$hidnm = 'ssc_submit_hid';
	$datanm[] = 'catenata_translation';
	$datanm[] = 'catenata_useinternal';
	if (isset($_POST[$hidnm]) && $_POST[$hidnm] == 'Y') {
		foreach ($datanm as $i => $nm) {
			$opt_val = $_POST[$nm];
			$opt_name = opt_name();
			update_option($opt_name[$i],$opt_val);
		}
		echo '<div class="updated"><p><strong>';
		_e('Settings saved!','menu-test');
		echo '</strong></p></div>';
	}
	
	$opt_curr[] = getTranslation();
	$opt_curr[] = get_option('ssc_selflink','off');
	$alltrs = translations();
	
	echo '<div class="wrap">';
	echo '<div style="padding:10px 20px 0 0;"><span style="font-size:23px;font-weight:400;margin:0;padding: 9px 15px 4px 0; line-height:29px;color:#23282d;">Sola Scripturae Catenata Settings</span>';
	echo '<h3>Only Links to Scripture</h3></div>';
	echo '<div class="notice notice-info"><p>If you want a particular reference not to create a link, place the shortcodes <strong>[noref]</strong> and <strong>[/noref]</strong> around <strong>that reference only!</strong> The shortcode will fail (and display!) if you try to enclose multiple references with it.</p></div>';
	echo '<form name="form1" method="post" action="">';
	echo '	<table class="form-table">';
	echo '		<tbody>';
	echo '			<tr>';
	echo '				<th scope="row">Preferred Translation</th>';
	echo '				<td>';
	echo '					<select name="' . $datanm[0] . '">';
	foreach ($alltrs as $abbr => $full) {
		echo '                      <option ' . ($opt_curr[0] == $abbr ? 'selected ' : '') . 'value="' . $abbr . '">' . $full . '</option>';
	}
	echo '					</select>';
	echo '				</td>';
	echo '			</tr>';
	echo '			<tr>';
	echo '				<th scope="row">Internal Linking</th>';
	echo '				<td>';
	echo '					<select name="' . $datanm[1] . '">';
	echo '						<option ' . ($opt_curr[1] == 'off' ? 'selected ' : '') . 'value="off">Off</option>';
	echo '						<option ' . ($opt_curr[1] == 'on' ? 'selected ' : '') . 'value="on">On</option>';
	echo '					</select>';
	echo '				</td>';
	echo '			</tr>';
	echo '		</tbody>';
	echo '	</table>';
	echo '	<input name="' . $hidnm . '" value="Y" type="hidden"/>';
	echo '	<p class="submit">';
	echo '		<input id="submit" class="button button-primary" name="submit" value="Save Changes" type="submit"/>';
	echo '	</p>';
	echo '</form>';
	echo '</div>';
}

/* --- add_ssc_action_links SUBROUTINE --- */
/* CALLED BY: [main]
   
   Adds a link to the SSC settings page on the Plugins page */
function add_ssc_action_links( $links ) {
  $mylinks = array(
	'<a href="' . admin_url( 'options-general.php?page=biblelinks-settings' ) .
	'">Settings</a>',
  );
  return array_merge( $links, $mylinks );
}

/* --- add_ssc_row_meta SUBROUTINE --- */
/* CALLED BY: [main]
   
   Adds several relevant details to the Plugins page */
function add_ssc_row_meta($links, $file) {
	if (strpos($file,'sola-scripturae-catenata.php') !== false) {
		$new_links = array(
						'by' => 'By <a href="http://www.versorbooks.com">' .
								'Nathaniel Turner</a>',
						'doc' => '<a href="../wp-content/plugins/' .
								'sola-scripturae-catenata/readme.txt">' .
								'Documentation</a>'
					);
		$links = array_merge($links,$new_links);
	}
	return $links;
}
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
////////////////////////////////// CONSTANTS //////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
/* --- vol CONSTANT --- */
/* CALLED BY: PatternSearch, ConcatLinks, standardBookName
   RETURNS: Str. a regular expression for book volumes
   
   The partial regular expression string to cover volumes in book names. */
function vol() {
	return 'I+\b|1st|2nd|3rd|First|Second|Third|1|2|3|IV|4th|Fourth|4';
}

/* --- opt_name CONSTANT --- */
/* CALLED BY: getTranslation, generateTargetLink, [main], catenata_options
   RETURNS: Arr. (Str.) options used by this plugin
   
   The list of options primarily used by this plugin. */
function opt_name() { return array('ssc_translation','ssc_selflink'); }

/* --- translations CONSTANT --- */
/* CALLED BY: catenata_options
   RETURNS: Arr. (Str.) associative array of available translations
   
   An associative array of all translations available to this plugin (i.e., all
   English translations on BibleGateway.com). Abbreviations are keys, full
   names are array values. */
function translations() {
	$a = array('KJ21' => '21st Century King James Version (KJ21)',
			   'ASV' => 'American Standard Version (ASV)',
			   'AMP' => 'Amplified Bible (AMP)',
			   'AMPC' => 'Amplified Bible, Classic Edition (AMPC)',
			   'BRG' => 'BRG Bible (BRG)',
			   'CEB' => 'Common English Bible (CEB)',
			   'CJB' => 'Complete Jewish Bible (CJB)',
			   'CEV' => 'Contemporary English Version (CEV)',
			   'DARBY' => 'Darby Translation (DARBY)',
			   'DLNT' => 'Disciples\' Literal New Testament (DLNT)',
			   'DRA' => 'Douay-Rheims 1899 American Edition (DRA)',
			   'ERV' => 'Easy-to-Read Version (ERV)',
			   'ESV' => 'English Standard Version (ESV)',
			   'ESVUK' => 'English Standard Version Anglicised (ESVUK)',
			   'EXB' => 'Expanded Bible (EXB)',
			   'GNV' => '1599 Geneva Bible (GNV)',
			   'GW' => 'GOD’S WORD Translation (GW)',
			   'GNT' => 'Good News Translation (GNT)',
			   'ICB' => 'International Children’s Bible (ICB)',
			   'ISV' => 'International Standard Version (ISV)',
			   'PHILLIPS' => 'J.B. Phillips New Testament (PHILLIPS)',
			   'JUB' => 'Jubilee Bible 2000 (JUB)',
			   'KJV' => 'King James Version (KJV)',
			   'AKJV' => 'Authorized (King James) Version (AKJV)',
			   'LEB' => 'Lexham English Bible (LEB)',
			   'TLB' => 'Living Bible (TLB)',
			   'MSG' => 'The Message (MSG)',
			   'MEV' => 'Modern English Version (MEV)',
			   'MOUNCE' => 'Mounce Reverse-Interlinear New Testament (MOUNCE)',
			   'NOG' => 'Names of God Bible (NOG)',
			   'NABRE' => 'New American Bible (Revised Edition) (NABRE)',
			   'NASB' => 'New American Standard Bible (NASB)',
			   'NCV' => 'New Century Version (NCV)',
			   'NET' => 'New English Translation (NET Bible)',
			   'NIRV' => 'New International Reader\'s Version (NIRV)',
			   'NIV' => 'New International Version (NIV)',
			   'NIVUK' => 'New International Version - UK (NIVUK)',
			   'NKJV' => 'New King James Version (NKJV)',
			   'NLV' => 'New Life Version (NLV)',
			   'NLT' => 'New Living Translation (NLT)',
			   'NRSV' => 'New Revised Standard Version (NRSV)',
			   'NRSVA' => 'New Revised Standard Version, Anglicised (NRSVA)',
			   'NRSVACE' => 'New Revised Standard Version, Anglicised Catholic Edition (NRSVACE)',
			   'NRSVCE' => 'New Revised Standard Version Catholic Edition (NRSVCE)',
			   'OJB' => 'Orthodox Jewish Bible (OJB)',
			   'RSV' => 'Revised Standard Version (RSV)',
			   'RSVCE' => 'Revised Standard Version Catholic Edition (RSVCE)',
			   'TLV' => 'Tree of Life Version (TLV)',
			   'VOICE' => 'The Voice (VOICE)',
			   'WEB' => 'World English Bible (WEB)',
			   'WE' => 'Worldwide English (New Testament) (WE)',
			   'WYC' => 'Wycliffe Bible (WYC)',
			   'YLT' => 'Young\'s Literal Translation (YLT)');
	return $a;
}

/* --- voldBooks CONSTANT --- */
/* CALLED BY: PatternSearch
   RETURNS: Str. a regular expression listing books that have volumes
   
   The partial regular expression to cover only those books which may be
   preceded by volume numbers. */
function voldBooks() {
	return 'Samuel|Sam?|Sm|Kings|Kn?gs?|Kin?|Chronicles|Chr?|Chron|'		  .
		   'Maccabees|Macc|Mc|Ma|Esdras|Es|Corinthians|Cor?|Corin|'			  .
		   'Thessalonians|Thess?|Th|Timothy|Tim?|Peter|Pete?|Pe?t|'			  .
		   'John|Jh?n|Jo';
}
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////// MAIN ////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

// make sure that settings exist
$opt_name = opt_name();
add_option($opt_name[0],getTranslation());
add_option($opt_name[1],'off');

// filter post/comment content
add_filter('the_content', 'PatternSearch');

// add a settings menu
add_action('admin_menu','catenata');

// add a custom settings link to the Plugins page of WP admin
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__),
			'add_ssc_action_links' );

// add custom information to the Plugins page of WP admin
add_filter('plugin_row_meta','add_ssc_row_meta',10,2);

///////////////////////////////////////////////////////////////////////////////
?>