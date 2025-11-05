<div>
    <!-- Livewire component: TestList -->
    <h2 class="text-red">test list</h2>
</div>

@push('css')
    <style>
        .text-red{
            color:red
        }
    </style>
@endpush

@push('js')
<script>
    document.addEventListener('livewire:init', () => {
        console.log('✅ Livewire 3 đã load thành công !!!');        
        Livewire.hook('component.init', ({ component, cleanup }) => {
                    console.log('component.init',component);
            })
        Livewire.hook('message.processed', (m, c) => console.log('🔁 Rerender:', c.fingerprint.name));
    });
</script>
@endpush