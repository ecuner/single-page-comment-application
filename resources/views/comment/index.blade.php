<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Aloware Test Project</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="{{ asset('style.css') }}" />
    </head>
    <body>
        <div id="loader">
            <div class="ring"></div>
        </div>
        <div class="container my-4">
            <div class="whiteBox">
                <h1>10 Blog Examples for Your Inspiration</h1>
                <img src="{{asset('image.webp')}}" alt="" class="mw-100 my-4" />
                <p>
                    Blogging has long been a popular way for people to express their passions, experiences and ideas with readers worldwide.<br /><br />
                    A blog can be its own website, or an add-on to an existing site. Whichever option you choose, it serves as a space to share your story or market your expertise in your own words, with your own visual language to match.<br /><br />
                    To help create a blog of your own, we’ve compiled this selection of ten blog examples. They’re packed with all the design wisdom you need to transform your blog into one of the best in the business.
                </p>
                <br />
                <h2>10 inspiring blog examples</h2>
                <ol class="my-4">
                    <li>Zion Adventure Photog</li>
                    <li>Mrs. Space Cadet</li>
                    <li>Simply Tabitha</li>
                    <li>Lizzy Hadfield</li>
                    <li>Suvelle Cuisine</li>
                    <li>Mikaela Reuben</li>
                    <li>Seasons in Colour</li>
                    <li>Not Another Cooking Show</li>
                    <li>Roshini Kumar</li>
                    <li>Olivia and Laura</li>
                </ol>
                <hr class="my-4" />
                <div id="comments">
                    <h2 class="h4 d-inline-block me-2" ref="start">Comments</h2>
                    <span class="d-inline-block">@{{comment_count}} in total, @{{comments.length}} shown</span>
                    <nav aria-label="Comment navigation" class="mt-4 mb-3" v-if="last_page > 1">
                        <ul class="pagination pagination-sm">
                        <li class="page-item" :class="{disabled: current_page == 1}">
                            <a class="page-link" href="javascript:void(0)" @click="changePage(current_page-1)">Previous</a>
                        </li>
                        <li v-for="page in pagesToShowInNav" class="page-item" :class="{active: page == current_page, disabled: page === '...'}">
                            <a class="page-link" href="javascript:void(0)" @click="page !== '...' && changePage(page)">@{{page}}</a>
                        </li>
                        <li class="page-item" :class="{disabled: current_page == last_page}">
                            <a class="page-link" href="javascript:void(0)" @click="changePage(current_page+1)">Next</a>
                        </li>
                        </ul>
                    </nav>
                    <div class="row">
                        <div class="col-md-12" v-for="comment in comments">
                            <comment
                                :key="comment.id"
                                :comment="comment"
                                :allsubcomments="comment.descendants"
                                :layer="0"
                                :replycommentid="replyCommentId" @update:replycommentid="replyCommentId = $event"
                                @add-comment="addComment($event.currentTarget)">
                            </comment>
                        </div>
                    </div>
                    <br />
                    <form action="javascript:void(0)" method="post" class="mt-4 replyForm" id="mainReplyForm">
                        <input type="hidden" name="parent_id" value="0" />
                        <div class="mb-3">
                            <input type="text" name="name" class="form-control" id="name" placeholder="Your name" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" name="content" rows="3" placeholder="Your comment" required></textarea>
                        </div>
                        <button type="button" @click="addComment($event.currentTarget, true)" class="btn btn-primary">Submit</button>
                    </form>
                    <nav aria-label="Comment navigation" class="mt-5" v-if="last_page > 1">
                        <ul class="pagination pagination-sm">
                        <li class="page-item" :class="{disabled: current_page == 1}">
                            <a class="page-link" href="javascript:void(0)" @click="changePage(current_page-1)">Previous</a>
                        </li>
                        <li v-for="page in pagesToShowInNav" class="page-item" :class="{active: page == current_page, disabled: page === '...'}">
                            <a class="page-link" href="javascript:void(0)" @click="page !== '...' && changePage(page)">@{{page}}</a>
                        </li>
                        <li class="page-item" :class="{disabled: current_page == last_page}">
                            <a class="page-link" href="javascript:void(0)" @click="changePage(current_page+1)">Next</a>
                        </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <script>
            var initialComments = @json($comments);
            var getCommentsRoute = '{{route('get_comments_json')}}';
            var postCommentRoute = '{{route('post_comment')}}';
            var layerLimit = {{$layerLimit}};
        </script>
        <script src="https://unpkg.com/vue@3"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>        
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/seamless-scroll-polyfill@latest/lib/bundle.min.js"></script>
        <script src="{{ asset('comment.js') }}"></script>
        <script src="{{ asset('script.js') }}"></script>
    </body>
</html>
