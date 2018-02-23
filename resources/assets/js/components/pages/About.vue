<template>
    <section class="home-wrapper" v-once>
        <nav class="nav has-shadow user-select">
            <div class="container">
                <h1 class="title">
                    {{ Laravel.title }}
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

        <section id="page" class="home-submissions">
            <p>
                We are a small team of developers risen from the world of open-source. We believe in an open and modern Internet.
                Voten's mission is to give people the power to share their content with not just their friends but the world and interact in real-time.
            </p>

            <br>
            <h2 class="title">Users are voters</h2>
            <p>
                On Voten users are in charge. They get to decide what deserves more attention by voting. After all the word "Voten" means "vote" in German and Spanish.
            </p>

            <br>
            <h2 class="title">Designed for the 21st century</h2>
            <p>
                Thanks to the power of open-source community, Voten has become the most modern social bookmarking platform on the Internet. It's been developed from scratch to work with latest web technologies such as WebSockets. Voters have full control over their browsing experience. Voten's UI is highly customizable yet deadly simple.
            </p>

            <br>
            <h2 class="title">An open-source project</h2>
            <p>
                    It gets even better: Voten is 100% <a href="https://github.com/voten-co/voten" target="_blank">open-source!</a> Yes, we've got nothing to hide. We also like the idea of giving back something to the open-source community. We also have awesome developer users who directly contribute in Voten's development.
                    </p>
        </section>
    </section>
</template>

<script>
export default {
    data() {
        return {
            Laravel
        };
    }
};
</script>
