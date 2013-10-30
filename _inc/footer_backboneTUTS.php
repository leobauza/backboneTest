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

	Person = Backbone.Model.extend({
		defaults: {
			name: 'Fetus',
			age: 0
		},
		initialize: function(){
			alert("Welcome to this world");
			this.on("change:name", function(model){
				var name = model.get("name"); // 'Stewie Griffin'
				alert("Changed my name to " + name );
			});
		}
	});

	var person = new Person({ name: "Thomas", age: 67});
	person.set({name: 'Stewie Griffin'}); // This triggers a change and will alert()

	</script>

</body>
</html>
