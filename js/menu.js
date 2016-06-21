window.addEvent('domready', function(){
	
	// The same as before: adding events
	$('menu1').addEvents({
		'mouseenter': function(){
			// Always sets the duration of the tween to 1000 ms and a bouncing transition
			// And then tweens the height of the element
			this.set('tween', {
				duration: 1000,
				transition: Fx.Transitions.Bounce.easeOut // This could have been also 'bounce:out'
			}).tween('height', '180px');
		},
		'mouseleave': function(){
			// Resets the tween and changes the element back to its original size
			this.set('tween', {}).tween('height', '20px');
		}
	});
	
		$('menu2').addEvents({
		'mouseenter': function(){
			// Always sets the duration of the tween to 1000 ms and a bouncing transition
			// And then tweens the height of the element
			this.set('tween', {
				duration: 1000,
				transition: Fx.Transitions.Bounce.easeOut // This could have been also 'bounce:out'
			}).tween('height', '180px');
		},
		'mouseleave': function(){
			// Resets the tween and changes the element back to its original size
			this.set('tween', {}).tween('height', '20px');
		}
	});
		
		$('menu3').addEvents({
		'mouseenter': function(){
			// Always sets the duration of the tween to 1000 ms and a bouncing transition
			// And then tweens the height of the element
			this.set('tween', {
				duration: 1000,
				transition: Fx.Transitions.Bounce.easeOut // This could have been also 'bounce:out'
			}).tween('height', '180px');
		},
		'mouseleave': function(){
			// Resets the tween and changes the element back to its original size
			this.set('tween', {}).tween('height', '20px');
		}
	});
		
		$('menu4').addEvents({
		'mouseenter': function(){
			// Always sets the duration of the tween to 1000 ms and a bouncing transition
			// And then tweens the height of the element
			this.set('tween', {
				duration: 1000,
				transition: Fx.Transitions.Bounce.easeOut // This could have been also 'bounce:out'
			}).tween('height', '180px');
		},
		'mouseleave': function(){
			// Resets the tween and changes the element back to its original size
			this.set('tween', {}).tween('height', '20px');
		}
	});
		
		$('menu5').addEvents({
		'mouseenter': function(){
			// Always sets the duration of the tween to 1000 ms and a bouncing transition
			// And then tweens the height of the element
			this.set('tween', {
				duration: 1000,
				transition: Fx.Transitions.Bounce.easeOut // This could have been also 'bounce:out'
			}).tween('height', '180px');
		},
		'mouseleave': function(){
			// Resets the tween and changes the element back to its original size
			this.set('tween', {}).tween('height', '20px');
		}
	});

});