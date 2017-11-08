define([ "backbone" ], function(Backbone) {

	var Comment = Backbone.Model.extend({

		defaults : {
			imdbID : "",
			commentHeading : "",
			commentText : "",
			rating : "", // user-defined rating value will be added
			// to the
			// database
			created : "",
			ip : ""
		},

		idAttribute : 'commentID',

		url : function() {
			return '/comment';
		}
	});

	return Comment;
});