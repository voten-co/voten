<nav class="nav has-shadow">
    <div class="container">
        <a href="/" class="backend-logo">
            <img src="/imgs/voten-circle.png" alt="Voten">
        </a>

        <div class="nav-right">
            <a class="nav-item is-tab is-hidden-mobile{{ url('backend') == url()->current() ? ' is-active' : '' }}" href="/backend">
                Dashboard
            </a>

            <a class="nav-item is-tab is-hidden-mobile{{ url('backend/announcements') == url()->current() ? ' is-active' : '' }}" href="/backend/announcements">
                Announcements
            </a>

            <a class="nav-item is-tab is-hidden-mobile{{ url('backend/emails') == url()->current() ? ' is-active' : '' }}" href="/backend/emails">
                Emails
            </a>

            <a class="nav-item is-tab is-hidden-mobile{{ url('backend/firewall') == url()->current() ? ' is-active' : '' }}" href="/backend/firewall">
                Firewall
            </a>

            <a class="nav-item is-tab is-hidden-mobile{{ url('backend/appointed-users') == url()->current() ? ' is-active' : '' }}" href="/backend/appointed-users">
                Appointed Users
            </a>

            <a class="nav-item is-tab is-hidden-mobile{{ url('backend/server-control') == url()->current() ? ' is-active' : '' }}" href="/backend/server-control">
                Server
            </a>

            <a class="nav-item is-tab is-hidden-mobile{{ url('backend/channels') == url()->current() ? ' is-active' : '' }}" href="/backend/channels">
                Channels
            </a>

            <a class="nav-item is-tab is-hidden-mobile{{ url('backend/users') == url()->current() ? ' is-active' : '' }}"
               href="/backend/users">
                Users
            </a>

            <a class="nav-item is-tab is-hidden-mobile{{ url('backend/spam') == url()->current() ? ' is-active' : '' }}"
               href="/backend/spam">
                Spam
            </a>

            <a class="nav-item is-tab is-hidden-mobile"
               href="/horizon">
                Horizon
            </a>
        </div>
    </div>
</nav>