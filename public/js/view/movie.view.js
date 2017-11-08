define(
		[ 'jquery', 'underscore', 'backbone', 'raty', 'movie', 'commentList',
				'commentListView' ],
		function($, _, Backbone, raty, Movie, CommentList, CommentListView) {

			var MovieView = Backbone.View
					.extend({
						el : $('#movies'),

						initialize : function() {
							console.log("Movie view initialized with imdbID: "
									+ this.model.attributes['imdbID']);
							this.listenTo(this.model, 'change:kinoRating',
									this.renderKinoRating);
							this.template = _.template($('#template-movie')
									.html());
							this.commentList = new CommentList([], {
								imdbID : this.model.attributes['imdbID']
							});
							this.commentListView = new CommentListView({
								collection : this.commentList
							});
						},

						render : function(displayCommentForm) {
							// this.$el.hide();
							this.$el.append(this
									.template(this.model.attributes));

							console.log("Movie attributes:"); // temporary
							console.log(this.model.attributes); // temporary

							this.commentListView.setElement("#"
									+ this.model.attributes['imdbID']
									+ " #comments");
							this.commentListView.render(displayCommentForm);

							this.commentList.fetch().done(null,
									function(response) {
										console.log(response);
									}).fail(null, function(response) {
								console.log(response);
							});

							if (this.model.has('imdbRating') && this.model.attributes['imdbRating'].length != 0) {
								$(
										"#" + this.model.attributes['imdbID']
												+ " .imdb-rating-div")
										.removeClass('hidden');

								$(
										"#" + this.model.attributes['imdbID']
												+ " .movie-rating")
										.raty(
												{
													hints : [ '1', '2', '3',
															'4', '5', '6', '7',
															'8', '9', '10' ],
													number : 10,
													path : '/lib/jquery.raty/images/',
													readOnly : true,
													score : this.model.attributes['imdbRating']
												});
							} else {
								$(
										"#" + this.model.attributes['imdbID']
												+ " .imdb-rating-div")
										.addClass('hidden');
							}

							this.model.attributes["kinoRating"] = 0;
							this.model.updateKinoRating();
							// this.renderKinoRating();
							// this.$el.show();
							return this;
						},

						renderKinoRating : function() {
							// this.$(".kino-div").removeClass('hidden');
							// context.model.updateKinoRating();
							if (this.model.attributes["kinoRating"] != 0) {
								if ($(
										"#" + this.model.attributes['imdbID']
												+ " .kino-div").hasClass(
										'hidden'))
									$(
											"#"
													+ this.model.attributes['imdbID']
													+ " .kino-div")
											.removeClass('hidden');
								$(
										"#" + this.model.attributes['imdbID']
												+ " .kino-rating")
										.raty(
												{
													score : this.model.attributes["kinoRating"],
													number : 10,
													path : '/lib/jquery.raty/images/',
													readOnly : true,
													hints : [ '1', '2', '3',
															'4', '5', '6', '7',
															'8', '9', '10' ]
												});
							}
						}
					});

			return MovieView;
		});