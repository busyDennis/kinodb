define([ "jquery", "backbone", "comment" ], function($, Backbone, Comment) {

	var CommentList = Backbone.Collection.extend({
		model : Comment,
		url : function() {
			return '/comment?id=' + this.imdbID;
		},
		imdbID : "", // imdbID of a movie that a specific comment list instance belongs to
		initialize : function(models, options) {
			this.imdbID = options.imdbID;
		}
	});

	return CommentList;

});