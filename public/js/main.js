requirejs.config({
	baseUrl : '../lib',
	paths : {

		// 3rd party dependencies
		backbone : 'backbone-min',
		bootstrap : 'bootstrap/js/bootstrap',
		jquery : 'jquery-2.2.4', // 'jquery-3.2.1',
		raty : 'jquery.raty/jquery.raty',
		moment : 'moment.min',
		prefixfree : 'prefixfree.min',
		underscore : 'underscore-min',

		// app modules

		// models
		movie : '../js/model/movie',
		comment : '../js/model/comment',

		// collections
		movieList : '../js/collection/movie.list',
		commentList : '../js/collection/comment.list',

		// views
		movieView : '../js/view/movie.view',
		movieListView : '../js/view/movie.list.view',
		commentView : '../js/view/comment.view',
		commentListView : '../js/view/comment.list.view',
		movieSearchView : '../js/view/movie.search.view',

		// router
		movieRouter : '../js/router/movie.router'
	},
	shim : {

		// 3rd party dependencies
		'backbone' : {
			deps : [ 'underscore', 'jquery' ],
			exports : 'Backbone'
		},
		'bootstrap' : {
			deps : [ 'jquery' ],
			exports : '$.fn.popover'
		},
		'jquery' : {
			exports : '$'
		},
		'raty' : [ 'jquery' ],
		'moment' : [ 'jquery' ],
		'prefixfree' : [ 'jquery' ],
		'underscore' : {
			exports : '_'
		},

		// app modules

		// models
		'movie' : {
			deps : [ 'backbone' ],
			exports : 'Movie'
		},
		'comment' : {
			deps : [ 'backbone' ],
			exports : 'Comment'
		},

		// collections
		'movieList' : {
			deps : [ 'jquery', 'backbone', 'movie' ],
			exports : 'MovieList'
		},
		'commentList' : {
			deps : [ 'jquery', 'backbone', 'comment' ],
			exports : 'CommentList'
		},

		// views
		'movieView' : {
			deps : [ 'jquery', 'underscore', 'backbone', 'raty', 'movie',
					'commentList', 'commentListView' ],
			exports : 'MovieView'
		},
		'movieListView' : {
			deps : [ 'jquery', 'backbone', 'movieView' ],
			exports : 'MovieListView'
		},
		'movieSearchView' : {
			deps : [ 'jquery', 'backbone' ],
			exports : 'MovieSearchView'
		},
		'commentView' : {
			deps : [ 'jquery', 'underscore', 'backbone' ],
			exports : 'CommentView'
		},
		'commentListView' : {
			deps : [ 'jquery', 'underscore', 'backbone', 'moment', 'comment',
					'commentView' ],
			exports : 'CommentListView'
		},

		// router
		'movieRouter' : {
			deps : [ 'jquery', 'backbone', 'movie', 'movieList', 'movieView',
					'movieListView', 'movieSearchView' ],
			exports : 'MovieRouter'
		}
	}
});

// app logic
requirejs([ 'movieRouter' ], function(MovieRouter) {
	$(document).ready(function() {
		(new MovieRouter()).start();
	});
});
