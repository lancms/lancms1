// Scripts for ontime

// Start timeago
window.addEventListener('DOMContentLoaded', function() {
	var timeagoInstance = new timeago();
	// use render to render it in real time
	timeagoInstance.render(document.querySelectorAll('.timeago-js'));
});

function showNewComment() {
    $("button.newcomment").remove();
    $("div.new-comment").show();
}
