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

	
		var Todo = Backbone.Model.extend({
			urlRoot: 'api/todos'
		});

		var TodoView = Backbone.View.extend({
			//template: _.template("<%= description %>"),
			//el: '.page',
			initialize: function(){
				this.setElement('.page');
			},
			render: function(){
				
				//The many ways to render out the template...still not sure how that works exactly...but if you pass the attributes it just works

				// var template = _.template("<%= todo.get('description') %>" , {todo: this.model});
				// var $view = this.$el.html(template);

				// var template = _.template("<%= description %>");
				// var $view = this.$el.html(template(this.model.attributes));

				//var $view = this.$el.html(_.template("<%= description %>")(this.model.attributes));

				var template = _.template("<h3> <%= description %> </h3>")(this.model.attributes);
				this.$el.html(template);
				//this.setElement(template);
			},
			click: function(){
				alert('click');
			},
			events: {
				'click h3' : 'click'
			}
		});

		var todo = new Todo({id:1});
		todo.fetch({
			success: function(){

				var todoView = new TodoView({model: todo});
				todoView.render();

				//fetch before success just has the ID of 1 after it actually has the attrs
				//console.log(todo.attributes);
			}
		});
		
		
	
	</script>

	<!-- <script src="../assets/js/initial.js"></script> 
	<script src="../assets/js/script.js"></script> -->

</body>
</html>
