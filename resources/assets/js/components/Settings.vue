<template>
    <div class="padding-bottom-10 flex1 user-select" id="settings">
        <div class="left-sidebar-box">
            <div class="side-tabs">
                <router-link :to="{ name: 'user-settings-account' }" active-class="is-active">
					My Account
				</router-link>

                <router-link :to="{ name: 'user-settings-profile' }" active-class="is-active">
					My Profile
				</router-link>

				<router-link :to="{ name: 'user-settings-email-and-password' }" active-class="is-active">
					Email & Password
				</router-link>

                <hr>

                <router-link :to="{ name: 'user-settings-delete-account' }" active-class="is-active" class="go-red">
					Delete Account 
				</router-link>
            </div>

            <div class="content">
                <transition name="slide-fade" mode="out-in">
                    <router-view></router-view>
                </transition>
            </div>
        </div>
    </div>
</template>
