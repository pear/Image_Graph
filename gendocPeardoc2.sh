#!/bin/sh
phpdoc -d ./ -i docs/,CVS/,generate_package.xml -t ../peardoc2_Image_Graph -o XML:DocBook/peardoc2 -s off -p on -dn "Image_Graph" -dc images -ti "Image_Graph"

