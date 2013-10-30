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
	var Todo = Backbone.Model.extend({
		urlRoot: 'api/todos', //remember this is URL ROOT not URL
		defaults: {
			'description' : 'empty to do..',
			'status' : 'incomplete'
		},
		initialize: function(){
			this.on('change', this.change);
		},
		change: function(){
			// console.log('CHANGE: model ' + this.attributes.id + ' description:')
			// console.log('CHANGE: ' + this.attributes.status);
		},
		toggleStatus: function(){
			var $status = this.get('status');
			($status == 'incomplete') ? this.set({'status':'complete'}) : this.set({'status':'incomplete'});
			this.save();
		}
	});

	//collection
	var Todos = Backbone.Collection.extend({
		url: 'api/todos',
		model: Todo
	});

	//views
	var TodoView = Backbone.View.extend({
		//tagName: 'li',
		template: _.template($('#todo-tpl').html()),
		initialize: function(){
			//this.listenTo(this.model, 'change', this.reRender);
			//NOTES:
			//single views can listen to models while collection views can listen to collections...
		},
		
		reRender: function(){
			this.remove();
			console.log('zombie alert');
		},
		render: function(){
			//var template = _.template($('#todo-tpl').html())(this.model.attributes);
			this.setElement(this.template(this.model.attributes));
		},
		toggleStatus: function(){
			this.model.toggleStatus();
			//this.$el.toggleClass('complete');//why rerender just to get a class on there?
		},
		events: {
			'click input[type=checkbox]' : 'toggleStatus'
		}
	});

	var TodosView = Backbone.View.extend({
		el: '.page ul',
		initialize: function(){
			//this.collection.on('change', this.render, this);
			this.listenTo(this.collection, 'change', this.render);
		},
		remove: function(){
		},
		render: function(){
			this.removeItemViews();//NEW

			console.log('render collection view');
			this.collection.forEach(this.addOne, this);
		},
		addOne: function(todo){
			var todoView = new TodoView({model: todo});
			
			todoView.listenTo(this, 'clean_up', todoView.reRender);
			//todoView.listenTo(this, 'clean_up', todoView.remove);
			//NEW...
			//WHY DOES THIS WORK: 
			//REMOVE is a method of views apparently...see docs
			//When I call remove and make a function it and inside of it put remove() it triggers an infinite loop so BOOM
			//when I set it to reRender and put remove() inside that it's the same as just .remove
			
			todoView.render();
			this.$el.append(todoView.el);
		},
		removeItemViews: function(){
			this.trigger('clean_up');//NEW
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
