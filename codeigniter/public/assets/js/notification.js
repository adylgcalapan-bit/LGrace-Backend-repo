/*
=========================================
Notifications
Community Problems Visibility System
=========================================
*/

document.addEventListener("DOMContentLoaded", function () {

    console.log("Notifications Page Loaded");

    // =====================================
    // Mark as Read
    // =====================================

    const markButtons = document.querySelectorAll(".btn-outline-success");

    const unreadCard = document.querySelectorAll(".notification-item.unread");

    let unreadCount = unreadCard.length;

    const unreadCounter = document.querySelectorAll(".card h2")[1];

    markButtons.forEach(function(button){

        button.addEventListener("click", function(){

            const notification = this.closest(".notification-item");

            if(notification.classList.contains("unread")){

                notification.classList.remove("unread");

                this.innerHTML = '<i class="bi bi-check-circle"></i> Read';

                this.disabled = true;

                unreadCount--;

                if(unreadCounter){

                    unreadCounter.textContent = unreadCount;

                }

            }else{

                this.innerHTML = '<i class="bi bi-check-circle"></i> Read';

                this.disabled = true;

            }

        });

    });

});


/*
=========================================
Future Backend Functions
=========================================

These functions will be connected
to CodeIgniter later.

Examples:

loadNotifications();

markNotificationAsRead(id);

deleteNotification(id);

fetchLatestNotifications();

=========================================
*/


/*
=========================================
Demo Notification Data
=========================================
*/

const notifications = [

    {
        title: "Report Submitted",
        status: "Unread"
    },

    {
        title: "Report In Progress",
        status: "Read"
    },

    {
        title: "Report Resolved",
        status: "Read"
    }

];

console.table(notifications);