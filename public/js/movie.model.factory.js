const DEFAULT_IMG_FNAME = "../assets/default_video.png";

define([ "backbone" ], function(Backbone) {
	return {
		getNewMovie : function() {
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

			return Movie;
		}
	}
});