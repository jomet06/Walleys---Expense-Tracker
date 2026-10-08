import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('appShell', () => ({
    sidebarOpen: false,
    isDesktop: window.matchMedia('(min-width: 1024px)').matches,
    init() {
        this.media = window.matchMedia('(min-width: 1024px)');
        this.onResize = () => {
            const focusWasInSidebar = this.$refs.sidebar.contains(document.activeElement);
            this.isDesktop = this.media.matches;
            if (this.isDesktop) this.closeSidebar(false);
            else if (focusWasInSidebar && !this.sidebarOpen) {
                this.$nextTick(() => this.$refs.menuToggle?.focus());
            }
        };
        this.media.addEventListener('change', this.onResize);
    },
    destroy() {
        this.media.removeEventListener('change', this.onResize);
        document.body.style.overflow = '';
    },
    openSidebar() {
        this.sidebarOpen = true;
        document.body.style.overflow = 'hidden';
        this.$nextTick(() => this.$refs.sidebar.querySelector('a, button')?.focus());
    },
    closeSidebar(restoreFocus = true) {
        if (!this.sidebarOpen) return;
        this.sidebarOpen = false;
        document.body.style.overflow = '';
        if (restoreFocus) this.$refs.menuToggle?.focus();
    },
    trapSidebarFocus(event) {
        if (event.key !== 'Tab' || this.isDesktop || !this.sidebarOpen) return;
        const elements = [...this.$refs.sidebar.querySelectorAll('a[href], button:not([disabled])')]
            .filter((element) => element.offsetParent !== null);
        const first = elements[0];
        const last = elements[elements.length - 1];
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault(); last?.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault(); first?.focus();
        }
    },
}));

Alpine.start();

if (document.getElementById('monthly-chart')) {
    import('./dashboard');
}
