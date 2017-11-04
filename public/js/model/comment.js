define([ "backbone" ], function(Backbone) {

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

});