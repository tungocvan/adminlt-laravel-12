<div>
    <!-- Livewire component: TestList -->
    @php
        $nameList = ['John Doe', 'David', 'Maria'];

    @endphp
    <h2 class="text-red">test list1</h2>
    <div class="col-md-6">
        <livewire:select-option-table 
                placeholder="Chọn tỉnh thành" 
                model="Area" 
                title="name" 
                id="code" 
                wire:model.live="code"
                :filters="['status' => 1,'area_type' =>'1']"
          
        />
    </div>
    <div class="col-md-4">
        <label x-text="type=='sold'?'Người mua':'Người bán'"></label>
    
        <livewire:select-option-table 
            wire:model.live="name"
            placeholder="-- Tất cả --"
            :options="$nameList"

        />
    </div>
    
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