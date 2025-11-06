@if ($isEdit)
    <form wire:submit="updateUser">
    @else
        <form wire:submit="createUser">
@endif
    <x-components::tnv-modal id="modalUser" title="{{ $isEdit ? 'Cập nhật User' : 'Tạo mới User' }}">        
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link {{ @when(!request()->tab, 'active') }}" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Home</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link {{ @when(request()->tab == 'profile', 'active') }}" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Profile</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link {{ @when(request()->tab == 'shipping', 'active') }}" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab" aria-controls="shipping" aria-selected="false">shipping</button>
              </li>
            </ul>
            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade  {{ @when(!request()->tab, 'show active') }}" id="home" role="tabpanel" aria-labelledby="home-tab"><br/>
                @include('livewire.users.user-form-content') 
              </div>

              <div class="tab-pane fade {{ @when(request()->tab == 'profile', 'show active') }}" id="profile" role="tabpanel" aria-labelledby="profile-tab"><br/>
                @include('livewire.users.user-form-profile') 
              </div>

              <div class="tab-pane fade {{ @when(request()->tab == 'shipping', 'show active') }}" id="shipping" role="tabpanel" aria-labelledby="shipping-tab"><br/>
                @include('livewire.users.user-form-shipping') 
              </div>
            </div>
        </div>
    </x-components::tnv-modal>
</form>
 