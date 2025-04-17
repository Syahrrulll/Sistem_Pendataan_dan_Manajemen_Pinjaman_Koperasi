// Real-time Notifications
class Notifications {
    constructor() {
        this.notificationBell = document.querySelector('.notification-bell');
        this.notificationCount = document.querySelector('.notification-count');
        this.notificationDropdown = document.querySelector('.notification-dropdown');
        
        if (this.notificationBell) {
            this.init();
        }
    }

    init() {
        // Load initial notifications
        this.loadNotifications();
        
        // Set up WebSocket connection
        this.setupWebSocket();
        
        // Mark as read event
        this.notificationDropdown.addEventListener('click', (e) => {
            if (e.target.classList.contains('mark-as-read')) {
                this.markAsRead(e.target.dataset.id);
            }
        });
    }
    
    loadNotifications() {
        fetch('/api/notifications')
            .then(response => response.json())
            .then(data => {
                this.updateNotificationCount(data.unread);
                this.renderNotifications(data.notifications);
            });
    }
    
    setupWebSocket() {
        const socket = io.connect('https://your-websocket-server.com');
        
        socket.on('new_notification', (data) => {
            this.addNotification(data);
            this.incrementCount();
            this.showBrowserNotification(data);
        });
    }
    
    addNotification(data) {
        const notification = document.createElement('a');
        notification.className = `notification-item ${data.unread ? 'unread' : ''}`;
        notification.href = data.url || '#';
        notification.innerHTML = `
            <div class="notification-icon bg-${data.type}">
                <i class="fas ${data.icon}"></i>
            </div>
            <div class="notification-content">
                <p>${data.message}</p>
                <small>${this.formatTime(data.timestamp)}</small>
            </div>
            ${data.unread ? '<button class="mark-as-read" data-id="' + data.id + '"><i class="fas fa-times"></i></button>' : ''}
        `;
        
        this.notificationDropdown.insertBefore(notification, this.notificationDropdown.firstChild);
    }
    
    renderNotifications(notifications) {
        this.notificationDropdown.innerHTML = '';
        
        if (notifications.length === 0) {
            this.notificationDropdown.innerHTML = '<div class="notification-empty">Tidak ada notifikasi</div>';
            return;
        }
        
        notifications.forEach(notification => {
            this.addNotification(notification);
        });
    }
    
    updateNotificationCount(count) {
        if (count > 0) {
            this.notificationCount.textContent = count;
            this.notificationCount.style.display = 'flex';
            this.notificationCount.classList.add('pulse');
        } else {
            this.notificationCount.style.display = 'none';
        }
    }
    
    incrementCount() {
        const current = parseInt(this.notificationCount.textContent) || 0;
        this.notificationCount.textContent = current + 1;
        this.notificationCount.style.display = 'flex';
        this.notificationCount.classList.add('pulse');
    }
    
    markAsRead(id) {
        fetch(`/api/notifications/${id}/read`, {
            method: 'POST'
        }).then(response => {
            if (response.ok) {
                const current = parseInt(this.notificationCount.textContent) || 0;
                if (current > 0) {
                    this.notificationCount.textContent = current - 1;
                    if (current - 1 === 0) {
                        this.notificationCount.style.display = 'none';
                    }
                }
                
                const notification = document.querySelector(`.notification-item.unread button[data-id="${id}"]`).parentNode;
                notification.classList.remove('unread');
                notification.querySelector('button').remove();
            }
        });
    }
    
    showBrowserNotification(data) {
        if (!("Notification" in window)) return;

        if (Notification.permission === "granted") {
            this.createNotification(data);
        } else if (Notification.permission !== "denied") {
            Notification.requestPermission().then(permission => {
                if (permission === "granted") {
                    this.createNotification(data);
                }
            });
        }
    }
    
    createNotification(data) {
        new Notification("Koperasi Digital", {
            body: data.message,
            icon: "/assets/images/logo.png"
        });
    }
    
    formatTime(timestamp) {
        return moment(timestamp).fromNow();
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    new Notifications();
});