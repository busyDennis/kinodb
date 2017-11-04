define([ 'jquery', 'backbone', 'movieView' ], function($, Backbone, MovieView) {

	var MovieListView = Backbone.View.extend({
		el : $('#movies'),
		initialize : function() {
			this.collection.on('reset sort', this.render, this);
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

	return MovieListView;

});