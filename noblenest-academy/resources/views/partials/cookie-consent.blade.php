{{-- Cookie Consent Banner (GDPR / COPPA)
     Dismissed flag stored in localStorage: nn_cookie_consent = 'accepted' | 'declined'
     Include once in layouts/app.blade.php before </body>
--}}
<div id="nn-cookie-banner"
     class="hidden fixed bottom-0 start-0 end-0 z-3 p-3"
     style="background:rgba(15,23,42,0.96);backdrop-filter:blur(6px)">
  <div class="container flex flex-col md:flex-row items-center justify-between gap-3">
    <p class="text-white mb-0 text-sm" style="max-width:640px">
      🍪 We use essential cookies to keep you signed in and optional analytics cookies to improve the experience.
      We never sell data. Children's data is collected only with verified parental consent (COPPA/GDPR-K).
      <a href="/privacy-policy" class="text-sky-600">Learn more</a>.
    </p>
    <div class="flex gap-2 flex-shrink-0">
      <button id="nn-cookie-decline" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-200 text-gray-100 hover:bg-gray-100 hover:text-gray-900 px-3 py-1.5 text-sm rounded-full">Essentials only</button>
      <button id="nn-cookie-accept"  class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-amber-500 text-gray-900 hover:bg-amber-600 px-3 py-1.5 text-sm rounded-full font-bold">Accept &amp; Continue</button>
    </div>
  </div>
</div>

<script>
(function () {
  const STORAGE_KEY = 'nn_cookie_consent';
  const banner = document.getElementById('nn-cookie-banner');
  const acceptBtn = document.getElementById('nn-cookie-accept');
  const declineBtn = document.getElementById('nn-cookie-decline');

  // Already decided
  if (localStorage.getItem(STORAGE_KEY)) return;

  // Show after a small delay so it doesn't flash on first paint
  setTimeout(() => banner && banner.classList.remove('d-none'), 900);

  acceptBtn && acceptBtn.addEventListener('click', function () {
    localStorage.setItem(STORAGE_KEY, 'accepted');
    banner.classList.add('d-none');
    // Emit custom event so analytics scripts can initialise
    document.dispatchEvent(new CustomEvent('nn:consent:accepted'));
  });

  declineBtn && declineBtn.addEventListener('click', function () {
    localStorage.setItem(STORAGE_KEY, 'declined');
    banner.classList.add('d-none');
  });
})();
</script>
