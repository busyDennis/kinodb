const DEFAULT_IMG_FNAME = "../assets/default_video.png";

define([ "backbone" ], function(Backbone) {
	var Movie = Backbone.Model.extend({
		defaults : {
			imdbID : "",
			Title : "", // Title is a sorting key
			Poster : DEFAULT_IMG_FNAME,
			Plot : "",
			Actors : "",
			imdbRating : "", // imdbRating is a sorting key
			kinoRating : 0,
			Year : "" // Year is a sorting key
		},
		idAttribute : "imdbID",
		url : function() {
			return "/movie?imdbID=" + this.get("imdbID");
		},
		updateKinoRating : function() {
			var context = this;
			$.ajax({
				url : "rating?id=" + context.get("imdbID"),
				type : "GET",
				dataType : "json",
				success : function(data, textStatus, jqXHR) {
					if (data.avgRating != 0) {
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
});