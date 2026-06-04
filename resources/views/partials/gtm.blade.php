{{-- Google Analytics (gtag.js) with Google Consent Mode v2 --}}
{{-- gtag.js loads on every page; cookie consent only toggles consent signals,
     so conversion events fire reliably (cookieless + modeled when consent denied). --}}

@php
    $gaMeasurementId = config('services.ga.measurement_id');
    $adsConversionId = config('services.google_ads.conversion_id');
    $adsSignupLabel = config('services.google_ads.conversions.signup');
    $adsPurchaseLabel = config('services.google_ads.conversions.purchase');
@endphp

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

    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    window.gtag = gtag;

    // Apply a stored/explicit consent choice to Google Consent Mode v2.
    function applyGoogleConsent(prefs) {
        if (!prefs) return;
        gtag('consent', 'update', {
            analytics_storage: prefs.analytics ? 'granted' : 'denied',
            ad_storage: prefs.marketing ? 'granted' : 'denied',
            ad_user_data: prefs.marketing ? 'granted' : 'denied',
            ad_personalization: prefs.marketing ? 'granted' : 'denied'
        });
    }
    window.applyGoogleConsent = applyGoogleConsent;

    // Consent Mode v2: default to denied BEFORE config so no cookies are set
    // until the visitor consents. GA still receives cookieless/modeled pings.
    gtag('consent', 'default', {
        ad_storage: 'denied',
        analytics_storage: 'denied',
        ad_user_data: 'denied',
        ad_personalization: 'denied',
        wait_for_update: 500
    });

    // If the visitor already made a choice, apply it immediately.
    (function() {
        try {
            var stored = localStorage.getItem('cookie_consent');
            if (stored) { applyGoogleConsent(JSON.parse(stored)); }
        } catch(e) {}
    })();

    gtag('js', new Date());
    gtag('config', '{{ $gaMeasurementId }}');
@if($adsConversionId)
    gtag('config', '{{ $adsConversionId }}');
@endif

    // Load gtag.js (always; consent signals above govern storage/cookies).
    (function() {
        var script = document.createElement('script');
        script.async = true;
        script.src = 'https://www.googletagmanager.com/gtag/js?id={{ $gaMeasurementId }}';
        document.head.appendChild(script);
    })();

    // React to consent changes from the cookie banner.
    window.addEventListener('cookie-consent-updated', function(e) {
        applyGoogleConsent(e.detail);
    });

    // Sign Up conversion event
    window.trackSignUp = function(method, userId) {
        gtag('event', 'sign_up', {
            method: method || 'email',
            user_id: userId || null
        });
@if($adsConversionId && $adsSignupLabel)
        gtag('event', 'conversion', {
            send_to: '{{ $adsConversionId }}/{{ $adsSignupLabel }}'
        });
@endif
    };

    // Purchase conversion event
    window.trackPurchase = function(transactionId, value, currency, interval) {
        gtag('event', 'purchase', {
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
@if($adsConversionId && $adsPurchaseLabel)
        gtag('event', 'conversion', {
            send_to: '{{ $adsConversionId }}/{{ $adsPurchaseLabel }}',
            value: value,
            currency: currency || 'EUR',
            transaction_id: transactionId
        });
@endif
    };
</script>
@endpush
