(function() {
  var $ = jQuery,
      container = $('.mailchimp-tools');

  var validate_form_data = function() {
    var form_data = container.find(':input').serializeArray();
        names = [],
        required = [
          'mailchimp[type]',
          'mailchimp[list_id]',
          'mailchimp[title]',
          'mailchimp[subject_line]'
        ],
        valid = true;

    $.each(form_data, function(idx, val) {
      names.push(val.name);
    });

    $.each(required, function(idx, req) {
      if (names.indexOf(req) < 0) {
        valid = false;
        return false;
      }
    });

    return valid;
  };

  container.find(':button, :submit').on('click', function(event) {
    if (!validate_form_data()) {
      alert('Please complete all fields before submitting your campaign.');
      return false;
    }

    $('form#post').off('submit.edit-post').on('submit.edit-post', function(event) {
        if (event.isDefaultPrevented())
            return;

        $(window).off('beforeunload.edit-post');
    });
  });

  container.find('input[name="mailchimp[send]"]').on('click', function() {
    if (!validate_form_data()) {
      return false;
    }

    if (!confirm('Are you sure you want to send this campaign?')) {
      return false;
    }
  });

  $('#mailchimp-use-post-title-for-campaign-subject').click(function() {
    container.find('input[name="mailchimp[subject_line]"]').val($('#title').val());
    return false;
  });

  $('#mailchimp-use-post-title-for-campaign-title').click(function() {
    container.find('input[name="mailchimp[title]"]').val($('#title').val());
    return false;
  });

  var CampaignTestModal = MCT.Modal.extend({
    actions: {
      'Send': 'send',
      'Cancel': 'close'
    },

    render: function() {
      var tmpl = _.template($('#mailchimp-tools-test-emails-tmpl').html());
      this.content = tmpl({ default_test_emails: ( typeof default_test_emails == 'undefined' ) ? false : default_test_emails });
      MCT.Modal.prototype.render.apply(this, arguments);
      return this;
    },

    send: function(event) {
      if ( $(event.currentTarget).hasClass('disabled') ) {
        return false;
      }
      var emails = this.$el.find('input[name="mailchimp[test_emails]"]').val();
      $('.mailchimp-tools').append('<input type="hidden" name="mailchimp[test_emails]" value="' + emails + '" />');
      $('input[name="mailchimp[send_test]"]').click();
      this.$el.find('.mailchimp-tools-modal-actions a').addClass('disabled');
      this.showSpinner();
      return false;
    },

    close: function() {
      $('input[name="mailchimp[send_test]"]').on('click', open_modal);
      MCT.Modal.prototype.close.apply(this, arguments);
      return false;
    }
  });

  var modal = new CampaignTestModal();

  var open_modal = function() {
    modal.render();
    $('input[name="mailchimp[send_test]"]').off('click', open_modal);
    return false;
  };

  $('input[name="mailchimp[send_test]"]').on('click', open_modal);

})();
