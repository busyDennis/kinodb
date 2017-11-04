define([ 'jquery', 'backbone' ], function($, Backbone) {

	var MovieSearchView = Backbone.View.extend({
		initialize : function() {
			$(".icon-loader").hide();
		},
		el : $("#form-search"),
		events : {
			"submit" : "trigger_search_route"
		},

		trigger_search_route : function(e) {
			e.preventDefault();
			Backbone.history.navigate("/search/" + $('#search_string').val(), {
				trigger : true,
				replace : false
			});
		},

		search_imdb : function(query) {
			// console.log(query);
			// if
			// (!$("#form-search").val())
			// return;

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

	return MovieSearchView;

});