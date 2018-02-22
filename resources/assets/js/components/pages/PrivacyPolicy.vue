<template>
    <section class="home-wrapper" v-once>
        <nav class="nav has-shadow user-select">
            <div class="container">
                <h1 class="title">
                    Privacy Policy
                </h1>

                <div class="flex-center">
                    <router-link to="/tos" class="margin-right-1">
                        <el-button round size="small" type="text">Terms of Service</el-button>
                    </router-link>
                    <router-link to="/credits" class="margin-right-1">
                        <el-button round size="small" type="text">Credits</el-button>
                    </router-link>
                    <router-link to="/about">
                        <el-button round size="small" type="text">About</el-button>
                    </router-link>
                </div>
            </div>
        </nav>

        <section id="page" class="home-submissions">
            <p>
                This page is for all users and non-users wondering what we do with users’ information when they use our website. These policies apply to all our services.
            </p>

            <h2 class="title">
                Information you provide us
            </h2>

            <p>
                We collect all information you provide when using our services, including:
            </p>

            <ul>
                <li>
                    Everything you use to create your account
                </li>

                <li>
                    Your preferences
                </li>

                <li>
                    The content you post
                </li>

                <li>
                    The content you send as encrypted private message (which we do not monitor)
                </li>
            </ul>

            <h2 class="title">
                Information We Collect Automatically
            </h2>

            <p>
                This includes:
            </p>

            <ul>
                <li>
                    Your IP addresses, OS, browser type, visited pages, device information, links clicked, user interaction, requested URLs, searched keywords, etc.
                </li>

                <li>
                    We delete IP addresses collected after certain days. The IPs are collected to prevent cheating and other prohibited behavior.
                </li>
            </ul>

            <h2 class="title">
                How We Use Information About You
            </h2>

            <p>
                Different websites collect their users' information for different reasons. We collect information in order to:
            </p>

            <ul>
                <li>
                    Send our users updates, alerts, support or private messages.
                </li>

                <li>
                    Provide our users with our services.
                </li>

                <li>
                    Maintain and improve our services.
                </li>

                <li>
                    Analyze and supervise the data usage, trends and activities.
                </li>

                <li>
                    Communicate with our users about topics we think that could be interesting to them.
                </li>

                <li>
                    Provide our users with advertisements and services which could match their interests.
                </li>
            </ul>

            <h2 class="title">
                How We Share Information
            </h2>

            <p>
                Unlike other social websites sharing your information with third parties for many reasons, Voten is conservative about keeping your information secret. We might connect with third parties in the future but we will never share your information with them.
            </p>

            <h2 class="title">
                Children under 13
            </h2>

            <p>
                We do not think contents on websites like ours, are intended for individuals under 13. Therefore, if you are under 13, you are not allowed to make an account or use our services.
            </p>

            <hr>

            <p>
                It is possible that we change some of our policies over time. Please note that, if you continue to use our services after we have changed our policies, it will mean your consent to our new policy.
            </p>
        </section>
    </section>
</template>
