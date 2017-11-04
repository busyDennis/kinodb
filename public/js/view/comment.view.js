define([ "jquery", "backbone" ], function($, Backbone) {

	var CommentView = Backbone.View
			.extend({
				initialize : function() {
					this.single_comment_template = _.template($(
							'#template-single-comment').html());
				},
				render : function() {
					this.$el.append(this
							.single_comment_template(this.model.attributes));
					return this.el;
				}
			});

	return CommentView;
});