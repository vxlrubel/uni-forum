;(function($){
    const doc = $(document);
    const AJAXURL = UF.ajax_url;

    class UniForumAdmin{
        runPrograms(){
            this.updateSettingsOptions();
        }
        updateSettingsOptions(){
            $('.uf-reset').on('submit', 'form#up-settings-optios', function(e){
                e.preventDefault();
                let _self     = $(this);
                let data      = $(this).serialize();
                let text      = _self.find('input[type="submit"]');
                let textValue = text.val();

                $.ajax({
                    type      : 'POST',
                    url       : AJAXURL,
                    data      : data,
                    beforeSend: ()=>{
                        text.val('Save Changing...');
                    },
                    success   : (res)=>{
                        _self.closest('.uf-reset').find('.notice').remove();
                        _self.closest('.uf-reset').find('.uni-settings-title').after(res.data);
                        setTimeout(() => {
                            _self.closest('.uf-reset').find('.notice').fadeOut(300, ()=>{
                                _self.closest('.uf-reset').find('.notice').remove();
                            });
                        }, 2000);
                        text.val(textValue);
                    },
                    error     : (err)=>{
                        console.log( 'error', err );
                    }
                });
            })
        }
    } 

    doc.ready(()=>{
        const uniForum = new UniForumAdmin;
        uniForum.runPrograms();
    })
})(jQuery);