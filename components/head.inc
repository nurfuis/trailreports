<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $page_title ?></title>
  <link rel="icon" href="/favicon.png" />
  <link rel="stylesheet" href="<?php echo $stylesheet ?>" />
  <link rel="stylesheet" href="<?php echo $lightTheme ?>" />
  
  <!-- Leaflet Code -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <style>
    #loading {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      /* Semi-transparent black background */
      display: flex;
      /* Center content horizontally */
      justify-content: center;
      /* Center content vertically */
      align-items: center;
      /* Center content within container */
      z-index: 999;
      /* Ensure loading indicator is above the map */
    }

    #loading-text {
      color: white;
      font-size: 20px;
    }
  </style>
  <!-- End Leaflet Code -->

</head>

<body>