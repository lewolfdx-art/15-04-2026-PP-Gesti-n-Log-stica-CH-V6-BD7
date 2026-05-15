@php
    use App\Models\User;
    $user = User::find(2);
    $unreadCount = $user ? $user->unreadNotifications()->count() : 0;
    $notifications = $user ? $user->unreadNotifications : collect();
@endphp

<div class="relative">
    <button 
        id="notification-bell"
        type="button"
        class="relative p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-full"
        onclick="toggleNotifications()"
    >
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold text-white bg-red-600 rounded-full min-w-[20px]">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>
    
    <div id="notification-dropdown" 
         class="hidden absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl z-50 border border-gray-200">
        <div class="p-3 border-b bg-gray-50 rounded-t-lg">
            <h3 class="text-sm font-semibold text-gray-700">🔔 Notificaciones</h3>
        </div>
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="p-4 border-b hover:bg-gray-50 cursor-pointer transition">
                    <div class="font-medium text-sm text-red-600">
                        {{ $notification->data['title'] ?? 'Alerta' }}
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        {{ $notification->data['body'] ?? '' }}
                    </div>
                    <div class="text-xs text-gray-400 mt-2">
                        {{ $notification->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500 text-sm">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p>No hay notificaciones</p>
                    <p class="text-xs mt-1">Todas las cuentas están al día</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
function toggleNotifications() {
    var dropdown = document.getElementById('notification-dropdown');
    dropdown.classList.toggle('hidden');
}

// Cerrar al hacer clic fuera
document.addEventListener('click', function(event) {
    var bell = document.getElementById('notification-bell');
    var dropdown = document.getElementById('notification-dropdown');
    if (bell && dropdown && !bell.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});

// Auto-actualizar cada 5 segundos
setInterval(function() {
    location.reload();
}, 30000);
</script>

<style>
#notification-bell:hover {
    background-color: #f3f4f6;
}
</style>