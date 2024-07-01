#!/bin/bash
cp -r assets/* html/assets/
cp -r components/* html/components/
cp -r layouts/* html/layouts/
cp -r pages/* html/pages/
cp index.php html/

echo "Files were copied to html."
