<?php
session_start();

# need fonts-freefont-ttf package
$fonts = array(
    '/usr/share/fonts/truetype/dejavu/DejaVuSansMono-Bold.ttf',
    '/usr/share/fonts/truetype/dejavu/DejaVuSerif.ttf',
    '/usr/share/fonts/truetype/dejavu/DejaVuSerif-Bold.ttf',
    '/usr/share/fonts/truetype/dejavu/DejaVuSansMono.ttf',
    '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
    '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
    '/usr/share/fonts/truetype/freefont/FreeMonoBold.ttf',
    '/usr/share/fonts/truetype/freefont/FreeMono.ttf',
    '/usr/share/fonts/truetype/freefont/FreeSerifBoldItalic.ttf',
    '/usr/share/fonts/truetype/freefont/FreeSansBold.ttf',
    '/usr/share/fonts/truetype/freefont/FreeSansBoldOblique.ttf',
    '/usr/share/fonts/truetype/freefont/FreeSans.ttf',
    '/usr/share/fonts/truetype/freefont/FreeSerif.ttf',
    '/usr/share/fonts/truetype/freefont/FreeMonoBoldOblique.ttf',
    '/usr/share/fonts/truetype/freefont/FreeSerifItalic.ttf',
    '/usr/share/fonts/truetype/freefont/FreeMonoOblique.ttf',
    '/usr/share/fonts/truetype/freefont/FreeSansOblique.ttf',
    '/usr/share/fonts/truetype/freefont/FreeSerifBold.ttf',
);

$num_of_chars = 6;
$min_font_size = 18;
$max_font_size = 22;

$width = ($num_of_chars+2) * $max_font_size;
$height = 3*$max_font_size;
$weird = imagecreatetruecolor($width, $height);

$code = "";
$candidates = "2345678abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
$left = $max_font_size;
for ($index=0; $index<$num_of_chars; $index++) {
    $size = rand($min_font_size, $max_font_size);
    $angle = rand(-60, 60);    // angles
    $base_line = rand($max_font_size, $height-$max_font_size);
    $color = imagecolorallocate($weird, rand(128,255), rand(128,255), rand(128,255)); // random color needs to be bright
    $font = $fonts[rand(0, count($fonts)-1)];
    $char = substr($candidates, rand(0, strlen($candidates)-1), 1);
    $code = $code . $char;
    $coords = imagettfbbox($size, $angle, $font, $char);
    $left -= min($coords[0], $coords[6]);
    imagettftext($weird, $size, $angle, $left, $base_line, $color, $font, $char);
    $left += max($coords[2], $coords[4]);
}

# draw random background first
for ($index=0; $index<$num_of_chars*5; $index++) {
    $color = imagecolorallocate($weird, rand(0,127), rand(0,127), rand(0,127));
    switch (rand(0,2)) {
        case 0:
            imageline($weird,rand(0,$width),rand(0,$height),rand(0,$width),rand(0,$height),$color);
            break;
        case 1:
            imageellipse($weird,rand(0,$width),rand(0,$height),rand(0,$width),rand(0,$height), $color);
            break;
        case 2:
            $s_angle = rand(0,360);
            $e_angle = rand($s_angle, 360);
            imagearc($weird,rand(0,$width),rand(0,$height),rand($width/2,$width),rand(0,$height),$s_angle, $e_angle,  $color);
            break;
    }
}

$_SESSION['secret'] = md5(strtolower($code));
// header("Set-Cookie: DEBUG=$code");
header("Content-type: image/png");
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
imagepng($weird);
?>
