import './bootstrap';
import { createApp } from 'vue';
import SidebarComponent from './components/Sidebar.vue';

// Create Vue app
const app = createApp({});

// Register the Sidebar component
app.component('sidebar-component', SidebarComponent);

// Mount the app to the #sidebar-app element
app.mount('#sidebar-app');