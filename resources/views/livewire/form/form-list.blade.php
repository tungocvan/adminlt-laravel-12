<div>
  <div class="row">
      <div class="col-md-6">
          <div class="card card-primary">
              <div class="card-header">
                  <h3 class="card-title">Quick Example</h3>
              </div>
 
              <!-- form start -->
              <form wire:submit.prevent="submit">
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
                          wire:model="start_date"
                      />

                      <livewire:date-picker 
                          placeholder="Ngày kết thúc" 
                          name="end_date" 
                          format="DD/MM/YYYY" 
                          wire:model="end_date"
                      />
                    
                      <x-image-upload 
                            label="Ảnh chính" 
                            model="imageUpload" 
                            :current="$image" 
                            removeMethod="removeImage" 
                     />
                      <livewire:text-editor 
                          wire:model="description" 
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
      <div class="col-md-6"></div>
  </div>

</div>

