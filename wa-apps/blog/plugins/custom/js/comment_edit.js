var commentEit = {
    currentEl : null,
    data : {},
    linkTmpl : '<a class="b-comment-edit small inline-link" href="#"><b><i>редактировать</i></b></a>',
    formTmpl :  '<div id="reviewEditContainer">' +
                    '<input id="reviewEditId" name="review_id" type="hidden" value="${id}" />' +
                    '<input id="reviewEditName" name="name" type="text" value="${name}" />' +
                    '<p><textarea id="reviewEditText" name="text" type="text" width="50%" >${text}</textarea></p>' +
                    '<button id="reviewEditSubmit" href="#" class="js-action inline button green">Сохранить</button>'+
                    '<button id="reviewEditClose" href="#" class="js-action inline button red">Отмена</button>'+
                '</div>',

    init : function(){
        var _this = this;
        $(_this.linkTmpl).insertAfter( ".b-comment-reply" );
        $(document.body).on('click','.b-comment-edit',function(){
            _this.open(this);
            return false;
        });
        $(document.body).on('click', '#reviewEditSubmit' ,_this.send.bind(_this));
        $(document.body).on('click', '#reviewEditClose' ,_this.close.bind(_this));
    },
    open : function(el) {
        this.loadData(el);
        $(el).replaceWith($.tmpl(this.formTmpl, this.data));
    },
    loadData : function(el){
        this.currentEl = $(el).parents('.b-comment');
        this.data.id = parseInt(this.currentEl.attr('id').replace('b-comment-',''), 10) || 0;
        this.data.text = this._getTextEl().html();
        this.data.name = this._getNameEl().html();
    },
    close : function() {
        $('#reviewEditContainer').replaceWith(this.linkTmpl);
        this.clear();
    },
    clear : function() {
        this.currentEl = null;
        this.data = {};
    },
    send : function(){
        var _this = this;
        _this.data = {
            id: $('#reviewEditId').val(),
            name: $('#reviewEditName').val(),
            text: $('#reviewEditText').val()
        };
        $.post('?plugin=custom&action=commentEdit',
            _this.data, function (json) {
                if( json.status == 'ok') {
                    if(json.data.changed) {
                        _this.update();
                    } else {
                        console.error('Review edit ',json)
                    }
                } else {
                    console.error('Review edit ',json)
                }
            }, 'json');
        return false;
    },
    update : function() {
        this._getNameEl().html(this.data.name);
        this._getTextEl().html(this.data.text);
        this.close();
    },

    _getTextEl : function(){
        return this.currentEl.find('.b-comment-text span');
    },
    _getNameEl : function() {
        return this.currentEl.find('.b-comment-auth-user span.bold').length ? this.currentEl.find('.b-comment-auth-user span.bold') : this.currentEl.find('.b-comment-auth-guest span.bold');
    }
}

$(document).ready(function(){
    commentEit.init();
})