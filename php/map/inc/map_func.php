<?php
function output_map_area($region, $scale, $map_left, $map_top, $value, $value_format, $link_template) {
    $prev_x = -1;
    $prev_y = -1;
    $param_region = urlencode($region);

    $raw_data = @file_get_contents(BASE_DIR . "/$region", "r");
    if ($raw_data === false) {
        return;
    }
    $coords = array();
    $last_1 = array(0, 0);
    $last_2 = array(0, 0);
    foreach (explode("\n", $raw_data) as $line) {
        if ($line == '') {
            if (count($coords) >= 3) {
                echo "<area shape=\"poly\" coords=\"" . implode(",", $coords) . "\"";
                echo " href=\"" . sprintf($link_template, $region, $value) . "\"";
                echo " alt=\"$region (" . sprintf($value_format, $value) . ")\"";
                echo " title=\"$region (" . sprintf($value_format, $value) . ")\">\n";
            }
            $coords = array();
            continue;
        }
        list($longitude, $latitude) = explode("\t", $line);
        $x = intval(($longitude - $map_left) * $scale);
        $y = intval(($map_top - $latitude) * $scale);

        // check if we can save some middle points
        $delta_x2 = $last_1[0] - $last_2[0];
        $delta_y2 = $last_1[1] - $last_2[1];
        $delta_x1 = $x - $last_1[0];
        $delta_y1 = $y - $last_1[1];
        if (($delta_x1 == 0) && ($delta_y1 == 0)) {
            // same point as last_1, simply ignore it
            continue;
        } else if (($delta_x1*$delta_y2 == $delta_x2*$delta_y1) && ($delta_x1*$delta_y2 != 0)) {
            // leaning line, keep the last point
            array_pop($coords);
            array_push($coords, "$x,$y");
            $last_1 = array($x, $y);
        } else if (($delta_x1 == 0) && ($delta_x2 == 0)) {
            // vertical line, keep the last point
            array_pop($coords);
            array_push($coords, "$x,$y");
            $last_1 = array($x, $y);
        } else if (($delta_y1 == 0) && ($delta_y2 == 0)) {
            // horizontal line, keep the last point
            array_pop($coords);
            array_push($coords, "$x,$y");
            $last_1 = array($x, $y);
        } else {
            // not in a line
            array_push($coords, "$x,$y");
            $last_2 = $last_1;
            $last_1 = array($x, $y);
        }
    }
    if (count($coords) >= 3) {
        echo "<area shape=\"poly\" coords=\"" . implode(",", $coords) . "\"";
        echo " href=\"" . sprintf($link_template, $region, $value) . "\"";
        echo " alt=\"$region (" . sprintf($value_format, $value) . ")\"";
        echo " title=\"$region (" . sprintf($value_format, $value) . ")\">\n";
    }
}

function draw_region($region, $image, $scale, $map_left, $map_top, $color, $region_selected) {
    if ($region_selected == $region) {
        $line_color = imagecolorallocate($image, 0xf0, 0xf0, 0xf0);
        $line_thickness = 2;
    } else {
        $line_color = imagecolorallocate($image, 0x0, 0x0, 0x0);
        $line_thickness = 1;
    }

    $prev_x = -1;
    $prev_y = -1;

    $raw_data = @file_get_contents(BASE_DIR . "/$region", "r");
    $points = array();
    foreach (explode("\n", $raw_data) as $line) {
        if ($line == '') {
            if (count($points) >= 6) {
                imagefilledpolygon($image, $points, count($points) / 2, $color);
                if ($region_selected == $region) {
                    imagesetthickness($image, $line_thickness);
                    imagepolygon($image, $points, count($points) / 2, $line_color);
                } else {
                    imagesetthickness($image, $line_thickness);
                    imagepolygon($image, $points, count($points) / 2, $line_color);
                }
            }
            $points = array();
            continue;
        }
        list($longitude, $latitude) = explode("\t", $line);
        $x = ($longitude - $map_left) * $scale;
        $y = ($map_top - $latitude) * $scale;
        array_push($points, $x, $y);
    }
    if (count($points) >= 6) {
        imagefilledpolygon($image, $points, count($points) / 2, $color);
        if ($region_selected == $region) {
            imagesetthickness($image, $line_thickness);
            imagepolygon($image, $points, count($points) / 2, $line_color);
        } else {
            imagesetthickness($image, $line_thickness);
            imagepolygon($image, $points, count($points) / 2, $line_color);
        }
    }
};
?>
