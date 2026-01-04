<div>
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-user mr-2"></i>Thông tin giao hàng
            </h5>
        </div>
        <div class="card-body">
            <form wire:submit="placeOrder">
                {{-- Customer Name --}}
                <div class="form-group">
                    <label for="customer_name" class="font-weight-bold">
                        Họ và tên <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           id="customer_name"
                           wire:model="customer_name"
                           class="form-control @error('customer_name') is-invalid @enderror"
                           placeholder="Nhập họ và tên người nhận">
                    @error('customer_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Customer Phone --}}
                <div class="form-group">
                    <label for="customer_phone" class="font-weight-bold">
                        Số điện thoại <span class="text-danger">*</span>
                    </label>
                    <input type="tel" 
                           id="customer_phone"
                           wire:model.blur="customer_phone"
                           class="form-control @error('customer_phone') is-invalid @enderror"
                           placeholder="Nhập số điện thoại">
                    @error('customer_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Customer Email --}}
                <div class="form-group">
                    <label for="customer_email" class="font-weight-bold">
                        Email <small class="text-muted">(không bắt buộc)</small>
                    </label>
                    <input type="email" 
                           id="customer_email"
                           wire:model.blur="customer_email"
                           class="form-control @error('customer_email') is-invalid @enderror"
                           placeholder="Nhập địa chỉ email">
                    @error('customer_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Customer Address --}}
                <div class="form-group">
                    <label for="customer_address" class="font-weight-bold">
                        Địa chỉ giao hàng <span class="text-danger">*</span>
                    </label>
                    <textarea id="customer_address"
                              wire:model="customer_address"
                              class="form-control @error('customer_address') is-invalid @enderror"
                              rows="3"
                              placeholder="Nhập địa chỉ chi tiết (số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố)"></textarea>
                    @error('customer_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Note --}}
                <div class="form-group">
                    <label for="note" class="font-weight-bold">
                        Ghi chú <small class="text-muted">(không bắt buộc)</small>
                    </label>
                    <textarea id="note"
                              wire:model="note"
                              class="form-control"
                              rows="2"
                              placeholder="Ghi chú cho đơn hàng (ví dụ: giao hàng giờ hành chính)"></textarea>
                </div>

                <hr>

                {{-- Payment Method --}}
                <div class="form-group">
                    <label class="font-weight-bold">Phương thức thanh toán</label>
                    <div class="card bg-light">
                        <div class="card-body py-3">
                            <div class="custom-control custom-radio">
                                <input type="radio" 
                                       id="payment_cod" 
                                       name="payment_method" 
                                       class="custom-control-input" 
                                       checked>
                                <label class="custom-control-label" for="payment_cod">
                                    <span class="mr-2">💵</span>
                                    Thanh toán khi nhận hàng (COD)
                                </label>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Bạn sẽ thanh toán bằng tiền mặt khi nhận hàng
                    </small>
                </div>

                {{-- Submit Button --}}
                <button type="submit" 
                        class="btn btn-danger btn-lg btn-block"
                        wire:loading.attr="disabled"
                        {{ $processing ? 'disabled' : '' }}>
                    <span wire:loading.remove wire:target="placeOrder">
                        <i class="fas fa-check-circle mr-2"></i>Đặt hàng
                    </span>
                    <span wire:loading wire:target="placeOrder">
                        <span class="spinner-border spinner-border-sm mr-2"></span>
                        Đang xử lý...
                    </span>
                </button>

                {{-- Terms --}}
                <p class="text-muted text-center mt-3 mb-0">
                    <small>
                        Bằng việc đặt hàng, bạn đồng ý với 
                        <a href="#" class="text-primary">Điều khoản dịch vụ</a> và 
                        <a href="#" class="text-primary">Chính sách bảo mật</a> của chúng tôi.
                    </small>
                </p>
            </form>
        </div>
    </div>
</div>