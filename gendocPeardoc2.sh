#!/bin/sh
phpdoc -d ./ -i "docs,doc*,example*,generate_package.xml,CVS,complex1.php,complex1b.php,simple1.php,simple2.php,stacked1.php" -t ../peardoc2_Image_Graph -o XML:DocBook/peardoc2 -s -p -dn "Image_Graph" -ti "Image_Graph"

