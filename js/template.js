// Modals
(function() {
	
	"use strict"

	function qs( q ) {
		return document.querySelector( q );
	}
	function qsa( q ) {
		return document.querySelectorAll( q );
	}
	var zIndex = 1000;
	var mobile;
	var freezeScrollPos;

	function modal( elem, focus )
	{
		if( !elem ) {
			return this;
		}

		mobile = false;
		window.onkeydown = function(e) {
			if(e.keyCode == 27) this.close();
		}.bind(this);

		window.addEventListener('resize', this.resize);

		var overlay = document.createElement('div');
		overlay.className = 'modalx-overlay close_mw',
		overlay.style.zIndex = zIndex,
		document.body.insertBefore(overlay, document.body.childNodes[0]);

		var win = qs( elem ).cloneNode( true );
		overlay.appendChild( win );
		win.style.display = 'block';
		win.style.zIndex = zIndex+1;
		var top = (window.innerHeight / 2) - (win.offsetHeight / 2);

		if( !mobile ) {
			win.style.top = (top < 20 ? 20 : top) + 'px';
		}

		overlay.style.visibility = 'visible';
		overlay.style.background = 'rgba(0, 0, 0, 0.9)';
		win.style.visibility = 'visible';
		win.style.transform =  'scale(1)';

		this.close = this.close.bind(this);
		var cl = qsa('.close_mw');
		for(var i=0; i<cl.length; ++i ) {
			cl[i].addEventListener('click', this.close);
		}

		if(document.body.scrollHeight - document.body.offsetHeight <= 0) {
			document.body.style.marginRight = scrollbarWidth() + 'px';
		}
		document.body.style.overflow = 'hidden';
		freezeScrollPos = $('html, body').scrollTop();
		window.addEventListener('scroll', freezeScroll);

		if( focus ) {
			setTimeout(function() {
				win.querySelector( focus ).focus();
			}, 200);
		}
		zIndex += 2;
		return false;
	}
	modal.arrows = modal.prototype.arrows = function(prev, next) {
		var w = qs('.modalx-overlay');
		if( prev ) {
			if( !qs('#prev_arrow') ) {
				var p = document.createElement('a');
				p.id = 'prev_arrow';
				p.href = '#';
				p.onclick = prev;
				w.insertBefore(p, w.firstChild);
			} else {
				qs('#prev_arrow').onclick = prev;
			}
		} else if( qs('#prev_arrow') ) {
			qs('#prev_arrow').remove();
		}
		if( next ) {
			if( !qs('#next_arrow') ) {
				var n = document.createElement('a');
				n.id = 'next_arrow';
				n.href = '#';
				n.onclick = next;
				w.appendChild(n);
				var right = getComputedStyle(n).getPropertyValue('right');
				if(w.scrollHeight - w.offsetHeight > 0) {
					qs('#next_arrow').style.right = parseInt(right) + scrollbarWidth() + 'px';
				}
			} else {
				qs('#next_arrow').onclick = next;
			}
		} else if( qs('#next_arrow') ) {
			qs('#next_arrow').remove();
		}
	}
	modal.closeAll = modal.prototype.closeAll = function() {
		var windows = qsa('.modalx-overlay');
		for(var i=1; i<windows.length; ++i) {
			document.body.removeChild(windows[i]);
		}
		this.close();
		return false;
	}
	modal.close = modal.prototype.close = function( e )
	{
		if( e !== undefined && e.target != e.currentTarget ) {
			return false;
		}

		if( e !== undefined ) {
			e.preventDefault();
		}
		window.onkeydown = null;
		var modals = qsa('.modalx-overlay').length;
		if( !modals ) {
			return;
		}
		var lastModal = qsa('.modalx-overlay')[0];
		var w = lastModal.querySelector(".modal_window");

		w.style.transform = 'scale(0)';
		w.style.opacity = 0;

		lastModal.addEventListener("transitionend", function (e) {
			if(e.target.className != lastModal.className) {
				return;
			}

			if( modals == 1 ) {
				window.removeEventListener('scroll', freezeScroll);
				document.body.style.overflow = 'auto';
				document.body.style.marginRight = 0;
			}
			if( e.target.parentNode ) {
				e.target.parentNode.removeChild(e.target);
			}
		})
		lastModal.style.background = 'rgba(0,0,0,0)';
		modal.afterClose( w.id );
		return false;
	}
	modal.afterClose = function(id) {}

	modal.prototype.resize = function()
	{
		var overlay = qsa('.modalx-overlay');
		if( !overlay.length ) {
			return;
		}
		for( var i=0; i<overlay.length; ++i) {
			var win = overlay[i].querySelector('.modal_window');
			var top = (window.innerHeight / 2) - (win.offsetHeight / 2);
			win.style.top = (top < 20 ? 20 : top) + 'px';
		}
	}
	function scrollbarWidth() {
		var div = document.createElement('div');
		div.style.overflowY = 'scroll';
		div.style.width =  '50px';
		div.style.height = '50px';
		div.style.visibility = 'hidden';
		document.body.appendChild(div);
		var scrollWidth = div.offsetWidth - div.clientWidth;
		document.body.removeChild(div);
		return scrollWidth;
	}
	function freezeScroll() {
		$('html, body').scrollTop(freezeScrollPos);
	}
	window.modal = modal;
})();


// Navigation

(function($){
	$(window).on("load",function(){
		$(".navigation-menu a,a[href='#top'],a[rel='m_PageScroll2id']").mPageScroll2id({
			highlightSelector:".navigation-menu a"
		});
		$("a[rel='next']").click(function(e){
			e.preventDefault();
			var to=$(this).parent().parent("section").next().attr("id");
			$.mPageScroll2id("scrollTo",to);
		});

	});
})(jQuery);


// Slider

$(document).ready(function() {

	var slides = $(".slider .slides").children(".slide");
	var width = $(".slider .slide").height();
	var i = slides.length;
	var offset = i * width;
	var cheki = i-1;

	$(".slider .slides").css('height',offset);

	for (j=0; j < slides.length; j++) {
		if (j==0) {
			$(".slider .navigation").append("<div class='dot active'></div>");
		}
		else {
			$(".slider .navigation").append("<div class='dot'></div>");
		}
	}

	var dots = $(".slider .navigation").children(".dot");
	offset = 0;
	i = 0;

	$('.slider .navigation .dot').click(function(){
		$(".slider .navigation .active").removeClass("active");
		$(this).addClass("active");
		i = $(this).index();
		offset = i * width;

		$('.slide').removeClass('active');
		var index=offset/width+1;
		$('.slider .slide:nth-child('+(index)+')').addClass('active');

		$(".slider .slides").css("transform","translate3d(0px, -"+offset+"px, 0px)");
	});



	$("body .slider .next").click(function(){
		if (offset < width * cheki) {
			offset += width;

			$('.slide').removeClass('active');
			var index=offset/width+1;
			$('.slider .slide:nth-child('+(index)+')').addClass('active');

			$(".slider .slides").css("transform","translate3d(0px, -"+offset+"px, 0px)");
			$(".slider .navigation .active").removeClass("active");
			$(dots[++i]).addClass("active");
		}
	});


	$("body .slider .prev").click(function(){
		if (offset > 0) {
			offset -= width;

			$('.slide').removeClass('active');
			var index=offset/width+1;
			$('.slider .slide:nth-child('+(index)+')').addClass('active');

			$(".slider .slides").css("transform","translate3d(0px, -"+offset+"px, 0px)");
			$(".slider .navigation .active").removeClass("active");
			$(dots[--i]).addClass("active");
		}
	});
	function autoSlide(){
		if ($(".slider .navigation .active").index() < $(".slider .slides").children(".slide").length-1) {
			$("body .slider .next").click();
		} else {
			$('.slider .navigation .dot:first-child').click();
		}
	}
	setInterval(function(){ autoSlide(); }, 9000);
});

// Gallery

$( document ).ready(function( $ ) {
	$( '#pictures' ).sliderPro({
		width: 774,
		height: 326,
		fade: true,
		arrows: true,
		buttons: false,
		fullScreen: true,
		shuffle: true,
		largeSize: 3000,
		thumbnailArrows: true,
		autoplay: true
	});
});


// Select

$('select.stylized').each(function(){
	var $this = $(this), numberOfOptions = $(this).children('option').length;

	$this.addClass('select-hidden');
	$this.wrap('<div class="select"></div>');
	$this.after('<div class="select-styled"></div>');

	var $styledSelect = $this.next('div.select-styled');
	$styledSelect.text($this.children('option').eq(0).text());

	var $list = $('<ul />', {
		'class': 'select-options'
	}).insertAfter($styledSelect);

	for (var i = 0; i < numberOfOptions; i++) {
		$('<li />', {
			text: $this.children('option').eq(i).text(),
			rel: $this.children('option').eq(i).val()
		}).appendTo($list);
	}

	var $listItems = $list.children('li');

	$styledSelect.click(function(e) {
		e.stopPropagation();
		$('div.select-styled.active').not(this).each(function(){
			$(this).removeClass('active').next('ul.select-options').hide();
		});
		$(this).toggleClass('active').next('ul.select-options').toggle();
	});

	$listItems.click(function(e) {
		e.stopPropagation();
		$styledSelect.text($(this).text()).removeClass('active');
		$this.val($(this).attr('rel'));
		$list.hide();
		//console.log($this.val());
	});

	$(document).click(function() {
		$styledSelect.removeClass('active');
		$list.hide();
	});

});

// Tabs

$('.btn-block').click(function(){
	$('.btn-block').removeClass('active');
	$(this).addClass('active');
	$('.step-block').removeClass('active');
	$('#'+$(this).attr('data-tab')).addClass('active');
})