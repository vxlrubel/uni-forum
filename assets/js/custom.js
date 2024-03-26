;(function($){
    const doc     = $(document);
    const ajaxUrl = UF.ajax_url;

    class UniForum{
        init(){
            this.addNewForumPosts();
            this.deleteForumPost();
            this.fetchForumDataInsidePopupForm();
            this.destroyfetchForumDataInsidePopupForm();
            this.updateForumPostById();
            this.updateProfileUserData();
            this.slideToggleUserEditForm();
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

        deleteForumPost(){
            $('.forum-wrap').on('click', 'button.button.delete', function(e){
                e.preventDefault();
                let item   = $(this).closest('li');
                let postID = parseInt(item.data('item'));

                if( ! confirm('Are you sure?') ){
                    return;
                }

                let data = {
                    action : 'delete_forum_post',
                    post_id: postID
                }
                
                $.ajax({
                    type      : 'POST',
                    url       : ajaxUrl,
                    data      : data,
                    beforeSend: ()=>{
                        $(this).text('Deleting...');
                    },
                    success   : ( response )=>{
                        let receiveData = response.data;
                        if( receiveData.status == 200 ){
                            item.css('background-color', '#f53b57').fadeOut(300, ()=>item.remove());
                        }else{
                            console.log( receiveData );
                        }
                    },
                    error     : ( error )=>{
                        console.log( 'Error:', error );
                    },
                });
            });
        }

        fetchForumDataInsidePopupForm(){
            $('.forum-wrap').on('click', 'button.button.edit', function(e){
                e.preventDefault();
                let _self   = $( this );
                let text = _self.text();
                let id      = parseInt( _self.closest('li').data('item') );

                let data = {
                    action : 'fetch_forum_post_by_id',
                    post_id: id
                }
                
                $.ajax({
                    type      : 'POST',
                    url       : ajaxUrl,
                    data      : data,
                    beforeSend: ()=>{
                        _self.text('Fetching...');
                    },
                    success   : ( response )=>{
                        let res     = response.data;
                        let getForm = getPopupform(res.post_id, res.post_title, res.post_content );
                        $('body').prepend(getForm);
                        _self.text(text)
                    },
                    error     : ( error )=>{
                        console.log( 'Error:', error );
                    },
                });
            });
        }

        destroyfetchForumDataInsidePopupForm(){
            $('body').on('click', '[data-close="dismiss"]', function(e){
                e.preventDefault();
                let form = $(this).closest('.uf-update-form-parent');
                form.fadeOut(300, function(e){
                    form.remove();
                });
            });

            doc.on('click', '.uf-update-form-parent', function(e){
                e.preventDefault();
                let targetedName = $(this).attr('class');
                
                if( e.target.classList.value != targetedName ){
                    return;
                }

                $(this).fadeOut(300, ()=>{
                    $(this).remove();
                });
            });
        }

        updateForumPostById(){
            $('body').on('click', '#forum-post-update', function(e){
                e.preventDefault();
                let _self   = $(this);
                let form    = $(this).closest('form.uf-update-form');
                let forumId = parseInt(form.data('update-id'));
                let title   = form.find('input[name="uf-post-title"]').val().trim();
                let excerpt = form.find('textarea[name="uf-post-content"]').val().trim();
                let item    = $('body').find(`li[data-item="${forumId}"]`);
                
                if ( title == null || title == '' && excerpt == null || excerpt == '' ){
                    console.log('empty')
                    return;
                }

                let data = {
                    action : 'update_forum_post_by_id',
                    post_id: forumId,
                    title  : title,
                    content: excerpt
                }

                $.ajax({
                    type      : 'POST',
                    url       : ajaxUrl,
                    data      : data,
                    beforeSend: ()=>{
                        _self.text('Updating...');
                    },
                    success   : ( response )=>{
                        if( response.data.status === 200 ){
                            // remove popup form
                            _self.closest('.uf-update-form-parent').fadeOut(300, ()=>{
                                _self.closest('.uf-update-form-parent').remove();
                            });

                            let splitExcerpt   = excerpt.split( ' ' );
                            let sliceExcerpt   = splitExcerpt.slice( 0, 20 );
                            let trimmedContent = sliceExcerpt.join( ' ' );

                            item.find('h2.forum-title').text( title );
                            item.find('p.text-uf-default').text( trimmedContent + '...' );
                        }
                    },
                    error     : ( error )=>{
                        console.log( 'Error:', error );
                    },
                });
            });
        }

        updateProfileUserData(){
            $('.forum-wrap').on('submit', '#profile-edit-form', function(e){
                e.preventDefault();
                let _self      = $(this);
                let id         = _self.find('#user-id').val().trim();
                let fName      = _self.find('#user-f-name').val().trim();
                let lName      = _self.find('#user-l-name').val().trim();
                let buttonText = _self.find('input[type="submit"]').val().trim();

                let data = {
                    action: 'update_user_profile',
                    id    : id,
                    f_name: fName,
                    l_name: lName
                }
                
                $.ajax({
                    type      : 'POST',
                    url       : ajaxUrl,
                    data      : data,
                    beforeSend: ()=>{
                        _self.find('input[type="submit"]').val('Updating...');
                    },
                    success: ( response ) => {
                        if( response ){
                            _self.siblings('h2.profile-name').html(response.data);
                            _self.find('input[type="submit"]').val(buttonText);
                        }
                    },
                    error: (error)=>{
                        console.log( 'error: ', + error );
                    }
                });
            });
        }

        slideToggleUserEditForm(){
            $('.forum-wrap').on('click', '#profile-edit-form-toggle', function(e){
                e.preventDefault();
                let _self = $(this);
                console.log( $(this))
                _self.closest('div.link').siblings('form#profile-edit-form').stop().slideToggle(300);
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

    const getPopupform = ( id, title, content)=>{
        $html = `
        <div class="uf-update-form-parent">
            <form action="javascript:void(0)" class="uf-update-form" data-update-id="${id}">
                <div class="form-head">
                    <h3>Forum Post Update</h3>
                    <span data-close="dismiss">×</span>
                </div>
                <div class="form-body">
                    <input type="text" class="uf-field mb-15" name="uf-post-title" value="${title}">
                    <textarea name="uf-post-content" class="uf-field">${content}</textarea>
                </div>
                <div class="form-footer">
                    <button class="uf-button update" id="forum-post-update" type="button">Update</button>
                    <button class="uf-button close" type="button" data-close="dismiss">Close</button>
                </div>
            </form>
        </div>
        `;
        
        return $html;
    }

    doc.ready(()=>{
        const forum = new UniForum;
        forum.init();
    });
    
})(jQuery);