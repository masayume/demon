<?php

// TODO
// other objects (type param)
// planet data
// http://stackoverflow.com/questions/667045/getpixel-from-html-canvas

// DONE
// http://www.html5rocks.com/en/tutorials/canvas/imagefilters/

// REF.
// http://superpixeltime.com/

// PARAMETERS

$layerdir	= "./img/demons/";

parse_str($_SERVER['QUERY_STRING'], $params);
if ($params['seed']) {
	$master_seed	= $params['seed'];
} else {
	$master_seed	= 100;
}
$page   = 1; $nextp  = 2; $prevp  = 1; $res_qs = ""; $type = "";
if ($params['page']) {
	$page	= $params['page']; $nextp = $page+1; $prevp = $page-1;
} else {
	$page	= 1; $nextp	= 2; $prevp	= 1;
}
if (!$params['results']) {
	$results = 12;
} else {
	$results = $params['results'];
	$res_qs  .= "&results=" . $results;
}
if (!$params['type']) {
        $type = "planets";
} else {
        $type = $params['type'];
        $res_qs  = "&type=" . $type;
}


// init master seed
mt_srand($master_seed);

// init planet, demon name data
planet_ini();
demon_ini();

$javascript 	= jfunction();
$css		= overcss();

echo <<< EOT
<html><head>
<title>generator</title>
<link href='http://fonts.googleapis.com/css?family=Oswald:700' rel='stylesheet' type='text/css'>
<style type="text/css">
a:link  { color:#ffffff; } 
a:visited { color:#ffffff; } 
</style>
$css
</head>
<body style="background-color:#000000; color: #ffffff; font-family: 'Oswald', sans-serif;">
$javascript
EOT;


// navigation && MAIN div
	print " NAVIGATION: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" . "<a href='" .$_SERVER['PHP_SELF'] . "?seed=" . $master_seed . "&page=" . $prevp . $res_qs . "'> &lt;&lt; previous </a> &nbsp;&nbsp;&nbsp; <a href='" . $_SERVER['PHP_SELF'] . "?seed=" . $master_seed . "&page=" .$nextp . $res_qs . "'> next >> </a> &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; <a href='" . $_SERVER['PHP_SELF'] . "?seed=" . $master_seed . "&page=" .$nextp . "&type=planets'>PLANETS</a>  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; <a href='" . $_SERVER['PHP_SELF'] . "?seed=" . $master_seed . "&page=" .$nextp . "&type=demons'>DEMONS</a>  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; <a href='" . $_SERVER['PHP_SELF'] . "?seed=" . $master_seed . "&page=" .$nextp . "&type=backs'>BACKGROUNDS</a>"; 
	print "\n\n\n\n<hr><div style=\"text-align:center;\">";

// PLANETS

	if ($type == "planets") {
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
	} // end planets

// DEMONS 

        if ($type == "demons") {
                for ($i=1; $i<=$page * $results; $i++) {
			$imgpath = "/demon/img/demons/" ;
			$demon_array = demon_gen();

                        if ($i>(($page - 1) * $results)) {

				$demon_name 	= $demon_array[0];
				$demon_url 	= $demon_array[1];
				$width		= 128;
				// echo $demon_img;
				echo demon($i, $imgpath, $demon_url, $demon_name, $width);

			}
		}
	} // end demons

// BACKGROUNDS - background layers 
// 	skybox
//	skyboxfx
// 	skyline
//	horizon
//	farawaybackground
//	background
//	nearbybackground
//	foreground

        if ($type == "backs") {
                for ($i=1; $i<=$page * $results; $i++) {

                        if ($i>(($page - 1) * $results)) {

                                echo "background " . $i . " ";

                        }
                }
        } // end backgrounds

	print "</div><hr>";
	print "ini: " . count($planetname_ini) . "  mid:" . count($planetname_mid) ."  end:" . count($planetname_end);
	print "</body></html>";

echo "\n";

exit(0);


// = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = -
// = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = -
// = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = - = -

function demon_layers($dir) {

	$dlayers	= array();
	$arr2ret	= array();
	if ($handle = opendir($dir)) {

	    while (false !== ($entry = readdir($handle))) {
	        if ($entry != "." && $entry != "..") {
	            	array_push($dlayers, $entry);
			// echo "$entry\n";
	        }
	    }
	    closedir($handle);
	}

// create various parts	
	foreach (array("HE", "LW", "RW", "BO", "LB") as $part) {
		$demon_elems	= array();
		$demon_elems 	= kind_elem($part, $dlayers); // elementi di tipo "HE"... 

		array_push($arr2ret, $demon_elems[(mt_rand(1,1000) % count($demon_elems))]); // carico nell'array da tornare l'rt_rnd-esimo elemento
	}

	$arr2ret[1] = $arr2ret[2]; // same right and left wing
	$arr2ret[1] = preg_replace("/RW/", "LW", $arr2ret[1]);

	return $arr2ret;

} // end function demon_layers


function kind_elem($kind, $dlayers) {

	$arr2ret  = array();
        foreach ($dlayers as $dlayer) { 
		if (strstr($dlayer, $kind)) { array_push($arr2ret, $dlayer); } }
	


	return $arr2ret;

} // end function rndret_elem()


function demon($i, $imgpath, $demon_url) {
        $demon = <<< EOP


<div id="container" class="demon" style="display:inline-block; width:200px;">
        <script>
            function tracedemon$i() {
                // window.alert("demon: $demon_name on canvas $i");   
                var canvas$i            = fx.canvas();
                var image$i             = document.getElementById('myImage-$i');
                var texture$i           = canvas$i.texture(image$i);
                var filter$i            = "canvas$i.draw(texture$i)$filter.update()"; // apply the ink filter
                eval(filter$i);
                image$i.parentNode.insertBefore(canvas$i, image$i);
                image$i.parentNode.removeChild(image$i);
            }
        </script>
        <div id='div0'><img id="myImage-$i-0" width="$width" height="$width" src="$imgpath$demon_url[0]" onload="tracedemon_0$i()"></div>
        <div id='div1'><img id="myImage-$i-1" width="$width" height="$width" src="$imgpath$demon_url[1]" onload="tracedemon_1$i()"></div>
        <div id='div2'><img id="myImage-$i-2" width="$width" height="$width" src="$imgpath$demon_url[2]" onload="tracedemon_2$i()"></div>
        <div id='div3'><img id="myImage-$i-3" width="$width" height="$width" src="$imgpath$demon_url[3]" onload="tracedemon_3$i()"></div>
        <div id='div4'><img id="myImage-$i-4" width="$width" height="$width" src="$imgpath$demon_url[4]" onload="tracedemon_4$i()"></div>
        <br clear="all"><small>$i:</small> $demon_name
        <br><small><small> $filter </small></small>
</div>

EOP;

        return $demon;

}

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

function overcss() {

	$overcss = '<link rel="stylesheet" type="text/css" href="./overcss.css">';
	return $overcss;

} // end function overcss

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

function demon_ini() {

        global $demonname_ini, $demonname_mid, $demonname_end, $demon_pic;
        $demonname_ini = array("a","ve","y");
        $demonname_mid = array("an","u","xi");
        $demonname_end = array("a","vi","vos");

} // end function demon_ini

function demon_gen() {

        global $demonname_ini, $demonname_mid, $demonname_end, $demon_pic, $layerdir;
        $demon_name = "";
        $demon = array();
        switch(mt_rand(1,10)){
                case 1:
                case 2:
                        $demon_name = $demonname_ini[(mt_rand(1,1000) % count($demonname_ini))] . $demonname_end[(mt_rand(1,1000) % count($demonname_end))];
                break;
                case 9:
                case 10:
                        $demon_name = $demonname_ini[(mt_rand(1,1000) % count($demonname_ini))] . $demonname_mid[(mt_rand(1,1000) % count($demonname_mid))] . $demonname_mid[(mt_rand(1,1000) % count($demonname_mid))] . $demonname_end[(mt_rand(1,1000) % count($demonname_end))];
                break;
                default:
                        $demon_name = $demonname_ini[(mt_rand(1,1000) % count($demonname_ini))] . $demonname_mid[(mt_rand(1,1000) % count($demonname_mid))] . $demonname_end[(mt_rand(1,1000) % count($demonname_end))];
        }

// demon name
        $demon[0]      = $demon_name;

// demon pic
        $demon[1]      = demon_layers($layerdir);


// demon size
        $demon[2]      = mt_rand(50,200);

	return $demon;

} // end function demon_gen

// 

?>
