<div class="card">
    <div class="card-body">
        <div class="form-group">
            <label for="company_name">Đơn vị mua hàng</label>
            <input type="text" id="company_name" class="form-control" placeholder="Đơn vị mua hàng"
                wire:model.defer="shipping_info.company_name">
        </div>
        <div class="form-group">
            <label for="address">Địa chỉ</label>
            <input type="text" id="address" class="form-control" placeholder="Địa chỉ"
                wire:model.defer="shipping_info.address">
        </div>
        <div class="form-group">
            <label for="tax_code">Mã số thuế</label>
            <input type="text" id="tax_code" class="form-control" placeholder="Mã số thuế"
                wire:model.defer="shipping_info.tax_code">
        </div>

        <div class="form-group">
            <label for="city">Email</label>
            <input type="text" id="Email" class="form-control" placeholder="Email"
                wire:model.defer="shipping_info.email">
        </div>

        <div class="form-group">
            <label for="phone">Số điện thoại</label>
            <input type="text" id="phone" class="form-control" placeholder="Số điện thoại"
                wire:model.defer="shipping_info.phone">
        </div>
        <div class="form-group">
            <label for="website">Website</label>
            <input type="text" id="website" class="form-control" placeholder="Website"
                wire:model.defer="shipping_info.website">
        </div>
    </div>
</div>
