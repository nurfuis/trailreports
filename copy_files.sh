#!/bin/bash

# Define the destination directory
html_dir="html"

# Loop through directories to copy
directories=(assets components layouts pages)

# Check if the html directory exists
if [ ! -d "$html_dir" ]; then
  echo "Creating directory: $html_dir"
  mkdir "$html_dir"
fi

# Copy files and directories
for dir in "${directories[@]}"; do
  # Check if the subdirectory exists inside html
  if [ ! -d "$html_dir/$dir" ]; then
    echo "Creating directory: $html_dir/$dir"
    mkdir -p "$html_dir/$dir"  # -p flag creates parent directories if needed
  fi
  
  # Copy the directory contents
  cp -r "$dir/"* "$html_dir/$dir/"
  echo "Copying files to: $html_dir/$dir"
done

# Copy index.php
cp index.php "$html_dir/"
echo "Copied index.php to: $html_dir"

echo "All files copied successfully!"
