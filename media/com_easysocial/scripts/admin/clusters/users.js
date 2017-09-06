EasySocial.module('admin/clusters/users', function($) {

var module = this;

EasySocial.Controller('Clusters.Users', {
    defaultOptions: {
        clusterid: null,
        clustertype: "",

        '{addMember}': '[data-cluster-add-member]',
        '{removeMember}': '[data-cluster-remove-member]',
        '{approveMember}': '[data-cluster-approve-member]',
        '{promoteMember}': '[data-cluster-promote-member]',
        '{demoteMember}': '[data-cluster-demote-member]'
    }
}, function(self, opts) { return {

    '{addMember} click': function(el, ev) {
        var members = {};

        // This is callback from the selected items
        window.addMembers = function(obj) {
            if (obj.state) {
                members[obj.id] = obj;
            } else {
                delete members[obj.id];
            }
        };

        EasySocial.dialog({
            content: EasySocial.ajax('admin/views/' + opts.clustertype + '/addMembers'),
            bindings: {
                '{submitButton} click': function() {

                    var form = $('[data-form-add-members]');
                    var input = form.find('[data-ids]');
                    var ids = [];

                    $.each(members, function(i, member) {
                        ids.push(member.id);
                    });
                    
                    input.val(JSON.stringify(ids));

                    $('[data-form-add-members]').submit();
                    
                }
            }
        });
    },

    '{removeMember} click': function(el, ev) {
        if(document.adminForm.boxchecked.value == 0) {
            alert(opts.error.empty);
        } else {
            $.Joomla('submitform', ['removeMembers']);
        }
    },

    '{approveMember} click': function(el, ev) {
        if(document.adminForm.boxchecked.value == 0) {
            alert(opts.error.empty);
        } else {
            $.Joomla('submitform', ['publishUser']);
        }
    },

    '{promoteMember} click': function(el, ev) {
        if(document.adminForm.boxchecked.value == 0) {
            alert(opts.error.empty);
        } else {
            $.Joomla('submitform', ['promoteMembers']);
        }
    },

    '{demoteMember} click': function(el, ev) {
        if(document.adminForm.boxchecked.value == 0) {
            alert(opts.error.empty);
        } else {
            $.Joomla('submitform', ['demoteMembers']);
        }
    }
}});

module.resolve();
});
