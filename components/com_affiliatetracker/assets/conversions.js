// JavaScript Document

window.addEvent('domready', function() {
	
	Calendar.setup({
		// Id of the input field
		inputField: "date_in",
		// Format of the input field
		ifFormat: "%Y-%m-%d",
		// Trigger for the calendar (button ID)
		button: "date_in_button",
		// Alignment (defaults to "Bl")
		align: "Tl",
		singleClick: true,
		firstDay: 0
	});
	
	Calendar.setup({
		// Id of the input field
		inputField: "date_out",
		// Format of the input field
		ifFormat: "%Y-%m-%d",
		// Trigger for the calendar (button ID)
		button: "date_out_button",
		// Alignment (defaults to "Bl")
		align: "Tl",
		singleClick: true,
		firstDay: 0
	});
	
});
