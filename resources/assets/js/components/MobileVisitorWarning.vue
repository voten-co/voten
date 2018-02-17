<template>
    <el-dialog
            :visible="visible"
            fullscreen
            @close="close"
            append-to-body
            title="Dear mobile user"
    >
        <p>
            Since beta 2.0, Voten's web client is no longer mobile-friendly. Instead, we are focusing on developing native mobile apps to bring a truly outstanding experience for our mobile using users. 
        </p>

        <p>
            Until then, please use a desktop browser. We will notify our registered users when mobile apps are ready. 
        </p>

        <p>
            Thank you for being patient with us. 
        </p>

        <div class="align-center margin-top-3">
            <a href="/register">
                <el-button round type="primary">
                        Take me to the register page
                </el-button>
            </a>
            
            <el-button round type="text margin-top-1" @click="close">
                I understand but still want to continue
            </el-button>
        </div>
    </el-dialog>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    props: ['visible'],

    methods: {
        close() {
            this.$emit('update:visible', false);
        }
    }
};
</script>
