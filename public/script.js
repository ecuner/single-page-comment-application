$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var app = Vue.createApp({
    data() {
		return {
            current_page: window.initialComments.current_page,
            last_page: window.initialComments.last_page,
            comment_count: window.initialComments.total,
            comments: window.initialComments.data,
            loading: 0,
            resetMainForm: false,
            replyCommentId: 0,
    	}
	},
    methods: {
        changePage(page){
            if (page >= 1 && page <= this.last_page)
                this.getComments({page: page});
        },
        getComments(data, url = 0, method = 'GET'){
            if (url === 0)
                url = window.getCommentsRoute;
            
            this.loading.show();
            var $this = this;
            $.ajax({
                type: method,
                url: url,
                data: data,
                dataType: 'json',
                success: function (res) {
                    $this.loading.hide();
                    if ($this.current_page == res.current_page) {
                        if (!$this.resetMainForm) { 
                            $(".comment > .replyForm").each(function(i){
                                $(this)[0].reset();
                            });
                            $this.replyCommentId = 0;
                        }
                    } else
                        $this.replyCommentId = 0;

                    if ($this.resetMainForm) {
                        $("#mainReplyForm")[0].reset();
                        $this.resetMainForm = false;
                    }

                    $this.comments = res.data;
                    $this.current_page = res.current_page;

                    // Add current page to URL, so we will stay in same page when we refresh it
                    var stateObj = { title: "comments page" };
					window.history.replaceState(stateObj, "comments page", window.location.href.split('?')[0] + "?page=" + res.current_page);

                    // These don't come if we use simplePaginate
                    if ('total' in res)
                        $this.comment_count = res.total;
                    if ('last_page' in res)
                        $this.last_page = res.last_page;

                    // "seamless" object comes from seamless-scroll-polyfill
                    if ('new_comment_id' in res) {
                        Vue.nextTick(function () {
                            seamless.scrollIntoView(document.querySelector(".comment[data-id=\""+res.new_comment_id+"\"]"), {
                                behavior: "smooth",
                                block: "center",
                                inline: "center",
                            });
                         });
                    } else {
                        seamless.scrollIntoView($this.$refs.start, {
                            behavior: "smooth",
                            block: "start",
                            inline: "nearest",
                        });
                    }
                },
                error: function (error) {
                    $this.loading.hide();
                    var errorText = "";
                    if(typeof(error.responseJSON) != "undefined") {
                        if ('errors' in error.responseJSON) {
                            $.each(error.responseJSON.errors,function(i,e){
                                errorText +='<br />'+e[0];
                            });
                        } else if ('error' in error.responseJSON) {
                            errorText = error.responseJSON.error;
                        }
                    }
                    Swal.fire({
                        title: 'Error',
                        html: errorText,
                        icon: 'error',
                        showCancelButton:true,
                        showConfirmButton:false,
                        cancelButtonText: "OK"
                    });
                }
            });
        },
        addComment(el, resetMainForm = false) {
            this.resetMainForm = resetMainForm;
            var form = $(el).closest("form");
            // Because post comment route also returns comments
            this.getComments(form.serialize() + '&page=' + this.current_page, window.postCommentRoute, 'POST');
        },
    },
    computed: {
        pagesToShowInNav: function () {
            var pages = [];
            for(var i = 1; i <= this.last_page; i++) {
                if (i <= 3)
                    pages.push(i);
                else if (i >= this.current_page - 2 && i <= this.current_page + 2)
                    pages.push(i);
                else if (i >= this.last_page - 2)
                    pages.push(i);
                else {
                    pages.push('...');
                    // Fast forward to pages we want
                    if (i < this.current_page - 2)
                        i = this.current_page - 3;
                    else
                        i = this.last_page - 3;
                }
            }
            return pages;
        },
    },
    mounted: function() {
        this.loading = $("#loader");
        this.loading.hide();
    },
});
app.component('comment', commentComponent); 
var commentAppRoot = app.mount("#comments");