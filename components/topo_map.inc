<div class="map__wrapper">
  <h3>Topographic Map</h3>

  <div class="map-image__wrapper">
    <img
      class="topo_map__link--image"
      src="../assets/images/map-static-300x200.png"
      onclick="openTopoMap()"
    />
  </div>
</div>

<script>
  function openTopoMap() {
    window.location.href = "./pages/topo_map.html";
  }
</script>
