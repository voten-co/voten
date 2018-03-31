export default {
    // Mark all notifications as seen.
    seenAllNotifications() {
        axios.post('/notifications').then(() => {
            Store.state.notifications.forEach((element, index) => {
                if (!element.read_at) {
                    element.read_at = moment()
                        .utc()
                        .format('YYYY-MM-DD HH:mm:ss');
                }
            });
        });
    }
};
