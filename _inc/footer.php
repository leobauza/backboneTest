	<footer class="site-footer container">
		a footer
	</footer>

	<!-- templates -->
<script type="text/template" id="single-todo-tpl">

	<li class="<%= status %>">
		<input data-id="<%= id %>" type="checkbox" <% if (status === "complete") print("checked") %> />
		<%= description %>
		<a href="/edit/<%= id %>">go to <%= id %></a>
	</li>

</script>

<script type="text/template" id="todo-tpl">

	<a href="/new" class="btn btn-main">new todo</a>

	<ul>
		<% _.each(todos, function(todo) { %>

			<%
				var
					status = todo.get('status'),
					id = todo.get('id'),
					description = todo.get('description')
				;
			%>

			<li class="<%= status %>">
				<input data-id="<%= id %>" type="checkbox" <% if (status === "complete") print("checked") %> />
				<%= description %>
				<a href="/edit/<%= id %>">go to <%= id %></a>
			</li>
		<% }) %>
	</ul>

</script>

<script type="text/template" id="todo-add-tpl">
	<h4><%= todo ? 'update' : 'create'  %> todo</h4>

	<%
		if(todo) {
			var
				status = todo.get('status'),
				id = todo.get('id'),
				description = todo.get('description')
			;
		}
	%>

	<form class="edit-todo-form">
		<label>description</label>
		<input type="text" name="description" value="<%= todo ? description : '' %>"/>
		<label>status</label>
		<input type="text" name="status" value="<%= todo ? status : 'incomplete' %>"/>
		<hr>
		<button class="btn btn-secondary" type="submit"><%= todo ? 'update' : 'create' %></button>
		<% if(todo){ %>
			<input type="hidden" name="id" value="<%= id %>"/>
			<button type="button" class="btn btn-main delete">Delete</button>
		<% }; %>
	</form>
</script>






	<!-- jquery libs -->
	<script src="../assets/js/jquery-1.9.1.js"></script>
	<script src="../assets/js/jquery-migrate--1.2.1.js"></script>

	<!-- BACKBONE -->
	<script src="../assets/js/underscore.js"></script>
	<script src="../assets/js/backbone.js"></script>

	<!-- code school script version -->
	<!-- <script src="../assets/js/test.js"></script> -->
	
	<script>
		//form serialize to JSON
		$.fn.serializeObject = function() {
			var o = {};
			var a = this.serializeArray();
			$.each(a, function() {
				if (o[this.name] !== undefined) {
					if (!o[this.name].push) {
						o[this.name] = [o[this.name]];
					}
					if(this.value == '') {
						
					} else {
						o[this.name].push(this.value || '');
					}
				} else {
					if(this.value == '') {
						
					} else {
						o[this.name] = this.value || '';
					}
				}
			});
			return o;
		};
		
		//Ajax Prefilter might be useful for PORT
		$.ajaxPrefilter(function(options, originalOptions, jqXHR){
			options.url = 'http://bb.test/' + options.url;
		});
	
		//MODEL
		var Todo = Backbone.Model.extend({
			urlRoot: 'api/todos',
			defaults: {
				description: 'empty todo...',
				status: 'incomplete'
			},
			toggleStatus: function(){ //keep model logic in the model (called from the view)
				if(this.get('status') === 'incomplete') {
					this.set({'status': 'complete'})
				} else {
					this.set({'status': 'incomplete'});
				}
				this.save(); 
				console.log(this.get('description') + " is " + this.get('status'));
			}
		});

		//COLLECTION
		var Todos = Backbone.Collection.extend({
			url: 'api/todos',
			model: Todo
		});
	
		//VIEW
		
		//collection view
		var TodoListView = Backbone.View.extend({
			el: '.page', //selects an existing element rather than creating a new one
			initialize: function() {
				
			},
			render: function(){
				var that = this;
				that.todos = new Todos(); //use "that" to make it available to the entire view
				that.todos.fetch({
					success: function(todos){
						var template = _.template($('#todo-tpl').html(), {todos: todos.models}); //pass the models we fetched a model is a JSON response
						//$('body').append(that.$el.html('content should show here'));
						that.$el.html(template);
						//NOTE: 
						//In code school we set the tpl on the model view and loop in the collection
						//here we set it on the collection view and loop in the tpl
					}
				});
			},
			toggleStatus: function(e){
				
				var $target = $(e.currentTarget);
				//update class
				var $theClass = $target.parent().attr('class');
				($theClass == 'incomplete') ? $newClass = 'complete' : $newClass = 'incomplete';
				$target.parent().removeClass().addClass($newClass);
				//toggle status at the model level
				var $id = $target.data('id');
				var $item = this.todos.get($id);
				$item.toggleStatus();
				console.log('checkbox');
			},
			events : {
				//NOTES:
				//When it was just "change input" it was triggering the form too
				//it was happening because the el for both of these is ".page"
				//so it was listening for any input inside ".page"
				'change li input': 'toggleStatus' //listening to a change in any input aka the checkbox
			}
		});
		
		var EditTodoView = Backbone.View.extend({
			el: '.page',
			render: function(options){
				var that = this;
				if(options.id){
					that.todo = new Todo({id: options.id}); //use "that" to make the model avaialable to the entire view ie to use in delete event
					that.todo.fetch({
						success: function(todo){
							var template = _.template($('#todo-add-tpl').html(), {todo: todo});
							that.$el.html(template);
						}
					}); // GET /todos/id
				} else {
					var template = _.template($('#todo-add-tpl').html(), {todo: null});
					this.$el.html(template);
				}
			},
			saveUser: function(ev) {
				var todoDetails = $(ev.currentTarget).serializeObject();
				var todo = new Todo();
				todo.save(todoDetails, {
					success: function(user){
						router.navigate('', {trigger: true});
					}
				});
				return false;
			},
			deleteUser: function(ev) {
				//DELETE to /todos/id
				this.todo.destroy({
					success: function() {
						router.navigate('', {trigger: true});
					}
				})
				return false;
			},
			events: {
				'submit .edit-todo-form': 'saveUser',
				'click .delete': 'deleteUser'
			}
		});
	
		//ROUTER
		var Router = Backbone.Router.extend({
			routes: {
				'': 'home',
				'new': 'editTodo',
				'edit/:id': 'editTodo'
			}
		});
	
		//view instance
		var todoListView = new TodoListView();
		var editTodoView = new EditTodoView();
		//route instance

		var router = new Router();

		//same as defining it inside the router like code school shows
		router.on('route:home', function(){
			//todoListView.render();
			todoListView.render();
			
		});
		router.on('route:editTodo', function(id){
			editTodoView.render({id: id});
		});

		
		Backbone.history.start({pushState:true});
		//navigate with push state
		$(document).on("click", "a[href^='/']", function(e) {
			$href = $(this).attr('href');
			router.navigate($href, true);
			e.preventDefault();
		});
		
	
	</script>

	<!-- <script src="../assets/js/initial.js"></script> 
	<script src="../assets/js/script.js"></script> -->

</body>
</html>
