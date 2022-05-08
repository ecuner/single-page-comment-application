const commentComponent = {
    props: ['comment', 'allsubcomments', 'replycommentid', 'layer'],
    emits: ['add-comment', 'update:replycommentid'],
    data() {
		return {
            layerLimit: window.layerLimit
    	}
	},
    methods: {
        reply(e) {
            if (this.layer > this.layerLimit)
                return;

            // Hide the form when reply button is clicked second time
            var commentToBeReplied = (this.replycommentid == this.comment.id ? 0 : this.comment.id);
            this.$emit('update:replycommentid', commentToBeReplied);
            
            if (commentToBeReplied !== 0) {
                var commentEl = $(e.currentTarget).closest(".comment");
                //var commentReplyForm = commentEl.children(".replyForm");
                //var formDist = (window.innerHeight - commentReplyForm.height()) / 2; 
                var formDist = window.innerHeight / 2;
                $([document.documentElement, document.body]).animate({
                    scrollTop: (commentEl.offset().top + commentEl.outerHeight()) - formDist
                }, 450);
            }
        }
    },
    template: `
        <div class="comment" v-bind:data-id="comment.id">
            <div class="title">
                <h2>{{comment.name}}</h2>
                <span>{{comment.time_past}}</span>
                <a v-if="layer < layerLimit" class="reply" href="javascript:void(0)" @click="reply"><i class="fa fa-reply"></i> Reply</a>
            </div>
            <div v-html="comment.content"></div>
            <comment
                v-for="child in allsubcomments.filter(x => x.parent_id === comment.id)"
                :key="child.id"
                :comment="child"
                :allsubcomments="allsubcomments"
                :layer="layer+1"
                :replycommentid="replycommentid" @update:replycommentid="this.$emit('update:replycommentid',$event)"
                @add-comment="this.$emit('add-comment',$event)"
            >
            </comment>
            <Transition name="expand">
                <form action="javascript:void(0)" method="post" class="replyForm" v-if="replycommentid == comment.id">
                    <input type="hidden" name="parent_id" :value="comment.id" />
                    <div class="mt-4 mb-3">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Your name" required>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="3" placeholder="Your comment" required></textarea>
                    </div>
                    <button type="button" @click="this.$emit('add-comment',$event)" class="btn btn-primary">Submit</button>
                </form>
            </Transition>
        </div>
    `
};
  