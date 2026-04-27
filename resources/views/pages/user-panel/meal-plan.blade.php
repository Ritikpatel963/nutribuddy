@extends('layouts.user-panel')
@section('title', 'Meal Plan')


@section('panel-content')

<!-- ── PAGE ── -->
<div class="page">

  <!-- Header -->
  <div class="page-header">
    <h1 class="page-title">7-Day Meal Plan</h1>
    <p class="page-meta">
      Arjun
      <span class="sep">·</span>
      5 years
      <span class="sep">·</span>
      Pure veg
      <span class="sep">·</span>
      West India
      <span class="sep">·</span>
      🌤️ Greeshma summer plan
    </p>
  </div>

  <!-- Season Alert -->
  <div class="season-alert">
    <div class="alert-icon">🌤️</div>
    <div>
      <div class="alert-title">Greeshma season overlay active</div>
      <div class="alert-body">Cooling and light foods prioritised. Coconut water 2× daily. Early dinner by 7pm. No cold fridge water.</div>
    </div>
  </div>

  <!-- Day Tabs -->
  <div class="tabs-scroll">
    <div class="tabs-track" id="tabsTrack">
      <button class="day-tab" data-day="mon">
        <span class="tab-day">Mon</span><span class="tab-label">Immunity</span>
      </button>
      <button class="day-tab active" data-day="tue">
        <span class="tab-day">Tue</span><span class="tab-label">Growth</span>
      </button>
      <button class="day-tab" data-day="wed">
        <span class="tab-day">Wed</span><span class="tab-label">Brain</span>
      </button>
      <button class="day-tab" data-day="thu">
        <span class="tab-day">Thu</span><span class="tab-label">Immunity</span>
      </button>
      <button class="day-tab" data-day="fri">
        <span class="tab-day">Fri</span><span class="tab-label">Energy</span>
      </button>
      <button class="day-tab" data-day="sat">
        <span class="tab-day">Sat</span><span class="tab-label">Bone</span>
      </button>
      <button class="day-tab" data-day="sun">
        <span class="tab-day">Sun</span><span class="tab-label">Rest</span>
      </button>
    </div>
  </div>

  <!-- Meal Card -->
  <div class="meal-card" id="mealCard">
    <!-- injected by JS -->
  </div>

</div>


   @push('scripts')
<script>
/* ── DATA ── */
const days = {
  mon: {
    label: 'Monday',
    focus: 'IMMUNITY BOOST',
    meals: [
      { time:'7 AM',  emoji:'🥛', name:'Turmeric milk', desc:'Curcumin + black pepper — activates anti-inflammatory pathway. Ayurvedic morning ritual.', tags:['Calcium:calcium','Vit D:vit-e'] },
      { time:'8 AM',  emoji:'🫐', name:'Ragi porridge + banana', desc:'Calcium 344mg · Iron 3.9mg per serving. Finger millet is the best plant calcium source.', tags:['Calcium:calcium','Iron:iron'] },
      { time:'10:30', emoji:'🍃', name:'Guava + peanut pack', desc:'Vit C from guava TRIPLES iron absorption from peanuts.', tags:['Vit C:vit-c','Iron:iron'] },
      { time:'1 PM',  emoji:'🍛', name:'Palak dal + chapati + curd', desc:'Lemon off-heat for 3× iron. Spinach + dal = complete amino profile.', tags:['Protein:protein','Iron:iron','Calcium:calcium'] },
      { time:'4:30',  emoji:'🫒', name:'Amla + coconut water', desc:'Amla = 20× Vit C of orange. Coconut water replenishes electrolytes in Greeshma heat.', tags:['Vit C:vit-c','Calcium:calcium'] },
      { time:'7 PM',  emoji:'🍲', name:'Moong dal khichdi + curd', desc:'Easily digestible. Curd = probiotic immunity builder. Light for early dinner.', tags:['Protein:protein','B Vitamins:b-vit'] },
    ]
  },
  tue: {
    label: 'Tuesday',
    focus: 'GROWTH & BONES',
    meals: [
      { time:'7 AM',  emoji:'🌰', name:'5 soaked almonds + warm milk', desc:'Soak overnight — removes tannins, better calcium absorption. Ayurvedic morning ritual.', tags:['Calcium:calcium','Vit E:vit-e'] },
      { time:'8 AM',  emoji:'🧆', name:'Paneer paratha + curd', desc:'Paneer = 18g protein + 480mg calcium/100g. Best vegetarian growth food.', tags:['Protein:protein','Calcium:calcium'] },
      { time:'10:30', emoji:'🍊', name:'Orange wedges + peanut pack', desc:'Vit C from orange TRIPLES iron absorption from peanuts.', tags:['Vit C:vit-c','Iron:iron'] },
      { time:'1 PM',  emoji:'🍱', name:'Rajma chawal + salad', desc:'Rajma = complete protein + iron + zinc. North India growth classic.', tags:['Protein:protein','Iron:iron','Zinc:zinc'] },
      { time:'4:30',  emoji:'🌺', name:'Til ladoo — 1 piece', desc:'Sesame = calcium 975mg/100g (higher than milk). Bone Rasayana.', tags:['Calcium:calcium','Iron:iron'] },
      { time:'7 PM',  emoji:'🍜', name:'Vegetable upma + warm milk at 9pm', desc:'Warm milk at bedtime = calcium absorbed during sleep-phase bone growth.', tags:['Calcium:calcium','B Vitamins:b-vit'] },
    ]
  },
  wed: {
    label: 'Wednesday',
    focus: 'BRAIN POWER',
    meals: [
      { time:'7 AM',  emoji:'🥜', name:'Walnut + soaked raisins', desc:'Walnuts = omega-3 DHA, the primary brain-building fat. Raisins for quick energy.', tags:['Protein:protein','Iron:iron'] },
      { time:'8 AM',  emoji:'🥞', name:'Moong chilla + green chutney', desc:'High protein breakfast. Green chutney adds iron + Vit C combo.', tags:['Protein:protein','Vit C:vit-c'] },
      { time:'10:30', emoji:'🍌', name:'Banana + peanut butter', desc:'Potassium + protein. Banana releases serotonin — mood and focus booster.', tags:['Protein:protein','B Vitamins:b-vit'] },
      { time:'1 PM',  emoji:'🍛', name:'Palak paneer + roti + dal', desc:'Spinach DHA precursors + paneer protein. Iron for oxygen to the brain.', tags:['Protein:protein','Iron:iron','Calcium:calcium'] },
      { time:'4:30',  emoji:'🌿', name:'Brahmi milk (warm)', desc:'Brahmi = adaptogen that improves memory retention and neural pathway growth.', tags:['Calcium:calcium','B Vitamins:b-vit'] },
      { time:'7 PM',  emoji:'🥗', name:'Rice + sambar + papad', desc:'Easy digest. Tamarind in sambar = Vit C for iron. Light evening meal.', tags:['Protein:protein','Iron:iron'] },
    ]
  },
  thu: {
    label: 'Thursday',
    focus: 'IMMUNITY',
    meals: [
      { time:'7 AM',  emoji:'🍋', name:'Warm lemon honey water', desc:'Kickstarts digestion. Lemon Vit C absorbed on empty stomach = maximum immunity boost.', tags:['Vit C:vit-c'] },
      { time:'8 AM',  emoji:'🥣', name:'Oats + milk + dates', desc:'Beta-glucan in oats = immune-modulating fiber. Dates add iron + natural sugar.', tags:['Calcium:calcium','Iron:iron'] },
      { time:'10:30', emoji:'🍎', name:'Apple + peanut butter', desc:'Quercetin in apple skin is a powerful antiviral compound.', tags:['Vit C:vit-c','Protein:protein'] },
      { time:'1 PM',  emoji:'🍲', name:'Chole + rice + cucumber raita', desc:'Chickpeas = zinc + iron. Raita = probiotic gut immunity. Cucumber cools for Greeshma.', tags:['Protein:protein','Iron:iron','Zinc:zinc'] },
      { time:'4:30',  emoji:'🫚', name:'Coconut water + roasted chana', desc:'Electrolytes + protein. Perfect Greeshma season snack.', tags:['Calcium:calcium','Protein:protein'] },
      { time:'7 PM',  emoji:'🍵', name:'Khichdi + ghee + pickle', desc:'Ghee = fat-soluble vitamin carrier. Pickle = probiotic. Perfect immunity dinner.', tags:['Protein:protein','B Vitamins:b-vit'] },
    ]
  },
  fri: {
    label: 'Friday',
    focus: 'ENERGY DAY',
    meals: [
      { time:'7 AM',  emoji:'🥤', name:'Banana smoothie + soaked almonds', desc:'Quick energy from banana. Almonds = sustained energy via healthy fats.', tags:['Calcium:calcium','Protein:protein'] },
      { time:'8 AM',  emoji:'🧇', name:'Poha + peanuts + lemon', desc:'Poha = iron-fortified. Lemon Vit C triples iron absorption. Quick and light.', tags:['Iron:iron','Vit C:vit-c'] },
      { time:'10:30', emoji:'🍇', name:'Grapes + cheese cube', desc:'Grapes = instant glucose. Cheese = protein to slow release. Sustained energy combo.', tags:['Calcium:calcium','Protein:protein'] },
      { time:'1 PM',  emoji:'🍛', name:'Dal makhani + jeera rice + salad', desc:'High protein dal. Jeera aids digestion. Tomato salad = Vit C + iron combo.', tags:['Protein:protein','Iron:iron','B Vitamins:b-vit'] },
      { time:'4:30',  emoji:'🍫', name:'Jaggery + peanut chikki', desc:'Jaggery = iron-rich natural sugar. Peanuts = protein. Energy Rasayana.', tags:['Iron:iron','Protein:protein'] },
      { time:'7 PM',  emoji:'🥘', name:'Vegetable pulao + dahi', desc:'Complex carbs for overnight recovery. Dahi probiotic for gut health.', tags:['Protein:protein','Calcium:calcium'] },
    ]
  },
  sat: {
    label: 'Saturday',
    focus: 'BONE STRENGTH',
    meals: [
      { time:'7 AM',  emoji:'🌰', name:'Soaked almonds + sesame balls', desc:'Sesame 975mg calcium/100g — highest plant calcium. Morning bone builder ritual.', tags:['Calcium:calcium','Iron:iron'] },
      { time:'8 AM',  emoji:'🥞', name:'Ragi dosa + sambar + coconut chutney', desc:'Ragi = finger millet 344mg calcium. Sambar adds iron. Coconut = healthy fats.', tags:['Calcium:calcium','Protein:protein'] },
      { time:'10:30', emoji:'🫐', name:'Amla candy + warm milk', desc:'Amla Vit C helps calcium absorption. Milk = direct bone mineral delivery.', tags:['Calcium:calcium','Vit C:vit-c'] },
      { time:'1 PM',  emoji:'🍛', name:'Rajma + palak rice + curd', desc:'Rajma zinc supports bone cell growth. Palak iron carries oxygen to bone marrow.', tags:['Protein:protein','Iron:iron','Zinc:zinc','Calcium:calcium'] },
      { time:'4:30',  emoji:'🌺', name:'Til ladoo — 1 piece', desc:'Sesame = calcium 975mg/100g. Traditional bone Rasayana. Jaggery adds iron.', tags:['Calcium:calcium','Iron:iron'] },
      { time:'7 PM',  emoji:'🥛', name:'Khichdi + warm milk at bedtime', desc:'Sleep-phase is peak bone mineralisation. Warm milk delivers calcium exactly when needed.', tags:['Calcium:calcium','Protein:protein','B Vitamins:b-vit'] },
    ]
  },
  sun: {
    label: 'Sunday',
    focus: 'REST & RESTORE',
    meals: [
      { time:'7 AM',  emoji:'🍵', name:'Warm ginger turmeric tea + fruit', desc:'Anti-inflammatory reset. Ginger aids digestion. Rest day = rebuild day.', tags:['Vit C:vit-c','B Vitamins:b-vit'] },
      { time:'9 AM',  emoji:'🥞', name:'Besan chilla + mint chutney', desc:'Light protein. Late breakfast on rest day is fine. Mint cools for Greeshma.', tags:['Protein:protein','Iron:iron'] },
      { time:'11:30', emoji:'🍊', name:'Seasonal fruits platter', desc:'Watermelon, mango, papaya. Greeshma cooling fruits. Vitamin C and natural hydration.', tags:['Vit C:vit-c','Iron:iron'] },
      { time:'1 PM',  emoji:'🍲', name:'Dal rice + papad + pickle + ghee', desc:'Sunday comfort meal. Ghee = fat-soluble vitamin absorption. Pickle = gut probiotic.', tags:['Protein:protein','Calcium:calcium','B Vitamins:b-vit'] },
      { time:'4:30',  emoji:'🥥', name:'Coconut water + banana', desc:'Electrolyte reset after a warm Greeshma afternoon. Potassium for muscle recovery.', tags:['Calcium:calcium','Vit C:vit-c'] },
      { time:'7 PM',  emoji:'🥣', name:'Light moong soup + roti', desc:'Moong is the most digestible pulse. Light dinner for gut rest and overnight recovery.', tags:['Protein:protein','Iron:iron'] },
    ]
  }
};

const focusColors = {
  'IMMUNITY BOOST':  'var(--mn)',
  'IMMUNITY':        'var(--mn)',
  'GROWTH & BONES':  'var(--pu)',
  'BRAIN POWER':     'var(--pk)',
  'ENERGY DAY':      'var(--ye)',
  'BONE STRENGTH':   'var(--pu)',
  'REST & RESTORE':  'var(--mn)',
};

const focusBg = {
  'IMMUNITY BOOST':  'var(--mnl)',
  'IMMUNITY':        'var(--mnl)',
  'GROWTH & BONES':  'var(--pul)',
  'BRAIN POWER':     'var(--pkl)',
  'ENERGY DAY':      'var(--yel)',
  'BONE STRENGTH':   'var(--pul)',
  'REST & RESTORE':  'var(--mnl)',
};

function renderCard(dayKey) {
  const d = days[dayKey];
  const card = document.getElementById('mealCard');
  const fc = focusColors[d.focus] || 'var(--mn)';
  const fb = focusBg[d.focus]    || 'var(--mnl)';

  const rows = d.meals.map(m => {
    const tags = m.tags.map(t => {
      const [label, cls] = t.split(':');
      return `<span class="n-tag ${cls}">${label}</span>`;
    }).join('');

    return `
      <div class="meal-row">
        <div class="meal-time"><span class="time-txt">${m.time}</span></div>
        <div class="meal-emoji">${m.emoji}</div>
        <div class="meal-content">
          <div class="meal-name">${m.name}</div>
          <div class="meal-desc">${m.desc}</div>
          <div class="nutrient-tags">${tags}</div>
        </div>
      </div>`;
  }).join('');

  card.innerHTML = `
    <div class="meal-card-header">
      <span class="card-day-title">${d.label}</span>
      <span class="focus-badge" style="color:${fc};background:${fb};border-color:${fc};">${d.focus}</span>
    </div>
    ${rows}
  `;

  card.classList.remove('animating');
  void card.offsetWidth; // reflow
  card.classList.add('animating');
}

/* ── TABS ── */
document.getElementById('tabsTrack').addEventListener('click', e => {
  const tab = e.target.closest('.day-tab');
  if (!tab) return;
  document.querySelectorAll('.day-tab').forEach(t => t.classList.remove('active'));
  tab.classList.add('active');
  renderCard(tab.dataset.day);
});

/* ── INIT ── */
renderCard('tue');
</script>
put this data in meal-plan.php in proper way like another page with header and footer

    @endpush
@endsection