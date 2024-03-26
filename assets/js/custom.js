;(function($){
    const doc     = $(document);
    const ajaxUrl = UF.ajax_url;

    class UniForum{
        init(){
            this.addNewForumPosts();
        }

        addNewForumPosts(){
            $('.forum-wrap').on('submit', 'form#add-new-forum-post', function(e){
                e.preventDefault();
                let _self       = $(this);
                let title       = _self.find('input[name="forum_tite"]').val().trim();
                let content     = _self.find('textarea').val().trim();
                let nonce       = _self.find('input[name="forum_nonce"]').val();
                let publishText = _self.find('input[type="submit"]').val();

                let data = {
                    action  : 'add_new_forum_post',
                    title   : title,
                    content : content,
                    security: nonce
                }
                
                if( ! title.length > 0  ){
                    _self.find('input[name="forum_tite"]').focus();
                    return;
                }
                if( ! content.length > 0  ){
                    _self.find('textarea').focus();
                    return;
                }
                
                $.ajax({
                    type      : 'POST',
                    url       : ajaxUrl,
                    data      : data,
                    beforeSend: ()=>{
                        _self.find('input[type="submit"]').val('Loading...');
                    },
                    success   : ( response )=>{
                        if( response ){
                            let receivedData = response.data;
                            let receivedItem = `
                            <li data-item="${receivedData.id}">
                                <p>
                                    <span class="author-name">${receivedData.author}</span>
                                    <span class="author-status active"></span>
                                </p>
                                <h2 class="forum-title">${receivedData.title}</h2>
                                <p class="text-uf-default">${receivedData.excerpt}</p>
                                <a href="${receivedData.permalink}" class="permalink">Read More</a>
                                <a href="javascript:void(0)" class="permalink edit">Edit</a>
                                <a href="javascript:void(0)" class="permalink delete" >Delete</a>
                                <span class="uf-comment-count">No comments</span>
                            </li>
                            `;
                            _self.closest('.forum-post').siblings('.forum-items').prepend( receivedItem);

                            // reset form data
                            _self.find('input[name="forum_tite"]').val('');
                            _self.find('textarea').val('');
                            _self.find('input[type="submit"]').val(publishText);
                        }
                    },
                    error     : ( error )=>{
                        console.log( error );
                    },
                });
            });
        }
    }

    doc.ready(()=>{
        const forum = new UniForum;
        forum.init();
    });
    
})(jQuery);