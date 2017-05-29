<template>
    <div class="col-7 padding-bottom-10 user-select">
        <div class="left-sidebar-box">
            <div class="side-tabs">
                <router-link :to="{ name: 'user-settings-account' }" active-class="is-active">
					My Account
				</router-link>

                <router-link :to="{ name: 'user-settings-profile' }" active-class="is-active">
					My Profile
				</router-link>

				<router-link :to="{ name: 'user-settings-feed' }" active-class="is-active">
					Home Feed
				</router-link>

				<router-link :to="{ name: 'user-settings-email-and-password' }" active-class="is-active">
					Email & Password
				</router-link>
            </div>

            <div class="content">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>
