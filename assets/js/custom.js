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
                            let receivedItem = forumPostStucture(receivedData.id, receivedData.author, receivedData.title, receivedData.excerpt, receivedData.permalink );
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

    const forumPostStucture = ( id, author, title, excerpt, url ) => {
        let htmlElement = `
        <li data-item="${id}">
            <div class="forum-header">
                <div class="author-name">
                    <span>${author}</span>
                    <span class="author-status active"></span>
                </div>
                <div class="own-post-manage">
                    <button type="button" class="button edit">Edit</button>
                    <button type="button" class="button delete">Delete</button>
                </div>
            </div>
            <h2 class="forum-title">${title}</h2>
            <p class="text-uf-default">${excerpt}</p>
            <a href="${url}" class="permalink">Read More</a>
            <div class="forum-footer">
                <button type="button" class="button like">Like</button>
                <button type="button" class="button comment">No comments</button>
            </div>
        </li>
        `;
        return htmlElement;
    }
    
    doc.ready(()=>{
        const forum = new UniForum;
        forum.init();
    });
    
})(jQuery);