<?php
require_once "inc/map_func.php";
require_once "inc/region_data.php";

define("PARAM_WIDTH", "w");
define("PARAM_HEIGHT", "h");
define("PARAM_ACTION", "a");
define("PARAM_SINGLE", "s");
define("PARAM_MAP_NAME", "mn");
define("PARAM_FORMAT", "vf");
define("PARAM_LINK_TPL", "lt");
define("PARAM_SELECTED", "sel");

define("IMAGE_WIDTH", 2000);
define("IMAGE_HEIGHT", 1200);

define("MAP_LEFT", 73);
define("MAP_RIGHT", 136);
define("MAP_TOP", 54);
define("MAP_BOTTOM", 18);

define("DECAY_CONSTANT", 1.5);

define("BASE_DIR", "map_data/regions");

if (array_key_exists(PARAM_WIDTH, $_REQUEST) && ($_REQUEST[PARAM_WIDTH]+0 > 0)) {
    $width = $_REQUEST[PARAM_WIDTH];
} else {
    $width = IMAGE_WIDTH;
}

if (array_key_exists(PARAM_HEIGHT, $_REQUEST) && ($_REQUEST[PARAM_HEIGHT]+0 > 0)) {
    $height = $_REQUEST[PARAM_HEIGHT];
} else {
    $height = IMAGE_HEIGHT;
}

if (array_key_exists(PARAM_ACTION, $_REQUEST)) {
    $action = $_REQUEST[PARAM_ACTION] == "img" ? "img" : "html";
} else {
    $action = "html";
}

if (array_key_exists(PARAM_SINGLE, $_REQUEST)) {
    $single = $_REQUEST[PARAM_SINGLE] == "1" ? true : false;
} else {
    $single = false;
}

if (array_key_exists(PARAM_MAP_NAME, $_REQUEST)) {
    $map_name = $_REQUEST[PARAM_MAP_NAME] == "" ? "geomap" : $_REQUEST[PARAM_MAP_NAME];
} else {
    $map_name = "geomap";
}

if (array_key_exists(PARAM_FORMAT, $_REQUEST)) {
    $value_format = $_REQUEST[PARAM_FORMAT] == "" ? "%f" : $_REQUEST[PARAM_FORMAT];
} else {
    $value_format = "%f";
}

if (array_key_exists(PARAM_LINK_TPL, $_REQUEST)) {
    $link_template = $_REQUEST[PARAM_LINK_TPL];
} else {
    $link_template = "";
}

if (array_key_exists(PARAM_SELECTED, $_REQUEST)) {
    $region_selected = $_REQUEST[PARAM_SELECTED];
} else {
    $region_selected = "";
}

$x_scale = $width / (MAP_RIGHT - MAP_LEFT);
$y_scale = $height / (MAP_TOP - MAP_BOTTOM);
$scale = $x_scale > $y_scale ? $y_scale : $x_scale;
$width = $scale * (MAP_RIGHT - MAP_LEFT);
$height = $scale * (MAP_TOP - MAP_BOTTOM);

$color_schemas = array(
    array(0xff, 0x26, 0x26),
    array(0xff, 0x73, 0x73),
    array(0xdb, 0x99, 0x00),
    array(0xff, 0xd0, 0x62),
    array(0xc6, 0xc6, 0xff),
    array(0x73, 0x73, 0xff),
);

$geos = array();
foreach (region_list() as $name) {
    if (array_key_exists($name, $_REQUEST)) {
        $geos[$name] = $_REQUEST[$name] + 0;
    } else {
        $geos[$name] = 0;
    }
}

if ($action == "img") {
    arsort($geos);
    $geo_list = array_keys($geos);
    $base = $geos[$geo_list[0]];

    if (array_key_exists($region_selected, $geos)) {
        $geo_list = preg_grep("/^$region_selected$/", $geo_list, PREG_GREP_INVERT);
        array_push($geo_list, $region_selected);
    }

    $image = imagecreatetruecolor($width, $height);
    $transparent = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $transparent);
    foreach ($geo_list as $name) {
        if ($geos[$name] == 0) {
            $color = array(255, 255, 255);
        } else {
            $color_index = log($base/$geos[$name], DECAY_CONSTANT);
            if ($color_index >= count($color_schemas)) {
                $color_index = count($color_schemas) - 1;
            }
            $color = $color_schemas[$color_index];
        }
        draw_region($name, $image, $scale, MAP_LEFT, MAP_TOP, imagecolorallocate($image, $color[0], $color[1], $color[2]), $region_selected);
    }
    imagesavealpha($image, TRUE);
    imagecolortransparent($image, $transparent);
    header("Content-type: image/gif");
    imagegif($image);
} else if ($action == "html") {
    header("Content-type: text/html; charset=UTF-8");
    echo "<html><body>" . PHP_EOL;
    $query_string = "a=img&w=$width&h=$height&sel=" . urlencode($region_selected);
    foreach ($geos as $name => $value) {
        $query_string .= "&" . urlencode($name) . "=$value";
    }

    echo "<img src=\"map.php?$query_string\" usemap=\"#$map_name\" border=\"0\" style=\"filter:none;-webkit-filter:grayscale(0);filter:alpha(opacity=100);opacity: 1.0;-moz-opacity:1.0;\">" . PHP_EOL;
    echo "<map name=\"$map_name\">" . PHP_EOL;

    foreach ($geos as $name => $value) {
        output_map_area($name, $scale, MAP_LEFT, MAP_TOP, $value, $value_format, $link_template);
    }
    echo "</map>" . PHP_EOL;
    echo "</body></html>" . PHP_EOL;
}
