define([ 'jquery', 'backbone', 'movie', 'movieList', 'movieView',
		'movieListView', 'movieSearchView' ], function($, Backbone, Movie,
		MovieList, MovieView, MovieListView, MovieSearchView) {

	var MovieRouter = Backbone.Router.extend({
		routes : {
			"" : "index",
			"search/:query" : "trigger_search",
			"movie/:imdbID" : "load_single_movie"
		},

		initialize : function() {
			this.movieList = new MovieList();
			this.searchView = new MovieSearchView({
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
			// this.trigger_search("Star
			// Trek");
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
			$(".icon-loader").addClass("icon-refresh-animate");
			$(".icon-loader").show();
			
//			console.log("Inside \'load_single_movie\' function:");
//			console.log(this.movieList);			
			
			$('#movies').empty();
			
			var icon = $(".icon-loader");
			
			if (this.movieList.get(imdbID) !== undefined
					&& this.movieList.get(imdbID) !== null) {
				console.log("inside if");
				var movieView = new MovieView({
					model : this.movieList.get(imdbID)
				});
				movieView.render(true);
				icon.removeClass("icon-refresh-animate");
				icon.hide();
			} else {
				console.log("inside else");

				var movieModel = new Movie({}, {
					url : "/movie?imdbID=" + imdbID
				});
				movieModel.fetch({
					success : function(collection, response, options) {
						var movieView = new MovieView({
							model : movieModel
						});
						movieView.render(true);
						icon.removeClass("icon-refresh-animate");
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

	return MovieRouter;

});