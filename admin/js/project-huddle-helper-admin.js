(function( $ ) {

	/**
	 * Ajax call to add all sub-sites to the project huddle
	 *
	 * @type {*|jQuery|HTMLElement}
	 */
	 var add_sites = $("#add_all_subsites_to_projecthuddle2");
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
		 })
		 .done(function(data) {
			 console.log('data: ', data);
		 })
		 .fail(function(error) {
			 console.log('error: ', error.responseJSON);
		 });
	 });


})( jQuery );
