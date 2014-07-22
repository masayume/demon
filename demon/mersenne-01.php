<?php

// init master seed
mt_srand(100);

// init planet name data
planet_ini();

# echo "\n 1-100: " . myrand(1,100,0);
# echo "\n 1-100: " . myrand(1,100,100000);

	print "<html><head></head>";
	print "<link href='http://fonts.googleapis.com/css?family=Oswald:700' rel='stylesheet' type='text/css'>";
	print "\n<style=\"text/css\">";
	print "\n</style><body style=\"background-color:#000000; color: #ffffff; font-family: 'Oswald', sans-serif;\">\n";
	print "ini:" . count($planetname_ini) . "  mid:" . count($planetname_mid) ."  end:" . count($planetname_end);

	print "<hr><div style=\"text-align:center;\">";
	for ($i=1; $i<=100; $i++) {
		$planet_array = planet_gen();
		print "\n<div class=\"planet\" style=\"display:inline-block; width:200px;\" > <img src=\"/demon/img/" . $planet_array[1] . "\" width=\"" . $planet_array[2]. "px\"><br clear=\"all\"><p><small>$i:</small> " . strtoupper($planet_array[0]) . "</p></div>"; 
	}

	print "</div><hr>";
	print "</body></html>";

echo "\n";

exit(0);


// = - = - = - = - = - = - = - = - = - = - = - = -

function myrand ($lo, $hi, $consume) {

	for ($i=1; $i<$consume; $i++) {
		mt_rand(1,1);
	}
	return mt_rand($lo, $hi);
}

function planet_ini() {
	global $planetname_ini, $planetname_mid, $planetname_end, $planet_pic;
	$planetname_ini = array("a","al","bel","ca","car","chal","cyl","da","dei","di","e","en","eu","e","ga","ha","har","he","i","ia","ju","ka","kal","la","le","ly","m","me","mer","mi","mne","mu","ou","pa","pan","pra","pho","pro","rhe","sa","si","ska","spon","tar","tay","the","thy","ti","ve","y");
	$planetname_mid = array("an","bio","bo","ce","cli","cu","de","di","do","dras","e","ga","ge","ger","i","la","le","les","ly","ma","mal","me","mis","mo","nan","ni","no","o","pa","pha","pe","pi","po","ry","ro","si","so","te","the","tla","u","xi");
	$planetname_end = array("a","ars","be","bos","da","de","dus","e","gir","ke","lya","mas","me","mir","mos","ne","nus","on","ops","pe","pso","ra","rix","ros","ry","s","tan","te","ter","thi","ti","tis","tne","to","tur","tus","tyl","us","ve","vi","vos");
	$planet_pic	= array("01.png","02.png","03.png","04.png","05.png","06.png","07.png","08.png","09.png",);
	
} // end function planet_ini

function planet_gen() {
	global $planetname_ini, $planetname_mid, $planetname_end, $planet_pic;
	$planet_name = "";
	$planet = array();
	switch(mt_rand(1,10)){
  		case 1:
  		case 2:
    			$planet_name = $planetname_ini[(mt_rand(1,1000) % count($planetname_ini))] . $planetname_end[(mt_rand(1,1000) % count($planetname_end))];
    		break;
        	case 9:
        	case 10:
        	        $planet_name = $planetname_ini[(mt_rand(1,1000) % count($planetname_ini))] . $planetname_mid[(mt_rand(1,1000) % count($planetname_mid))] . $planetname_mid[(mt_rand(1,1000) % count($planetname_mid))] . $planetname_end[(mt_rand(1,1000) % count($planetname_end))];
        	break;
  		default:
                	$planet_name = $planetname_ini[(mt_rand(1,1000) % count($planetname_ini))] . $planetname_mid[(mt_rand(1,1000) % count($planetname_mid))] . $planetname_end[(mt_rand(1,1000) % count($planetname_end))];
	}	

// planet name
	$planet[0] 	= $planet_name;
// planet pic
	$planet[1] 	= $planet_pic[(mt_rand(1,1000) % count($planet_pic))];
// planet size
	$planet[2]	= mt_rand(50,200);

	return $planet;
}

// 

?>
