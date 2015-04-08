var reviewEdit = {
    loaded : false,
    data : {},
    currentEl : null,
    formTmpl :  '<div id="reviewEditContainer">' +
                    '<input id="reviewEditId" name="review_id" type="hidden" value="${id}" />' +
                    '<input id="reviewEditName" name="name" type="text" value="${name}" />' +
                    '<p><textarea id="reviewEditText" name="text" type="text" width="50%" >${text}</textarea></p>' +
                    '<button id="reviewEditSubmit" href="#" class="js-action inline button green">Сохранить</button>'+
                    '<button id="reviewEditClose" href="#" class="js-action inline button red">Отмена</button>'+
                '</div>',

    linkTmpl : '<a class="s-review-edit small inline-link" href="#"><b><i>редактировать</i></b></a>',

    init : function(){
        var _this = this;
        _this.loaded = setInterval(function(){
            if( $('.s-reviews').length ) {
                $(_this.linkTmpl).insertAfter( ".s-review-reply" );

                $(document.body).on('click','.s-review-edit',function(){
                    _this.open(this);
                    return false;
                });
                $(document.body).on('click', '#reviewEditSubmit' ,_this.send);
                $(document.body).on('click', '#reviewEditClose' ,_this.close);

                clearInterval(_this.loaded);
            }
        },100);
    },
    loadData: function(el) {
        this.currentEl = $(el).parents('li:first');
        this.data.id = parseInt(this.currentEl.attr('data-id'), 10) || 0,
        this.data.text = this._getTextEl().html();
        this.data.name = this._getNameEl().html();
    },
    open : function(el) {
        this.loadData(el);
        $(el).replaceWith($.tmpl(this.formTmpl, this.data));
    },
    close : function() {
        $('#reviewEditContainer').replaceWith(reviewEdit.linkTmpl);
        reviewEdit.clear();
    },
    clear : function() {
        this.currentEl = null;
        this.data = {};
    },
    send : function(){
        reviewEdit.data = {
                id: $('#reviewEditId').val(),
                name: $('#reviewEditName').val(),
                text: $('#reviewEditText').val()
            };
        $.post('/custom/review-edit/',
            reviewEdit.data, function (json) {
                if( json.status == 'ok') {
                    reviewEdit.update();
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
        return this.currentEl.find('.s-review-text span');
    },
    _getNameEl : function() {
        return this.currentEl.find('.details span.hint strong').length ? this.currentEl.find('.details span.hint strong') : this.currentEl.find('.details span.hint .bold');
    }


};

if(document.location.search == '?action=products') {
    reviewEdit.init();
}