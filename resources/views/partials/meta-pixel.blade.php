{{-- Meta Pixel - Conditional loading based on cookie consent (marketing) --}}

{{-- Meta Pixel Head Script --}}
@push('meta-head')
<script>
    function loadMetaPixel() {
        if (window.metaPixelLoaded) return;
        window.metaPixelLoaded = true;

        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '26269284699419129');
        fbq('track', 'PageView');
    }

    // Check if consent already given
    document.addEventListener('DOMContentLoaded', function() {
        var stored = localStorage.getItem('cookie_consent');
        if (stored) {
            try {
                var preferences = JSON.parse(stored);
                if (preferences.marketing) {
                    loadMetaPixel();
                }
            } catch(e) {}
        }
    });

    // Listen for cookie consent event
    window.addEventListener('cookie-consent-marketing', function() {
        loadMetaPixel();
    });

    // Meta Pixel conversion helpers (with event_id for CAPI deduplication)
    window.trackMetaSignUp = function(method, userId, eventId) {
        if (typeof fbq === 'function') {
            var params = {content_name: method || 'email', status: true};
            var options = eventId ? {eventID: eventId} : {};
            fbq('track', 'CompleteRegistration', params, options);
        }
    };

    window.trackMetaPurchase = function(transactionId, value, currency, eventId) {
        if (typeof fbq === 'function') {
            var params = {value: value, currency: currency || 'EUR', content_type: 'product', content_name: 'SyncMyDay Pro'};
            var options = eventId ? {eventID: eventId} : {};
            fbq('track', 'Purchase', params, options);
        }
    };
</script>
@endpush

{{-- Meta Pixel NoScript --}}
@push('meta-body')
<noscript><img height="1" width="1" style="display:none" id="meta-pixel-noscript"
src="https://www.facebook.com/tr?id=26269284699419129&ev=PageView&noscript=1"
/></noscript>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var stored = localStorage.getItem('cookie_consent');
        var img = document.getElementById('meta-pixel-noscript');
        if (img) {
            if (stored) {
                try {
                    var preferences = JSON.parse(stored);
                    if (!preferences.marketing) {
                        img.remove();
                    }
                } catch(e) {
                    img.remove();
                }
            } else {
                img.remove();
            }
        }
    });
</script>
@endpush
