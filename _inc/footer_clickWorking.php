	<footer class="site-footer container">
		a footer
	</footer>

	<!-- jquery libs -->
	<script src="../assets/js/jquery-1.9.1.js"></script>
	<script src="../assets/js/jquery-migrate--1.2.1.js"></script>

	<!-- BACKBONE -->
	<script src="../assets/js/underscore.js"></script>
	<script src="../assets/js/backbone.js"></script>

	
	<script>

		//models
		// var Todo = Backbone.Model.extend({
		// 	urlRoot: 'api/todos'
		// });

		//views
		var TodoView = Backbone.View.extend({
			tagName: 'li',
			render: function(){
				var template = _.template(" <%= description %> ")(this.model.attributes);
				this.$el.append(template);
			},
			click: function(){
				alert('click');
			},
			events: {
				'click' : 'click'
			}
		});

		var TodosView = Backbone.View.extend({
			el: '.page ul',
			initialize: function(){
			},
			render: function(){
				this.$el.empty(); //gotta empty the el at some point?
				this.collection.forEach(this.addOne, this);
			},
			addOne: function(todo){
				
				console.log(todo.attributes);
				// var todo = new Todo();
				// todo.fetch({
				// 	success: function(){
						var todoView = new TodoView({model: todo});
						todoView.render();
						this.$el.append(todoView.el);
						//NOTES:
						//setting the el to ul and then appending to that
						//then using .html(); breaks the events for some reason.
						//always use append?
						
						
				// 	}
				// });
			
			}
		});

		//collection
		var Todos = Backbone.Collection.extend({
			url: 'api/todos'
			//model: Todo
		});

		var todos = new Todos();
		todos.fetch({
			success: function(){
				var todosView = new TodosView({collection:todos});
				todosView.render();
			}
		});
		

		// var todo = new Todo();
		// todo.fetch({
		// 	success: function(){
				// var todoView = new TodoView({model: todo});
				// $('.page').html(this.el);
				// todoView.render();
		// 	}
		// });	
		
	
	</script>

</body>
</html>
