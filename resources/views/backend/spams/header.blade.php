<div class="tabs is-toggle is-fullwidth">
    <ul>
        <li class="{{ url('/backend/spams/submissions') == url()->current() ? 'is-active' : '' }}">
            <a href="/backend/spams/submissions">
                <span>Reprted Submissions</span>
            </a>
        </li>

        <li class="{{ url('/backend/spams/comments') == url()->current() ? 'is-active' : '' }}">
            <a href="/backend/spams/comments">
                <span>Reprted Comments</span>
            </a>
        </li>

        <li class="{{ url('/backend/spams/multiple-accounts') == url()->current() ? 'is-active' : '' }}">
            <a href="/backend/spams/multiple-accounts">
                <span>Mutiple Accounts</span>
            </a>
        </li>
    </ul>
</div>