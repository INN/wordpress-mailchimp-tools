var MCT = MCT || {};

(function() {
  var $ = jQuery,
      container = $('.mailchimp-tools');

  container.find('.segment input, .group input, .subgroup input').on('click', function() {
    var list = $(this).closest('.list');
    list.find('> input').first().attr('checked', 'checked');

    if ( $(this).parent().hasClass('subgroup') ) {
      var group = $(this).closest('.group');
      group.find('> input').first().attr('checked', 'checked');
    }

    if ( $(this).parent().hasClass('group') ) {
      var group = $(this).closest('.group');
      group.find('.subgroup > input').first().attr('checked', 'checked');
    }

    if ( $(this).parent().hasClass('group') || $(this).parent().hasClass('subgroup') || $(this).parent().hasClass('segment') ) {
      list.siblings().find('.segment input, .group input').removeAttr('checked');
    }
  });

  container.find('.list > input').on('click', function() {
    var list = $(this).closest('.list');
    list.siblings().find('.segment input').removeAttr('checked');
    list.siblings().find('.group input').removeAttr('checked');
  });

  container.find('input[name*="[type]"]').on('click', function() {
    if ($(this).val() == 'plaintext') {
      container.find('#mailchimp-tools-template').hide();
    } else {
      container.find('#mailchimp-tools-template').show();
    }
  });

  // Views
  MCT.BaseView = Backbone.View.extend({
      showSpinner: function() {
          this.$el.find('.spinner').css('display', 'inline-block');
          this.$el.find('.spinner').css('visibility', 'visible');
      },

      hideSpinner: function() {
          this.$el.find('.spinner').css('display', 'none');
          this.$el.find('.spinner').css('visibility', 'hidden');
      }
  });

  MCT.Modal = MCT.BaseView.extend({
      actions: null,

      content: null,

      events: {
          "click .close": "close"
      },

      initialize: function(options) {
          var self = this;

          this.$el.addClass('mailchimp-tools-modal');

          Backbone.View.prototype.initialize.apply(this, arguments);
          this.template = _.template($('#mailchimp-tools-modal-tmpl').html());

          if (!this.content)
              this.content = (options && typeof options.content !== 'undefined')? options.content : '';

          if (!this.actions)
              this.actions = (options && typeof options.actions !== 'undefined')? options.actions : {};

          this.setEvents();

          $('body').append(this.$el);
          if ($('#mailchimp-tools-modal-overlay').length == 0)
              $('body').append('<div id="mailchimp-tools-modal-overlay" />');

          return this;
      },

      render: function() {
          this.$el.html(this.template({
              content: this.content,
              actions: this.actions
          }));
          this.setEvents();
          this.open();
      },

      setEvents: function() {
          var events = {};
          _.each(this.actions, function(v, k) { events['click .' + k] = v; });
          this.delegateEvents(_.extend(this.events, events));
      },

      open: function() {
          $('body').addClass('mailchimp-tools-modal-open');
          this.$el.removeClass('hide');
          this.$el.addClass('show');
          return false;
      },

      close: function() {
        if ($('.mailchimp-tools-modal').length <= 1) {
          $('body').removeClass('mailchimp-tools-modal-open');
        }
        this.$el.removeClass('show');
        this.$el.addClass('hide');
        return false;
      },

      hide: function() {
        this.$el.hide();
      },

      show: function() {
        this.$el.show();
      }
  });

})();
