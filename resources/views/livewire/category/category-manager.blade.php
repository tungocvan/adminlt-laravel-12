<div class="content">

    {{-- Toolbar --}}
    @include('livewire.category.parts.toolbar')

    {{-- Import / Export --}}
    @include('livewire.category.parts.import-export')

    {{-- Alerts --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mt-2">
            {{ session('message') }}
            <button type="button" class="close" data-dismiss="alert">×</button>
        </div>
    @endif

    {{-- Filters --}}
    @include('livewire.category.parts.filters')

    {{-- Table --}}
    @include('livewire.category.parts.table')

    {{-- Modal form --}}
    @include('livewire.category.parts.modal-form')

</div>

@push('js')
    {{-- JS để show/hide modal + fix perPage select --}}
    <script>
        document.addEventListener('livewire:init', () => {
            // Show / hide modal
            document.addEventListener('showModal', () => {
                $('#categoryModal').modal({
                    backdrop: 'static',
                    keyboard: false,
                }).modal('show');
            });
            document.addEventListener('refreshUsers', () => {
                $('#modalUser').modal('hide');
            });
            document.addEventListener('show-modal-role', () => {
                $('#modalRole').modal({
                    backdrop: 'static',
                    keyboard: false
                }).modal('show');
            });
            document.addEventListener('modalRole', () => {
                $('#modalRole').modal('hide');
            });

            $('[data-dismiss="modal"]').on('click', function() {
                $(this).closest('.modal').modal('hide');          
                Livewire.dispatch('reset-form');    
            });


            

        });
    </script>
@endpush