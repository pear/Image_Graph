#!/bin/sh
phpdoc -d ./ -i docs/,CVS/,generate_package.xml -t ../dochtml_Image_Graph -o HTML:frames:earthli -s off -p on -dn "Image_Graph" -dc images -ti "Image_Graph"

