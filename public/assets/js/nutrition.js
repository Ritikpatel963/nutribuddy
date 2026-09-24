// Nutrition Module Local State & Logic

document.addEventListener('DOMContentLoaded', () => {
    // 1. Tab Switching Logic
    const tabs = document.querySelectorAll('.nutri-tab');
    const tabContents = document.querySelectorAll('.nutri-tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active classes
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(tc => tc.classList.remove('active'));

            // Add active class to clicked tab
            tab.classList.add('active');
            
            // Show corresponding content
            const targetId = tab.getAttribute('data-target');
            document.getElementById(targetId).classList.add('active');
        });
    });

    // 2. Date Selector Logic
    const dateItems = document.querySelectorAll('.nutri-date-item');
    dateItems.forEach(item => {
        item.addEventListener('click', () => {
            dateItems.forEach(d => d.classList.remove('active'));
            item.classList.add('active');
        });
    });


    // 4. Chart Filter Logic
    const chartBtns = document.querySelectorAll('.chart-filter-btn');
    chartBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active classes
            chartBtns.forEach(b => {
                b.classList.remove('active');
                b.style.background = '#fff';
                b.style.color = 'var(--muted)';
            });
            // Add active styles to clicked button
            btn.classList.add('active');
            btn.style.background = 'var(--pkl)';
            btn.style.color = 'var(--pk)';
            
            // Randomize chart heights for visual feedback
            const bars = document.querySelectorAll('#tab-calories > div.nutri-card:nth-child(2) > div:nth-child(3) > div');
            bars.forEach(bar => {
                const randomHeight = Math.floor(Math.random() * (95 - 20 + 1) + 20);
                bar.style.height = `${randomHeight}%`;
            });
        });
    });

    // 4. Modal / Add Food Logic
    const addFoodBtns = document.querySelectorAll('.meal-add-btn');
    const modal = document.getElementById('addFoodModal');
    const closeBtn = document.querySelector('.close-modal');
    const modalOverlay = document.querySelector('.nutri-modal-overlay');

    let currentMealTarget = null;

    addFoodBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            currentMealTarget = e.currentTarget.closest('.meal-section').querySelector('.food-list');
            modal.classList.add('active');
        });
    });

    const closeModal = () => {
        modal.classList.remove('active');
        currentMealTarget = null;
    };

    closeBtn.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) closeModal();
    });

    // Handle adding food from modal
    const searchResultAddBtns = document.querySelectorAll('.search-result-add');
    searchResultAddBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (currentMealTarget) {
                const item = e.currentTarget.closest('.search-result-item');
                const name = item.querySelector('.food-name').innerText;
                const details = item.querySelector('.food-details').innerText;
                const cals = item.querySelector('.food-cal').innerText;

                // Create new food item element
                const foodDiv = document.createElement('div');
                foodDiv.className = 'food-item';
                foodDiv.innerHTML = `
                    <div>
                        <div class="food-name">${name}</div>
                        <div class="food-details">${details}</div>
                    </div>
                    <div class="food-cal">${cals}</div>
                `;
                
                // Append to meal list
                currentMealTarget.appendChild(foodDiv);

                // Add simple animation
                foodDiv.style.opacity = '0';
                foodDiv.style.transform = 'translateY(-10px)';
                foodDiv.style.transition = 'all 0.3s ease';
                setTimeout(() => {
                    foodDiv.style.opacity = '1';
                    foodDiv.style.transform = 'translateY(0)';
                }, 50);

                closeModal();
                updateCalories(parseInt(cals.replace(/\D/g,''))); // Simple parse
            }
        });
    });


    // Helper: Update Calories (Frontend only simulation)
    const consumedEl = document.getElementById('calsConsumed');
    const remainingEl = document.getElementById('calsRemaining');
    const ringProgress = document.querySelector('.cal-ring-progress');
    
    let totalCals = 2100;
    let consumedCals = 0;

    function updateCalories(addedCals) {
        if (!consumedEl || !remainingEl || !ringProgress) return;

        consumedCals += addedCals;
        const remaining = Math.max(0, totalCals - consumedCals);
        
        consumedEl.innerText = consumedCals;
        remainingEl.innerText = remaining;

        // Update SVG Ring (circumference is ~ 2 * PI * 40 = 251.2, let's say stroke-dasharray = 251.2)
        // Actually wait, radius is 40? SVG viewBox is 100 100, radius is 40. 2 * Math.PI * 40 = 251.3
        const circumference = 251.3;
        const percent = Math.min(consumedCals / totalCals, 1);
        const dashoffset = circumference - (percent * circumference);
        
        ringProgress.style.strokeDasharray = circumference;
        ringProgress.style.strokeDashoffset = dashoffset;
    }
    
    // Init Ring
    if (ringProgress) {
        ringProgress.style.strokeDasharray = 251.3;
        ringProgress.style.strokeDashoffset = 251.3; 
    }
});
