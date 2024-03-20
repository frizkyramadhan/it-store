<!-- jQuery -->
<script src="{{ asset('assets/vendors/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('assets/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('assets/vendors/fastclick/lib/fastclick.js') }}"></script>
<!-- NProgress -->
<script src="{{ asset('assets/vendors/nprogress/nprogress.js') }}"></script>
<!-- jQuery custom content scroller -->
<script src="{{ asset('assets/vendors/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js') }}"></script>
<!-- Custom Theme Scripts -->
<script src="{{ asset('assets/build/js/custom.min.js') }}"></script>

<script>
  $(document).ready(function() {
    // Create a new MutationObserver instance
    var observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.attributeName === "class") {
          var attributeValue = $(mutation.target).prop(mutation.attributeName);
          if (attributeValue === "nav-sm") {
            $('.site_title img').css('width', '70%'); // Increase the image size
            $('.site_title img').css('margin-left', '8%');
          } else {
            $('.site_title img').css('width', '15%'); // Decrease the image size
            $('.site_title img').css('margin-left', '0%');
          }
        }
      });
    });

    // Start observing the body for configured mutations
    observer.observe(document.body, {
      attributes: true
    });
  });

</script>

@yield('scripts')

</body>
</html>
