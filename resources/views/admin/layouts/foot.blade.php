<!-- jQuery 3 -->
<script src="{{ asset('Admin/js/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('Admin/js/bootstrap.min.js') }}"></script>

<!-- AdminLTE App -->
<script src="{{ asset('Admin/js/adminlte.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->

<!--custom js script -->
<script type="text/javascript"  src="{{ asset('Admin/js/js.min.js') }}"></script>

<script>
  $(document).ready(function () {
    $('.sidebar-menu').tree();
    $('[data-toggle="tooltip"]').tooltip(); 
  });
  $('textarea').each(function(){
    $(this).val($(this).val().trim());
   });
</script>
