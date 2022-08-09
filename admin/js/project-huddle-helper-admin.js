(function( $ ) {

	/**
	 * Ajax call to add all sub-sites to the project huddle
	 *
	 * @type {*|jQuery|HTMLElement}
	 */
	var add_sites_loader = $( 'span#ph_network_add_sites_status' );
	var remove_sites_loader = $( 'span#ph_network_remove_sites_status' );

	var add_sites = $("#add_all_subsites_to_projecthuddle2");
	var remove_sites = $("#remove_all_subsites_to_projecthuddle2");

	 add_sites.on('click', function(e) {
		 e.preventDefault();
		 // do ajax request
		 $.ajax({
			 url: ph_network_vars.ajaxurl,
			 method: 'POST',
			 data: {
				 action: 'ph_network_sub_sites',
				 nonce: ph_network_vars.nonce,
				 job: 'add'
			 },
			 beforeSend: function() {
				 add_sites_loader.css('display', 'inline-block');
			 }
		 })
		 .done(function(data) {
			 add_sites_loader.hide();
			 console.log('data: ', data);
		 })
		 .fail(function(error) {
			 console.log('error: ', error.responseJSON);
		 });
	 });

	 remove_sites.on('click', function(e) {
		 e.preventDefault();
		 // do ajax request
		 $.ajax({
			 url: ph_network_vars.ajaxurl,
			 method: 'POST',
			 data: {
				 action: 'ph_network_sub_sites',
				 nonce: ph_network_vars.nonce,
				 job: 'remove'
			 },
			 beforeSend: function() {
				 remove_sites_loader.css('display', 'inline-block');
			 }
		 })
		 .done(function(data) {
			 remove_sites_loader.hide();
			 console.log('data: ', data);
		 }).fail(function(error) {
			 console.log('error: ', error.responseJSON);
		 });
	 });


})( jQuery );
