/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import 'bootstrap'
import './styles/app.css';



document.addEventListener('DOMContentLoaded', function() {
    const carousels = document.querySelectorAll('.carousel');
    const completedCarousels = new Set();
    const totalCarousels = carousels.length;
    const slideIndexes = {};

    carousels.forEach(carousel => {
        const artistId = carousel.getAttribute('data-artist-id');
        slideIndexes[artistId] = 0;

        carousel.addEventListener('slid.bs.carousel', function(event) {
            const items = carousel.querySelectorAll('.carousel-item');
            const activeIndex = Array.from(items).indexOf(event.relatedTarget);

            if (activeIndex === 0) {
                completedCarousels.add(artistId);
            }

            slideIndexes[artistId] = activeIndex;

            if (completedCarousels.size === totalCarousels) {
                shuffleGrid();
                completedCarousels.clear();
            }
        });
    });

    function shuffleGrid() {
        var parent = document.getElementById('artists-grid');
        var items = Array.prototype.slice.call(parent.children);

        items.forEach(function(item) {
            item.classList.add('shuffle-leave');
        });

        setTimeout(function() {
            var shuffledItems = items.sort(function() {
                return 0.5 - Math.random();
            });
            shuffledItems.forEach(function(item) {
                parent.appendChild(item);
                item.classList.remove('shuffle-leave');
                item.classList.add('shuffle-enter');
            });

            setTimeout(function() {
                shuffledItems.forEach(function(item) {
                    item.classList.remove('shuffle-enter');
                });
            }, 50);
        }, 500);
    }
});
