if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.replies = {

    init: function()
    {
        var callback = $jMaQma.proxy(function()
        {
            $jMaQma('#redactor_modal .redactor_reply_link').each($jMaQma.proxy(function(i,s)
            {
                $jMaQma(s).click($jMaQma.proxy(function()
                {
                    this.insertReply($jMaQma(s).next().html());
                    return false;
                }, this));
            }, this));
            this.selectionSave();
            this.bufferSet();
        }, this);
        this.buttonAddSeparator();
        this.buttonAdd('replies', IMQM_REPLIES_TITLE, function()
        {
            this.modalInit(IMQM_REPLIES_TITLE, '#repliesmodal', 500, callback);
        });

    },

    insertReply: function(html)
    {
        this.selectionRestore();
        this.execCommand('inserthtml', html);
        this.modalClose();
    }

}