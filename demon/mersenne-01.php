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
EOT;


// navigation
	print " NAVIGATION: " . "<a href='" .$_SERVER['PHP_SELF'] . "?seed=" . $master_seed . "&page=" . $prevp . $res_qs . "'> &lt;&lt; previous </a> ||| <a href='" . $_SERVER['PHP_SELF'] . "?seed=" . $master_seed . "&page=" .$nextp . $res_qs . "'> next >> </a>";

	print "\n\n\n\n<hr><div style=\"text-align:center;\">";

	for ($i=1; $i<=$page * $results; $i++) {
//
                $planet_array = planet_gen();

		if ($i>(($page - 1) * $results)) {

			$planet_name = strtoupper($planet_array[0]);
			// $planet_img = "<img id=\"planet-$i\" src=\"/demon/img/" .$planet_array[1] . "\" width=\"" . $planet_array[2]. "px\">"; 
			$planet_img 	= "<img id=\"planet-$i\" src=\"/demon/img/" .$planet_array[1] . "\" width=0 height=0 \">"; 
			$width		= $planet_array[2];
	
				echo <<< EOP
<div class="planet" style="display:inline-block; width:200px;">
$planet_img
<br clear="all"><p><small>$i:</small> $planet_name </p>
<canvas id="myCanvas-$i" width="200" height="200" style="border:1px solid #d3d3d3;"> 
<script>
var c=document.getElementById("myCanvas-$i");
var ctx=c.getContext("2d");
var img=document.getElementById("planet-$i");
ctx.drawImage(img,10,10,$width,$width);

runFilter('myCanvas-$i', Filters.grayscale);
</script>
</div>		

EOP;
		}
	}

	print "</div><hr>";
	print "$javascript";
	print "ini: " . count($planetname_ini) . "  mid:" . count($planetname_mid) ."  end:" . count($planetname_end);
	print "</body></html>";

echo "\n";

exit(0);


// = - = - = - = - = - = - = - = - = - = - = - = -

function jfunction() {

	$jfun = " 
<script type=\"text/javascript\">
        Filters = {};
        Filters.getPixels = function(img) {
          var c,ctx;
          if (img.getContext) {
            c = img;
            try { ctx = c.getContext('2d'); } catch(e) {}
          }
          if (!ctx) {
            c = this.getCanvas(img.width, img.height);
            ctx = c.getContext('2d');
            ctx.drawImage(img, 0, 0);
          }
          return ctx.getImageData(0,0,c.width,c.height);
        };

        Filters.getCanvas = function(w,h) {
          var c = document.createElement('canvas');
          c.width = w;
          c.height = h;
          return c;
        };

        Filters.filterImage = function(filter, image, var_args) {
          var args = [this.getPixels(image)];
          for (var i=2; i<arguments.length; i++) {
            args.push(arguments[i]);
          }
          return filter.apply(null, args);
        };

        Filters.grayscale = function(pixels, args) {
          var d = pixels.data;
          for (var i=0; i<d.length; i+=4) {
            var r = d[i];
            var g = d[i+1];
            var b = d[i+2];
            // CIE luminance for the RGB
            var v = 0.2126*r + 0.7152*g + 0.0722*b;
            d[i] = d[i+1] = d[i+2] = v
          }
          return pixels;
        };

        Filters.brightness = function(pixels, adjustment) {
          var d = pixels.data;
          for (var i=0; i<d.length; i+=4) {
            d[i] += adjustment;
            d[i+1] += adjustment;
            d[i+2] += adjustment;
          }
          return pixels;
        };

        Filters.threshold = function(pixels, threshold) {
          var d = pixels.data;
          for (var i=0; i<d.length; i+=4) {
            var r = d[i];
            var g = d[i+1];
            var b = d[i+2];
            var v = (0.2126*r + 0.7152*g + 0.0722*b >= threshold) ? 255 : 0;
            d[i] = d[i+1] = d[i+2] = v
          }
          return pixels;
        };

        Filters.tmpCanvas = document.createElement('canvas');
        Filters.tmpCtx = Filters.tmpCanvas.getContext('2d');

        Filters.createImageData = function(w,h) {
          return this.tmpCtx.createImageData(w,h);
        };

        Filters.convolute = function(pixels, weights, opaque) {
          var side = Math.round(Math.sqrt(weights.length));
          var halfSide = Math.floor(side/2);

          var src = pixels.data;
          var sw = pixels.width;
          var sh = pixels.height;

          var w = sw;
          var h = sh;
          var output = Filters.createImageData(w, h);
          var dst = output.data;

          var alphaFac = opaque ? 1 : 0;

          for (var y=0; y<h; y++) {
            for (var x=0; x<w; x++) {
              var sy = y;
              var sx = x;
              var dstOff = (y*w+x)*4;
              var r=0, g=0, b=0, a=0;
              for (var cy=0; cy<side; cy++) {
                for (var cx=0; cx<side; cx++) {
                  var scy = Math.min(sh-1, Math.max(0, sy + cy - halfSide));
                  var scx = Math.min(sw-1, Math.max(0, sx + cx - halfSide));
                  var srcOff = (scy*sw+scx)*4;
                  var wt = weights[cy*side+cx];
                  r += src[srcOff] * wt;
                  g += src[srcOff+1] * wt;
                  b += src[srcOff+2] * wt;
                  a += src[srcOff+3] * wt;
                }
              }
              dst[dstOff] = r;
              dst[dstOff+1] = g;
              dst[dstOff+2] = b;
              dst[dstOff+3] = a + alphaFac*(255-a);
            }
          }
          return output;
        };

        if (!window.Float32Array)
          Float32Array = Array;

        Filters.convoluteFloat32 = function(pixels, weights, opaque) {
          var side = Math.round(Math.sqrt(weights.length));
          var halfSide = Math.floor(side/2);

          var src = pixels.data;
          var sw = pixels.width;
          var sh = pixels.height;

          var w = sw;
          var h = sh;
          var output = {
            width: w, height: h, data: new Float32Array(w*h*4)
          };
          var dst = output.data;

          var alphaFac = opaque ? 1 : 0;

          for (var y=0; y<h; y++) {
            for (var x=0; x<w; x++) {
              var sy = y;
              var sx = x;
              var dstOff = (y*w+x)*4;
              var r=0, g=0, b=0, a=0;
              for (var cy=0; cy<side; cy++) {
                for (var cx=0; cx<side; cx++) {
                  var scy = Math.min(sh-1, Math.max(0, sy + cy - halfSide));
                  var scx = Math.min(sw-1, Math.max(0, sx + cx - halfSide));
                  var srcOff = (scy*sw+scx)*4;
                  var wt = weights[cy*side+cx];
                  r += src[srcOff] * wt;
                  g += src[srcOff+1] * wt;
                  b += src[srcOff+2] * wt;
                  a += src[srcOff+3] * wt;
                }
              }
              dst[dstOff] = r;
              dst[dstOff+1] = g;
              dst[dstOff+2] = b;
              dst[dstOff+3] = a + alphaFac*(255-a);
            }
          }
          return output;
        };
  </script>
  <script>
    var img = document.getElementById('myCanvas-1');
    img.addEventListener('load', function() {

      var canvases = document.getElementsByTagName('canvas');
      for (var i=0; i<canvases.length; i++) {
        var c = canvases[i];
        c.parentNode.insertBefore(img.cloneNode(true), c);
        c.style.display = 'none';
      }

      function runFilter(id, filter, arg1, arg2, arg3) {
        var c = document.getElementById(id);
        var s = c.previousSibling.style;
        var b = c.parentNode.getElementsByTagName('button')[0];
        if (b.originalText == null) {
          b.originalText = b.textContent;
        }
        if (s.display == 'none') {
          s.display = 'inline';
          c.style.display = 'none';
          b.textContent = b.originalText;
        } else {
          var idata = Filters.filterImage(filter, img, arg1, arg2, arg3);
          c.width = idata.width;
          c.height = idata.height;
          var ctx = c.getContext('2d');
          ctx.putImageData(idata, 0, 0);
          s.display = 'none';
          c.style.display = 'inline';
          b.textContent = 'Restore original image';
        }
      }

      grayscale = function() {
        runFilter('grayscale', Filters.grayscale);
      }

      brightness = function() {
        runFilter('brightness', Filters.brightness, 40);
      }

      threshold = function() {
        runFilter('threshold', Filters.threshold, 128);
      }

      sharpen = function() {
        runFilter('sharpen', Filters.convolute,
          [ 0, -1,  0,
           -1,  5, -1,
            0, -1,  0]);
      }

      blurC = function() {
        runFilter('blurC', Filters.convolute,
          [ 1/9, 1/9, 1/9,
            1/9, 1/9, 1/9,
            1/9, 1/9, 1/9 ]);
      }

      sobel = function() {
        runFilter('sobel', function(px) {
          px = Filters.grayscale(px);
          var vertical = Filters.convoluteFloat32(px,
            [-1,-2,-1,
              0, 0, 0,
              1, 2, 1]);
          var horizontal = Filters.convoluteFloat32(px,
            [-1,0,1,
             -2,0,2,
             -1,0,1]);
          var id = Filters.createImageData(vertical.width, vertical.height);
          for (var i=0; i<id.data.length; i+=4) {
            var v = Math.abs(vertical.data[i]);
            id.data[i] = v;
            var h = Math.abs(horizontal.data[i]);
            id.data[i+1] = h
            id.data[i+2] = (v+h)/4;
            id.data[i+3] = 255;
          }
          return id;
        });
      }

      custom = function() {
        var inputs = document.getElementById('customMatrix').getElementsByTagName('input');
        var arr = [];
        for (var i=0; i<inputs.length; i++) {
          arr.push(parseFloat(inputs[i].value));
        }
        runFilter('custom', Filters.convolute, arr, true);
      }

    }, false);
  </script>
";

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
