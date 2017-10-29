const DEFAULT_IMG_FNAME = "img/default_video.png";

$(document)
.ready(
		function() {
			var Movie = Backbone.Model.extend({
				defaults : {
					imdbID : "",
					Title : "",
					Poster : DEFAULT_IMG_FNAME,
					Plot : "",
					Actors : "",
					rating : "",
					kinoRating : 0,
					Year : ""
				},
				idAttribute : "imdbID",
				url : function() {
					return '/movie?id=' + this.get('imdbID');
				},
				updateKinoRating : function() {
					var context = this;
					$.ajax({
						url : "rating?id=" + context.get("imdbID"),
						type : "GET",
						dataType : "json",
						success : function(data, textStatus, jqXHR) {
							if (data.avg_rating != 0) {
								context.set({
									kinoRating : data.avgRating
								});
							}
						},
						error : function() {
							console.log("Error: AJAX call");
						}
					});
				}
			});

			var Comment = Backbone.Model.extend({
				defaults : {
					imdbID : "",
					commentHeading : "",
					commentText : "",
					rating : "",
					created : "",
					ip : ""
				},
				idAttribute : 'commentID',
				url : function() {
					return '/comment/?id=' + this.get('imdbID');
				}
			});

			var MovieList = Backbone.Collection
			.extend({
				model : Movie,
				url : '/movie',
				initialize : function() {
					this.comparator = this.descendingComparator;
					this.sortKey = "rating";
					$("#byRating a").wrapInner("<b />");

					$("#byRating").on('click', {
						context : this
					}, this.sortByRating);
					$("#byTitle").on('click', {
						context : this
					}, this.sortByTitle);
					$("#byYear").on('click', {
						context : this
					}, this.sortByYear);
					$("#ascending").on('click', {
						context : this
					}, this.setAscOrder);
					$("#descending").on('click', {
						context : this
					}, this.setDescOrder);
				},

				ascendingComparator : function(a, b) {
					a = a.get(this.sortKey);
					b = b.get(this.sortKey);
					if (a > b)
						return 1;
					else if (a < b)
						return -1;
					else
						return 0;
				},

				descendingComparator : function(a, b) {
					return this.ascendingComparator(b, a);
				},

				setAscendingComparator : function(ascending) {
					if (ascending)
						this.comparator = this.ascendingComparator;
					else
						this.comparator = this.descendingComparator;
				},

				setKeyAndSort : function(key) {
					this.emphasizeSortingPreference(key);
					this.sortKey = key;
					this.sort();
				},

				sortByRating : function(event) {
					event.preventDefault();
					event.data.context.setKeyAndSort("rating");
				},

				sortByTitle : function(event) {
					event.preventDefault();
					event.data.context.setKeyAndSort("Title");
				},

				sortByYear : function(event) {
					event.preventDefault();
					event.data.context.setKeyAndSort("Year");
				},

				setAscOrder : function(event) {
					event.preventDefault();
					$('#order').html("Ascending");
					event.data.context
					.setAscendingComparator(true);
					event.data.context.sort();
				},

				setDescOrder : function(event) {
					event.preventDefault();
					$('#order').html("Descending");
					event.data.context
					.setAscendingComparator(false);
					event.data.context.sort();
				},

				/**
				 * The new sorting preference in the dropdown
				 * menu is emphasized in bold, and the old ones
				 * are deemphasized
				 */
				emphasizeSortingPreference : function(
						newSortKey) {
					var sortHashTable = {
							rating : "#byRating",
							Title : "#byTitle",
							Year : "#byYear"
					};

					if (newSortKey != this.sortKey) {
						$(sortHashTable[newSortKey] + " a")
						.wrapInner("<b />");
						$(sortHashTable[this.sortKey])
						.find("b")
						.replaceWith(
								$(
										sortHashTable[this.sortKey]
										+ " b")
										.contents());
					}
				}
			});

			var CommentList = Backbone.Collection.extend({
				model : Comment,
				url : function() {
					return '/comment?id=' + this.imdbID;
				},
				imdbID : "",
				initialize : function(models, options) {
					this.imdbID = options.imdbID;
				}
			});

			var MovieView = Backbone.View
			.extend({
				el : $('#movies'),

				initialize : function() {
					console
					.log("Movie view initialized with imdbID: "
							+ this.model.attributes['imdbID']);
					this.listenTo(this.model,
							'change:kino_rating',
							this.renderKinoRating);
					this.template = _.template($(
					'#template-movie').html());
					this.commentList = new CommentList(
							[],
							{
								imdbID : this.model.attributes['imdbID']
							});
					this.commentListView = new CommentListView(
							{
								collection : this.commentList
							});
				},

				render : function(flag) {
					// this.$el.hide();
					this.$el.append(this
							.template(this.model.attributes));

					console.log("Movie attributes:"); // temporary
					console.log(this.model.attributes); // temporary

					this.commentListView.setElement("#"
							+ this.model.attributes['imdbID']
							+ " #comments");
					this.commentListView.render(flag);

					this.commentList.fetch().done(null,
							function(response) {
						console.log(response);
					}).fail(null, function(response) {
						console.log(response);
					});

					$(
							"#"
							+ this.model.attributes['imdbID']
							+ " .movie-rating")
							.raty(
									{
										number : 10,
										readOnly : true,
										score : this.model.attributes['rating'],
										hints : [ '1', '2',
											'3', '4', '5',
											'6', '7', '8',
											'9', '10' ]
									});

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
								"#"
								+ this.model.attributes['imdbID']
								+ " .kino-div")
								.hasClass('hidden'))
							$(
									"#"
									+ this.model.attributes['imdbID']
									+ " .kino-div")
									.removeClass('hidden');
						$(
								"#"
								+ this.model.attributes['imdbID']
								+ " .kino-rating")
								.raty(
										{
											score : this.model.attributes["kino_rating"],
											number : 10,
											readOnly : true,
											hints : [ '1', '2',
												'3', '4',
												'5', '6',
												'7', '8',
												'9', '10' ]
										});
					}
				}
			});

			var CommentView = Backbone.View
			.extend({
				initialize : function() {
					this.single_comment_template = _
					.template($(
							'#template-single-comment')
							.html());
				},
				render : function() {
					this.$el
					.append(this
							.single_comment_template(this.model.attributes));
					return this.el;
				}
			});

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
					// console.log("the submit button was
					// clicked");
					$(".enableOnInput").prop('disabled', true);
					$('.enableClearOnInput').prop('disabled',
							true);
					$("#submit-comment").text("Submitting...");
					var collection = this.collection;
					var context = this;
					var rating = $("#rating").raty('score');

					var newComment = new Comment({
						imdbID : collection.imdbID,
						heading : $("#comment-heading").val(),
						comment : $("#comment-field").val(),
						rating : $("#rating").raty('score'),
						created : moment().format(
						"YYYY-MM-DD HH:mm:ss")
					});
					newComment
					.save()
					.done(
							function() {
								setTimeout(
										function() {
											$(
													"#submit-comment")
													.text(
													"Submitted!");
											context
											.clearFields();
											setTimeout(
													function() {
														$(
																"#submit-comment")
																.text(
																"Submit");
													},
													1000);
										}, 1000);
								console
								.log("Comment submitted");
								console.log(arguments);
								collection
								.add(newComment);
							}).fail(function() {
								console.log('Save failed!');
								console.log(arguments);
							});

					this.submitKinoRating(collection.imdbID,
							rating);
				},

				submitKinoRating : function(imdbID, rating) {
					$.ajax({
						url : "http://kinodb/rating",
						type : "POST",
						data : {
							imdbID : imdbID,
							total_rating : rating,
							avg_rating : "0",
							times_rated : "0",
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
						$('.enableOnInput').prop('disabled',
								true);
					} else {
						$(".text-error").addClass("hidden");
						$('.enableOnInput').prop('disabled',
								false);
					}

					if ($("#comment-heading").val() != ''
						|| $("#comment-field").val() != '') {
						$('.enableClearOnInput').prop(
								'disabled', false);
					} else {
						$('.enableClearOnInput').prop(
								'disabled', true);
					}
				},

				clearFields : function() {
					$("#comment-heading, #comment-field").val(
					"");
					$('.enableOnInput').prop('disabled', true);
					$('.enableClearOnInput').prop('disabled',
							true);
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
						$("#comments-list").removeClass(
						"hidden");
						$('html, body')
						.animate(
								{
									scrollTop : $(
											"#comment-toggle-visibility")
											.offset().top
								}, 'slow');
						$("#comment-toggle-visibility i")
						.addClass(
						"icon-showcomments-animateUp")
						.attr("Title", "hide comments");
					} else {
						console.log("inside else toggle");
						$("#comment-toggle-visibility i")
						.addClass(
						"icon-showcomments-animateDown")
						.attr("Title", "show comments");
						$('html, body')
						.animate(
								{
									scrollTop : 0
								},
								'slow',
								function() {
									$("#comments-list")
									.addClass(
									"hidden");
								});
						$("#comment-toggle-visibility")
						.addClass(
						"icon-showcomments-animateDown")
						.attr("Title", "show comments");
					}
				},

				render : function(flag) {
					// console.log("comments form rendered");
					if (flag)
						this.renderCommentForm();
					this.renderComments();
				},

				renderCommentForm : function() {
					this.$el.append(this
							.comments_form_template({}));
					$("#rating").raty(
							{
								number : 10,
								score : 1,
								hints : [ '1', '2', '3', '4',
									'5', '6', '7', '8',
									'9', '10' ]
							});
				},

				renderComments : function() {
					this.collection.forEach(
							this.addCommentView, this);
				},

				addCommentView : function(currentComment) {
					// console.log("comment view added");
					var currentCommentView = new CommentView({
						model : currentComment
					});
					this.$('#comments-list').append(
							currentCommentView.render());
				}
			});

			var MovieListView = Backbone.View
			.extend({
				el : $('#movies'),
				initialize : function() {
					this.collection.on('reset sort',
							this.render, this);
				},
				render : function() {
					this.$el.empty();
					// $(this.el).hide();
					this.collection.forEach(this.addView, this);
					$(this.el).fadeIn("slow");
					return this;
				},
				addView : function(currentMovie) {
					var currentView = new MovieView({
						model : currentMovie
					});
					currentView.render(false);
				}
			});

			var SearchView = Backbone.View.extend({
				initialize : function() {
					$(".icon-loader").hide();
				},
				el : $("#form-search"),
				events : {
					"submit" : "trigger_search_route"
				},

				trigger_search_route : function(e) {
					e.preventDefault();
					Backbone.history.navigate("/search/"
							+ $('#search_string').val(), {
								trigger : true,
								replace : false
							});
				},

				search_imdb : function(query) {
					// console.log(query);
					// if (!$("#form-search").val()) return;

					// $('html, body').animate({
					// scrollTop : 0
					// }, 'slow');

					$('#movies').empty();
					var icon = $(".icon-loader");
					icon.addClass("icon-refresh-animate");
					icon.show();
					var search_params = {
							"s" : query,
							"limit" : "10"
					};

					this.collection.fetch({
						data : $.param(search_params),
						success : function(response) {
							icon.removeClass("icon-refresh-animate");
							icon.hide();
						},
						error : function(response) {
							console.log(response);
							icon.removeClass("icon-refresh-animate");
							icon.hide();
						},
						reset : false
					});
				}
			});

			var MovieRouter = Backbone.Router
			.extend({
				routes : {
					"" : "index",
					"search/:query" : "trigger_search",
					"movie/:id" : "load_single_movie"
				},

				initialize : function() {
					this.movieList = new MovieList();
					this.searchView = new SearchView({
						collection : this.movieList
					});
					this.movieListView = new MovieListView({
						collection : this.movieList
					});
				},

				index : function() {
					this.navigate("search/Star Trek", {
						trigger : true,
						replace : true
					});
					// this.trigger_search("Star Trek");
				},

				trigger_search : function(query) {
					console.log("search route triggered");
					$(".dropdown").removeClass("hidden");
					// if(this.query != query) {
					// this.query = query;
					this.searchView.search_imdb(query);
					// }
				},

				load_single_movie : function(imdbID) {
					$(".dropdown").addClass("hidden");
					$('html, body').animate({
						scrollTop : 0
					}, 'slow');
					$(".icon-loader").addClass(
					"icon-refresh-animate");
					$(".icon-loader").show();
					$('#movies').empty();
					var icon = $(".icon-loader");
					if (this.movieList.get(imdbID) !== undefined
							&& this.movieList.get(imdbID) !== null) {
						console.log("inside if");
						var movieView = new MovieView({
							model : this.movieList.get(imdbID)
						});
						movieView.render(true);
						icon
						.removeClass("icon-refresh-animate");
						icon.hide();
					} else {
						console.log("inside else");

						var movieModel = new Movie({}, {
							url : "/movie?id=" + imdbID
						});
						movieModel
						.fetch({
							success : function(
									collection,
									response, options) {
								var movieView = new MovieView(
										{
											model : movieModel
										});
								movieView.render(true);
								icon
								.removeClass("icon-refresh-animate");
								icon.hide();
							},
							reset : true
						});
					}
				},

				start : function() {
					Backbone.history.start({
						pushState : false
					});
				}
			});

			(new MovieRouter()).start();
		});

var json1 = {
		"runtime" : [ "127 min" ],
		"rating" : 8.0,
		"genres" : [ "Action", "Adventure", "Sci-Fi" ],
		"rated" : "PG_13",
		"language" : [ "English" ],
		"Title" : "Star Trek",
		"filming_locations" : "Bakersfield, California, USA",
		"Poster" : "http://ia.media-imdb.com/images/M/MV5BMjE5NDQ5OTE4Ml5BMl5BanBnXkFtZTcwOTE3NDIzMw@@._V1._SY317_.jpg",
		"imdb_url" : "http://www.imdb.com/Title/tt0796366/",
		"writers" : [ "Roberto Orci", "Alex Kurtzman", "and 1 more credit" ],
		"imdbID" : "tt0796366",
		"directors" : [ "J.J. Abrams" ],
		"rating_count" : 265861,
		"Actors" : [ "Chris Pine", "Zachary Quinto", "Leonard Nimoy", "Eric Bana",
			"Bruce Greenwood", "Karl Urban", "Zoe Saldana", "Simon Pegg",
			"John Cho", "Anton Yelchin", "Ben Cross", "Winona Ryder",
			"Chris Hemsworth", "Jennifer Morrison", "Rachel Nichols" ],
			"Plot" : "The brash James T. Kirk tries to live up to his father's legacy with Mr. Spock keeping him in check as a vengeful, time-traveling Romulan creates black holes to destroy the Federation one planet at a time.",
			"Year" : 2009,
			"country" : [ "USA", "Germany" ],
			"type" : "M",
			"release_date" : 20090515,
			"also_known_as" : [ "Star Trek: The Future Begins" ]
};