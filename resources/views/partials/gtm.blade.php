{{-- Google Analytics (gtag.js) - Conditional loading based on cookie consent --}}

{{-- GA Head Script - Place in <head> section --}}
@push('gtm-head')
<script>
    // Capture UTM parameters for campaign tracking
    (function() {
        var urlParams = new URLSearchParams(window.location.search);
        var utmParams = {};
        ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function(param) {
            var value = urlParams.get(param);
            if (value) {
                utmParams[param] = value;
            }
        });

        if (Object.keys(utmParams).length > 0) {
            sessionStorage.setItem('utm_params', JSON.stringify(utmParams));
        } else {
            var storedUtm = sessionStorage.getItem('utm_params');
            if (storedUtm) {
                try { Object.assign(utmParams, JSON.parse(storedUtm)); } catch(e) {}
            }
        }

        window.utmParams = utmParams;
    })();

    // Function to load Google Analytics (called after cookie consent)
    function loadGA() {
        if (window.gaLoaded) return;
        window.gaLoaded = true;

        var script = document.createElement('script');
        script.async = true;
        script.src = 'https://www.googletagmanager.com/gtag/js?id=G-8DVXSB7DJK';
        document.head.appendChild(script);

        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        window.gtag = gtag;
        gtag('js', new Date());
        gtag('config', 'G-8DVXSB7DJK');
    }

    // Check if consent already given
    document.addEventListener('DOMContentLoaded', function() {
        var stored = localStorage.getItem('cookie_consent');
        if (stored) {
            try {
                var preferences = JSON.parse(stored);
                if (preferences.analytics || preferences.marketing) {
                    loadGA();
                }
            } catch(e) {}
        }
    });

    // Listen for cookie consent events
    window.addEventListener('cookie-consent-analytics', function() {
        loadGA();
    });

    window.addEventListener('cookie-consent-marketing', function() {
        loadGA();
    });

    // Sign Up conversion event
    window.trackSignUp = function(method, userId) {
        if (typeof window.gtag === 'function') {
            window.gtag('event', 'sign_up', {
                method: method || 'email',
                user_id: userId || null
            });
        }
    };

    // Purchase conversion event
    window.trackPurchase = function(transactionId, value, currency, interval) {
        if (typeof window.gtag === 'function') {
            window.gtag('event', 'purchase', {
                transaction_id: transactionId,
                value: value,
                currency: currency || 'EUR',
                items: [{
                    item_name: 'SyncMyDay Pro - ' + (interval || 'Yearly'),
                    item_category: 'Subscription',
                    price: value,
                    quantity: 1
                }]
            });
        }
    };
</script>
@endpush
