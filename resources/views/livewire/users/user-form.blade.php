{{-- Modal Add / Edit (Alpine + entangle with showModal) --}}
<div x-data="{ open: @entangle('showModal') }" x-cloak>
    <div x-show="open" x-transition.opacity style="display:none"
         class="modal-backdrop fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center">
        <div x-show="open" x-transition class="bg-white rounded shadow-lg w-full max-w-2xl mx-3" @click.away="open = false">
            <div class="modal-header p-3 bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="modal-title mb-0">{{ $isEdit ? 'Edit User' : 'Add User' }}</h5>
                <button type="button" class="close text-white" @click="open = false">&times;</button>
            </div>

            <div class="modal-body p-3">
                <form wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Name</label>
                            <input type="text" wire:model.defer="name" class="form-control">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" wire:model.defer="email" class="form-control">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Username</label>
                            <input type="text" wire:model.defer="username" class="form-control" {{ $isEdit ? 'disabled' : '' }}>
                            @error('username') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label>Password</label>
                            <input type="password" wire:model.defer="password" class="form-control" placeholder="{{ $isEdit ? 'Leave blank to keep current' : '' }}">
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Role</label>
                            <select wire:model="role" class="form-control">
                                <option value="">-- Chọn role --</option>
                                @foreach($this->roles as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Birthdate</label>
                            <input type="date" wire:model.defer="birthdate" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Google ID</label>
                        <input type="text" wire:model.defer="google_id" class="form-control">
                    </div>

                    <div class="text-right">
                        <button type="button" class="btn btn-secondary btn-sm mr-2" @click="open = false">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ $isEdit ? 'Update' : 'Save' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
