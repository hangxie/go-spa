#!/usr/bin/perl -w

use strict;
use GD;
# use GD::Polyline;
use GD::Polygon;

use constant IMAGE_WIDTH => 2000;
use constant IMAGE_HEIGHT => 1500;

use constant MAP_LEFT => 70;
use constant MAP_RIGHT => 136;
use constant MAP_TOP => 54;
use constant MAP_BOTTOM => 8;

use constant BASE_DIR => "/homes/xiehang/svn/xiehang/map_data/maps";

sub draw_region {
    my ($region, $image, $scale, $map_left, $map_top, $color) = @_;

    my $poly = new GD::Polygon;
    open REGION, "<" . BASE_DIR . "/$region";
    my $prev_x = -1;
    my $prev_y = -1;
    while (<REGION>) {
        chomp;
        my $line = $_;
        if ($line eq '') {
            $image->filledPolygon($poly, $color);
            undef $poly;
            $poly = new GD::Polygon;
            next;
        }
        my ($longtitude, $latitude) = split(/\t/, $_);
        my $x = ($longtitude - $map_left) * $scale;
        my $y = ($map_top - $latitude) * $scale;
        $poly->addPt($x, $y);
    }
    close REGION;
    $image->filledPolygon($poly, $color);
};

print "Content-type: image/png\n\n";
my $x_scale = IMAGE_WIDTH / (MAP_RIGHT - MAP_LEFT);
my $y_scale = IMAGE_HEIGHT / (MAP_TOP - MAP_BOTTOM);

my $scale = $x_scale > $y_scale ? $y_scale : $x_scale;

my $image = new GD::Image(IMAGE_WIDTH, IMAGE_HEIGHT);

my $white = $image->colorAllocate(255,255,255);
my $black = $image->colorAllocate(0, 0, 0);
my $red = $image->colorAllocate(255, 0, 0);
my $blue = $image->colorAllocate(0, 0, 255);
my $green = $image->colorAllocate(50, 200, 0);
my $purple = $image->colorAllocate(200, 0, 255);
my $orange = $image->colorAllocate(255, 200, 0); 

# draw_region('中国', $image, $scale, MAP_LEFT, MAP_TOP, $orange);
draw_region('新疆', $image, $scale, MAP_LEFT, MAP_TOP, $red);
draw_region('西藏', $image, $scale, MAP_LEFT, MAP_TOP, $blue);
draw_region('青海', $image, $scale, MAP_LEFT, MAP_TOP, $purple);
draw_region('甘肃', $image, $scale, MAP_LEFT, MAP_TOP, $green);
draw_region('内蒙古', $image, $scale, MAP_LEFT, MAP_TOP, $blue);
draw_region('黑龙江', $image, $scale, MAP_LEFT, MAP_TOP, $red);
draw_region('吉林', $image, $scale, MAP_LEFT, MAP_TOP, $green);
draw_region('辽宁', $image, $scale, MAP_LEFT, MAP_TOP, $purple);
draw_region('河北', $image, $scale, MAP_LEFT, MAP_TOP, $green);
draw_region('北京', $image, $scale, MAP_LEFT, MAP_TOP, $blue);
draw_region('天津', $image, $scale, MAP_LEFT, MAP_TOP, $purple);
draw_region('山西', $image, $scale, MAP_LEFT, MAP_TOP, $red);
draw_region('陕西', $image, $scale, MAP_LEFT, MAP_TOP, $purple);
draw_region('宁夏', $image, $scale, MAP_LEFT, MAP_TOP, $red);
draw_region('河南', $image, $scale, MAP_LEFT, MAP_TOP, $blue);
draw_region('山东', $image, $scale, MAP_LEFT, MAP_TOP, $purple);
draw_region('安徽', $image, $scale, MAP_LEFT, MAP_TOP, $red);
draw_region('江苏', $image, $scale, MAP_LEFT, MAP_TOP, $green);
draw_region('湖北', $image, $scale, MAP_LEFT, MAP_TOP, $green);
draw_region('四川', $image, $scale, MAP_LEFT, MAP_TOP, $red);
draw_region('重庆', $image, $scale, MAP_LEFT, MAP_TOP, $blue);
draw_region('湖南', $image, $scale, MAP_LEFT, MAP_TOP, $purple);
draw_region('贵州', $image, $scale, MAP_LEFT, MAP_TOP, $green);
draw_region('云南', $image, $scale, MAP_LEFT, MAP_TOP, $purple);
draw_region('浙江', $image, $scale, MAP_LEFT, MAP_TOP, $purple);
draw_region('上海', $image, $scale, MAP_LEFT, MAP_TOP, $red);
draw_region('江西', $image, $scale, MAP_LEFT, MAP_TOP, $blue);
draw_region('福建', $image, $scale, MAP_LEFT, MAP_TOP, $green);
draw_region('广东', $image, $scale, MAP_LEFT, MAP_TOP, $red);
draw_region('广西', $image, $scale, MAP_LEFT, MAP_TOP, $blue);
draw_region('海南', $image, $scale, MAP_LEFT, MAP_TOP, $green);
draw_region('香港', $image, $scale, MAP_LEFT, MAP_TOP, $purple);
draw_region('澳门', $image, $scale, MAP_LEFT, MAP_TOP, $blue);
draw_region('台湾', $image, $scale, MAP_LEFT, MAP_TOP, $blue);

$image->transparent($white);
binmode STDOUT;
print $image->png;
