<footer class="kiddex-footer">
    <div class="footer-anim">
        <div class="fa-dot" style="width:300px;height:300px;background:var(--pk);top:-80px;left:-80px;--dur:8s;--del:0s"></div>
        <div class="fa-dot" style="width:200px;height:200px;background:var(--pu);bottom:-50px;right:10%;--dur:6s;--del:2s"></div>
        <div class="fa-dot" style="width:150px;height:150px;background:var(--ye);top:40%;left:40%;--dur:10s;--del:1s"></div>
    </div>

    <div class="footer-widget-area">
        <div class="fw-brand">
            <a href="{{ route('home') }}" class="footer-logo-text">
                <img src="{{ asset('img/logo.png') }}" alt="NutriBuddy"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
                <span style="display:none;font-family:'Fredoka One',cursive;font-size:1.6rem;color:var(--pk)">NutriBuddy</span>
            </a>
            <ul class="footer-contact-list">
                <li>
                    <span class="fci"><img src="{{ asset('img/location.png') }}" alt="" onerror="this.outerHTML='📍'"></span>
                    42, Wellness Tower, Bengaluru – 560001, Karnataka, India
                </li>
                <li>
                    <span class="fci"><img src="{{ asset('img/phone.png') }}" alt="" onerror="this.outerHTML='📞'"></span>
                    <a href="tel:18001234567">1800-123-4567</a> (Toll-Free)
                </li>
                <li>
                    <span class="fci"><img src="{{ asset('img/email.png') }}" alt="" onerror="this.outerHTML='✉️'"></span>
                    <a href="mailto:hello@nutribuddy.in">hello@nutribuddy.in</a>
                </li>
            </ul>
            <div class="footer-socials">
                <a href="#" title="Instagram"><img src="{{ asset('img/instagram.png') }}" alt="Instagram" onerror="this.outerHTML='<span style=\'font-size:1.5rem\'>📷</span>'"></a>
                <a href="#" title="Facebook"><img src="{{ asset('img/facebook.png') }}" alt="Facebook" onerror="this.outerHTML='<span style=\'font-size:1.5rem\'>📘</span>'"></a>
                <a href="#" title="WhatsApp"><img src="{{ asset('img/whatsapp.png') }}" alt="WhatsApp" onerror="this.outerHTML='<span style=\'font-size:1.5rem\'>💬</span>'"></a>
                <a href="#" title="Twitter"><img src="{{ asset('img/twitter.png') }}" alt="Twitter" onerror="this.outerHTML='<span style=\'font-size:1.5rem\'>🐦</span>'"></a>
            </div>
        </div>

        <div class="fw-links">
            <h4>Products</h4>
            <ul>
                <li><a href="#">GrowStrong Gummies</a></li>
                <li><a href="#">BrainBoost Chews</a></li>
                <li><a href="#">DreamCalm Drops</a></li>
                <li><a href="#">Subscription Packs</a></li>
                <li><a href="#">Shop All</a></li>
            </ul>
        </div>

        <div class="fw-links">
            <h4>Company</h4>
            <ul>
                <li><a href="{{ route('about') }}">About Us</a></li>
                <li><a href="#">Our Ingredients</a></li>
                <li><a href="#">Blog & Tips</a></li>
                <li><a href="#">Pediatrician Network</a></li>
                <li><a href="{{ route('contact') }}">Contact Us</a></li>
            </ul>
        </div>

        <div class="fw-links">
            <h4>Support</h4>
            <ul>
                <li><a href="#">FAQs</a></li>
                <li><a href="#">Track My Order</a></li>
                <li><a href="{{ route('return-policy') }}">Returns & Refunds</a></li>
                <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                <li><a href="{{ route('terms') }}">Terms of Service</a></li>
            </ul>
        </div>

        <div class="fw-subscribe">
            <h4>Stay Updated</h4>
            <p>Join 25,000+ parents getting Ayurvedic parenting tips, exclusive offers & early access every week.</p>
            <div class="subscribe-wrap">
                <input type="email" maxlength="50" placeholder="Enter your email" class="subs-input">
                <button class="subs-btn">Subscribe</button>
            </div>
        </div>
    </div>

    <div class="footer-payment-row">
        <p style="color:#888;font-size:.78rem;font-family:'Nunito',sans-serif;font-weight:700">Secure Payments</p>
        <div class="footer-payment-cards">
            <div class="payment-card"><img src="{{ asset('img/visa.webp') }}" alt="Visa" onerror="this.outerHTML='<span>VISA</span>'"></div>
            <div class="payment-card"><img src="{{ asset('img/upi.png') }}" alt="UPI" onerror="this.outerHTML='<span>UPI</span>'"></div>
            <div class="payment-card"><img src="{{ asset('img/phonepe.webp') }}" alt="PhonePe" onerror="this.outerHTML='<span>PhonePe</span>'"></div>
            <div class="payment-card"><img src="{{ asset('img/Paytm-logo.webp') }}" alt="Paytm" onerror="this.outerHTML='<span>Paytm</span>'"></div>
        </div>
    </div>

    <div class="footer-bottom-bar">
        <div class="copyright">© 2025 <a href="{{ route('home') }}">NutriBuddy Kids</a>. All rights reserved.</div>
        <ul class="foot-links">
            <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
            <li><a href="{{ route('terms') }}">Terms of Service</a></li>
            <li><a href="#">Cookie Policy</a></li>
        </ul>
    </div>
</footer>
