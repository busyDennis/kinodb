define([ "backbone" ], function(Backbone) {
	return {
		getNewComment : function() {
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
					return '/comment';
				}
			});

			return Comment;
		}

	}
});