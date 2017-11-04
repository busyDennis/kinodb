define([ "jquery", "backbone", "movie" ], function($, Backbone, Movie) {

	var MovieList = Backbone.Collection.extend({
		
		model : Movie,
		url : '/movie',

		initialize : function() {
			this.comparator = this.descendingComparator;
			this.sortKey = "rating";
			$("#byRating a").wrapInner("<b />");

			$("#byRating").on('click', {
				context : this
			}, this.sortByRating);
			$("#byTitle").on('click', {
				context : this
			}, this.sortByTitle);
			$("#byYear").on('click', {
				context : this
			}, this.sortByYear);
			$("#ascending").on('click', {
				context : this
			}, this.setAscOrder);
			$("#descending").on('click', {
				context : this
			}, this.setDescOrder);
		},

		ascendingComparator : function(a, b) {
			a = a.get(this.sortKey);
			b = b.get(this.sortKey);
			if (a > b)
				return 1;
			else if (a < b)
				return -1;
			else
				return 0;
		},

		descendingComparator : function(a, b) {
			return this.ascendingComparator(b, a);
		},

		setAscendingComparator : function(ascending) {
			if (ascending)
				this.comparator = this.ascendingComparator;
			else
				this.comparator = this.descendingComparator;
		},

		setKeyAndSort : function(key) {
			this.emphasizeSortingPreference(key);
			this.sortKey = key;
			this.sort();
		},

		sortByRating : function(event) {
			event.preventDefault();
			event.data.context.setKeyAndSort("rating");
		},

		sortByTitle : function(event) {
			event.preventDefault();
			event.data.context.setKeyAndSort("Title");
		},

		sortByYear : function(event) {
			event.preventDefault();
			event.data.context.setKeyAndSort("Year");
		},

		setAscOrder : function(event) {
			event.preventDefault();
			$('#order').html("Ascending");
			event.data.context.setAscendingComparator(true);
			event.data.context.sort();
		},

		setDescOrder : function(event) {
			event.preventDefault();
			$('#order').html("Descending");
			event.data.context.setAscendingComparator(false);
			event.data.context.sort();
		},

		/**
		 * The new sorting preference in the dropdown menu is emphasized
		 * in bold, and the old ones are deemphasized
		 */
		emphasizeSortingPreference : function(newSortKey) {
			var sortHashTable = {
				rating : "#byRating",
				Title : "#byTitle",
				Year : "#byYear"
			};

			if (newSortKey != this.sortKey) {
				$(sortHashTable[newSortKey] + " a").wrapInner("<b />");
				$(sortHashTable[this.sortKey]).find("b").replaceWith(
						$(sortHashTable[this.sortKey] + " b").contents());
			}
		}
	});

	return MovieList;

});