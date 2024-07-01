#!/bin/bash

# Define the destination directory
html_dir="html"

# Loop through directories to copy
directories=(assets components layouts pages)

# Check if the html directory exists
if [ -d "$html_dir" ]; then
  echo "WARNING: The directory '$html_dir' already exists and contains files."
  echo "This script will DELETE ALL FILES AND SUBDIRECTORIES in this directory. Are you sure? (y/N)"
  read -r confirm

  if [[ ! $confirm =~ ^([Yy]$) ]]; then
    echo "Exiting script. No changes made."
    exit 0
  fi

  # Clear the directory (use with EXTREME CAUTION!)
  rm -rf "$html_dir"
  # Recreate the empty directory
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
