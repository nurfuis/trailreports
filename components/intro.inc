<div class="introduction regular-padding">
  <h1>Trail Reports</h1>
  <h2>Big Sur, California</h2>

  <p class="fade-out-paragraph">
    <span class="em larger">Help us keep our trails open and accessible!</span>
    Share your adventures and discoveries by submitting a trail report. Your firsthand knowledge is invaluable. By sharing trail conditions, you're contributing to a thriving outdoor community to preserve our beloved trails!
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