<div class="regular-padding">

    <div class="regular-padding narrow">

        <div class="scroll-text">Scroll<br><img class="scroll-arrow" src="../assets/images/chevron-shadow.png" width="90px">
        </div>

        <div class="p1"></div>


        <div class="indent-wide margin-tall-first border-box-round regular-padding">

            <h3>Find Your Place</h3>

            <p>Use the dropdown menu to find a feature in Big Sur, including California State Parks, Los Padres National Forest Monterey District, the Ventana Wilderness, and other noteworthy locations.</p>
        </div>


        <div class="scroll-text">Scroll<br><img class="scroll-arrow" src="../assets/images/chevron-shadow.png" width="90px"></div>


        <div class="p2"></div>

        <div class="indent-wide margin-tall border-box-round regular-padding">
            <h3>Explore the Trail Map</h3>
            <p>Use the trail map to quickly locate trails and explore the surrounding area.</p>
        </div>


        <div class="scroll-text">Scroll<br><img class="scroll-arrow" src="../assets/images/chevron-shadow.png" width="90px"></div>


        <div class="p3"></div>

        <div class="indent-wide margin-tall border-box-round regular-padding">
            <h3>Discover Public Trails</h3>
            <p>Share your trail experiences and help others discover underutilized, off-the-beaten-path locations in Big Sur. Many of these special places are at risk due to lack of use, neglect, extreme weather, and opposition to their preservation.</p>
        </div>

        <div class="scroll-text">Scroll<br><img class="scroll-arrow" src="../assets/images/chevron-shadow.png" width="90px"></div>

    </div>


    <p><span class="em">Read the latest trail conditions from our community</span></p>

</div>

<script>
    const arrowContainers = document.querySelectorAll('.scroll-arrow');

    function isElementInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight ||
                document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    function handleScroll() {
        arrowContainers.forEach(arrowContainer => {
        if (isElementInViewport(arrowContainer)) {
            // arrowContainer.classList.toggle('scroll-arrow');
            setTimeout(() => {
                arrowContainer.classList.toggle('scroll-arrow');
            }, 4000)
        }
        });

    }

    window.addEventListener('scroll', handleScroll);
</script>