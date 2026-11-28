<section class="home-world-nutrition" aria-label="Children's nutrition research around the world">
    <div class="home-world-nutrition-shell">
        <iframe
            id="homeWorldNutritionFrame"
            class="home-world-nutrition-frame"
            src="{{ asset('assets/research/world-nutrition-map.html') }}"
            title="A Question Parents Everywhere Share - Children’s supplement use around the world"
            loading="lazy"
            scrolling="no"></iframe>
    </div>
</section>

<style>
    .home-world-nutrition {
        width: 100%;
        position: relative;
        background: radial-gradient(ellipse at 50% 44%, rgba(237, 50, 139, .10), transparent 36%),
                    radial-gradient(ellipse at 50% 55%, #f0e5ff 0, transparent 67%),
                    linear-gradient(135deg, #fffcf8 0%, #fff6fb 58%, #f5efff 100%);
        overflow: hidden;
    }

    .home-world-nutrition-shell {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        position: relative;
    }

    .home-world-nutrition-frame {
        width: 100%;
        min-height: 940px;
        border: none;
        display: block;
        background: transparent;
        transition: height 0.25s ease-out;
        overflow: hidden;
    }

    @media (max-width: 768px) {
        .home-world-nutrition-frame {
            min-height: 1020px;
        }
    }
</style>

<script>
    (function () {
        const frame = document.getElementById('homeWorldNutritionFrame');
        if (!frame) return;

        window.addEventListener('message', function (e) {
            if (e.data && e.data.type === 'nb-research-height' && typeof e.data.height === 'number') {
                const targetHeight = Math.ceil(e.data.height);
                if (targetHeight > 200) {
                    frame.style.height = targetHeight + 'px';
                }
            }
        });
    })();
</script>
