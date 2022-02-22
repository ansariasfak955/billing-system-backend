@if (Session::has('success'))  
    @push('custom-scripts')
        <script type="text/javascript">        
            var Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              onOpen: function(toast) {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
              }
            })
            
            Toast.fire({
              icon: 'success',
              title: '{{ \Session::get('success') }}'
            })
        </script>
    @endpush
@endif

@if (\Session::has('error'))
    @push('custom-scripts')
        <script type="text/javascript">
            var Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              onOpen: function(toast) {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
              }
            })
            
            Toast.fire({
              icon: 'error',
              title: '{{ \Session::get('error') }}'
            })
        </script>
    @endpush
@endif