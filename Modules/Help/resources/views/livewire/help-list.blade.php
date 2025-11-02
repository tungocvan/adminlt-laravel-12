<div class="d-flex position-relative"
    :class="{ 'dark-mode': @js($darkMode), 'light-mode': !@js($darkMode) }">
    <!-- Sidebar -->
    <div class="card {{ $collapsed ? 'collapsed-sidebar' : 'sidebar-expanded' }} shadow-sm"
        style="transition: width 0.3s;">

        <!-- Header -->
        <div
            class="card-header d-flex justify-content-between align-items-center {{ $darkMode ? 'bg-dark text-light' : 'bg-light text-dark' }}">
            <span>
                @if (!$collapsed)
                    <i class="fas fa-book mr-2"></i> Hướng dẫn
                @endif
            </span>

            <!-- Sidebar Toggle & Theme Toggle luôn hiển thị ngoài cùng -->
            <div class="toggle-icons-container d-flex flex-column position-absolute"
                style="top: 1px; left: {{ $collapsed ? '5px' : '259px' }}; z-index: 20;">

                <!-- Theme toggle -->
                <button wire:click="toggleTheme" class="btn btn-sm btn-light mb-1">
                    <i class="{{ $darkMode ? 'fas fa-sun' : 'fas fa-moon' }}"></i>
                </button>

                <!-- Sidebar toggle -->
                <button wire:click="toggleSidebar" class="btn btn-sm btn-light">
                    <i class="{{ $collapsed ? 'fas fa-angle-right' : 'fas fa-angle-left' }}"></i>
                </button>
            </div>

        </div>

        @unless ($collapsed)
            <div class="card-body p-2">
                <!-- Search input -->
                <input type="text" wire:model="search" class="form-control mb-2" placeholder="Tìm kiếm...">

                <!-- File list -->
                <div class="list-group list-group-flush" style="max-height: 70vh; overflow-y: auto;">
                    @forelse ($files as $file)
                        <div wire:click="openFile('{{ $file['name'] }}')"
                            class="list-group-item list-group-item-action {{ $currentFile === $file['name'] ? 'active font-weight-bold' : '' }}"
                            style="cursor:pointer;">
                            <div class="d-flex flex-column">
                                <!-- File name -->
                                <span class="file-name text-truncate" title="{{ $file['name'] }}">
                                    <i class="fas fa-file-alt mr-1"></i> {{ ucwords(str_replace('-', ' ', $file['name'])) }}
                                </span>

                                <!-- Timestamp -->
                                <small class="timestamp" style="font-size:0.75rem; margin-top:2px; display:block;">
                                    {{ \Carbon\Carbon::createFromTimestamp($file['modified'])->format('d/m/Y H:i:s') }}
                                </small>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-muted">Không tìm thấy file nào.</div>
                    @endforelse
                </div>
            </div>
        @endunless
    </div>

    <!-- Content -->
    <div class="flex-grow-1 ml-2">
        <div class="card {{ $darkMode ? 'bg-dark text-light' : 'bg-light text-dark' }} shadow-sm">
            <div class="card-body markdown-body">
                {!! $currentContent ?: '<div class="text-muted">Chọn file hướng dẫn từ sidebar bên trái.</div>' !!}
            </div>
        </div>
    </div>

</div>

@push('styles')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.2.0/github-markdown.min.css" />
    <style>
        /* Sidebar width */
        .sidebar-expanded {
            width: 500px !important;
        }

        .collapsed-sidebar {
            width: 50px !important;
        }

        .collapsed-sidebar .card-body {
            display: none !important;
        }

        /* File name truncate + max-width */
        .file-name {
            max-width: 500px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Timestamp hiển thị rõ ràng */
        .timestamp {
            font-weight: 500;
            display: block;
        }

        /* Markdown content */
        .markdown-body {
            padding: 1rem;
            max-height: 80vh;
            overflow-y: auto;
        }

        .markdown-body pre {
            background: #f9fafb;
            padding: 1rem;
            border-radius: .5rem;
            overflow-x: auto;
        }

        .markdown-body code {
            background: #f3f4f6;
            padding: .2rem .4rem;
            border-radius: .25rem;
        }

        /* Toggle button ngoài cùng bên phải sidebar */
        .toggle-sidebar-container {
            display: flex;
            align-items: left;
            /* margin-left: 0px; */
            left: 0px !important;
            z-index: 10;
            position: relative;
        }

        .toggle-icons-container button {
            width: 35px;
            height: 35px;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Dark / light mode */
        .dark-mode .sidebar-expanded,
        .dark-mode .collapsed-sidebar {
            background-color: #1f2937 !important;
            color: #f8f9fa !important;
        }

        .dark-mode .card-body,
        .dark-mode .list-group-item {
            background-color: #1f2937 !important;
            color: #f8f9fa !important;
        }

        .dark-mode .markdown-body {
            background-color: #111827 !important;
            color: #f8f9fa !important;
        }

        .light-mode .sidebar-expanded,
        .light-mode .collapsed-sidebar {
            background-color: #f9fafb !important;
            color: #111827 !important;
        }

        .light-mode .card-body,
        .light-mode .list-group-item {
            background-color: #f9fafb !important;
            color: #111827 !important;
        }

        .light-mode .markdown-body {
            background-color: #ffffff !important;
            color: #111827 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('livewire:update', function() {
            const activeItem = document.querySelector('.list-group-item.active');
            if (activeItem) activeItem.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        });
    </script>
@endpush
