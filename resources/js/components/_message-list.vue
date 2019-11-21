<template>
    <div class="chat-conversation nicescroll">
        <ul class="conversation-list">
            <li class="clearfix" :class="[message.user.id==user.id ? 'odd' : '']" v-for="(message, index) in allMessages" :key="index" >
                <div class="chat-avatar">
                    <img src="/images/avatar.png" alt="">
                </div>
                <div class="conversation-text">
                    <div class="ctext-wrap" :title="message.created_at">
                        <p>{{message.message}}</p>
                        <div v-if="message.attachment" class="image-container">
                            <img v-if="message.is_image" width="160" class="attachment-image my-1" :src="'/'+message.attachment" alt="" @click="imageView('/'+message.attachment)">
                            <a v-if="!message.is_image" width="100" :href="'/'+message.attachment" download><span class="icon-attach"><i class="fa fa-paperclip"></i></span></a>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: ['user', 'allMessages'],
        methods: {
            imageView(path) {
                $("#image_preview").html('')
                $("#image_preview").verySimpleImageViewer({
                    imageSource: path,
                    frame: ['100%', '100%'],
                    maxZoom: '900%',
                    zoomFactor: '10%',
                    mouse: true,
                    keyboard: true,
                    toolbar: true,
                });
                $("#attachModal").modal();
            }
        },
        created() {
            // console.log(this.user)
        }
    }
</script>


<style scoped>
    .chat-conversation {
        height: 100%;
    }
    .icon-attach i {
        font-size: 20px;
        color: #5f60b5;
    }
</style>