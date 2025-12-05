{{-- Google Tag Manager - Conditional loading based on cookie consent --}}
@php
    $gtmContainerId = config('services.gtm.container_id');
@endphp

@if($gtmContainerId)
{{-- GTM Head Script - Place in <head> section --}}
@push('gtm-head')
<script>
    // Initialize dataLayer
    window.dataLayer = window.dataLayer || [];
    
    // Capture UTM parameters for campaign tracking
    (function() {
        const urlParams = new URLSearchParams(window.location.search);
        const utmParams = {};
        ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function(param) {
            const value = urlParams.get(param);
            if (value) {
                utmParams[param] = value;
            }
        });
        
        if (Object.keys(utmParams).length > 0) {
            // Store UTM params in session storage for later use
            sessionStorage.setItem('utm_params', JSON.stringify(utmParams));
            window.dataLayer.push({
                'event': 'utm_captured',
                ...utmParams
            });
        } else {
            // Try to restore from session storage
            const storedUtm = sessionStorage.getItem('utm_params');
            if (storedUtm) {
                try {
                    const parsed = JSON.parse(storedUtm);
                    Object.assign(utmParams, parsed);
                } catch(e) {}
            }
        }
        
        window.utmParams = utmParams;
    })();
    
    // Function to load GTM (called after cookie consent)
    function loadGTM() {
        if (window.gtmLoaded) return;
        window.gtmLoaded = true;
        
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $gtmContainerId }}');
        
        console.log('GTM loaded: {{ $gtmContainerId }}');
    }
    
    // Check if consent already given
    document.addEventListener('DOMContentLoaded', function() {
        const stored = localStorage.getItem('cookie_consent');
        if (stored) {
            try {
                const preferences = JSON.parse(stored);
                if (preferences.analytics || preferences.marketing) {
                    loadGTM();
                }
            } catch(e) {}
        }
    });
    
    // Listen for cookie consent events
    window.addEventListener('cookie-consent-analytics', function() {
        loadGTM();
    });
    
    window.addEventListener('cookie-consent-marketing', function() {
        loadGTM();
    });
</script>
@endpush

{{-- GTM NoScript - Place immediately after opening <body> tag --}}
@push('gtm-body')
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmContainerId }}"
            height="0" width="0" style="display:none;visibility:hidden"
            id="gtm-noscript-frame"></iframe>
</noscript>
<script>
    // Show noscript iframe only if consent given
    document.addEventListener('DOMContentLoaded', function() {
        const stored = localStorage.getItem('cookie_consent');
        const frame = document.getElementById('gtm-noscript-frame');
        if (frame) {
            if (stored) {
                try {
                    const preferences = JSON.parse(stored);
                    if (!preferences.analytics && !preferences.marketing) {
                        frame.remove();
                    }
                } catch(e) {
                    frame.remove();
                }
            } else {
                frame.remove();
            }
        }
    });
</script>
@endpush
@endif

{{-- 
    DataLayer Event Helper Functions
    Use these to push conversion events from your views/controllers
--}}
@push('gtm-head')
<script>
    // Helper function to push events to dataLayer
    window.pushDataLayerEvent = function(eventName, eventData) {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            'event': eventName,
            ...eventData
        });
        console.log('DataLayer event:', eventName, eventData);
    };
    
    // Sign Up conversion event
    window.trackSignUp = function(method, userId) {
        window.pushDataLayerEvent('sign_up', {
            'method': method || 'email',
            'user_id': userId || null
        });
    };
    
    // Purchase conversion event
    window.trackPurchase = function(transactionId, value, currency, interval) {
        window.pushDataLayerEvent('purchase', {
            'transaction_id': transactionId,
            'value': value,
            'currency': currency || 'EUR',
            'items': [{
                'item_name': 'SyncMyDay Pro - ' + (interval || 'Yearly'),
                'item_category': 'Subscription',
                'price': value,
                'quantity': 1
            }]
        });
    };
</script>
@endpush

