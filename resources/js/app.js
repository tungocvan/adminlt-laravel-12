import './bootstrap';
import './echo';
import { initSummernote } from './summernote-helper';
window.initSummernote = initSummernote; // gắn global nếu muốn gọi trực tiếp trong Blade
