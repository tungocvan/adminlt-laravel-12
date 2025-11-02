<div>
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-cogs"></i> Cấu hình hệ thống (.env)</h3>
            <button wire:click="backupEnv" class="btn btn-warning btn-sm">
                <i class="fas fa-save"></i> Backup .env
            </button>
        </div>

        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3 mb-3">
                    <button class="btn btn-outline-primary btn-block" data-toggle="modal" data-target="#emailModal">
                        <i class="fas fa-envelope"></i> Cấu hình Email
                    </button>
                </div>
                <div class="col-md-3 mb-3">
                    <button class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#databaseModal">
                        <i class="fas fa-database"></i> Cấu hình Database
                    </button>
                </div>
                <div class="col-md-3 mb-3">
                    <button class="btn btn-outline-info btn-block" data-toggle="modal" data-target="#appModal">
                        <i class="fas fa-cube"></i> Cấu hình Ứng dụng
                    </button>
                </div>
                <div class="col-md-3 mb-3">
                    <button class="btn btn-outline-secondary btn-block" data-toggle="modal" data-target="#cacheModal">
                        <i class="fas fa-sync"></i> Cấu hình Cache & Queue
                    </button>
                </div>
                <div class="col-md-3 mb-3">
                    <button class="btn btn-outline-danger btn-block" data-toggle="modal" data-target="#gmailModal">
                        <i class="fas fa-sync"></i> Cấu hình Login gmail
                    </button>
                </div>
            </div>
        </div>
    </div>

   
    <!-- Modal: Cấu hình Email -->
    @include('livewire.env.partials.email-config')
    <!-- Modal: Cấu hình Database -->
    @include('livewire.env.partials.database-config')
    <!-- Modal: Cấu hình App -->
    @include('livewire.env.partials.app-config')
    <!-- Modal cấu hình Cache & Queue -->
    @include('livewire.env.partials.cache-queue-config')
    <!-- Modal cấu hình Cache & Queue -->
    @include('livewire.env.partials.login-gmail-config')
    

    <!-- Loading Modal -->
    <div wire:loading wire:target="updateEmailConfig,updateDatabaseConfig,updateAppConfig,updateCacheConfig,backupEnv">
        <div class="modal-backdrop fade show"></div>
        <div class="modal d-block" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered text-center">
                <div class="spinner-border text-primary" style="width: 4rem; height: 4rem;" role="status"></div>
                <p class="mt-3 text-muted">Đang xử lý, vui lòng chờ...</p>
            </div>
        </div>
    </div>
</div>
