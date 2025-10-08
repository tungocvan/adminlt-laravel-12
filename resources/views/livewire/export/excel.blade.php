<div>
    <!-- Nút mở modal -->
  
    <button class="btn btn-outline-success btn-sm mr-2" wire:click="openModal">
        <i class="fa fa-download"></i> Xuất Excel
    </button>
  
    <!-- Modal -->
     <div 
        class="modal fade @if($showModal) show d-block @endif" 
        tabindex="-1" 
        role="dialog" 
        @if($showModal) style="background: rgba(0,0,0,0.5);" @endif
    >


      <div class="modal-dialog  modal-dialog-scrollable">
        <div class="modal-content">
  
          <div class="modal-header">
            <h5 class="mb-0">📊 Xuất Excel từ {{ class_basename($model) }}</h5>
            <button type="button" class="close" wire:click="closeModal"> <span aria-hidden="true">&times;</span></button>
          </div>
  
          <div class="modal-body">
            <div class="card shadow-lg">
                
            
                <div class="card-body">
                    <div class="form-group">
                        <label>Tiêu đề báo cáo</label>
                        <input type="text" class="form-control" wire:model="title">
                    </div>
            
                    <div class="form-group">
                        <label>Footer (Người lập bảng)</label>
                        <input type="text" class="form-control" wire:model="footer">
                    </div>
            
                    <hr>
            
                    <h6>Chọn cột muốn xuất:</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">
                                        <input checked type="checkbox" wire:click="toggleSelectAll" {{ $selectAll ? 'checked' : '' }}>
                                    </th>
                                    <th>Tên field</th>
                                    <th>Tên hiển thị (VN)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fields as $index => $field)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" wire:click="toggleField({{ $index }})"
                                                {{ $field['selected'] ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $field['name'] }}</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm"
                                                wire:model.lazy="fields.{{ $index }}.label">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
            
                    <div class="text-right mt-3">
                        <button wire:click="export" class="btn btn-success">
                            <i class="fa fa-file-excel-o"></i> Xuất Excel
                        </button>
                    </div>
                </div>
            </div>
          </div>
  
        </div>
      </div>
    </div>

    <script>
    
    
        window.addEventListener('exported', function(){
            var modalEl = document.getElementById('exportExcelModal');
            if (modalEl) {
                // Bootstrap 4: dùng jQuery API
                $('#exportExcelModal').modal('hide');
                // toastr.success('Đăng ký thành công!');
            }
           
        });
    
    
    </script>
    
</div>