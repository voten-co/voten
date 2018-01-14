<template>
	<transition name="el-fade-in-linear">
		<div class="no-content-wrapper user-select"
		     v-show="visible">
			<component :is="currentView"
			           width="160"
			           height="160"></component>

			<p v-text="text"></p>
		</div>
	</transition>
</template>


<script>
import SubmissionIcon from './Icons/SubmissionIcon.vue';
import CommentIcon from './Icons/ChatIcon.vue';
import ChannelIcon from './Icons/ChannelIcon.vue';
import UserIcon from './Icons/UserIcon.vue';

export default {
    props: ['text', 'icon'],

    components: {
        SubmissionIcon,
        CommentIcon,
        ChannelIcon,
        UserIcon
    },

    data() {
        return {
            visible: true
        };
    },

    computed: {
        currentView() {
            switch (this.icon) {
                case 'comment':
                    return 'CommentIcon';
                    break;

                case 'channel':
                    return 'ChannelIcon';
                    break;

                case 'user':
                    return 'UserIcon';
                    break;

                default:
                    // 'submission'
                    return 'SubmissionIcon';
                    break;
            }
        }
    }
};
</script>
