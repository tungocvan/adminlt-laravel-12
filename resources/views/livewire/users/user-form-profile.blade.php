<div class="card">
    <div class="card-body">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" class="form-control" placeholder="Full Name"
                wire:model.defer="profile.full_name">
        </div>

        <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" class="form-control" wire:model.defer="profile.gender">
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="birthdate">Birthdate</label>
            <input type="date" id="birthdate" class="form-control"
                wire:model.defer="profile.birthdate">
        </div>

        <div class="form-group">
            <label for="job">Job</label>
            <input type="text" id="job" class="form-control" placeholder="Job"
                wire:model.defer="profile.job">
        </div>

        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea id="bio" class="form-control" placeholder="Bio"
                wire:model.defer="profile.bio"></textarea>
        </div>
        <div class="form-group">
            <label for="bio">Mail Password</label>
            <textarea id="bio" class="form-control" placeholder="mail_password"
                wire:model.defer="profile.mail_password"></textarea>
        </div>
    </div>
</div>
