<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <!-- 🔔 Icon -->
    <button @click="open = !open" class="relative focus:outline-none">
        <i class="fas fa-bell text-gray-600 text-xl"></i>
        @if ($unreadCount > 0)
            <span
                class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <!-- 📋 Dropdown -->
    <div x-show="open" x-transition
         class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-xl shadow-xl z-50">
        <div class="p-2 font-semibold border-b text-gray-700">Thông báo</div>

        @if ($notifications->isEmpty())
            <div class="p-4 text-gray-500 text-sm text-center">Không có thông báo mới.</div>
        @else
            <ul class="max-h-96 overflow-y-auto divide-y divide-gray-100">
                @foreach ($notifications as $alert)
                    <li wire:click="markAsRead({{ $alert->id }})"
                        class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex justify-between items-start">
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-bell text-gray-500 mt-1"></i>
                            <div>
                                <p class="text-sm text-gray-800 font-medium">{{ $alert->title }}</p>
                                <p class="text-xs text-gray-500">{{ $alert->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @if(!$alert->is_read)
                            <span class="w-2 h-2 bg-red-500 rounded-full mt-2"></span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
