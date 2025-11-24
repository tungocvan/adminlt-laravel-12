<div>
  <div class="row">
      <div class="col-md-6">
          <div class="card card-primary">
              <div class="card-header">
                  <h3 class="card-title">Quick Example</h3>
              </div>
 
              <!-- form start -->
              <form wire:submit="submit">
                  <div class="card-body">
                      <livewire:select-option-table 
                          placeholder="Chọn sản phẩm" 
                          model="WpProduct" 
                          title="title" 
                          id="id" 
                          wire:model.live="products"
                      />

                      <livewire:date-picker 
                          placeholder="Ngày bắt đầu" 
                          name="start_date" 
                          format="DD/MM/YYYY" 
                          wire:model.live="start_date"
                      />

                      <livewire:date-picker 
                          placeholder="Ngày kết thúc" 
                          name="end_date" 
                          format="DD/MM/YYYY" 
                          wire:model.live="end_date"
                      />
                    
                      <x-image-upload 
                            label="Ảnh chính" 
                            model="imageUpload" 
                            :current="$image" 
                            removeMethod="removeImage" 
                     />
                      <livewire:text-editor 
                          wire:model.live="description" 
                          name="description" 
                          label="Mô tả sản phẩm ngắn" 
                          placeholder="Nhập mô tả..."        
                          height=100      
                      />
                               
                  </div>

                  <div class="card-footer">
                      <button type="submit" class="btn btn-primary">Get Data</button>
                  </div>
              </form>
          </div>
      </div>
      <div class="col-md-6">
        <livewire:select-option-table 
                placeholder="Chọn tỉnh thành" 
                model="Area" 
                title="name" 
                id="code" 
                wire:model.live="code"
                :filters="['status' => 1,'area_type' =>'1']"
                {{-- wire:key="code-{{ $code }}" --}}
        />
        <livewire:select-option-table 
                placeholder="Chọn phường xã" 
                model="Area" 
                title="name" 
                id="code" 
                wire:model.live="ward"
                :filters="['status' => 1,'area_type' =>'2','parent_code' => $ward]"
                wire:key="ward-{{ $ward }}"
        />
            {{ $ward}}
      </div>
  </div>

</div>

