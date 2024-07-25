<div class="main__intro regular-padding">
  <h1>Trail Reports</h1>
  <h2>Big Sur, California</h2>

  <p class="fade-out-paragraph">
    View and report accurate information on trail conditions in Monterey County's Los Padres National Forest and
    Ventana Wilderness areas. This open-source platform allows you to submit trail reports.
  </p>

  <script>
    const paragraph = document.querySelectorAll(".fade-out-paragraph")[0];
    const fadeOutThreshold = 20;

    window.addEventListener("scroll", () => {
      const scrolledPixels = window.scrollY;
      const opacity = Math.max(0, 1 - scrolledPixels / fadeOutThreshold / 2);
      paragraph.style.opacity = opacity;
    });
  </script>
</div>
