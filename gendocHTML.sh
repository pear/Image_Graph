#!/bin/sh
phpdoc -d ./ -i "docs,doc*,example*,generate_package.xml,CVS,complex1.php,complex1b.php,simple1.php,simple2.php,stacked1.php" -t ../dochtml_Image_Graph -o HTML:frames:earthli -s -p -dn "Image_Graph" -dc images -ti "Image_Graph"

