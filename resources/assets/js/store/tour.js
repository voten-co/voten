export default {
    show: false,
    step: 1,

    items: [
        {
            id: 'left-sidebar',
            title: 'Workbench:',
            body: `<p>On your left sidebar, you find handy links and tools that you use all the time. Just hover over each icon to see what it does. There's also a keyboard shortcut for each of them.</p>
                <p>The question mark at the end of it links to our help center which holds answers to most of your questions.</p>`
        },
        {
            id: 'feed',
            title: 'Feed:',
            body: `<p>Your feed/homepage is your best friend on Voten. It is where you find all the cool stuff posted to Voten.</p>
                <p>By default it displays posts only from channels you have already subscribed to; but you can adjust it to your needs via many filters tailored to your very needs. Just click on the "gear" icon to open the config page. </p>`
        },
        {
            id: 'right-sidebar-channels',
            title: 'Channels:',
            body: `<p>On your right sidebar, you find all the channels you have subscribed. You can use the instant search to filter through them.</p>
                <p>You can even customize the sidebar entirely with various colors and filters tailored for your very personal taste.</p>`
        },
        {
            id: 'right-sidebar-menu',
            title: 'Menu:',
            body: `<p>Every other link that you do not use all the time is located on the menu. To toggle the menu, click on the arrow icon or your profile picture.</p>`
        },
        {
            id: 'os-notifications',
            title: 'Push Notifications:',
            body: `<p>Last but not least, click on "Allow" to turn on web push notifications for Voten to receive notifications in your OS when Voten is not the active tab in your browser. (like when you have minimized the browser to watch Game of Thrones!)</p>
            <p><strong>Note:</strong> Unlike other websites, we do not bother you. We only send push notifications when Voten is present in your browser.  </p>`
        }
    ]
};
