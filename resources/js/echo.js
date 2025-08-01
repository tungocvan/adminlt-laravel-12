import Echo from 'laravel-echo';
import io from 'socket.io-client';

window.io = io;

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: 'http://127.0.0.1:6001', // thêm http://
    transports: ['polling'], // ép dùng polling (HTTP)
});