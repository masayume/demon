<?php

// TODO
// 1) http://www.html5rocks.com/en/tutorials/canvas/imagefilters/

// REF.
// http://superpixeltime.com/

// PARAMETERS

parse_str($_SERVER['QUERY_STRING'], $params);
if ($params['seed']) {
	$master_seed	= $params['seed'];
} else {
	$master_seed	= 100;
}
$page   = 1; $nextp  = 2; $prevp  = 1; $res_qs = "";
if ($params['page']) {
	$page	= $params['page']; $nextp = $page+1; $prevp = $page-1;
} else {
	$page	= 1; $nextp	= 2; $prevp	= 1;
}
if (!$params['results']) {
	$results = 25;
} else {
	$results = $params['results'];
	$res_qs  = "&results=" . $results;
}

// init master seed
mt_srand($master_seed);

// init planet name data
planet_ini();

$javascript 	= jfunction();

echo <<< EOT
<html><head>
<title>generator</title>
<link href='http://fonts.googleapis.com/css?family=Oswald:700' rel='stylesheet' type='text/css'>
<style type="text/css">
a:link  { color:#ffffff; } 
a:visited { color:#ffffff; } 
</style>
</head>
<body style="background-color:#000000; color: #ffffff; font-family: 'Oswald', sans-serif;">
$javascript
EOT;


// navigation
	print " NAVIGATION: " . "<a href='" .$_SERVER['PHP_SELF'] . "?seed=" . $master_seed . "&page=" . $prevp . $res_qs . "'> &lt;&lt; previous </a> ||| <a href='" . $_SERVER['PHP_SELF'] . "?seed=" . $master_seed . "&page=" .$nextp . $res_qs . "'> next >> </a>";

	print "\n\n\n\n<hr><div style=\"text-align:center;\">";


// show n planets
	for ($i=1; $i<=$page * $results; $i++) {

// planetary values generation
                $planet_array = planet_gen();

		if ($i>(($page - 1) * $results)) {
			$img = "/demon/img/" . $planet_array[1];
			$planet_name = strtoupper($planet_array[0]);
			// $planet_img = "<img id=\"planet-$i\" src=\"/demon/img/" .$planet_array[1] . "\" width=\"" . $planet_array[2]. "px\">"; 
			$planet_url = "/demon/img/" . $planet_array[1];
			$planet_img = "<img id=\"planet-$i\" src=\"/demon/img/" .$planet_array[1] . "\" width=0 height=0 \">"; 
			$width		= $planet_array[2];
			$filter		= $planet_array[3];
			
			echo planet($i, $planet_url, $planet_name, $width, $img, $filter);
	
		}
	}

	print "</div><hr>";
	print "ini: " . count($planetname_ini) . "  mid:" . count($planetname_mid) ."  end:" . count($planetname_end);
	print "</body></html>";

echo "\n";

exit(0);


// = - = - = - = - = - = - = - = - = - = - = - = -

function planet($i, $planet_url, $planet_name, $width, $img, $filter) {

        $planet = <<< EOP

<div class="planet" style="display:inline-block; width:200px;">
<!--    <canvas id="myImage-$i" width="200" height="200" style="border:1px solid #d3d3d3;"></canvas> -->
    	<script>
	    function traceplanet$i() {
		// window.alert("planet: $planet_name on canvas $i");	
		var canvas$i 		= fx.canvas();
    		var image$i 		= document.getElementById('myImage-$i');
    		var texture$i 		= canvas$i.texture(image$i);
    		var filter$i		= "canvas$i.draw(texture$i)$filter.update()"; // apply the ink filter
		eval(filter$i);
    		image$i.parentNode.insertBefore(canvas$i, image$i);
    		image$i.parentNode.removeChild(image$i);
	    }
    	</script>
	<img id="myImage-$i" width="$width" height="$width" src="$planet_url" onload="traceplanet$i()">
	<br clear="all"><small>$i:</small> $planet_name 
	<br><small><small> $filter </small></small>
</div>

EOP;

        return $planet;

} // end function planet

function jfunction() {

	$jfun = "<script src=\"./glfx.js\"></script>"; 
	return $jfun;

} // end function jfunction 


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
	$planet_pic	= array("01.png","02.png","03.png","04.png","05.png","06.png","07.png","08.png","09.png","10.png","11.png","12.png","13.png","14.png","15.png","16.png","17.png","18.png","19.png","20.png",);
	
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

/*
// apply the hue/saturation filter - range: -1:1 ; -1:1
    // canvas3.draw(texture3).huesaturation(-0.83,0).update(); 
// apply the colorHalftone filter - range: 0:1 
    canvas7.draw(texture7).colorHalftone(100,100,0.785,2).update(); 
*/

// planet filter
	$filter = "";
        switch(mt_rand(1,9)){
                case 1:
			$v1	= mt_rand(5,20);
			$filter = ".denoise($v1)"; break;
                case 2:
			$v1	= mt_rand(1,3) / 10;
			$filter = ".noise($v1)"; break;
                case 3:
			$v1	= mt_rand(1,100) / 100;
                        $filter = ".sepia($v1)"; break;
                case 4:
			$v1	= mt_rand(1,5);
			$v2	= mt_rand(1,5);
                        $filter = ".unsharpMask($v1,$v2)"; break;
                case 5:
			$v1	= mt_rand(-10,10) / 10;
                        $filter = ".vibrance($v1)"; break;
                case 6:
			$v1	= mt_rand(1,35) / 10;
			$v2	= mt_rand(1,10) / 10;
			$v3	= mt_rand(1,90);
                        $filter = ".lensBlur($v1,$v2,$v3)"; break;
                case 7:
			$v1	= mt_rand(1,5) / 10;
			$filter = ".ink($v1)"; break;
                case 8:
			$min	= 1; $max = 3;
                        $v1a    = mt_rand($min,$max) / 10;
                        $v1b    = mt_rand($min,$max) / 10;
                        $v2a    = mt_rand($min,$max) / 10;
                        $v2b    = mt_rand($min,$max) / 10;
                        $v3a    = mt_rand($min,$max) / 10;
                        $v3b    = mt_rand($min,$max) / 10;
			$curves = "";
			switch(mt_rand(1,3)){
				case 1:
					$curves = "[[$v1a,$v1b],[$v2a,$v2b]],[[0,0],[1,1]],[[0,0],[1,1]]"; break;
                                case 2:
					$curves = "[[0,0],[1,1]],[[$v1a,$v1b],[$v2a,$v2b]],[[0,0],[1,1]]"; break;
                                case 3:
					$curves = "[[0,0],[1,1]],[[0,0],[1,1]],[[$v1a,$v1b],[$v2a,$v2b]]"; break;
			}
                        $filter = ".curves($curves)"; break;
		default;
		
	}

	$planet[3]	= $filter;

	return $planet;
}

// 

?>
