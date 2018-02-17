<template>
    <section class="home-wrapper" v-once>
        <nav class="nav has-shadow user-select">
            <div class="container">
                <h1 class="title">
                    Credits
                </h1>

                <div class="flex-center">
                    <router-link to="/tos" class="margin-right-1">
                        <el-button round size="small" type="text">Terms of Service</el-button>
                    </router-link>
                    <router-link to="/privacy-policy" class="margin-right-1">
                        <el-button round size="small" type="text">Privacy Policy</el-button>
                    </router-link>
                    <router-link to="/credits">
                        <el-button round size="small" type="text">Credits</el-button>
                    </router-link>
                </div>
            </div>
        </nav>

        <section id="page" class="home-submissions credits">
            <p>
                Voten would like to thank the authors of the following open source components and third party services that contributed essential functionality to our applications:
            </p>

            <div class="container-grid margin-top-2">
                <div class="item">
                    <h3>
                        <a href="http://php.net/" rel="nofollow">
                            <img src="/landing/credits/php.png" alt="PHP7">
                        </a>
                    </h3>

                    <div>
                        <p>
                            PHP is a widely-used open source general-purpose scripting language that is especially suited for web development. It was originally created by Rasmus Lerdorf in 1994, but is now produced by The PHP Development Team.
                        </p>
                    </div>
                </div>

                <div class="item">
                    <h3>
                        <a href="https://laravel.com/" rel="nofollow">
                            <img src="/landing/credits/laravel.png" alt="laravel">
                        </a>
                    </h3>

                    <div>
                        <p>
                            Laravel is a free, open-source PHP web framework, created by Taylor Otwell and intended for the development of web applications following the model–view–controller (MVC) architectural pattern.
                        </p>

                        <p>
                            Other than the framework itself, Laravel's warm community has helped us a lot during development.
                        </p>
                    </div>
                </div>

                <div class="item">
                    <h3>
                        <a href="https://vuejs.org/" rel="nofollow">
                            <img src="/landing/credits/vue.jpg" alt="vue">
                        </a>
                    </h3>

                    <div>
                        <p>
                            Vue is a progressive framework for building user interfaces. Vue's bleeding fast performance is the main reason Voten's developer team picked it as our primary JS framework.
                        </p>

                        <p>
                            Vue is going to be huge in 2017, we are happy to be one of the first teams using it on a project at this size.
                        </p>
                    </div>
                </div>

                <div class="item">
                    <h3>
                        <a href="http://element.eleme.io/#/en-US" rel="nofollow">
                            <img src="/landing/credits/element.jpg" alt="Element">
                        </a>
                    </h3>

                    <div>
                        <p>
                            Element is a Chinese JavaScript and CSS library based on Vue 2.0. 
                        </p>

                        <p>
                            Element simplifies writing Vue codes by letting developers to focus on what's really important: the logic. 
                        </p>
                    </div>
                </div>

                <div class="item">
                    <h3>
                        <a href="http://www.mysql.com" rel="nofollow">
                            <img src="/landing/credits/mysql.png" alt="mysql">
                        </a>
                    </h3>

                    <div>
                        <p>
                            MySQL, the most popular Open Source SQL database management system, was created by a Swedish company, MySQL AB, founded by David Axmark, Allan Larsson and Michael "Monty" Widenius.
                        </p>

                        <p>
                            Currently it is being developed, distributed, and supported by Oracle Corporation.
                        </p>
                    </div>
                </div>

                <!-- <div class="item">
                    <h3>
                        <a href="https://www.elastic.co/products/elasticsearch" rel="nofollow">
                            <img src="/landing/credits/elastic.png">
                        </a>
                    </h3>

                    <div>
                        <p>
                            Elasticsearch is a search server based on Lucene. It provides a distributed, multitenant-capable full-text search engine with a RESTful web interface and schema-free JSON documents.
                        </p>

                        <p>
                            Elasticsearch is developed in Java and is released as open source under the terms of the Apache License.
                        </p>
                    </div>
                </div> -->
                
                <div class="item">
                    <h3>
                        <a href="https://www.algolia.com/referrals/fb684d54/join" rel="nofollow">
                            <img src="/landing/credits/algolia.svg">
                        </a>
                    </h3>

                    <div>
                        <p>
                            Algolia is a hosted full-text, numerical, and faceted search engine capable of delivering realtime results from the first keystroke. Algolia’s powerful API lets you quickly and seamlessly implement search within your websites and mobile applications.
                        </p>
                    </div>
                </div>

                <div class="item">
                    <h3>
                        <a href="https://nodejs.org/en/" rel="nofollow">
                            <img src="/landing/credits/node.png" alt="nodeJS">
                        </a>
                    </h3>
                    <div>
                        <p>
                            Node.js is a platform built on Chrome's JavaScript runtime for easily building fast and scalable network applications.
                        </p>

                        <p>
                            Node.js uses an event-driven, non-blocking I/O model that makes it lightweight and efficient, perfect for data-intensive real-time applications that run across distributed devices.
                        </p>
                    </div>
                </div>

                <div class="item">
                    <h3>
                        <a href="http://socket.io/" rel="nofollow">
                            <img src="/landing/credits/socket.png" alt="socket">
                        </a>
                    </h3>

                    <div>
                        <p>
                            Socket.IO enables real-time bidirectional event-based communication. It works on every platform, browser or device, focusing equally on reliability and speed.
                        </p>
                        <p>
                            Socket.IO is one of the most powerful JavaScript frameworks on GitHub, and most depended-upon NPM module.
                        </p>
                    </div>
                </div>

                <div class="item">
                    <h3>
                        <a href="https://nginx.org/" rel="nofollow">
                            <img src="/landing/credits/nginx.png" alt="nginx">
                        </a>
                    </h3>

                    <div>
                        <p>
                            Nginx [engine x] is an HTTP and reverse proxy server, a mail proxy server, and a generic TCP/UDP proxy server, originally written by Igor Sysoev.
                        </p>

                        <p>
                            Nginx has grown in popularity since its release due to its light-weight resource utilization and its ability to scale easily on minimal hardware.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </section>
</template>
