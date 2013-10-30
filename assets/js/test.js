/* 
 * =============================================================
 * MODEL
 * =============================================================
 */

//class
var TodoItem = Backbone.Model.extend({
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
		//console.log(this.get('description') + " is " + this.get('status'));
	}
});



/* 
 * =============================================================
 * VIEW
 * =============================================================
 */

//class
var TodoView = Backbone.View.extend({
	tagName: 'li',
	id: 'todo-view',
	className: 'todo',

	template: _.template($('#todo-tpl').html()), //see backbone guy video for using this in the html file and selecting with jquery
	initialize: function(){
		this.model.on('change', this.render, this); //third this is to refer to the view instance instead of window because of context
		this.model.on('destroy', this.remove, this);
		this.model.on('hide', this.destroy, this); //getting called from the collection
	},

	render: function(){
		var attributes = this.model.toJSON();
		this.$el.html(this.template(attributes));
		console.log(attributes)
	},
	
	destroy: function(){
		this.$el.remove();
	},
	toggleStatus: function(e) {
		this.model.toggleStatus();
	},
	events: {
		'change input' : 'toggleStatus'
	}
	
});


//instance
//var todoView = new TodoView({model: todoItem})


/* 
 * =============================================================
 * COLLECTIONS
 * =============================================================
 */

var TodoList = Backbone.Collection.extend({
	url: '/api/todos',
	model: TodoItem,
	initialize: function(){
		this.on('remove', this.hideModel);
	},
	hideModel: function(model){//model coming from model above (TodoItem)
		model.trigger('hide');
	},
	
	// focusOnTodoItem: function(id) {
	// 	var modelsToRemove = this.filter(function(todoItem) {
	// 		return todoItem.id != id;
	// 	});
	// 
	// 	this.remove(modelsToRemove);
	// }
	
});
var todoList = new TodoList();



/* 
 * =============================================================
 * COLLECTIONS VIEW
 * =============================================================
 */
var TodoListView = Backbone.View.extend({
	tagName: 'ul',
	id: 'todo-view-list',
	className: 'todo-list',
	
	initialize: function(){
		this.collection.on('add', this.addOne, this);
		this.collection.on('reset', this.addAll, this);
	},
	render: function(options){
		
		//this.addAll(); //why is his firing addAll??
	},
	addOne: function(todoItem){
		var todoView = new TodoView({model: todoItem});
		//code school has this code that doesn't work:
		//this.$el.append(todoView.render().el);
		todoView.render(); 
		this.$el.append(todoView.el);
	},
	addAll: function(){
		this.$el.empty(); //this!!! problem with all the crazy dups fixed BUT ZOMBIES
		this.collection.forEach(this.addOne, this);
		console.log('add all fired');
	}
});
var todoListView = new TodoListView({collection: todoList})

/* 
 * =============================================================
 * ROUTER
 * =============================================================
 */
	var TodoRouter = Backbone.Router.extend({
		routes: {
			"" : "index",
			"todos/:id": "show"
		},
		index: function() {
			console.log("home");
			//creates the collection with my array (poor mans JSON response)
			todoList.fetch();
			//todoList.reset(todos); //fetch without a db (fetch will intelligently merge unless you pass {reset:true} which is what this is.)
			//todoList.set(todos); //merges them...so the order changes as u click and stuff
		},
		show: function(id){
			//todoList.reset(todos);
			//todoList.fetch();
			//this.todoList.focusOnTodoItem(id);
			todoList.fetch({id:id});
			console.log(id);
		},
		initialize: function(options){
			this.todoList = options.todoList;
		}
		
	});
	// Initiate the router
	var router = new TodoRouter({todoList: todoList});
	// Start Backbone history a necessary step for bookmarkable URL's
	Backbone.history.start({ pushState: true });

	//navigate with push state
	$(document).on("click", "a[href^='/']", function(e) {
		$href = $(this).attr('href');
		router.navigate($href, true);
		e.preventDefault();
	});


/* 
 * =============================================================
 * DO STUFF
 * =============================================================
 */
	todoListView.render();
	$('#body').append(todoListView.el);
	
	
	
	
