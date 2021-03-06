	<footer class="site-footer container">
		a footer
	</footer>

<script type="text/template" id="todo-tpl"
><li class="<%= status %>"> 
	<input data-id="<%= id %>" type="checkbox" <% if (status === "complete") print("checked") %> />
	<strong>order: <%= order %></strong> <%= description %>
	<a class="btn btn-main" href="/todos/<%= id %>">go to <%= id %></a>
	<a class="btn btn-main" href="/form/<%= id %>">form for <%= id %></a>
</li>
</script>

<script type="text/template" id="todo-form-tpl"
><form>
	<label>description</label>
	<textarea name="description" value="<%= description %>"><%= description %></textarea>
	<label>status</label>
	<input name="status" type="text" value="<%= status %>"></input>
</form>
</script>


	<!-- jquery libs -->
	<script src="../assets/js/jquery-1.9.1.js"></script>
	<script src="../assets/js/jquery-migrate--1.2.1.js"></script>

	<!-- BACKBONE -->
	<script src="../assets/js/underscore.js"></script>
	<script src="../assets/js/backbone.js"></script>

	
<script>
/* 
 * =============================================================
 * HELPERS
 * =============================================================
 */
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

/* 
 * =============================================================
 * MODELS
 * =============================================================
 */
	var Todo = Backbone.Model.extend({
		urlRoot: '/api/todos', //remember this is URL ROOT not URL
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

/* 
 * =============================================================
 * COLLECTIONS
 * =============================================================
 */
	var Todos = Backbone.Collection.extend({
		url: '/api/todos',
		model: Todo
	});

/* 
 * =============================================================
 * VIEWS
 * =============================================================
 */
	var TodoView = Backbone.View.extend({
		//tagName: 'li',
		template: _.template($('#todo-tpl').html()),
		initialize: function(){
			this.listenTo(router, "route:single | route:home", this.remove);
		},
		render: function(){
			this.setElement(this.template(this.model.attributes));
		},
		renderOne: function(){
			console.log(this.options.stuff)
			console.log(this.model.attributes.id)
			this.setElement(this.template(this.model.attributes));
			$('.page ul').html(this.el);
		},
		toggleStatus: function(){
			this.model.toggleStatus();
			if(this.options.stuff == "my stuff") {
				console.log("yes stuff")
				this.$el.removeClass('incomplete').toggleClass('complete');
			} else {
				console.log("no stuff");
			}
		},
		events: {
			'click input[type=checkbox]' : 'toggleStatus'
		}
	});

	var TodosView = Backbone.View.extend({
		el: '.page ul',
		initialize: function(){
			this.listenTo(this.collection, 'change', this.render);
			this.listenTo(router, 'route:single | route:home', this.removeItemViews);
		},
		render: function(){
			this.removeItemViews(); 
			//this.$el.empty(); //empty non removed ones
			this.collection.forEach(this.addOne, this);

		},
		addOne: function(todo){
			var todoView = new TodoView({model: todo});
			
			todoView.listenTo(this, 'clean_up', todoView.remove); //have this todoView listen to clean_up!
			todoView.render();
			this.$el.append(todoView.el);
		},
		removeItemViews: function(){
			this.trigger('clean_up');
		}
	});

	var FormView = Backbone.View.extend({
		template: _.template($('#todo-form-tpl').html()),
		initialize: function(){
			this.listenTo(router, "route", this.remove);
		},
		render: function(){
			//fill in body if it's empty
			if(!$('.page ul > *').length){
				var todos = new Todos();
				todos.fetch({
					success: function(){
						var todosView = new TodosView({collection:todos});
						todosView.render();
					}
				});
			}
			this.setElement(this.template(this.model.attributes));
			$('.form').html(this.el);
		},
		autoSaver: function(e){
			
			//some vars
			var that = this;
			var $target = $(e.currentTarget);
			var index = $target.index();
			
			//auto save timer
			//when you focus a time is start to save every 5 seconds...
			//once saved it starts over as called by the auto save function
			var saveTimer = [];
			saveTimer[index] = setTimeout(function(){autoSave('auto')}, 5000);

			//save at the end of typing
			var typeTimer = [];
			$target.keyup(function(){
				clearTimeout(typeTimer[index]);
				if ($target.val) {
					typeTimer[index] = setTimeout(function(){autoSave('type')}, 1000);
				}
			});

			//initial target value
			var $targetVal = $target.val();
			//auto save func
			function autoSave(source){
				//value at save trigger
				var $newTargetVal = $target.val();
				
				if($targetVal == $newTargetVal) {
					//console.log('value is the same so don\'t do anything');
				} else {
					console.log('value has changed so do something!');
					//set target val to new target val to re-evaluate
					$targetVal = $newTargetVal;

					//only log the details if the value has changed!
					var todoDetails = $(e.currentTarget).closest('form').serializeObject();
					that.model.save(todoDetails, {
						success: function(){
							console.log(todoDetails);
						}
					});	

				}
				
				//console.log(source);
				if(source == 'auto') {
					saveTimer[index] = setTimeout(function(){autoSave(source)}, 5000);
				}
				//console.log("this log brought to you by index: " + index);
				
			}

			$target.blur(function(){
				
				//CHECK if there are differences and save on blur...??
				//probably not because if there is a difference it woulda been saved when typing...
				clearTimeout(saveTimer[index]);
				$target.unbind();
			});

			
		},
		events: {
			'focus input[type="text"], textarea':'autoSaver'
		}
	});
/* 
 * =============================================================
 * ROUTERS
 * =============================================================
 */
	var TodoRouter = Backbone.Router.extend({
		routes: {
			"" : "home",
			"todos/:id" : "single",
			"form/:id" : "form"
		},
	});

	var router = new TodoRouter();

	//home
	router.on('route:home', function(){
		var todos = new Todos();
		todos.comparator = "order"; //to override!
		
		// todos.comparator = function(todo1, todo2){
		// 	console.log("todo1: " + todo1.attributes.id);
		// 	console.log("todo2: " + todo2.attributes.id);
		// 	return todo1.get('status') < todo2.get('status')
		// };
		
		todos.fetch({
			success: function(){
				var todosView = new TodosView({
					collection:todos
				});
				todosView.render();
			}
		});
	});
	//single
	router.on('route:single', function(id){
		var todo = new Todo({id:id});
		todo.fetch({
			success: function(){
				var todoView = new TodoView({
					model:todo, 
					stuff:"my stuff"
				});
				todoView.renderOne();
			}
		});
	});
	//form
	router.on('route:form', function(id){
		var todo = new Todo({id:id});
		todo.fetch({
			success: function(){
				var formView = new FormView({
					model:todo, 
					stuff:"my stuff"
				});
				formView.render();
			}
		});
	});


	Backbone.history.start({ pushState: true });
	//click events cancel and route navigate
	$(document).on("click", "a[href^='/']", function(e) {
		$href = $(this).attr('href');
		router.navigate($href, true);
		e.preventDefault();
	});

</script>

</body>
</html>
