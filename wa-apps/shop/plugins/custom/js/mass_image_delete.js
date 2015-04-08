var massImageDelete = {
    loaded : false,
    parent_container : '#s-product-image-list',
    button_cont : '#s-product-edit-save-panel .block.bordered-top',
    checkboxTmpl :  '<div class="mass-image-delete-checkbox">' +
                        '<input type="checkbox" class="remove-image" />' +
                    '</div>',
    buttonTmpl : '<button id="massImageDelete" class="button red">Удалить выбранные фото</button>',
    checkboxAllTmpl : '<span class="mass-select-all-image-container"><input type="checkbox" class="mass-select-all-image" />Выбрать все фото</span>',

    init : function(){
        var _this = this;

        $(document).on('change', '.remove-image' ,_this.mark.bind(_this));
        $(document).on('click', '.mass-select-all-image' ,_this.markAll.bind(_this));
        $(document).on('click', '#massImageDelete' ,function(){ _this.deleteImages(); return false});

        setInterval(function(){
            if(!_this.loaded) {
                if( $(_this.parent_container).length ) {
                    $(_this.checkboxTmpl).appendTo( _this.parent_container + ' li');
                    $( _this.checkboxAllTmpl).appendTo( _this.button_cont );
                    _this.loaded = true;
                }
            } else if( !$(_this.parent_container).length ){
                _this.loaded = false;
                _this.toggleButton();
            }
        },100);
    },
    markAll : function(e) {
        if(e.target.checked) { // check select status
            $('.remove-image').each(function() {
                this.checked = true;
                $(this).trigger("change");
            });
        } else {
            $('.remove-image').each(function() {
                this.checked = false;
                $(this).trigger("change");
            });
        }
        this.toggleButton();
    },
    mark : function(e){
        var el = e.target;
        if(el.checked) {
            $(el).parents('li:first').addClass('mass-deleting');
        } else {
            $(el).parents('li:first').removeClass('mass-deleting');
        }
        this.toggleButton();
    },
    toggleButton : function() {
        if( $( this.parent_container + ' .remove-image:checked').length && !$('#massImageDelete').length ) {
            $( this.buttonTmpl).appendTo( this.button_cont );
        } else if( !$( this.parent_container + ' .remove-image:checked').length && $('#massImageDelete').length ) {
            $('#massImageDelete').remove();
        }
    },
    deleteImages : function() {
        var _this = this;
        if( $( this.parent_container + ' .remove-image:checked').length ){
            var image_ids = [];
            $( this.parent_container + ' .remove-image:checked').each(function(key, el) {
                image_ids.push(parseInt($(el).parents('li:first').attr('data-image-id'), 10) || 0);
            })
            if( !confirm( 'Картинки “'+ image_ids.join(", ") +'” будут удалены без возможности восстановления. Вы уверены что хотите это сделать?' ) ) {
                return false;
            } else {
                for(key in image_ids){
                    $.shop.jsonPost('?module=product&action=ImageDelete&id='+ image_ids[key],{},function(r){
                        $(_this.parent_container).find('li[data-image-id='+r.data.id+']').remove();
                        _this.toggleButton();
                    });
                }
            }
        }
    }
};

if(document.location.search == '?action=products') {
    massImageDelete.init();
}