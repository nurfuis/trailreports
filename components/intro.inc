<div class="introduction regular-padding">
  <h1>Trail Reports</h1>
  <h2>Big Sur, California</h2>

  <p class="fade-out-paragraph">
    <span class="em larger">Help keep our trails open and accessible!</span>
    Your firsthand knowledge is invaluable. By sharing trail conditions, you're contributing to a thriving outdoor community and helping to preserve our beloved trails! Use the selector below to find a trail, camp, or other points of interest.
  </p>
</div>
<script>
  const paragraph = document.querySelectorAll(".fade-out-paragraph")[0];
  const fadeOutThreshold = 20;

  window.addEventListener("scroll", () => {
    const scrolledPixels = window.scrollY;
    const opacity = Math.max(0, 1 - scrolledPixels / fadeOutThreshold / 2);
    paragraph.style.opacity = opacity;
  });
</script>