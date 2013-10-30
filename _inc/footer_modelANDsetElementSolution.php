	<footer class="site-footer container">
		a footer
	</footer>

<script type="text/template" id="todo-tpl"
><li class="<%= status %>"> 
	<input data-id="<%= id %>" type="checkbox" <% if (status === "complete") print("checked") %> />
	<%= description %>
	<a href="/edit/<%= id %>">go to <%= id %></a>
</li>
</script>




	<!-- jquery libs -->
	<script src="../assets/js/jquery-1.9.1.js"></script>
	<script src="../assets/js/jquery-migrate--1.2.1.js"></script>

	<!-- BACKBONE -->
	<script src="../assets/js/underscore.js"></script>
	<script src="../assets/js/backbone.js"></script>

	
<script>
	//NOTES:
	//you need a todo model to set defaults or add individual todos and save 'em
	//You can also have initialize functions and listen to different events on the model
	var Todo = Backbone.Model.extend({
		url: 'api/todos',
		defaults: {
			'description' : 'empty to do..',
			'status' : 'incomplete'
		},
		initialize: function(){
			// console.log('model ' + this.attributes.id + ' description:')
			// console.log(this.attributes.description);
			this.on('change', this.change);
		},
		change: function(){
			console.log('CHANGE: model ' + this.attributes.id + ' description:')
			console.log('CHANGE: ' + this.attributes.description);
		}
	});

	// var todo = new Todo();
	// todo.set({'description':'setting a description 7'}); //triggers change
	// todo.save(); //triggers change

	//collection
	//...is a collection of models (the model is implied and only need to define it if I want defaults or something?????)
	var Todos = Backbone.Collection.extend({
		url: 'api/todos',
		model: Todo
	});

	//views
	var TodoView = Backbone.View.extend({
		//tagName: 'li',
		initialize: function(){
			
		},
		render: function(){
			var template = _.template($('#todo-tpl').html())(this.model.attributes);
			//this.$el.append(template);
			//this.el = template; //this causes events to stop working
			this.setElement(template); //this rebinds my click events!
			//NOTES
			//Notice the template has the script carat on the same line as li
			//this is to get rid of the jquery migrate error that occurs...
			//find a better way
			//see: http://ianstormtaylor.com/rendering-views-in-backbonejs-isnt-always-simple/
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

			var todoView = new TodoView({model: todo});
			todoView.render();
			this.$el.append(todoView.el);
			//NOTES:
			//setting the el to ul and then appending to that
			//then using .html(); breaks the events for some reason.
			//always use append? no...you can use html() but then use set element
			//see: http://ianstormtaylor.com/rendering-views-in-backbonejs-isnt-always-simple/

		}
	});

	//instantiation
	var todos = new Todos();
	todos.fetch({
		success: function(){
			var todosView = new TodosView({collection:todos});
			todosView.render();
		}
	});

</script>

</body>
</html>
