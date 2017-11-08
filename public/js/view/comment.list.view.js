define(
		[ 'jquery', 'underscore', 'backbone', 'moment', 'comment',
				'commentView' ],
		function($, _, Backbone, moment, Comment, CommentView) {

			var CommentListView = Backbone.View
					.extend({
						initialize : function() {
							this.comments_form_template = _.template($(
									'#template-comments-form').html());

							this.listenTo(this.collection, 'add',
									this.addCommentView);
							this.listenTo(this.collection, 'reset',
									this.renderComments);
						},

						events : {
							"click #submit-comment" : "submitComment",
							"click #submit-clear" : "clearFields",
							"keyup #comment-heading" : "toggleCommentButtons",
							"keyup #comment-field" : "toggleCommentButtons",
							"click #comment-toggle-visibility" : "toggleComments",
							"focusout #comment-heading" : "hideRequiredText",
							"focusout #comment-field" : "hideRequiredText",
							"focusin #comment-heading" : "showRequiredText",
							"focusin #comment-field" : "showRequiredText"
						},

						submitComment : function() {
							// console.log("the
							// submit button was
							// clicked");
							$(".enableOnInput").prop('disabled', true);
							$('.enableClearOnInput').prop('disabled', true);
							$("#submit-comment").text("Submitting...");
							var collection = this.collection;
							var context = this;
							var rating = $("#rating").raty('score');

							var newComment = new Comment({
								imdbID : collection.imdbID,
								commentHeading : $("#comment-heading").val(),
								commentText : $("#comment-field").val(),
								rating : $("#rating").raty('score'),
								created : moment()
										.format("YYYY-MM-DD HH:mm:ss")
							});
							newComment.save().done(function() {
								setTimeout(function() {
									$("#submit-comment").text("Submitted!");
									context.clearFields();
									setTimeout(function() {
										$("#submit-comment").text("Submit");
									}, 1000);
								}, 1000);
								console.log("Comment submitted");
								console.log(arguments);
								collection.add(newComment);
							}).fail(function() {
								console.log('Save failed!');
								console.log(arguments);
							});

							this.submitKinoRating(collection.imdbID, rating);
						},

						submitKinoRating : function(imdbID, rating) {
							$.ajax({
								url : "/rating",
								type : "POST",
								data : {
									imdbID : imdbID,
									totalRating : rating,
									avgRating : "0",
									timesRated : "0"
								}
							});
						},

						hideRequiredText : function() {
							if ($("#comment-heading").val() == ''
									&& $("#comment-field").val() == '') {
								$(".text-error").addClass("hidden");
							}
						},

						showRequiredText : function() {
							if ($("#comment-heading").val() == ''
									|| $("#comment-field").val() == '') {
								$(".text-error").removeClass("hidden");
							}
						},

						toggleCommentButtons : function() {
							if ($("#comment-heading").val() == ''
									|| $("#comment-field").val() == '') {
								this.showRequiredText();
								$('.enableOnInput').prop('disabled', true);
							} else {
								$(".text-error").addClass("hidden");
								$('.enableOnInput').prop('disabled', false);
							}

							if ($("#comment-heading").val() != ''
									|| $("#comment-field").val() != '') {
								$('.enableClearOnInput')
										.prop('disabled', false);
							} else {
								$('.enableClearOnInput').prop('disabled', true);
							}
						},

						clearFields : function() {
							$("#comment-heading, #comment-field").val("");
							$('.enableOnInput').prop('disabled', true);
							$('.enableClearOnInput').prop('disabled', true);
							$("#rating").raty('score', 1);
							this.hideRequiredText();
						},

						toggleComments : function(e) {
							e.preventDefault();
							this
									.$("#comment-toggle-visibility i")
									.removeClass(
											"icon-showcomments-animateUp icon-showcomments-animateDown");
							if ($("#comments-list").hasClass("hidden")) {
								console.log("inside if toggle");
								$("#comments-list").removeClass("hidden");
								$('html, body')
										.animate(
												{
													scrollTop : $(
															"#comment-toggle-visibility")
															.offset().top
												}, 'slow');
								$("#comment-toggle-visibility i").addClass(
										"icon-showcomments-animateUp").attr(
										"Title", "hide comments");
							} else {
								console.log("inside else toggle");
								$("#comment-toggle-visibility i").addClass(
										"icon-showcomments-animateDown").attr(
										"Title", "show comments");
								$('html, body').animate({
									scrollTop : 0
								}, 'slow', function() {
									$("#comments-list").addClass("hidden");
								});
								$("#comment-toggle-visibility").addClass(
										"icon-showcomments-animateDown").attr(
										"Title", "show comments");
							}
						},

						render : function(displayCommentForm) {
							// console.log("comments
							// form rendered");
							if (displayCommentForm)
								this.renderCommentForm();
							this.renderComments();
						},

						renderCommentForm : function() {
							this.$el.append(this.comments_form_template({}));
							$("#rating").raty(
									{
										number : 10,
										path : '/lib/jquery.raty/images/',
										score : 1,
										hints : [ '1', '2', '3', '4', '5', '6',
												'7', '8', '9', '10' ]
									});
						},

						renderComments : function() {
							this.collection.forEach(this.addCommentView, this);
						},

						addCommentView : function(currentComment) {
							// console.log("comment view
							// added");
							var currentCommentView = new CommentView({
								model : currentComment
							});
							this.$('#comments-list').append(
									currentCommentView.render());
						}
					});

			return CommentListView;

		});