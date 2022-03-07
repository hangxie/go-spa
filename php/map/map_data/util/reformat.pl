#!/usr/bin/perl -w

use strict;

my ($start_long, $start_lat);
while (<>) {
    chomp $_;
    next if ($_ eq '');
    my ($long, $lat) = split(/\t/, $_);
    if (!defined($start_long)) {
        $start_long = $long;
        $start_lat = $lat;
        print "$long\t$lat\n";
    } elsif (($long == $start_long) && ($lat == $start_lat)) {
        print "$long\t$lat\n\n";
        undef $start_long;
        undef $start_lat;
    } else {
        print "$long\t$lat\n";
    }
}
