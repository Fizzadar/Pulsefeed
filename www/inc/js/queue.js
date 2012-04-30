/*
	file: inc/js/queue.js
	desc: template functions
*/
var queue = {};
queue.items = new Array();
queue.timer = 0;
queue.pos = 0;

//start the queue
queue.start = function() {
	setInterval( this.process, 50 );
}

//add to queue
queue.add = function( func, time, args ) {
	this.items[this.items.length] = {
		time: time,
		func: func,
		args: args
	};
}

//process the queue
queue.process = function() {
	//empty list?
	if( queue.items[queue.pos] == undefined )
		return;

	//timer up?
	if( queue.timer <= 0 ) {
		//do next item
		queue.items[queue.pos].func( queue.items[queue.pos].args );
		//up timer
		queue.timer = queue.items[queue.pos].time;
		//up position
		queue.pos++;
	}

	//decrease timer
	queue.timer -= 50;
}