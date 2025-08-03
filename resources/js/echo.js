import Echo from 'laravel-echo';
import { io } from 'socket.io-client';

window.io = io;

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: 'https://adminlt.laravel.tk:6001',
    path: '/socket.io',
    transports: ['websocket'],
    forceTLS: true,
});

